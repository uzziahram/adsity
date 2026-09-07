<?php

session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'instructor') {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in with an instructor account.'));
    exit;
}

require_once __DIR__ . '/../database/config.php';

// Check if upload exceeded post_max_size
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST)) {
    header('Location: create_course.php?status=error&message=' . urlencode('Uploaded files exceeded server limit (' . ini_get('post_max_size') . '). Please upload smaller video files.'));
    exit;
}

if (!isset($_POST['create_course'])) {
    header('Location: create_course.php');
    exit;
}

$title                  = trim($_POST['title'] ?? '');
$category               = trim($_POST['category'] ?? '');
$description            = trim($_POST['description'] ?? '');
$thumbnail              = trim($_POST['thumbnail'] ?? 'Web_Development_Basics.png');
$assessmentType         = trim($_POST['assessment_type'] ?? 'github_repo');
$assessmentInstructions = trim($_POST['assessment_instructions'] ?? '');
$lessonTitles           = $_POST['lesson_titles'] ?? [];
$lessonDurations        = $_POST['lesson_durations'] ?? [];

$instructorId = $_SESSION['user_id'];

// 1. Basic validation
if ($title === '' || $category === '' || $description === '') {
    header('Location: create_course.php?status=error&message=' . urlencode('Please fill out all required course fields.'));
    exit;
}

if (!isset($_FILES['course_thumbnail']) || $_FILES['course_thumbnail']['error'] !== UPLOAD_ERR_OK) {
    header('Location: create_course.php?status=error&message=' . urlencode('Please upload a course cover thumbnail image.'));
    exit;
}

$thumbFile = $_FILES['course_thumbnail']['name'];
$thumbTmp  = $_FILES['course_thumbnail']['tmp_name'];
$thumbExt  = strtolower(pathinfo($thumbFile, PATHINFO_EXTENSION));
$allowedImgExts = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($thumbExt, $allowedImgExts)) {
    header('Location: create_course.php?status=error&message=' . urlencode('Invalid thumbnail format. Please upload a PNG, JPG, or WEBP image.'));
    exit;
}

if (empty($lessonTitles)) {
    header('Location: create_course.php?status=error&message=' . urlencode('Please add at least one lesson video to the course.'));
    exit;
}

if (!in_array($assessmentType, ['github_repo', 'file_upload', 'live_url'])) {
    $assessmentType = 'github_repo';
}

$totalLessons = count($lessonTitles);

