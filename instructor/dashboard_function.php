<?php

session_start();

// Protect Instructor Portal: Must be logged in as instructor
if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'instructor') {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in with an instructor account.'));
    exit;
}

require_once __DIR__ . '/../database/config.php';

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

$instructorId = $_SESSION['user_id'];
$courses      = [];

$totalCourses      = 0;
$totalStudents     = 0;
$totalCertificates = 0;

try {
    $pdo = getConnection();

    // 1. Fetch Instructor Details
    $stmtUser = $pdo->prepare("SELECT id, full_name, email, created_at FROM users WHERE id = :id AND role_id = 2 LIMIT 1");
    $stmtUser->bindValue(':id', $instructorId, PDO::PARAM_INT);
    $stmtUser->execute();
    $instructor = $stmtUser->fetch();

    // 2. Fetch Courses Created by this Instructor
    $sqlCourses = "SELECT 
                        c.id,
                        c.title,
                        c.description,
                        c.category,
                        c.thumbnail,
                        c.total_lessons,
                        c.created_at,
                        COUNT(DISTINCT e.user_id) AS enrolled_students,
                        COUNT(DISTINCT cert.id) AS certificates_issued
                    FROM courses c
                    LEFT JOIN enrollments e ON e.course_id = c.id
                    LEFT JOIN certificates cert ON cert.course_id = c.id
                    WHERE c.instructor_id = :instructor_id
                    GROUP BY c.id
                    ORDER BY c.id DESC";
    $stmtCourses = $pdo->prepare($sqlCourses);
    $stmtCourses->bindValue(':instructor_id', $instructorId, PDO::PARAM_INT);
    $stmtCourses->execute();
    $courses = $stmtCourses->fetchAll();

    $totalCourses = count($courses);

    foreach ($courses as $c) {
        $totalStudents     += (int)$c['enrolled_students'];
        $totalCertificates += (int)$c['certificates_issued'];
    }

} catch (PDOException $e) {
    $status  = 'error';
    $message = $e->getMessage();
}
