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

if (!isset($_POST['delete_course'])) {
    header('Location: dashboard.php#courses');
    exit;
}

$courseId = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;

if ($courseId <= 0) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Invalid course ID.') . '#courses');
    exit;
}

try {
    $pdo = getConnection();

    // Fetch course title for confirmation message
    $stmtTitle = $pdo->prepare("SELECT title FROM courses WHERE id = :id LIMIT 1");
    $stmtTitle->execute([':id' => $courseId]);
    $course = $stmtTitle->fetch();

    if (!$course) {
        header('Location: dashboard.php?status=error&message=' . urlencode('Course not found or already deleted.') . '#courses');
        exit;
    }

    $courseTitle = $course['title'];

    // Delete course (Cascades to lessons, enrollments, certificates, submissions, etc.)
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = :id");
    $stmt->execute([':id' => $courseId]);

    header('Location: dashboard.php?status=success&message=' . urlencode("Course '{$courseTitle}' (#{$courseId}) was successfully deleted.") . '#courses');
    exit;
} catch (PDOException $e) {
    header('Location: dashboard.php?status=error&message=' . urlencode($e->getMessage()) . '#courses');
    exit;
}
