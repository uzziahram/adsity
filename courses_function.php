<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database/config.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $_SESSION['full_name'] ?? '';
$userRole   = $_SESSION['role_name'] ?? '';

$search   = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

$courses    = [];
$categories = [];

try {
    $pdo = getConnection();

    // Fetch distinct categories for filter buttons
    $stmtCat = $pdo->prepare("SELECT DISTINCT category FROM courses WHERE category IS NOT NULL AND status = 'published' ORDER BY category ASC");
    $stmtCat->execute();
    $categories = $stmtCat->fetchAll(PDO::FETCH_COLUMN);

    // Build courses query with optional search & category filter (only published courses)
    $sql = "SELECT id, title, description, category, thumbnail, total_lessons, created_at FROM courses WHERE status = 'published'";
    $params = [];

    if ($search !== '') {
        $sql .= " AND (title LIKE :search OR description LIKE :search)";
        $params[':search'] = "%{$search}%";
    }

    if ($category !== '' && $category !== 'all') {
        $sql .= " AND category = :category";
        $params[':category'] = $category;
    }

    $sql .= " ORDER BY id ASC";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->execute();
    $courses = $stmt->fetchAll();

    // Fetch enrolled course IDs if logged-in student
    $enrolledCourseIds = [];
    if ($isLoggedIn && $userRole === 'student') {
        $stmtEnr = $pdo->prepare("SELECT course_id FROM enrollments WHERE user_id = :uid");
        $stmtEnr->bindValue(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmtEnr->execute();
        $enrolledCourseIds = $stmtEnr->fetchAll(PDO::FETCH_COLUMN);
    }

} catch (PDOException $e) {
    error_log('Courses catalog error: ' . $e->getMessage());
    $courses = [];
    $enrolledCourseIds = [];
}
