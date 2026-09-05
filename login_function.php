<?php

session_start();

require 'database/config.php';
require 'validation.php';

if (!isset($_POST['login'])) {
    header('Location: login.php');
    exit;
}

$result = validateLoginInput($_POST);
$errors = $result['errors'];

if (!empty($errors)) {
    $message = implode(' ', $errors);
    header('Location: login.php?status=error&message=' . urlencode($message));
    exit;
}

try {
    $pdo = getConnection();

    $sql = "SELECT u.id, u.full_name, u.email, u.password, u.role_id, r.name AS role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.email = :email 
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $result['data']['email']);
    $stmt->execute();

    $user = $stmt->fetch();
    $inputPassword = $_POST['password'] ?? '';

    $passwordValid = $user && password_verify($inputPassword, $user['password']);


    // Immediately purge sensitive credentials and password hash from memory
    if ($user) {
        unset($user['password']);
    }
    unset($inputPassword, $_POST['password']);

    if (!$passwordValid) {
        header('Location: login.php?status=error&message=' . urlencode('Invalid email or password.'));
        exit;
    }

    // Set user session data
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email']     = $user['email'];
    $_SESSION['role_id']   = $user['role_id'];
    $_SESSION['role_name'] = $user['role_name'];

    // Role-based redirection
    if ($user['role_name'] === 'admin') {
        header('Location: admin/dashboard.php');
        exit;
    }

    if ($user['role_name'] === 'instructor') {
        header('Location: instructor/dashboard.php');
        exit;
    }

    if ($user['role_name'] === 'student') {
        $redirectCourse = isset($_POST['redirect_course']) ? (int)$_POST['redirect_course'] : 0;
        if ($redirectCourse > 0) {
            // Check if already enrolled to direct straight to classroom
            $stmtEnr = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = :uid AND course_id = :cid LIMIT 1");
            $stmtEnr->execute([':uid' => $user['id'], ':cid' => $redirectCourse]);
            if ($stmtEnr->fetch()) {
                header('Location: student/learn.php?course_id=' . $redirectCourse);
                exit;
            }
            header('Location: course_details.php?id=' . $redirectCourse);
            exit;
        }
        header('Location: student/dashboard.php');
        exit;
    }

    header('Location: index.php?status=success&message=' . urlencode('Welcome back, ' . $user['full_name'] . '!'));
    exit;
} catch (PDOException $e) {
    header('Location: login.php?status=error&message=' . urlencode($e->getMessage()));
    exit;
}
