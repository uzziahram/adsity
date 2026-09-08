<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../database/config.php';

$code = isset($_GET['code']) ? trim((string)$_GET['code']) : null;
$certificate = null;

// Determine intelligent back URL and label based on viewer session role
$viewerRole = $_SESSION['role_name'] ?? null;
$backUrl    = '../index.php';
$backLabel  = 'Back to Home';

if ($viewerRole === 'admin') {
    $backUrl   = '../admin/dashboard.php#analytics';
    $backLabel = 'Back to Admin Control Center';
} elseif ($viewerRole === 'instructor') {
    $backUrl   = '../instructor/dashboard.php';
    $backLabel = 'Back to Instructor Studio';
} elseif ($viewerRole === 'student') {
    $backUrl   = 'dashboard.php';
    $backLabel = 'Back to Student Dashboard';
}

if (!empty($code)) {
    try {
        $pdo = getConnection();
        $sql = "SELECT 
                    cert.id,
                    cert.certificate_code,
                    cert.issued_at,
                    cert.status,
                    cert.revocation_reason,
                    cert.revoked_at,
                    u.full_name AS student_name,
                    c.title AS course_title,
                    c.category
                FROM certificates cert
                JOIN users u ON cert.user_id = u.id
                JOIN courses c ON cert.course_id = c.id
                WHERE cert.certificate_code = :code
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':code', $code);
        $stmt->execute();
        $certificate = $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Certificate lookup error: ' . $e->getMessage());
        $certificate = null;
    }
}
