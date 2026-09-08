<?php

require_once __DIR__ . '/../helpers.php';
requireAuth('admin', '../login.php?status=error&message=' . urlencode('Please log in with an administrator account.'));

require_once __DIR__ . '/../database/config.php';

$adminId = getCurrentUserId();
$redirectTarget = 'dashboard.php?status=error&message=';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    redirect($redirectTarget . urlencode('Invalid request method.') . '#payouts');
}

verifyCsrfOrRedirect($redirectTarget . urlencode('Invalid security token. Please refresh the page and try again.') . '#payouts');

$payoutId             = isset($_POST['payout_id']) ? (int)$_POST['payout_id'] : 0;
$action               = trim($_POST['action'] ?? '');
$transactionReference = trim($_POST['transaction_reference'] ?? '');
$adminNotes           = trim($_POST['admin_notes'] ?? '');

if ($payoutId <= 0) {
    header('Location: ' . $redirectTarget . urlencode('Invalid payout request specified.') . '#payouts');
    exit;
}

if (!in_array($action, ['approve', 'reject'], true)) {
    header('Location: ' . $redirectTarget . urlencode('Invalid processing action selected.') . '#payouts');
    exit;
}

if ($action === 'reject' && empty($adminNotes)) {
    header('Location: ' . $redirectTarget . urlencode('Please provide an administrative reason when rejecting a withdrawal request so the instructor knows why.') . '#payouts');
    exit;
}

try {
    $pdo = getConnection();
    $pdo->beginTransaction();

    // 1. Fetch pending payout request with row-level lock
    $stmtRequest = $pdo->prepare("SELECT p.*, u.full_name AS instructor_name, u.email AS instructor_email 
                                  FROM payout_requests p
                                  JOIN users u ON p.instructor_id = u.id
                                  WHERE p.id = :pid AND p.status = 'pending'
                                  FOR UPDATE");
    $stmtRequest->execute([':pid' => $payoutId]);
    $payout = $stmtRequest->fetch();

    if (!$payout) {
        $pdo->rollBack();
        header('Location: ' . $redirectTarget . urlencode('Payout request not found or has already been reviewed.') . '#payouts');
        exit;
    }

    $instructorId   = (int)$payout['instructor_id'];
    $amount         = (float)$payout['amount'];
    $instructorName = $payout['instructor_name'];

    if ($action === 'approve') {
        // A. Update payout request status to completed
        $stmtApprove = $pdo->prepare("UPDATE payout_requests 
                                      SET status = 'completed',
                                          transaction_reference = :ref,
                                          admin_notes = :notes,
                                          processed_by = :admin_id,
                                          processed_at = CURRENT_TIMESTAMP 
                                      WHERE id = :pid");
        $stmtApprove->execute([
            ':ref'      => !empty($transactionReference) ? $transactionReference : null,
            ':notes'    => !empty($adminNotes) ? $adminNotes : 'Payout approved and funds disbursed.',
            ':admin_id' => $adminId,
            ':pid'      => $payoutId
        ]);

        // B. Increment total_withdrawn in instructor_wallets
        $stmtWallet = $pdo->prepare("UPDATE instructor_wallets 
                                     SET total_withdrawn = total_withdrawn + :amt 
                                     WHERE instructor_id = :iid");
        $stmtWallet->execute([
            ':amt' => $amount,
            ':iid' => $instructorId
        ]);

        $pdo->commit();

        $successMsg = "Payout #" . $payoutId . " of $" . number_format($amount, 2) . " for " . $instructorName . " approved and marked as disbursed!";
        header('Location: dashboard.php?status=success&message=' . urlencode($successMsg) . '#payouts');
        exit;

    } elseif ($action === 'reject') {
        // A. Update payout request status to rejected
        $stmtReject = $pdo->prepare("UPDATE payout_requests 
                                     SET status = 'rejected',
                                         admin_notes = :notes,
                                         processed_by = :admin_id,
                                         processed_at = CURRENT_TIMESTAMP 
                                     WHERE id = :pid");
        $stmtReject->execute([
            ':notes'    => $adminNotes,
            ':admin_id' => $adminId,
            ':pid'      => $payoutId
        ]);

        // B. Auto-refund the locked amount back into the instructor's available_balance
        $stmtRefund = $pdo->prepare("UPDATE instructor_wallets 
                                     SET available_balance = available_balance + :amt 
                                     WHERE instructor_id = :iid");
        $stmtRefund->execute([
            ':amt' => $amount,
            ':iid' => $instructorId
        ]);

        $pdo->commit();

        $rejectMsg = "Payout #" . $payoutId . " for " . $instructorName . " rejected. $" . number_format($amount, 2) . " has been refunded back to their available balance.";
        header('Location: dashboard.php?status=success&message=' . urlencode($rejectMsg) . '#payouts');
        exit;
    }

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Admin process payout error: ' . $e->getMessage());
    header('Location: ' . $redirectTarget . urlencode('An error occurred while processing the payout request. Please try again.') . '#payouts');
    exit;
}
