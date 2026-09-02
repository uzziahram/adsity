<?php

session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'student') {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in with a student account.'));
    exit;
}

require_once __DIR__ . '/../database/config.php';

$studentId = $_SESSION['user_id'];
$courseId  = $_GET['course_id'] ?? ($_POST['course_id'] ?? null);
$status    = $_GET['status'] ?? null;
$message   = $_GET['message'] ?? null;

$course = null;

if (!$courseId) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Invalid course specified.'));
    exit;
}

try {
    $pdo = getConnection();

    // Fetch course details
    $stmtCourse = $pdo->prepare("SELECT id, title, description, category, thumbnail, total_lessons, assessment_type, assessment_instructions FROM courses WHERE id = :id LIMIT 1");
    $stmtCourse->bindValue(':id', $courseId, PDO::PARAM_INT);
    $stmtCourse->execute();
    $course = $stmtCourse->fetch();

    if (!$course) {
        header('Location: dashboard.php?status=error&message=' . urlencode('Course not found.'));
        exit;
    }

    // Handle Form Submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_assessment'])) {
        $assessmentType = $course['assessment_type'] ?? 'github_repo';
        $notes          = trim($_POST['notes'] ?? '');
        $submissionVal  = '';

        if ($assessmentType === 'github_repo') {
            $githubUrl = trim($_POST['github_url'] ?? '');
            if ($githubUrl === '' || !filter_var($githubUrl, FILTER_VALIDATE_URL)) {
                header('Location: submit_exam.php?course_id=' . $courseId . '&status=error&message=' . urlencode('Please provide a valid GitHub repository URL.'));
                exit;
            }
            $submissionVal = $githubUrl;

        } elseif ($assessmentType === 'live_url') {
            $liveUrl = trim($_POST['live_url'] ?? '');
            if ($liveUrl === '' || !filter_var($liveUrl, FILTER_VALIDATE_URL)) {
                header('Location: submit_exam.php?course_id=' . $courseId . '&status=error&message=' . urlencode('Please provide a valid live demo URL.'));
                exit;
            }
            $submissionVal = $liveUrl;

        } elseif ($assessmentType === 'file_upload') {
            if (!isset($_FILES['project_file']) || $_FILES['project_file']['error'] !== UPLOAD_ERR_OK) {
                header('Location: submit_exam.php?course_id=' . $courseId . '&status=error&message=' . urlencode('Please select a valid project file to upload.'));
                exit;
            }

            $file     = $_FILES['project_file'];
            $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed  = ['zip', 'rar', 'tar', 'gz', 'pdf', 'png', 'jpg', 'jpeg', 'txt', 'json'];

            if (!in_array($ext, $allowed)) {
                header('Location: submit_exam.php?course_id=' . $courseId . '&status=error&message=' . urlencode('Invalid file format. Allowed: ' . implode(', ', $allowed)));
                exit;
            }

            $uploadDir = __DIR__ . '/../assets/submissions/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $uniqueFileName = 'sub_' . $studentId . '_' . $courseId . '_' . time() . '.' . $ext;
            $destination    = $uploadDir . $uniqueFileName;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                header('Location: submit_exam.php?course_id=' . $courseId . '&status=error&message=' . urlencode('Failed to save uploaded file.'));
                exit;
            }

            $submissionVal = $uniqueFileName;
        }

        // 1. Insert into course_submissions
        $sqlSub = "INSERT INTO course_submissions (user_id, course_id, submission_type, submission_value, notes, status)
                   VALUES (:user_id, :course_id, :submission_type, :submission_value, :notes, 'approved')";
        $stmtSub = $pdo->prepare($sqlSub);
        $stmtSub->bindValue(':user_id', $studentId, PDO::PARAM_INT);
        $stmtSub->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmtSub->bindValue(':submission_type', $assessmentType);
        $stmtSub->bindValue(':submission_value', $submissionVal);
        $stmtSub->bindValue(':notes', htmlspecialchars($notes));
        $stmtSub->execute();

        // 2. Mark Enrollment Complete
        $sqlEnr = "INSERT INTO enrollments (user_id, course_id, progress_percent, status, completed_at)
                   VALUES (:user_id, :course_id, 100, 'completed', CURRENT_TIMESTAMP)
                   ON DUPLICATE KEY UPDATE progress_percent = 100, status = 'completed', completed_at = CURRENT_TIMESTAMP";
        $stmtEnr = $pdo->prepare($sqlEnr);
        $stmtEnr->bindValue(':user_id', $studentId, PDO::PARAM_INT);
        $stmtEnr->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmtEnr->execute();

        // 3. Generate & Issue Certificate
        $certCode = 'ADS-' . date('Y') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        $sqlCert = "INSERT INTO certificates (user_id, course_id, certificate_code, issued_at)
                    VALUES (:user_id, :course_id, :certificate_code, CURRENT_TIMESTAMP)
                    ON DUPLICATE KEY UPDATE certificate_code = VALUES(certificate_code)";
        $stmtCert = $pdo->prepare($sqlCert);
        $stmtCert->bindValue(':user_id', $studentId, PDO::PARAM_INT);
        $stmtCert->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmtCert->bindValue(':certificate_code', $certCode);
        $stmtCert->execute();

        header('Location: certificate.php?code=' . urlencode($certCode) . '&status=success&message=' . urlencode('Assessment passed! Your verified certificate is ready.'));
        exit;
    }

} catch (PDOException $e) {
    header('Location: dashboard.php?status=error&message=' . urlencode($e->getMessage()));
    exit;
}
