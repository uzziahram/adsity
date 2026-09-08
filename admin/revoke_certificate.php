<?php

require_once __DIR__ . '/../validation.php';
requireAuth('admin', '../login.php?status=error&message=' . urlencode('Unauthorized: Administrator access required.'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['certificate_id'], $_POST['action'])) {
    redirect('dashboard.php?status=error&message=' . urlencode('Invalid certificate action request.') . '#analytics');
}

verifyCsrfOrRedirect('dashboard.php?status=error&message=' . urlencode('Invalid security token. Please refresh the page and try again.') . '#analytics');

require_once __DIR__ . '/../database/config.php';

$adminId = getCurrentUserId();
$certId  = (int)$_POST['certificate_id'];
$action  = trim($_POST['action']);
$reason  = trim($_POST['revocation_reason'] ?? '');

if ($certId <= 0) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Invalid Certificate ID.') . '#analytics');
    exit;
}

try {
    $pdo = getConnection();

    // Verify certificate exists
    $stmtCheck = $pdo->prepare("SELECT c.id, c.certificate_code, u.full_name AS student_name 
                                 FROM certificates c 
                                 JOIN users u ON c.user_id = u.id 
                                 WHERE c.id = :id LIMIT 1");
    $stmtCheck->execute([':id' => $certId]);
    $cert = $stmtCheck->fetch();

    if (!$cert) {
        header('Location: dashboard.php?status=error&message=' . urlencode('Certificate record not found.') . '#analytics');
        exit;
    }

    if ($action === 'revoke') {
        if ($reason === '') {
            $reason = 'Credential revoked following academic integrity review.';
        }

        $stmtUpdate = $pdo->prepare("UPDATE certificates 
                                     SET status = 'revoked', 
                                         revocation_reason = :reason, 
                                         revoked_at = CURRENT_TIMESTAMP, 
                                         revoked_by = :admin_id 
                                     WHERE id = :id");
        $stmtUpdate->execute([
            ':reason'   => $reason,
            ':admin_id' => $adminId,
            ':id'       => $certId
        ]);

        $msg = "Certificate '{$cert['certificate_code']}' issued to {$cert['student_name']} has been officially revoked.";
        header('Location: dashboard.php?status=success&message=' . urlencode($msg) . '#analytics');
        exit;

    } elseif ($action === 'restore') {
        $stmtUpdate = $pdo->prepare("UPDATE certificates 
                                     SET status = 'valid', 
                                         revocation_reason = NULL, 
                                         revoked_at = NULL, 
                                         revoked_by = NULL 
                                     WHERE id = :id");
        $stmtUpdate->execute([':id' => $certId]);

        $msg = "Certificate '{$cert['certificate_code']}' has been reinstated to Verified / Valid status.";
        header('Location: dashboard.php?status=success&message=' . urlencode($msg) . '#analytics');
        exit;

    } else {
        header('Location: dashboard.php?status=error&message=' . urlencode('Invalid certificate action.') . '#analytics');
        exit;
    }

} catch (PDOException $e) {
    error_log('Admin certificate action error: ' . $e->getMessage());
    header('Location: dashboard.php?status=error&message=' . urlencode('An error occurred while updating the certificate status. Please try again.') . '#analytics');
    exit;
}
