<?php

require_once __DIR__ . '/../validation.php';
requireAuth('admin', '../login.php?status=error&message=' . urlencode('Please log in with an administrator account.'));

require_once __DIR__ . '/../database/config.php';

if (!isset($_POST['delete_user'])) {
    redirect('dashboard.php#users');
}

verifyCsrfOrRedirect('dashboard.php?status=error&message=' . urlencode('Invalid security token. Please refresh the page and try again.') . '#users');

$userId       = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$currentAdmin = getCurrentUserId();

if ($userId <= 0) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Invalid user ID.') . '#users');
    exit;
}

if ($userId === $currentAdmin) {
    header('Location: dashboard.php?status=error&message=' . urlencode('You cannot delete your own administrative account.') . '#users');
    exit;
}

try {
    $pdo = getConnection();

    // Prevent deleting admin accounts (role_id != 1)
    $sql  = "DELETE FROM users WHERE id = :id AND role_id != 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        header('Location: dashboard.php?status=success&message=' . urlencode('User removed from platform successfully.') . '#users');
    } else {
        header('Location: dashboard.php?status=error&message=' . urlencode('User could not be deleted or is a protected administrator.') . '#users');
    }
    exit;
} catch (PDOException $e) {
    error_log('Admin delete user error: ' . $e->getMessage());
    header('Location: dashboard.php?status=error&message=' . urlencode('An error occurred while removing the user account. Please try again.') . '#users');
    exit;
}
