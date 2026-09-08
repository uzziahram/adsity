<?php
require_once __DIR__ . '/../validation.php';
ensureSessionStarted();

// Protect Student Dashboard: Must be logged in as student
if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'student') {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in to access your student dashboard.'));
    exit;
}

require_once __DIR__ . '/../database/config.php';

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

$studentId = $_SESSION['user_id'];

$student          = null;
$inProgress       = [];
$completedCourses = [];
$certificates     = [];

$totalEnrolled    = 0;
$inProgressCount  = 0;
$completedCount   = 0;
$certCount        = 0;

try {
    $pdo = getConnection();

    // 1. Fetch Student User from Session ID
    $stmtUser = $pdo->prepare("SELECT id, full_name, email, created_at FROM users WHERE id = :id AND role_id = 3 LIMIT 1");
    $stmtUser->bindValue(':id', $studentId, PDO::PARAM_INT);
    $stmtUser->execute();
    $student = $stmtUser->fetch();

    if ($student) {
        $studentId = $student['id'];

        // 2. Fetch In-Progress Courses
        $sqlProgress = "SELECT 
                            e.id AS enrollment_id,
                            e.progress_percent,
                            e.enrolled_at,
                            c.id AS course_id,
                            c.title,
                            c.description,
                            c.category,
                            c.thumbnail,
                            c.total_lessons,
                            ROUND((e.progress_percent / 100) * c.total_lessons) AS completed_lessons
                        FROM enrollments e
                        JOIN courses c ON e.course_id = c.id
                        WHERE e.user_id = :user_id AND e.status = 'in_progress'
                        ORDER BY e.enrolled_at DESC";
        $stmtProgress = $pdo->prepare($sqlProgress);
        $stmtProgress->bindValue(':user_id', $studentId, PDO::PARAM_INT);
        $stmtProgress->execute();
        $inProgress = $stmtProgress->fetchAll();

        // 3. Fetch Completed Courses
        $sqlCompleted = "SELECT 
                            e.id AS enrollment_id,
                            e.completed_at,
                            c.id AS course_id,
                            c.title,
                            c.description,
                            c.category,
                            c.thumbnail,
                            c.total_lessons,
                            cert.certificate_code
                        FROM enrollments e
                        JOIN courses c ON e.course_id = c.id
                        LEFT JOIN certificates cert ON (cert.user_id = e.user_id AND cert.course_id = c.id)
                        WHERE e.user_id = :user_id AND e.status = 'completed'
                        ORDER BY e.completed_at DESC";
        $stmtCompleted = $pdo->prepare($sqlCompleted);
        $stmtCompleted->bindValue(':user_id', $studentId, PDO::PARAM_INT);
        $stmtCompleted->execute();
        $completedCourses = $stmtCompleted->fetchAll();

        // 4. Fetch Certificates
        $sqlCert = "SELECT 
                        cert.id AS cert_id,
                        cert.certificate_code,
                        cert.issued_at,
                        c.id AS course_id,
                        c.title AS course_title,
                        c.category,
                        c.thumbnail
                    FROM certificates cert
                    JOIN courses c ON cert.course_id = c.id
                    WHERE cert.user_id = :user_id
                    ORDER BY cert.issued_at DESC";
        $stmtCert = $pdo->prepare($sqlCert);
        $stmtCert->bindValue(':user_id', $studentId, PDO::PARAM_INT);
        $stmtCert->execute();
        $certificates = $stmtCert->fetchAll();

        $inProgressCount  = count($inProgress);
        $completedCount   = count($completedCourses);
        $certCount        = count($certificates);
        $totalEnrolled    = $inProgressCount + $completedCount;
    }
} catch (PDOException $e) {
    error_log('Student dashboard error: ' . $e->getMessage());
    $status  = 'error';
    $message = 'An error occurred while loading your student dashboard.';
}
