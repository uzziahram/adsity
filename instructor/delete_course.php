<?php

session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'instructor') {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in with an instructor account.'));
    exit;
}

require_once __DIR__ . '/../database/config.php';

if (!isset($_POST['delete_course'])) {
    header('Location: dashboard.php');
    exit;
}

$courseId     = $_POST['course_id'] ?? null;
$instructorId = $_SESSION['user_id'];

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
    header('Location: dashboard.php?status=error&message=' . urlencode($e->getMessage()));
    exit;
}
