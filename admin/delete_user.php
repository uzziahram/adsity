<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protect Admin Panel: Must be logged in as admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'admin') {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in with an administrator account.'));
    exit;
}

require_once __DIR__ . '/../database/config.php';

if (!isset($_POST['delete_user'])) {
    header('Location: dashboard.php#users');
    exit;
}

$userId      = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$currentAdmin = (int)$_SESSION['user_id'];

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
    header('Location: dashboard.php?status=error&message=' . urlencode($e->getMessage()) . '#users');
    exit;
}
