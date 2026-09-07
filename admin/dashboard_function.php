<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    // 1. Fetch Admin Profile Info
    $adminUserId = (int)$_SESSION['user_id'];
    $stmtAdmin = $pdo->prepare("SELECT id, full_name, email, created_at FROM users WHERE id = :id LIMIT 1");
    $stmtAdmin->execute([':id' => $adminUserId]);
    $adminProfile = $stmtAdmin->fetch() ?: [
        'id' => $adminUserId,
        'full_name' => 'Administrator',
        'email' => 'admin@adsity.org',
        'created_at' => date('Y-m-d H:i:s')
    ];

    // 2. Fetch all available roles for user creation & role switching
    $stmtRoles = $pdo->query("SELECT id, name, description FROM roles ORDER BY id ASC");
    $allRoles = $stmtRoles->fetchAll();

    // 3. Fetch all students (role_id = 3) with activity metrics
    $sqlStudents = "SELECT 
                        u.id, 
                        u.full_name, 
                        u.email, 
                        u.created_at, 
                        r.id AS role_id,
                        r.name AS role_name,
                        COUNT(DISTINCT e.id) AS enrolled_count,
                        COUNT(DISTINCT cert.id) AS certs_count
                    FROM users u 
                    JOIN roles r ON u.role_id = r.id 
                    LEFT JOIN enrollments e ON e.user_id = u.id
                    LEFT JOIN certificates cert ON cert.user_id = u.id
                    WHERE u.role_id = 3 
                    GROUP BY u.id
                    ORDER BY u.id DESC";
    $stmtStudents = $pdo->query($sqlStudents);
    $students = $stmtStudents->fetchAll();

    // 4. Fetch all instructors (role_id = 2) with courses & wallet metrics
    $sqlInstructors = "SELECT 
                           u.id, 
                           u.full_name, 
                           u.email, 
                           u.created_at, 
                           r.id AS role_id,
                           r.name AS role_name,
                           COUNT(DISTINCT c.id) AS courses_count,
                           COALESCE(w.available_balance, 0.00) AS available_balance,
                           COALESCE(w.total_earned, 0.00) AS total_earned,
                           COALESCE(w.total_withdrawn, 0.00) AS total_withdrawn
                       FROM users u 
                       JOIN roles r ON u.role_id = r.id 
                       LEFT JOIN courses c ON c.instructor_id = u.id
                       LEFT JOIN instructor_wallets w ON w.instructor_id = u.id
                       WHERE u.role_id = 2 
                       GROUP BY u.id
                       ORDER BY u.id DESC";
    $stmtInstructors = $pdo->query($sqlInstructors);
    $instructors = $stmtInstructors->fetchAll();

    // 5. Fetch all administrators (role_id = 1)
    $stmtAdmins = $pdo->query("SELECT u.id, u.full_name, u.email, u.created_at, r.name AS role_name 
                               FROM users u 
                               JOIN roles r ON u.role_id = r.id 
                               WHERE u.role_id = 1 
                               ORDER BY u.id ASC");
    $adminsList = $stmtAdmins->fetchAll();

    $totalStudents    = count($students);
    $totalInstructors = count($instructors);
    $totalAdmins      = count($adminsList);
    $totalUsers       = $totalStudents + $totalInstructors + $totalAdmins;

    // 6. Fetch all Courses with instructor, lessons count, enrollment count, and ad stats
    $sqlCourses = "SELECT 
                       c.id,
                       c.title,
                       c.category,
                       c.thumbnail,
                       c.total_lessons,
                       c.assessment_type,
                       c.status,
                       c.rejection_reason,
                       c.reviewed_at,
                       c.reviewed_by,
                       c.created_at,
                       u.id AS instructor_id,
                       u.full_name AS instructor_name,
                       u.email AS instructor_email,
                       rev_u.full_name AS reviewer_name,
                       COUNT(DISTINCT l.id) AS actual_lessons_count,
                       COUNT(DISTINCT e.id) AS total_enrolled,
                       COUNT(DISTINCT cert.id) AS total_graduates,
                       COALESCE(SUM(aal.amount_earned), 0.00) AS course_ad_revenue
                   FROM courses c
                   LEFT JOIN users u ON c.instructor_id = u.id
                   LEFT JOIN users rev_u ON c.reviewed_by = rev_u.id
                   LEFT JOIN lessons l ON l.course_id = c.id
                   LEFT JOIN enrollments e ON e.course_id = c.id
                   LEFT JOIN certificates cert ON cert.course_id = c.id
                   LEFT JOIN ad_activity_logs aal ON aal.course_id = c.id
                   GROUP BY c.id
                   ORDER BY 
                       CASE WHEN c.status = 'pending_review' THEN 1 ELSE 2 END,
                       c.id DESC";
    $stmtCourses = $pdo->query($sqlCourses);
    $allCourses = $stmtCourses->fetchAll();
    $totalCourses = count($allCourses);

    $pendingCoursesCount   = 0;
    $publishedCoursesCount = 0;
    $rejectedCoursesCount  = 0;
    foreach ($allCourses as $c) {
        $cStatus = $c['status'] ?? 'published';
        if ($cStatus === 'pending_review') {
            $pendingCoursesCount++;
        } elseif ($cStatus === 'published') {
            $publishedCoursesCount++;
        } elseif ($cStatus === 'rejected') {
            $rejectedCoursesCount++;
        }
    }

    // Extract distinct categories for filter
    $courseCategories = [];
    foreach ($allCourses as $c) {
        if (!empty($c['category']) && !in_array($c['category'], $courseCategories, true)) {
            $courseCategories[] = $c['category'];
        }
    }
    sort($courseCategories);

    // 7. Fetch all Payout / Withdrawal Requests
    $sqlPayouts = "SELECT 
                      p.id,
                      p.instructor_id,
                      p.amount,
                      p.payout_method,
                      p.payout_details,
                      p.instructor_notes,
                      p.admin_notes,
                      p.transaction_reference,
                      p.status,
                      p.created_at,
                      p.processed_at,
                      u.full_name AS instructor_name,
                      u.email AS instructor_email,
                      COALESCE(w.available_balance, 0.00) AS instructor_balance,
                      admin_u.full_name AS processed_by_name
                   FROM payout_requests p
                   JOIN users u ON p.instructor_id = u.id
                   LEFT JOIN instructor_wallets w ON w.instructor_id = p.instructor_id
                   LEFT JOIN users admin_u ON p.processed_by = admin_u.id
                   ORDER BY 
                       CASE WHEN p.status = 'pending' THEN 1 ELSE 2 END,
                       p.created_at DESC";
    $stmtPayouts = $pdo->query($sqlPayouts);
    $payoutRequests = $stmtPayouts->fetchAll();

    $pendingPayoutCount  = 0;
    $pendingPayoutAmount = 0.00;
    $totalDisbursed      = 0.00;

    foreach ($payoutRequests as $pr) {
        if ($pr['status'] === 'pending') {
            $pendingPayoutCount++;
            $pendingPayoutAmount += (float)$pr['amount'];
        } elseif ($pr['status'] === 'completed') {
            $totalDisbursed += (float)$pr['amount'];
        }
    }

    // 8. Platform Monetization & Analytics Metrics
    $stmtAdStats = $pdo->query("SELECT 
                                    COUNT(*) AS total_ad_impressions,
                                    COALESCE(SUM(amount_earned), 0.00) AS total_ad_revenue
                                FROM ad_activity_logs");
    $adStats = $stmtAdStats->fetch() ?: ['total_ad_impressions' => 0, 'total_ad_revenue' => 0.00];
    $totalAdImpressions = (int)($adStats['total_ad_impressions'] ?? 0);
    $totalAdRevenue     = (float)($adStats['total_ad_revenue'] ?? 0.00);

    // Total active enrollments and completions
    $stmtEnrollStats = $pdo->query("SELECT 
                                        COUNT(*) AS total_enrollments,
                                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_enrollments
                                    FROM enrollments");
    $enrollStats = $stmtEnrollStats->fetch() ?: ['total_enrollments' => 0, 'completed_enrollments' => 0];
    $totalEnrollments     = (int)($enrollStats['total_enrollments'] ?? 0);
    $completedEnrollments = (int)($enrollStats['completed_enrollments'] ?? 0);
    $platformCompletionRate = $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0;

    // Total certificates issued and status breakdown
    $stmtCertCount = $pdo->query("SELECT 
                                      COUNT(*) AS total_certs,
                                      SUM(CASE WHEN status = 'valid' THEN 1 ELSE 0 END) AS valid_certs,
                                      SUM(CASE WHEN status = 'revoked' THEN 1 ELSE 0 END) AS revoked_certs
                                  FROM certificates");
    $certCounts = $stmtCertCount->fetch() ?: ['total_certs' => 0, 'valid_certs' => 0, 'revoked_certs' => 0];
    $totalCertificates   = (int)($certCounts['total_certs'] ?? 0);
    $validCertificates   = (int)($certCounts['valid_certs'] ?? 0);
    $revokedCertificates = (int)($certCounts['revoked_certs'] ?? 0);

    // 9. Fetch Recent Certificates (Last 50)
    $sqlRecentCerts = "SELECT 
                           cert.id,
                           cert.certificate_code,
                           cert.issued_at,
                           cert.status,
                           cert.revocation_reason,
                           cert.revoked_at,
                           u.full_name AS student_name,
                           u.email AS student_email,
                           c.id AS course_id,
                           c.title AS course_title,
                           inst.full_name AS instructor_name,
                           admin_rev.full_name AS revoked_by_admin
                       FROM certificates cert
                       JOIN users u ON cert.user_id = u.id
                       JOIN courses c ON cert.course_id = c.id
                       LEFT JOIN users inst ON c.instructor_id = inst.id
                       LEFT JOIN users admin_rev ON cert.revoked_by = admin_rev.id
                       ORDER BY cert.issued_at DESC
                       LIMIT 50";
    $stmtRecentCerts = $pdo->query($sqlRecentCerts);
    $recentCertificates = $stmtRecentCerts->fetchAll();

    // 10. Fetch Recent Ad Activity Logs (Last 25)
    $sqlRecentAds = "SELECT 
                        aal.id,
                        aal.amount_earned,
                        aal.ad_duration_seconds,
                        aal.created_at,
                        c.title AS course_title,
                        l.title AS lesson_title,
                        l.lesson_number,
                        student.full_name AS student_name,
                        inst.full_name AS instructor_name,
                        s_ad.sponsor_name,
                        s_ad.campaign_title
                    FROM ad_activity_logs aal
                    JOIN courses c ON aal.course_id = c.id
                    LEFT JOIN lessons l ON aal.lesson_id = l.id
                    LEFT JOIN users student ON aal.student_id = student.id
                    LEFT JOIN users inst ON aal.instructor_id = inst.id
                    LEFT JOIN sponsor_ads s_ad ON aal.ad_id = s_ad.id
                    ORDER BY aal.id DESC
                    LIMIT 25";
    $stmtRecentAds = $pdo->query($sqlRecentAds);
    $recentAdLogs = $stmtRecentAds->fetchAll();

    // 11. Fetch All Sponsor Ad Campaigns
    $stmtSponsorAds = $pdo->query("SELECT * FROM sponsor_ads ORDER BY id DESC");
    $allSponsorAds   = $stmtSponsorAds ? $stmtSponsorAds->fetchAll() : [];
    $totalSponsorAds = count($allSponsorAds);
    $activeSponsorAds = 0;
    foreach ($allSponsorAds as $ad) {
        if ($ad['status'] === 'active') {
            $activeSponsorAds++;
        }
    }

    // 12. Fetch Platform Monetization Settings
    $stmtSettings = $pdo->query("SELECT setting_key, setting_value, description FROM platform_settings");
    $platformSettings = [
        'default_ad_cpm'               => '0.0500',
        'instructor_rev_share_percent' => '70',
        'platform_rev_share_percent'   => '30',
        'ad_interval_minutes'          => '5',
    ];
    if ($stmtSettings) {
        while ($row = $stmtSettings->fetch()) {
            $platformSettings[$row['setting_key']] = $row['setting_value'];
        }
    }

} catch (PDOException $e) {
    $adminProfile        = ['id' => 1, 'full_name' => 'Administrator', 'email' => 'admin@adsity.org'];
    $allRoles            = [];
    $students            = [];
    $instructors         = [];
    $adminsList          = [];
    $allCourses          = [];
    $courseCategories    = [];
    $payoutRequests      = [];
    $recentCertificates  = [];
    $recentAdLogs        = [];
    $totalStudents       = 0;
    $totalInstructors    = 0;
    $totalAdmins         = 0;
    $totalUsers          = 0;
    $totalCourses        = 0;
    $pendingPayoutCount  = 0;
    $pendingPayoutAmount = 0.00;
    $totalDisbursed      = 0.00;
    $totalAdImpressions  = 0;
    $totalAdRevenue      = 0.00;
    $totalEnrollments    = 0;
    $completedEnrollments= 0;
    $platformCompletionRate = 0;
    $totalCertificates   = 0;
    $status              = 'error';
    $message             = $e->getMessage();
}