try {
    $pdo = getConnection();

    // 2. Insert Course record (starts in pending_review for admin moderation)
    $sql = "INSERT INTO courses (title, description, category, thumbnail, total_lessons, instructor_id, assessment_type, assessment_instructions, status)
            VALUES (:title, :description, :category, :thumbnail, :total_lessons, :instructor_id, :assessment_type, :assessment_instructions, 'pending_review')";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':title', htmlspecialchars($title));
    $stmt->bindValue(':description', htmlspecialchars($description));
    $stmt->bindValue(':category', htmlspecialchars($category));
    $stmt->bindValue(':thumbnail', 'Web_Development_Basics.png');
    $stmt->bindValue(':total_lessons', $totalLessons, PDO::PARAM_INT);
    $stmt->bindValue(':instructor_id', $instructorId, PDO::PARAM_INT);
    $stmt->bindValue(':assessment_type', $assessmentType);
    $stmt->bindValue(':assessment_instructions', htmlspecialchars($assessmentInstructions));
    $stmt->execute();

    $courseId = $pdo->lastInsertId();

    // 3. Create Instructor-specific directory structure:
    // uploads/instructors/{instructor_id}/{course_id}/thumbnail/
    $instructorDir = __DIR__ . '/../uploads/instructors/' . $instructorId . '/';
    $courseDir     = $instructorDir . $courseId . '/';
    $thumbnailDir  = $courseDir . 'thumbnail/';

    if (!is_dir($thumbnailDir)) {
        mkdir($thumbnailDir, 0777, true);
    }

    $safeThumbName   = 'thumbnail_' . time() . '.' . $thumbExt;
    $targetThumbPath = $thumbnailDir . $safeThumbName;

    if (move_uploaded_file($thumbTmp, $targetThumbPath)) {
        $finalThumbnail = 'uploads/instructors/' . $instructorId . '/' . $courseId . '/thumbnail/' . $safeThumbName;

        $updateThumbStmt = $pdo->prepare("UPDATE courses SET thumbnail = :thumbnail WHERE id = :id");
        $updateThumbStmt->execute([
            ':thumbnail' => $finalThumbnail,
            ':id'        => $courseId
        ]);
    }

    // 4. Process Multi-Video Lessons
    $allowedVideoExts = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];

    $sqlLesson = "INSERT INTO lessons (course_id, lesson_number, title, video_path, duration)
                  VALUES (:course_id, :lesson_number, :title, :video_path, :duration)";
    $stmtLesson = $pdo->prepare($sqlLesson);

    // Helper function to detect video duration from uploaded file via ffprobe
    if (!function_exists('detectVideoDurationFromFile')) {
        function detectVideoDurationFromFile($filePath) {
            if (file_exists($filePath)) {
                $escaped = escapeshellarg($filePath);
                $cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 {$escaped} 2>/dev/null";
                $output = trim(shell_exec($cmd) ?? '');
                if ($output !== '' && is_numeric($output)) {
                    $totalSecs = (int)round((float)$output);
                    $hrs = floor($totalSecs / 3600);
                    $mins = floor(($totalSecs % 3600) / 60);
                    $secs = $totalSecs % 60;
                    if ($hrs > 0) {
                        return sprintf('%02d:%02d:%02d', $hrs, $mins, $secs);
                    }
                    return sprintf('%02d:%02d', $mins, $secs);
                }
            }
            return null;
        }
    }

    foreach ($lessonTitles as $index => $lessonTitle) {
        $lessonNumber = $index + 1;
        $titleClean   = trim($lessonTitle);
        $clientDuration = trim($lessonDurations[$index] ?? '');
        $duration     = $clientDuration !== '' ? $clientDuration : '10:00';
        $videoPath    = 'assets/ad/sample_ad.mp4'; // Default fallback video

        // Check if an actual video file was uploaded for this lesson
        if (
            isset($_FILES['lesson_videos']['name'][$index]) &&
            $_FILES['lesson_videos']['error'][$index] === UPLOAD_ERR_OK
        ) {
            $fileName = $_FILES['lesson_videos']['name'][$index];
            $tmpPath  = $_FILES['lesson_videos']['tmp_name'][$index];
            $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (in_array($ext, $allowedVideoExts)) {
                $safeName    = 'lesson_' . $lessonNumber . '_' . time() . '.' . $ext;
                $targetFile  = $courseDir . $safeName;

                if (move_uploaded_file($tmpPath, $targetFile)) {
                    $videoPath = 'uploads/instructors/' . $instructorId . '/' . $courseId . '/' . $safeName;

                    // Detect exact duration using ffprobe
                    $detectedDuration = detectVideoDurationFromFile($targetFile);
                    if ($detectedDuration) {
                        $duration = $detectedDuration;
                    }
                }
            }
        }

        $stmtLesson->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmtLesson->bindValue(':lesson_number', $lessonNumber, PDO::PARAM_INT);
        $stmtLesson->bindValue(':title', htmlspecialchars($titleClean));
        $stmtLesson->bindValue(':video_path', $videoPath);
        $stmtLesson->bindValue(':duration', htmlspecialchars($duration));
        $stmtLesson->execute();
    }

    header('Location: dashboard.php?status=success&message=' . urlencode('Multi-video course "' . $title . '" published with ' . $totalLessons . ' lessons!'));
    exit;

} catch (PDOException $e) {
    header('Location: create_course.php?status=error&message=' . urlencode($e->getMessage()));
    exit;
}
