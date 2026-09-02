<?php

session_start();

// Protect Admin Panel: Must be logged in as admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'admin') {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in with an administrator account.'));
    exit;
}

require_once __DIR__ . '/../database/config.php';

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

try {
    $pdo = getConnection();

    // Fetch all students (role_id = 3)
    $stmtStudents = $pdo->prepare("SELECT u.id, u.full_name, u.email, u.created_at, r.name AS role_name 
                                  FROM users u 
                                  JOIN roles r ON u.role_id = r.id 
                                  WHERE u.role_id = 3 
                                  ORDER BY u.id DESC");
    $stmtStudents->execute();
    $students = $stmtStudents->fetchAll();

    // Fetch all instructors (role_id = 2)
    $stmtTeachers = $pdo->prepare("SELECT u.id, u.full_name, u.email, u.created_at, r.name AS role_name 
                                  FROM users u 
                                  JOIN roles r ON u.role_id = r.id 
                                  WHERE u.role_id = 2 
                                  ORDER BY u.id DESC");
    $stmtTeachers->execute();
    $instructors = $stmtTeachers->fetchAll();

    $totalStudents    = count($students);
    $totalInstructors = count($instructors);
    $totalUsers       = $totalStudents + $totalInstructors;
} catch (PDOException $e) {
    $students         = [];
    $instructors      = [];
    $totalStudents    = 0;
    $totalInstructors = 0;
    $totalUsers       = 0;
    $status           = 'error';
    $message          = $e->getMessage();
}
