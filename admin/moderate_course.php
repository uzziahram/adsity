<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Guard: Only authenticated administrators can moderate courses
if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'admin') {
    header('Location: ../login.php?status=error&message=' . urlencode('Unauthorized: Administrator access required.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['course_id'], $_POST['action'])) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Invalid moderation request.') . '#courses');
    exit;
}

require_once __DIR__ . '/../database/config.php';

$adminId  = (int)$_SESSION['user_id'];
$courseId = (int)$_POST['course_id'];
$action   = trim($_POST['action']);
$reason   = trim($_POST['rejection_reason'] ?? '');

if ($courseId <= 0) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Invalid Course ID.') . '#courses');
    exit;
}

try {
    $pdo = getConnection();

    // Verify course exists
    $stmtCheck = $pdo->prepare("SELECT id, title, status FROM courses WHERE id = :id LIMIT 1");
    $stmtCheck->execute([':id' => $courseId]);
    $course = $stmtCheck->fetch();

    if (!$course) {
        header('Location: dashboard.php?status=error&message=' . urlencode('Course not found.') . '#courses');
        exit;
    }

    if ($action === 'approve') {
        $stmtUpdate = $pdo->prepare("UPDATE courses 
                                     SET status = 'published', 
                                         rejection_reason = NULL, 
                                         reviewed_at = CURRENT_TIMESTAMP, 
                                         reviewed_by = :admin_id 
                                     WHERE id = :id");
        $stmtUpdate->execute([
            ':admin_id' => $adminId,
            ':id'       => $courseId
        ]);

        $msg = "Course '{$course['title']}' (ID #{$courseId}) has been approved and published to the public catalog.";
        header('Location: dashboard.php?status=success&message=' . urlencode($msg) . '#courses');
        exit;

    } elseif ($action === 'reject') {
        if ($reason === '') {
            $reason = 'Course does not meet platform curriculum quality or assessment standards.';
        }

        $stmtUpdate = $pdo->prepare("UPDATE courses 
                                     SET status = 'rejected', 
                                         rejection_reason = :reason, 
                                         reviewed_at = CURRENT_TIMESTAMP, 
                                         reviewed_by = :admin_id 
                                     WHERE id = :id");
        $stmtUpdate->execute([
            ':reason'   => htmlspecialchars($reason),
            ':admin_id' => $adminId,
            ':id'       => $courseId
        ]);

        $msg = "Course '{$course['title']}' (ID #{$courseId}) was flagged for revisions / rejected.";
        header('Location: dashboard.php?status=success&message=' . urlencode($msg) . '#courses');
        exit;

    } elseif ($action === 'pending') {
        $stmtUpdate = $pdo->prepare("UPDATE courses 
                                     SET status = 'pending_review', 
                                         rejection_reason = NULL, 
                                         reviewed_at = NULL, 
                                         reviewed_by = NULL 
                                     WHERE id = :id");
        $stmtUpdate->execute([':id' => $courseId]);

        $msg = "Course '{$course['title']}' (ID #{$courseId}) has been moved back to Pending Review.";
        header('Location: dashboard.php?status=success&message=' . urlencode($msg) . '#courses');
        exit;

    } else {
        header('Location: dashboard.php?status=error&message=' . urlencode('Invalid moderation action.') . '#courses');
        exit;
    }

} catch (PDOException $e) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Database error: ' . $e->getMessage()) . '#courses');
    exit;
}
