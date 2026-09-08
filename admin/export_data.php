<?php

require_once __DIR__ . '/../validation.php';
ensureSessionStarted();

// Protect Admin Panel: Must be logged in as admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'admin') {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in with an administrator account.'));
    exit;
}

require_once __DIR__ . '/../database/config.php';

$type = trim($_GET['type'] ?? 'users');
$allowedTypes = ['users', 'courses', 'payouts', 'certificates', 'ad_logs', 'sponsor_ads', 'admin_withdrawals'];

if (!in_array($type, $allowedTypes, true)) {
    die('Invalid export type requested.');
}

try {
    $pdo = getConnection();
    $timestamp = date('Y-m-d_His');
    $filename = "adsity_{$type}_{$timestamp}.csv";

    // Set headers for download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    switch ($type) {
        case 'users':
            fputcsv($output, ['User ID', 'Full Name', 'Email Address', 'Role', 'Enrolled Courses', 'Published Courses', 'Wallet Balance ($)', 'Joined Date']);
            $sql = "SELECT 
                        u.id, 
                        u.full_name, 
                        u.email, 
                        r.name AS role_name,
                        COUNT(DISTINCT e.id) AS enrollments_count,
                        COUNT(DISTINCT c.id) AS courses_count,
                        COALESCE(w.available_balance, 0.00) AS wallet_balance,
                        u.created_at
                    FROM users u
                    JOIN roles r ON u.role_id = r.id
                    LEFT JOIN enrollments e ON e.user_id = u.id
                    LEFT JOIN courses c ON c.instructor_id = u.id
                    LEFT JOIN instructor_wallets w ON w.instructor_id = u.id
                    GROUP BY u.id
                    ORDER BY u.id ASC";
            $stmt = $pdo->query($sql);
            while ($row = $stmt->fetch()) {
                fputcsv($output, [
                    $row['id'],
                    $row['full_name'],
                    $row['email'],
                    ucfirst($row['role_name']),
                    $row['enrollments_count'],
                    $row['courses_count'],
                    number_format((float)$row['wallet_balance'], 2),
                    $row['created_at']
                ]);
            }
            break;

        case 'courses':
            fputcsv($output, ['Course ID', 'Title', 'Category', 'Instructor Name', 'Instructor Email', 'Status', 'Total Lessons', 'Enrolled Students', 'Certified Graduates', 'Created Date']);
            $sql = "SELECT 
                        c.id, 
                        c.title, 
                        c.category, 
                        u.full_name AS instructor_name, 
                        u.email AS instructor_email,
                        c.status,
                        c.total_lessons,
                        COUNT(DISTINCT e.id) AS enrolled_count,
                        COUNT(DISTINCT cert.id) AS cert_count,
                        c.created_at
                    FROM courses c
                    LEFT JOIN users u ON c.instructor_id = u.id
                    LEFT JOIN enrollments e ON e.course_id = c.id
                    LEFT JOIN certificates cert ON cert.course_id = c.id
                    GROUP BY c.id
                    ORDER BY c.id ASC";
            $stmt = $pdo->query($sql);
            while ($row = $stmt->fetch()) {
                fputcsv($output, [
                    $row['id'],
                    $row['title'],
                    $row['category'],
                    $row['instructor_name'] ?? 'Unassigned',
                    $row['instructor_email'] ?? 'N/A',
                    ucfirst($row['status'] ?? 'published'),
                    $row['total_lessons'],
                    $row['enrolled_count'],
                    $row['cert_count'],
                    $row['created_at']
                ]);
            }
            break;

        case 'payouts':
            fputcsv($output, ['Payout ID', 'Instructor Name', 'Instructor Email', 'Amount ($)', 'Method', 'Account Details', 'Status', 'Transaction Reference', 'Admin Notes', 'Requested Date', 'Processed Date']);
            $sql = "SELECT 
                        p.id, 
                        u.full_name AS instructor_name, 
                        u.email AS instructor_email, 
                        p.amount, 
                        p.payout_method, 
                        p.payout_details, 
                        p.status, 
                        p.transaction_reference, 
                        p.admin_notes, 
                        p.created_at, 
                        p.processed_at
                    FROM payout_requests p
                    JOIN users u ON p.instructor_id = u.id
                    ORDER BY p.id DESC";
            $stmt = $pdo->query($sql);
            while ($row = $stmt->fetch()) {
                $details = json_decode($row['payout_details'], true) ?: [];
                $detailStr = implode(' | ', array_map(fn($k, $v) => "$k: " . (is_array($v) ? json_encode($v) : $v), array_keys($details), $details));
                fputcsv($output, [
                    $row['id'],
                    $row['instructor_name'],
                    $row['instructor_email'],
                    number_format((float)$row['amount'], 2),
                    strtoupper($row['payout_method']),
                    $detailStr,
                    ucfirst($row['status']),
                    $row['transaction_reference'] ?? '',
                    $row['admin_notes'] ?? '',
                    $row['created_at'],
                    $row['processed_at'] ?? ''
                ]);
            }
            break;

        case 'certificates':
            fputcsv($output, ['Cert ID', 'Certificate Code', 'Student Name', 'Student Email', 'Course Title', 'Category', 'Status', 'Revocation Reason', 'Issue Date']);
            $sql = "SELECT 
                        cert.id, 
                        cert.certificate_code, 
                        u.full_name AS student_name, 
                        u.email AS student_email, 
                        c.title AS course_title, 
                        c.category,
                        cert.status,
                        cert.revocation_reason,
                        cert.issued_at
                    FROM certificates cert
                    JOIN users u ON cert.user_id = u.id
                    JOIN courses c ON cert.course_id = c.id
                    ORDER BY cert.id DESC";
            $stmt = $pdo->query($sql);
            while ($row = $stmt->fetch()) {
                fputcsv($output, [
                    $row['id'],
                    $row['certificate_code'],
                    $row['student_name'],
                    $row['student_email'],
                    $row['course_title'],
                    $row['category'],
                    ucfirst($row['status'] ?? 'valid'),
                    $row['revocation_reason'] ?? '',
                    $row['issued_at']
                ]);
            }
            break;

        case 'ad_logs':
            fputcsv($output, ['Log ID', 'Course Title', 'Lesson Title', 'Sponsor Brand', 'Student Viewer', 'Instructor Beneficiary', 'Gross CPM ($)', 'Instructor Share (65%) ($)', 'Platform Share (35%) ($)', 'Duration Seconds', 'Timestamp']);
            $sql = "SELECT 
                        aal.id,
                        c.title AS course_title,
                        l.title AS lesson_title,
                        s_ad.sponsor_name,
                        student.full_name AS student_name,
                        inst.full_name AS instructor_name,
                        aal.gross_cpm,
                        aal.amount_earned,
                        aal.platform_earned,
                        aal.ad_duration_seconds,
                        aal.created_at
                    FROM ad_activity_logs aal
                    JOIN courses c ON aal.course_id = c.id
                    LEFT JOIN lessons l ON aal.lesson_id = l.id
                    LEFT JOIN users student ON aal.student_id = student.id
                    LEFT JOIN users inst ON aal.instructor_id = inst.id
                    LEFT JOIN sponsor_ads s_ad ON aal.ad_id = s_ad.id
                    ORDER BY aal.id DESC";
            $stmt = $pdo->query($sql);
            while ($row = $stmt->fetch()) {
                fputcsv($output, [
                    $row['id'],
                    $row['course_title'],
                    $row['lesson_title'],
                    $row['sponsor_name'] ?? 'Adsity Default',
                    $row['student_name'] ?? 'Guest',
                    $row['instructor_name'] ?? 'Instructor',
                    number_format((float)($row['gross_cpm'] > 0 ? $row['gross_cpm'] : ($row['amount_earned'] + $row['platform_earned'])), 4),
                    number_format((float)$row['amount_earned'], 4),
                    number_format((float)$row['platform_earned'], 4),
                    $row['ad_duration_seconds'],
                    $row['created_at']
                ]);
            }
            break;

        case 'sponsor_ads':
            fputcsv($output, ['Ad ID', 'Sponsor Name', 'Campaign Title', 'Video URL', 'Target URL', 'CPM Rate ($)', 'Impressions Served', 'Status', 'Created Date']);
            $sql = "SELECT * FROM sponsor_ads ORDER BY id DESC";
            $stmt = $pdo->query($sql);
            while ($row = $stmt->fetch()) {
                fputcsv($output, [
                    $row['id'],
                    $row['sponsor_name'],
                    $row['campaign_title'],
                    $row['video_url'],
                    $row['click_url'] ?? '',
                    number_format((float)$row['cpm_rate'], 4),
                    $row['total_impressions'],
                    ucfirst($row['status']),
                    $row['created_at']
                ]);
            }
            break;

        case 'admin_withdrawals':
            fputcsv($output, ['Withdrawal ID', 'Admin Name', 'Amount ($)', 'Bank Name', 'Account Name', 'Account Number', 'Reference Trace', 'Notes', 'Status', 'Date']);
            $sql = "SELECT aw.id, u.full_name AS admin_name, aw.amount, aw.bank_name, aw.account_name, aw.account_number, aw.transaction_reference, aw.notes, aw.status, aw.created_at 
                    FROM admin_withdrawals aw 
                    JOIN users u ON aw.admin_id = u.id 
                    ORDER BY aw.id DESC";
            $stmt = $pdo->query($sql);
            while ($row = $stmt->fetch()) {
                fputcsv($output, [
                    $row['id'],
                    $row['admin_name'],
                    number_format((float)$row['amount'], 2),
                    $row['bank_name'],
                    $row['account_name'],
                    $row['account_number'],
                    $row['transaction_reference'],
                    $row['notes'] ?? '',
                    ucfirst($row['status']),
                    $row['created_at']
                ]);
            }
            break;
    }

    fclose($output);
    exit;

} catch (PDOException $e) {
    error_log('Database error during export: ' . $e->getMessage());
    die('A database error occurred during export generation. Please try again later.');
}
