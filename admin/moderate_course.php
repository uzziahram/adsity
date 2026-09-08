<?php

require_once __DIR__ . '/../validation.php';
requireAuth('admin', '../login.php?status=error&message=' . urlencode('Unauthorized: Administrator access required.'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['course_id'], $_POST['action'])) {
    redirect('dashboard.php?status=error&message=' . urlencode('Invalid moderation request.') . '#courses');
}

verifyCsrfOrRedirect('dashboard.php?status=error&message=' . urlencode('Invalid security token. Please refresh the page and try again.') . '#courses');

require_once __DIR__ . '/../database/config.php';

$adminId  = getCurrentUserId();
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
            ':reason'   => $reason,
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
    error_log('Admin course moderation error: ' . $e->getMessage());
    header('Location: dashboard.php?status=error&message=' . urlencode('An error occurred while updating course status. Please try again.') . '#courses');
    exit;
}
