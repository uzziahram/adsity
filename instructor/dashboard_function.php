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
                        c.status,
                        c.rejection_reason,
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

    // 3. Fetch Student Project Submissions
    $sqlSubmissions = "SELECT 
                            sub.id,
                            sub.user_id,
                            sub.course_id,
                            sub.submission_type,
                            sub.submission_value,
                            sub.notes,
                            sub.instructor_feedback,
                            sub.status,
                            sub.reviewed_at,
                            sub.submitted_at,
                            u.full_name AS student_name,
                            u.email AS student_email,
                            c.title AS course_title,
                            cert.certificate_code
                       FROM course_submissions sub
                       JOIN users u ON sub.user_id = u.id
                       JOIN courses c ON sub.course_id = c.id
                       LEFT JOIN certificates cert ON cert.course_id = sub.course_id AND cert.user_id = sub.user_id
                       WHERE c.instructor_id = :instructor_id
                       ORDER BY sub.submitted_at DESC";
    $stmtSubs = $pdo->prepare($sqlSubmissions);
    $stmtSubs->bindValue(':instructor_id', $instructorId, PDO::PARAM_INT);
    $stmtSubs->execute();
    $submissions = $stmtSubs->fetchAll();

    // 4. Fetch Instructor Wallet & Earnings Summary
    $stmtWallet = $pdo->prepare("SELECT total_earned, available_balance, total_withdrawn FROM instructor_wallets WHERE instructor_id = :iid LIMIT 1");
    $stmtWallet->bindValue(':iid', $instructorId, PDO::PARAM_INT);
    $stmtWallet->execute();
    $wallet = $stmtWallet->fetch();

    if (!$wallet) {
        $wallet = [
            'total_earned'      => '0.00',
            'available_balance' => '0.00',
            'total_withdrawn'   => '0.00'
        ];
    }

    // 5. Total Ad Activity Views count
    $stmtViews = $pdo->prepare("SELECT COUNT(*) AS total_views FROM ad_activity_logs WHERE instructor_id = :iid");
    $stmtViews->bindValue(':iid', $instructorId, PDO::PARAM_INT);
    $stmtViews->execute();
    $totalAdViews = (int)($stmtViews->fetchColumn() ?: 0);

    // 6. Recent Ad Activity Logs (Last 20)
    $sqlAdLogs = "SELECT 
                    aal.id,
                    aal.amount_earned,
                    aal.ad_duration_seconds,
                    aal.created_at,
                    c.title AS course_title,
                    l.lesson_number,
                    l.title AS lesson_title,
                    u.full_name AS student_name
                  FROM ad_activity_logs aal
                  JOIN courses c ON aal.course_id = c.id
                  JOIN lessons l ON aal.lesson_id = l.id
                  JOIN users u ON aal.student_id = u.id
                  WHERE aal.instructor_id = :iid
                  ORDER BY aal.created_at DESC
                  LIMIT 20";
    $stmtAdLogs = $pdo->prepare($sqlAdLogs);
    $stmtAdLogs->bindValue(':iid', $instructorId, PDO::PARAM_INT);
    $stmtAdLogs->execute();
    $adActivityLogs = $stmtAdLogs->fetchAll();

    // 7. Fetch Instructor Payout Requests History
    $sqlPayouts = "SELECT 
                        id,
                        amount,
                        payout_method,
                        payout_details,
                        instructor_notes,
                        admin_notes,
                        transaction_reference,
                        status,
                        created_at,
                        processed_at
                   FROM payout_requests
                   WHERE instructor_id = :iid
                   ORDER BY created_at DESC";
    $stmtPayouts = $pdo->prepare($sqlPayouts);
    $stmtPayouts->bindValue(':iid', $instructorId, PDO::PARAM_INT);
    $stmtPayouts->execute();
    $payoutRequests = $stmtPayouts->fetchAll();

} catch (PDOException $e) {
    $submissions    = [];
    $adActivityLogs = [];
    $payoutRequests = [];
    $wallet = [
        'total_earned'      => '0.00',
        'available_balance' => '0.00',
        'total_withdrawn'   => '0.00'
    ];
    $totalAdViews = 0;
    $status  = 'error';
    $message = $e->getMessage();
}
