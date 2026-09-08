<?php

require_once __DIR__ . '/../helpers.php';
ensureSessionStarted();

// 1. Authentication check via reusable helper
if (!isAuthenticated() || !hasRole('student')) {
    sendJsonResponse(['success' => false, 'message' => 'Unauthorized: Please log in as a student.'], 401);
}

require_once __DIR__ . '/../database/config.php';

$studentId = getCurrentUserId();

// 2. Parse input (supports JSON or Form POST)
$rawInput = file_get_contents('php://input');
$data     = json_decode($rawInput, true);

$courseId = isset($data['course_id']) ? (int)$data['course_id'] : (int)($_POST['course_id'] ?? 0);
$lessonId = isset($data['lesson_id']) ? (int)$data['lesson_id'] : (int)($_POST['lesson_id'] ?? 0);

if ($courseId <= 0 || $lessonId <= 0) {
    sendJsonResponse(['success' => false, 'message' => 'Invalid course or lesson ID provided.'], 400);
}

try {
    $pdo = getConnection();

    // 3. Verify user is enrolled in this course
    $stmtEnr = $pdo->prepare("SELECT id, progress_percent, status FROM enrollments WHERE user_id = :uid AND course_id = :cid LIMIT 1");
    $stmtEnr->execute([':uid' => $studentId, ':cid' => $courseId]);
    $enrollment = $stmtEnr->fetch();

    if (!$enrollment) {
        sendJsonResponse(['success' => false, 'message' => 'You are not enrolled in this course.'], 403);
    }

    // 4. Verify lesson exists and belongs to course
    $stmtLesson = $pdo->prepare("SELECT id, lesson_number, title FROM lessons WHERE id = :lid AND course_id = :cid LIMIT 1");
    $stmtLesson->execute([':lid' => $lessonId, ':cid' => $courseId]);
    $lesson = $stmtLesson->fetch();

    if (!$lesson) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Lesson not found in this course.']);
        exit;
    }

    // 5. Insert or update record in lesson_completions
    $sqlInsert = "INSERT INTO lesson_completions (user_id, course_id, lesson_id, completed_at)
                  VALUES (:uid, :cid, :lid, CURRENT_TIMESTAMP)
                  ON DUPLICATE KEY UPDATE completed_at = CURRENT_TIMESTAMP";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->execute([':uid' => $studentId, ':cid' => $courseId, ':lid' => $lessonId]);

    // 6. Calculate new progress percentage
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM lessons WHERE course_id = :cid");
    $stmtTotal->execute([':cid' => $courseId]);
    $totalLessons = (int)$stmtTotal->fetchColumn();

    $stmtComp = $pdo->prepare("SELECT COUNT(*) FROM lesson_completions WHERE user_id = :uid AND course_id = :cid");
    $stmtComp->execute([':uid' => $studentId, ':cid' => $courseId]);
    $completedLessons = (int)$stmtComp->fetchColumn();

    $totalLessons = max(1, $totalLessons);
    $progressPercent = min(100, (int)round(($completedLessons / $totalLessons) * 100));
    $allCompleted = ($progressPercent >= 100 || $completedLessons >= $totalLessons);

    // 7. Update enrollments table
    $stmtUpdateEnr = $pdo->prepare("UPDATE enrollments SET progress_percent = :progress WHERE user_id = :uid AND course_id = :cid");
    $stmtUpdateEnr->execute([
        ':progress' => $progressPercent,
        ':uid'      => $studentId,
        ':cid'      => $courseId
    ]);

    // 8. Find the next uncompleted lesson
    $sqlNext = "SELECT id, lesson_number, title 
                FROM lessons 
                WHERE course_id = :cid 
                  AND id NOT IN (SELECT lesson_id FROM lesson_completions WHERE user_id = :uid)
                ORDER BY lesson_number ASC 
                LIMIT 1";
    $stmtNext = $pdo->prepare($sqlNext);
    $stmtNext->execute([':cid' => $courseId, ':uid' => $studentId]);
    $nextLesson = $stmtNext->fetch();

    sendJsonResponse([
        'success'           => true,
        'message'           => 'Lesson marked as completed!',
        'lesson_completed'  => [
            'id'            => (int)$lesson['id'],
            'lesson_number' => (int)$lesson['lesson_number'],
            'title'         => $lesson['title']
        ],
        'completed_lessons' => $completedLessons,
        'total_lessons'     => $totalLessons,
        'progress_percent'  => $progressPercent,
        'all_completed'     => $allCompleted,
        'next_lesson'       => $nextLesson ? [
            'id'            => (int)$nextLesson['id'],
            'lesson_number' => (int)$nextLesson['lesson_number'],
            'title'         => $nextLesson['title']
        ] : null
    ]);

} catch (PDOException $e) {
    error_log('Complete lesson error: ' . $e->getMessage());
    sendJsonResponse(['success' => false, 'message' => 'A server error occurred while updating your lesson progress.'], 500);
}
