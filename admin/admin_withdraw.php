<?php

require_once __DIR__ . '/../helpers.php';
requireAuth('admin', '../login.php?status=error&message=' . urlencode('Unauthorized: Administrator access required.'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['admin_withdraw'])) {
    redirect('dashboard.php?status=error&message=' . urlencode('Invalid withdrawal request.') . '#payouts');
}

verifyCsrfOrRedirect('dashboard.php?status=error&message=' . urlencode('Invalid security token. Please refresh the page and try again.') . '#payouts');

require_once __DIR__ . '/../database/config.php';

$adminId       = getCurrentUserId();
$amount        = (float)($_POST['amount'] ?? 0);
$bankName      = trim($_POST['bank_name'] ?? '');
$accountName   = trim($_POST['account_name'] ?? '');
$accountNumber = trim($_POST['account_number'] ?? '');
$notes         = trim($_POST['notes'] ?? '');

// 2. Validation
if ($amount <= 0) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Please enter a valid withdrawal amount greater than $0.00.') . '#payouts');
    exit;
}

if ($bankName === '' || $accountName === '' || $accountNumber === '') {
    header('Location: dashboard.php?status=error&message=' . urlencode('Bank Name, Account Holder Name, and Account Number are all required.') . '#payouts');
    exit;
}

try {
    $pdo = getConnection();

    // 3. Check Platform Treasury available balance with row lock
    $pdo->beginTransaction();

    $stmtWallet = $pdo->query("SELECT id, total_earned, available_balance, total_withdrawn 
                               FROM platform_wallet 
                               WHERE id = 1 
                               FOR UPDATE");
    $wallet = $stmtWallet ? $stmtWallet->fetch() : null;

    if (!$wallet) {
        $pdo->rollBack();
        header('Location: dashboard.php?status=error&message=' . urlencode('Platform Treasury Wallet record not found.') . '#payouts');
        exit;
    }

    $availableBal = (float)$wallet['available_balance'];

    if ($amount > $availableBal) {
        $pdo->rollBack();
        $msg = sprintf('Withdrawal failed: Requested $%s exceeds current available platform balance of $%s.', number_format($amount, 2), number_format($availableBal, 2));
        header('Location: dashboard.php?status=error&message=' . urlencode($msg) . '#payouts');
        exit;
    }

    // 4. Deduct available balance and increment total withdrawn
    $stmtUpdateWallet = $pdo->prepare("UPDATE platform_wallet 
                                       SET available_balance = available_balance - :amt,
                                           total_withdrawn = total_withdrawn + :amt2 
                                       WHERE id = 1");
    $stmtUpdateWallet->execute([
        ':amt'  => $amount,
        ':amt2' => $amount
    ]);

    // 5. Generate Bank Transaction Reference Code
    $txRef = 'BNK-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));

    // 6. Record withdrawal in admin_withdrawals ledger
    $stmtInsert = $pdo->prepare("INSERT INTO admin_withdrawals 
                                 (admin_id, amount, bank_name, account_name, account_number, transaction_reference, notes, status, created_at)
                                 VALUES (:aid, :amt, :bname, :aname, :anum, :tx, :notes, 'completed', CURRENT_TIMESTAMP)");
    $stmtInsert->execute([
        ':aid'   => $adminId,
        ':amt'   => $amount,
        ':bname' => $bankName,
        ':aname' => $accountName,
        ':anum'  => $accountNumber,
        ':tx'    => $txRef,
        ':notes' => $notes !== '' ? $notes : null
    ]);

    $pdo->commit();

    $successMsg = sprintf('Successfully transferred $%s to %s (Account: %s). Reference: %s', number_format($amount, 2), htmlspecialchars($bankName), htmlspecialchars($accountNumber), $txRef);
    header('Location: dashboard.php?status=success&message=' . urlencode($successMsg) . '#payouts');
    exit;

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Database error processing withdrawal: ' . $e->getMessage());
    header('Location: dashboard.php?status=error&message=' . urlencode('A database error occurred while processing your withdrawal. Please try again later.') . '#payouts');
    exit;
}
