<?php

require '../database/config.php';

if (!isset($_POST['delete_user'])) {
    header('Location: dashboard.php');
    exit;
}

$userId = $_POST['user_id'] ?? null;

if (!$userId) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Invalid user ID.'));
    exit;
}

try {
    $pdo = getConnection();

    // Prevent deleting admin accounts (role_id != 1)
    $sql  = "DELETE FROM users WHERE id = :id AND role_id != 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: dashboard.php?status=success&message=' . urlencode('User deleted successfully.'));
    exit;
} catch (PDOException $e) {
    header('Location: dashboard.php?status=error&message=' . urlencode($e->getMessage()));
    exit;
}
