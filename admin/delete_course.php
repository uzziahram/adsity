<?php

require_once __DIR__ . '/../helpers.php';
requireAuth('admin', '../login.php?status=error&message=' . urlencode('Please log in with an administrator account.'));

require_once __DIR__ . '/../database/config.php';

if (!isset($_POST['delete_course'])) {
    redirect('dashboard.php#courses');
}

verifyCsrfOrRedirect('dashboard.php?status=error&message=' . urlencode('Invalid security token. Please refresh the page and try again.') . '#courses');

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
    error_log('Admin delete course error: ' . $e->getMessage());
    header('Location: dashboard.php?status=error&message=' . urlencode('An error occurred while deleting the course. Please try again.') . '#courses');
    exit;
}
