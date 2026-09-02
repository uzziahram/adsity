<?php

session_start();

require 'database/config.php';
require 'validation.php';

if (!isset($_POST['teach'])) {
    header('Location: teach.php');
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

    $sql = "INSERT INTO users (full_name, email, password, role_id)
            VALUES (:full_name, :email, :password, :role_id)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':full_name', $result['data']['full_name']);
    $stmt->bindValue(':email', $result['data']['email']);
    $stmt->bindValue(':password', $result['data']['password']);
    $stmt->bindValue(':role_id', $result['data']['role_id'], PDO::PARAM_INT); // 2 = instructor
    $stmt->execute();

    $newId = $pdo->lastInsertId();

    // Set session data
    $_SESSION['user_id']   = $newId;
    $_SESSION['full_name'] = $result['data']['full_name'];
    $_SESSION['email']     = $result['data']['email'];
    $_SESSION['role_id']   = 2;
    $_SESSION['role_name'] = 'instructor';

    header('Location: instructor/dashboard.php?status=success&message=' . urlencode('Welcome to Adsity Instructor Studio, ' . $result['data']['full_name'] . '!'));
    exit;
} catch (PDOException $e) {
    header('Location: teach.php?status=error&message=' . urlencode($e->getMessage()));
    exit;
}
