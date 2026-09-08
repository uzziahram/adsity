<?php

require_once __DIR__ . '/../helpers.php';
requireAuth('instructor', '../login.php?status=error&message=' . urlencode('Please log in with an instructor account.'));

require_once __DIR__ . '/../database/config.php';

if (!isset($_POST['delete_course'])) {
    redirect('dashboard.php');
}

verifyCsrfOrRedirect('dashboard.php?status=error&message=' . urlencode('Invalid security token. Please refresh the page and try again.'));

$courseId     = $_POST['course_id'] ?? null;
$instructorId = getCurrentUserId();

if (!$courseId) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Invalid course ID.'));
    exit;
}

try {
    $pdo = getConnection();

    // Only allow instructor to delete their own course
    $sql = "DELETE FROM courses WHERE id = :id AND instructor_id = :instructor_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $courseId, PDO::PARAM_INT);
    $stmt->bindValue(':instructor_id', $instructorId, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: dashboard.php?status=success&message=' . urlencode('Course deleted successfully.'));
    exit;
} catch (PDOException $e) {
    error_log('Instructor delete course error: ' . $e->getMessage());
    header('Location: dashboard.php?status=error&message=' . urlencode('An error occurred while deleting your course. Please try again.'));
    exit;
}
