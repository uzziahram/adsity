<?php

require 'database/config.php';
require 'validation.php';

ensureSessionStarted();

if (!isset($_POST['teach'])) {
    header('Location: teach.php');
    exit;
}

if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    header('Location: teach.php?status=error&message=' . urlencode('Invalid security token. Please refresh the page and try again.'));
    exit;
}

$result = validateTeacherSignupInput($_POST);
$errors = $result['errors'];

if (!empty($errors)) {
    $message = implode(' ', $errors);
    header('Location: teach.php?status=error&message=' . urlencode($message));
    exit;
}

try {
    $pdo = getConnection();

    // Check if user already exists
    $checkSql = "SELECT id FROM users WHERE email = :email LIMIT 1";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->bindValue(':email', $result['data']['email']);
    $checkStmt->execute();

    if ($checkStmt->fetch()) {
        header('Location: teach.php?status=error&message=' . urlencode('An account with this email address already exists.'));
        exit;
    }

    $rawPassword = $_POST['password'] ?? '';
    $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (full_name, email, password, role_id)
            VALUES (:full_name, :email, :password, :role_id)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':full_name', $result['data']['full_name']);
    $stmt->bindValue(':email', $result['data']['email']);
    $stmt->bindValue(':password', $hashedPassword);
    $stmt->bindValue(':role_id', $result['data']['role_id'], PDO::PARAM_INT); // 2 = instructor
    $stmt->execute();

    // Immediately purge sensitive credentials and plain-text passwords from memory
    unset($rawPassword, $hashedPassword, $_POST['password'], $_POST['confirm_password']);

    $newId = (int)$pdo->lastInsertId();

    // Provision instructor wallet record ($0.00 initial balance)
    $stmtWallet = $pdo->prepare("INSERT INTO instructor_wallets (instructor_id, total_earned, available_balance, total_withdrawn) 
                                 VALUES (:id, 0.0000, 0.0000, 0.0000) 
                                 ON DUPLICATE KEY UPDATE instructor_id = instructor_id");
    $stmtWallet->execute([':id' => $newId]);

    // Prevent session fixation by regenerating session ID
    session_regenerate_id(true);

    // Set session data
    $_SESSION['user_id']   = $newId;
    $_SESSION['full_name'] = $result['data']['full_name'];
    $_SESSION['email']     = $result['data']['email'];
    $_SESSION['role_id']   = 2;
    $_SESSION['role_name'] = 'instructor';

    header('Location: instructor/dashboard.php?status=success&message=' . urlencode('Welcome to Adsity Instructor Studio, ' . $result['data']['full_name'] . '!'));
    exit;
} catch (PDOException $e) {
    error_log('Teacher signup error: ' . $e->getMessage());
    header('Location: teach.php?status=error&message=' . urlencode('A system error occurred while creating your instructor account. Please try again later.'));
    exit;
}
