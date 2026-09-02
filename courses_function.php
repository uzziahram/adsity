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
    $stmtCat = $pdo->prepare("SELECT DISTINCT category FROM courses WHERE category IS NOT NULL ORDER BY category ASC");
    $stmtCat->execute();
    $categories = $stmtCat->fetchAll(PDO::FETCH_COLUMN);

    // Build courses query with optional search & category filter
    $sql = "SELECT id, title, description, category, thumbnail, total_lessons, created_at FROM courses WHERE 1=1";
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

} catch (PDOException $e) {
    $courses = [];
}
