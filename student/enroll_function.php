<?php

require_once __DIR__ . '/../validation.php';
ensureSessionStarted();

require_once __DIR__ . '/../database/config.php';

// Must be logged in as a student
if (!isset($_SESSION['user_id'])) {
    $courseId = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    header('Location: ../login.php?redirect_course=' . $courseId . '&status=error&message=' . urlencode('Please log in with your student account to enroll.'));
    exit;
}

if (($_SESSION['role_name'] ?? '') !== 'student') {
    header('Location: ../courses.php?status=error&message=' . urlencode('Only student accounts can enroll in courses.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['course_id'])) {
    header('Location: ../courses.php');
    exit;
}

if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $courseId = (int)$_POST['course_id'];
    header('Location: ../course_details.php?id=' . $courseId . '&status=error&message=' . urlencode('Invalid security token. Please refresh the page and try again.'));
    exit;
}

$courseId  = (int)$_POST['course_id'];
$studentId = (int)$_SESSION['user_id'];

try {
    $pdo = getConnection();

    // Verify course exists and is published
    $stmt = $pdo->prepare("SELECT id, title, status FROM courses WHERE id = :id LIMIT 1");
    $stmt->bindValue(':id', $courseId, PDO::PARAM_INT);
    $stmt->execute();
    $course = $stmt->fetch();

    if (!$course) {
        header('Location: ../courses.php?status=error&message=' . urlencode('The selected course does not exist.'));
        exit;
    }

    if (($course['status'] ?? '') !== 'published') {
        header('Location: ../courses.php?status=error&message=' . urlencode('This course is not currently open for enrollment.'));
        exit;
    }

    // Insert or maintain enrollment
    $sqlEnroll = "INSERT INTO enrollments (user_id, course_id, progress_percent, status, enrolled_at)
                  VALUES (:user_id, :course_id, 0, 'in_progress', CURRENT_TIMESTAMP)
                  ON DUPLICATE KEY UPDATE user_id = user_id";
    $stmtEnroll = $pdo->prepare($sqlEnroll);
    $stmtEnroll->bindValue(':user_id', $studentId, PDO::PARAM_INT);
    $stmtEnroll->bindValue(':course_id', $courseId, PDO::PARAM_INT);
    $stmtEnroll->execute();

    // Redirect to course details page with success message and prominent video player button
    $successMsg = 'Successfully enrolled in "' . $course['title'] . '"! Click below to start the course.';
    header('Location: ../course_details.php?id=' . $courseId . '&status=success&message=' . urlencode($successMsg));
    exit;

} catch (PDOException $e) {
    error_log('Enrollment error: ' . $e->getMessage());
    header('Location: ../courses.php?status=error&message=' . urlencode('Failed to enroll in the course due to a system error. Please try again.'));
    exit;
}
