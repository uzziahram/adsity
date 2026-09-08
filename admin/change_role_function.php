<?php

require_once __DIR__ . '/../helpers.php';
requireAuth('admin', '../login.php?status=error&message=' . urlencode('Please log in with an administrator account.'));

require_once __DIR__ . '/../database/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['change_role'])) {
    redirect('dashboard.php#users');
}

verifyCsrfOrRedirect('dashboard.php?status=error&message=' . urlencode('Invalid security token. Please refresh the page and try again.') . '#users');

$userId       = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$newRoleId    = isset($_POST['new_role_id']) ? (int)$_POST['new_role_id'] : 0;
$currentAdmin = getCurrentUserId();

if ($userId <= 0 || !in_array($newRoleId, [1, 2, 3], true)) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Invalid user or role parameters.') . '#users');
    exit;
}

if ($userId === $currentAdmin) {
    header('Location: dashboard.php?status=error&message=' . urlencode('You cannot alter your own administrative role.') . '#users');
    exit;
}

try {
    $pdo = getConnection();

    // Fetch user and check if target is protected root admin
    $stmtUser = $pdo->prepare("SELECT u.id, u.full_name, u.role_id, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = :id LIMIT 1");
    $stmtUser->execute([':id' => $userId]);
    $user = $stmtUser->fetch();

    if (!$user) {
        header('Location: dashboard.php?status=error&message=' . urlencode('Target user does not exist.') . '#users');
        exit;
    }

    // Protect root admin account (id 1)
    if ((int)$user['id'] === 1) {
        header('Location: dashboard.php?status=error&message=' . urlencode('The primary root administrator role cannot be altered.') . '#users');
        exit;
    }

    // Update role
    $stmtUpdate = $pdo->prepare("UPDATE users SET role_id = :role_id WHERE id = :id");
    $stmtUpdate->execute([
        ':role_id' => $newRoleId,
        ':id' => $userId
    ]);

    // If promoted to Instructor (role_id 2), ensure wallet is initialized
    if ($newRoleId === 2) {
        $stmtWallet = $pdo->prepare("INSERT INTO instructor_wallets (instructor_id, total_earned, available_balance, total_withdrawn) 
                                     VALUES (:id, 0.00, 0.00, 0.00) 
                                     ON DUPLICATE KEY UPDATE instructor_id = instructor_id");
        $stmtWallet->execute([':id' => $userId]);
    }

    // Get new role name for flash message
    $stmtRoleName = $pdo->prepare("SELECT name FROM roles WHERE id = :id LIMIT 1");
    $stmtRoleName->execute([':id' => $newRoleId]);
    $newRoleName = ucfirst($stmtRoleName->fetchColumn() ?: 'User');

    header('Location: dashboard.php?status=success&message=' . urlencode("User {$user['full_name']} has been updated to role '{$newRoleName}'.") . '#users');
    exit;

} catch (PDOException $e) {
    error_log('Admin change role error: ' . $e->getMessage());
    header('Location: dashboard.php?status=error&message=' . urlencode('An error occurred while updating the user role. Please try again.') . '#users');
    exit;
}
