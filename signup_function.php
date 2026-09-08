<?php
require 'database/config.php';
require 'validation.php';

ensureSessionStarted();

if (!isset($_POST['signup'])) {
    header('Location: signup.php');
    exit;
}

if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    header('Location: signup.php?status=error&message=' . urlencode('Invalid security token. Please refresh the page and try again.'));
    exit;
}

$result = validateSignupInput($_POST);
$errors = $result['errors'];

if (!empty($errors)) {
    $message = implode(' ', $errors);
    header('Location: signup.php?status=error&message=' . urlencode($message));
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
        header('Location: signup.php?status=error&message=' . urlencode('An account with this email address already exists.'));
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
    $stmt->bindValue(':role_id', 3, PDO::PARAM_INT); // 3 = student
    $stmt->execute();

    // Immediately purge sensitive credentials and plain-text passwords from memory
    unset($rawPassword, $hashedPassword, $_POST['password'], $_POST['confirm_password']);

    $newId = $pdo->lastInsertId();

    // Prevent session fixation by regenerating session ID
    session_regenerate_id(true);

    // Auto-login new student into session
    $_SESSION['user_id']   = $newId;
    $_SESSION['full_name'] = $result['data']['full_name'];
    $_SESSION['email']     = $result['data']['email'];
    $_SESSION['role_id']   = 3;
    $_SESSION['role_name'] = 'student';

    $redirectCourse = isset($_POST['redirect_course']) ? (int)$_POST['redirect_course'] : 0;
    if ($redirectCourse > 0) {
        header('Location: course_details.php?id=' . $redirectCourse);
        exit;
    }

    header('Location: student/dashboard.php');
    exit;
} catch (PDOException $e) {
    error_log('Signup error: ' . $e->getMessage());
    header('Location: signup.php?status=error&message=' . urlencode('A system error occurred while creating your account. Please try again later.'));
    exit;
}
