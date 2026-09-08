<?php

require_once __DIR__ . '/../helpers.php';
requireAuth('instructor', '../login.php?status=error&message=' . urlencode('Please log in with an instructor account.'));

require_once __DIR__ . '/../database/config.php';

$instructorId = getCurrentUserId();

// Determine default redirect target
$redirectTo = $_POST['redirect_to'] ?? 'dashboard.php#submissions';
// Basic sanity check on redirect_to to prevent open redirect
if (!str_starts_with($redirectTo, 'dashboard.php') && !str_starts_with($redirectTo, 'course_overview.php')) {
    $redirectTo = 'dashboard.php#submissions';
}

function redirectWithMessage($target, $status, $msg) {
    // Separate anchor/fragment if present
    $parts = explode('#', $target, 2);
    $url   = $parts[0];
    $hash  = isset($parts[1]) ? '#' . $parts[1] : '';

    $separator = str_contains($url, '?') ? '&' : '?';
    $finalUrl = $url . $separator . 'status=' . urlencode($status) . '&message=' . urlencode($msg) . $hash;

    header('Location: ' . $finalUrl);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    redirectWithMessage($redirectTo, 'error', 'Invalid request method.');
}

if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    redirectWithMessage($redirectTo, 'error', 'Invalid security token. Please refresh the page and try again.');
}

$submissionId = isset($_POST['submission_id']) ? (int)$_POST['submission_id'] : 0;
$action       = trim($_POST['action'] ?? '');
$feedback     = trim($_POST['instructor_feedback'] ?? '');

if ($submissionId <= 0) {
    redirectWithMessage($redirectTo, 'error', 'Invalid submission specified.');
}

if (!in_array($action, ['approve', 'request_revision'], true)) {
    redirectWithMessage($redirectTo, 'error', 'Invalid grading action selected.');
}

if ($action === 'request_revision' && empty($feedback)) {
    redirectWithMessage($redirectTo, 'error', 'Please provide instructor feedback so the student knows what to revise.');
}

try {
    $pdo = getConnection();

    // 1. Fetch submission and verify instructor owns the course
    $stmtCheck = $pdo->prepare("SELECT 
                                    sub.id,
                                    sub.user_id,
                                    sub.course_id,
                                    sub.status,
                                    sub.submission_type,
                                    sub.submission_value,
                                    c.instructor_id,
                                    c.title AS course_title,
                                    u.full_name AS student_name,
                                    u.email AS student_email
                                FROM course_submissions sub
                                JOIN courses c ON sub.course_id = c.id
                                JOIN users u ON sub.user_id = u.id
                                WHERE sub.id = :sid AND c.instructor_id = :iid
                                LIMIT 1");
    $stmtCheck->execute([
        ':sid' => $submissionId,
        ':iid' => $instructorId
    ]);
    $submission = $stmtCheck->fetch();

    if (!$submission) {
        redirectWithMessage($redirectTo, 'error', 'Submission not found or you are not authorized to grade this course project.');
    }

    $studentId   = (int)$submission['user_id'];
    $courseId    = (int)$submission['course_id'];
    $studentName = $submission['student_name'];

    if ($action === 'approve') {
        // A. Set submission to approved
        $stmtApprove = $pdo->prepare("UPDATE course_submissions 
                                      SET status = 'approved',
                                          instructor_feedback = :feedback,
                                          reviewed_at = CURRENT_TIMESTAMP
                                      WHERE id = :id");
        $stmtApprove->execute([
            ':feedback' => $feedback ?: 'Great job! Your project deliverable has been reviewed and approved.',
            ':id'       => $submissionId
        ]);

        // B. Mark enrollment completed
        $stmtEnrollment = $pdo->prepare("UPDATE enrollments 
                                         SET status = 'completed',
                                             progress_percent = 100,
                                             completed_at = CURRENT_TIMESTAMP
                                         WHERE user_id = :uid AND course_id = :cid");
        $stmtEnrollment->execute([
            ':uid' => $studentId,
            ':cid' => $courseId
        ]);

        // C. Issue Certificate if not already generated
        $stmtCertCheck = $pdo->prepare("SELECT certificate_code FROM certificates WHERE user_id = :uid AND course_id = :cid LIMIT 1");
        $stmtCertCheck->execute([':uid' => $studentId, ':cid' => $courseId]);
        $existingCert = $stmtCertCheck->fetch();

        if (!$existingCert) {
            $certCode = 'ADS-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));
            $stmtCertInsert = $pdo->prepare("INSERT INTO certificates (user_id, course_id, certificate_code, issued_at) 
                                             VALUES (:uid, :cid, :code, CURRENT_TIMESTAMP)");
            $stmtCertInsert->execute([
                ':uid'  => $studentId,
                ':cid'  => $courseId,
                ':code' => $certCode
            ]);
        }

        redirectWithMessage(
            $redirectTo, 
            'success', 
            "Project approved for {$studentName}! Certificate has been successfully issued."
        );

    } elseif ($action === 'request_revision') {
        // A. Set submission to revision_needed
        $stmtRevision = $pdo->prepare("UPDATE course_submissions 
                                       SET status = 'revision_needed',
                                           instructor_feedback = :feedback,
                                           reviewed_at = CURRENT_TIMESTAMP
                                       WHERE id = :id");
        $stmtRevision->execute([
            ':feedback' => $feedback,
            ':id'       => $submissionId
        ]);

        // B. Ensure enrollment remains in_progress (in case it was previously marked completed)
        $stmtEnrollment = $pdo->prepare("UPDATE enrollments 
                                         SET status = 'in_progress',
                                             completed_at = NULL
                                         WHERE user_id = :uid AND course_id = :cid");
        $stmtEnrollment->execute([
            ':uid' => $studentId,
            ':cid' => $courseId
        ]);

        // C. If a certificate had been issued earlier, revoke/delete it
        $stmtCertRevoke = $pdo->prepare("DELETE FROM certificates WHERE user_id = :uid AND course_id = :cid");
        $stmtCertRevoke->execute([
            ':uid' => $studentId,
            ':cid' => $courseId
        ]);

        redirectWithMessage(
            $redirectTo, 
            'success', 
            "Revision requested for {$studentName}. Feedback has been communicated."
        );
    }

} catch (PDOException $e) {
    error_log('Instructor review submission error: ' . $e->getMessage());
    redirectWithMessage($redirectTo, 'error', 'An error occurred while grading this submission. Please try again.');
}
