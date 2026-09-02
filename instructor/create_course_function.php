<?php

session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'instructor') {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in with an instructor account.'));
    exit;
}

require_once __DIR__ . '/../database/config.php';

if (!isset($_POST['create_course'])) {
    header('Location: create_course.php');
    exit;
}

$title        = trim($_POST['title'] ?? '');
$category     = trim($_POST['category'] ?? '');
$description  = trim($_POST['description'] ?? '');
$totalLessons = (int)($_POST['total_lessons'] ?? 10);
$thumbnail    = trim($_POST['thumbnail'] ?? 'Web_Development_Basics.png');

$instructorId = $_SESSION['user_id'];

// Basic validation
if ($title === '' || $category === '' || $description === '') {
    header('Location: create_course.php?status=error&message=' . urlencode('Please fill out all required fields.'));
    exit;
}

if ($totalLessons <= 0) {
    $totalLessons = 10;
}

try {
    $pdo = getConnection();

    $sql = "INSERT INTO courses (title, description, category, thumbnail, total_lessons, instructor_id)
            VALUES (:title, :description, :category, :thumbnail, :total_lessons, :instructor_id)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':title', htmlspecialchars($title));
    $stmt->bindValue(':description', htmlspecialchars($description));
    $stmt->bindValue(':category', htmlspecialchars($category));
    $stmt->bindValue(':thumbnail', $thumbnail);
    $stmt->bindValue(':total_lessons', $totalLessons, PDO::PARAM_INT);
    $stmt->bindValue(':instructor_id', $instructorId, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: dashboard.php?status=success&message=' . urlencode('Course "' . $title . '" published successfully!'));
    exit;
} catch (PDOException $e) {
    header('Location: create_course.php?status=error&message=' . urlencode($e->getMessage()));
    exit;
}
