<?php

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Authentication check
if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'student') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Please log in as a student.']);
    exit;
}

require_once __DIR__ . '/../database/config.php';

$studentId = (int)$_SESSION['user_id'];

// 2. Parse input (supports JSON or POST)
$rawInput = file_get_contents('php://input');
$data     = json_decode($rawInput, true);

$courseId = isset($data['course_id']) ? (int)$data['course_id'] : (int)($_POST['course_id'] ?? 0);
$lessonId = isset($data['lesson_id']) ? (int)$data['lesson_id'] : (int)($_POST['lesson_id'] ?? 0);
$adId     = isset($data['ad_id']) ? (int)$data['ad_id'] : (int)($_POST['ad_id'] ?? 0);
$duration = isset($data['duration_watched']) ? max(1, (int)$data['duration_watched']) : 15;

if ($courseId <= 0 || $lessonId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid course or lesson ID provided.']);
    exit;
}

try {
    $pdo = getConnection();

    // Fetch dynamic monetization settings
    $settings = [];
    $stmtSet = $pdo->query("SELECT setting_key, setting_value FROM platform_settings");
    if ($stmtSet) {
        while ($row = $stmtSet->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }

    $cpmRate          = isset($settings['default_ad_cpm']) ? (float)$settings['default_ad_cpm'] : 0.0500;
    $instructorShare  = isset($settings['instructor_rev_share_percent']) ? (float)$settings['instructor_rev_share_percent'] : 70.0;
    $cooldownMinutes  = isset($settings['ad_interval_minutes']) ? max(1, (int)$settings['ad_interval_minutes']) : 5;

    // If a specific active ad was played, use its custom CPM rate if defined
    $actualAdId = null;
    if ($adId > 0) {
        $stmtAd = $pdo->prepare("SELECT id, cpm_rate FROM sponsor_ads WHERE id = :ad_id AND status = 'active' LIMIT 1");
        $stmtAd->execute([':ad_id' => $adId]);
        $adRow = $stmtAd->fetch();
        if ($adRow) {
            $actualAdId = (int)$adRow['id'];
            if ((float)$adRow['cpm_rate'] > 0) {
                $cpmRate = (float)$adRow['cpm_rate'];
            }
        }
    }

    // Calculate instructor earning based on configured revenue share split
    $instructorEarning = round($cpmRate * ($instructorShare / 100.0), 4);

    // 3. Verify student is enrolled in this course
    $stmtEnr = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = :uid AND course_id = :cid LIMIT 1");
    $stmtEnr->execute([':uid' => $studentId, ':cid' => $courseId]);
    if (!$stmtEnr->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Student is not enrolled in this course.']);
        exit;
    }

    // 4. Verify lesson exists and find course instructor
    $stmtCourse = $pdo->prepare("SELECT c.instructor_id, l.id AS lesson_exists 
                                 FROM courses c
                                 JOIN lessons l ON l.course_id = c.id
                                 WHERE c.id = :cid AND l.id = :lid LIMIT 1");
    $stmtCourse->execute([':cid' => $courseId, ':lid' => $lessonId]);
    $courseInfo = $stmtCourse->fetch();

    if (!$courseInfo) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Course or lesson not found.']);
        exit;
    }

    $instructorId = (int)$courseInfo['instructor_id'];

    // Don't credit self-views if instructor is testing as a student
    if ($instructorId === $studentId) {
        echo json_encode([
            'success'          => true,
            'message'          => 'Self-preview: No ad earnings credited to own wallet.',
            'already_recorded' => true
        ]);
        exit;
    }

    // 5. Anti-spam / Cooldown check (using configured interval):
    $stmtCooldown = $pdo->prepare("SELECT id FROM ad_activity_logs 
                                   WHERE student_id = :sid 
                                     AND lesson_id = :lid 
                                     AND created_at >= NOW() - INTERVAL :cooldown MINUTE 
                                   LIMIT 1");
    $stmtCooldown->execute([':sid' => $studentId, ':lid' => $lessonId, ':cooldown' => $cooldownMinutes]);
    if ($stmtCooldown->fetch()) {
        echo json_encode([
            'success'          => true,
            'message'          => 'Ad activity already logged for this lesson session.',
            'already_recorded' => true
        ]);
        exit;
    }

    // 6. Record Ad Activity & Credit Instructor Wallet in a Transaction
    $pdo->beginTransaction();

    // Insert log record
    $stmtLog = $pdo->prepare("INSERT INTO ad_activity_logs 
                              (course_id, lesson_id, student_id, instructor_id, ad_id, amount_earned, ad_duration_seconds, created_at)
                              VALUES (:cid, :lid, :sid, :iid, :adid, :amount, :dur, CURRENT_TIMESTAMP)");
    $stmtLog->execute([
        ':cid'    => $courseId,
        ':lid'    => $lessonId,
        ':sid'    => $studentId,
        ':iid'    => $instructorId,
        ':adid'   => $actualAdId,
        ':amount' => $instructorEarning,
        ':dur'    => $duration
    ]);

    // Increment sponsor impressions counter if tied to ad campaign
    if ($actualAdId !== null) {
        $stmtAdImp = $pdo->prepare("UPDATE sponsor_ads SET total_impressions = total_impressions + 1 WHERE id = :adid");
        $stmtAdImp->execute([':adid' => $actualAdId]);
    }

    // Update instructor wallet
    $stmtWallet = $pdo->prepare("INSERT INTO instructor_wallets 
                                 (instructor_id, total_earned, available_balance, total_withdrawn)
                                 VALUES (:iid, :earned_amt, :balance_amt, 0.00)
                                 ON DUPLICATE KEY UPDATE 
                                     total_earned = total_earned + :earned_amt2,
                                     available_balance = available_balance + :balance_amt2");
    $stmtWallet->execute([
        ':iid'          => $instructorId,
        ':earned_amt'   => $instructorEarning,
        ':balance_amt'  => $instructorEarning,
        ':earned_amt2'  => $instructorEarning,
        ':balance_amt2' => $instructorEarning
    ]);

    $pdo->commit();

    echo json_encode([
        'success'       => true,
        'message'       => 'Ad activity recorded successfully and instructor credited.',
        'amount_earned' => $instructorEarning,
        'instructor_id' => $instructorId
    ]);

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
