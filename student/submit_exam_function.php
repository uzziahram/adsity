<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    // Check enrollment and completion
    $stmtEnr = $pdo->prepare("SELECT id, progress_percent, status FROM enrollments WHERE user_id = :uid AND course_id = :cid LIMIT 1");
    $stmtEnr->bindValue(':uid', $studentId, PDO::PARAM_INT);
    $stmtEnr->bindValue(':cid', $courseId, PDO::PARAM_INT);
    $stmtEnr->execute();
    $enrollment = $stmtEnr->fetch();

    if (!$enrollment) {
        header('Location: dashboard.php?status=error&message=' . urlencode('You must enroll in this course first before viewing or submitting final deliverables.'));
        exit;
    }

    $isCourseFinished = ((int)($enrollment['progress_percent'] ?? 0) >= 100) || (($enrollment['status'] ?? '') === 'completed');

    // Fetch latest existing submission
    $stmtSub = $pdo->prepare("SELECT * FROM course_submissions WHERE user_id = :uid AND course_id = :cid ORDER BY id DESC LIMIT 1");
    $stmtSub->bindValue(':uid', $studentId, PDO::PARAM_INT);
    $stmtSub->bindValue(':cid', $courseId, PDO::PARAM_INT);
    $stmtSub->execute();
    $latestSubmission = $stmtSub->fetch();

    // Fetch existing certificate if any
    $stmtCertCheck = $pdo->prepare("SELECT * FROM certificates WHERE user_id = :uid AND course_id = :cid LIMIT 1");
    $stmtCertCheck->bindValue(':uid', $studentId, PDO::PARAM_INT);
    $stmtCertCheck->bindValue(':cid', $courseId, PDO::PARAM_INT);
    $stmtCertCheck->execute();
    $existingCertificate = $stmtCertCheck->fetch();

    // Handle Form Submission
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['submit_assessment'])) {
        if (!$isCourseFinished) {
            header('Location: submit_exam.php?course_id=' . $courseId . '&status=error&message=' . urlencode('You cannot submit your project until you have finished all lessons in the course.'));
            exit;
        }

        if ($latestSubmission && $latestSubmission['status'] === 'approved') {
            header('Location: submit_exam.php?course_id=' . $courseId . '&status=error&message=' . urlencode('Your assessment has already been approved!'));
            exit;
        }

        if ($latestSubmission && $latestSubmission['status'] === 'pending') {
            header('Location: submit_exam.php?course_id=' . $courseId . '&status=error&message=' . urlencode('Your project deliverable is already pending review by your instructor.'));
            exit;
        }

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

        // Save submission as 'pending' for instructor manual grading
        if ($latestSubmission && $latestSubmission['status'] === 'revision_needed') {
            $sqlUpdate = "UPDATE course_submissions 
                          SET submission_type = :submission_type, 
                              submission_value = :submission_value, 
                              notes = :notes, 
                              status = 'pending', 
                              submitted_at = CURRENT_TIMESTAMP 
                          WHERE id = :id";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([
                ':submission_type'  => $assessmentType,
                ':submission_value' => $submissionVal,
                ':notes'            => htmlspecialchars($notes),
                ':id'               => $latestSubmission['id']
            ]);
        } else {
            $sqlInsert = "INSERT INTO course_submissions (user_id, course_id, submission_type, submission_value, notes, status)
                          VALUES (:user_id, :course_id, :submission_type, :submission_value, :notes, 'pending')";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([
                ':user_id'          => $studentId,
                ':course_id'        => $courseId,
                ':submission_type'  => $assessmentType,
                ':submission_value' => $submissionVal,
                ':notes'            => htmlspecialchars($notes)
            ]);
        }

        header('Location: submit_exam.php?course_id=' . $courseId . '&status=success&message=' . urlencode('Project deliverable submitted successfully! Your instructor will review and grade your project.'));
        exit;
    }

} catch (PDOException $e) {
    header('Location: dashboard.php?status=error&message=' . urlencode($e->getMessage()));
    exit;
}
