<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Guard: Only authenticated administrators can manage sponsor ads and monetization settings
if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'admin') {
    header('Location: ../login.php?status=error&message=' . urlencode('Unauthorized: Administrator access required.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Invalid ad management request.') . '#ads');
    exit;
}

require_once __DIR__ . '/../database/config.php';

$action = trim($_POST['action']);

try {
    $pdo = getConnection();

    if ($action === 'add_ad') {
        $sponsorName   = trim($_POST['sponsor_name'] ?? '');
        $campaignTitle = trim($_POST['campaign_title'] ?? '');
        $videoUrl      = trim($_POST['video_url'] ?? '');
        $clickUrl      = trim($_POST['click_url'] ?? '');
        $cpmRate       = max(0.0001, (float)($_POST['cpm_rate'] ?? 0.0500));
        $status        = ($_POST['status'] ?? 'active') === 'paused' ? 'paused' : 'active';

        if ($sponsorName === '' || $campaignTitle === '') {
            header('Location: dashboard.php?status=error&message=' . urlencode('Sponsor name and campaign title are required.') . '#ads');
            exit;
        }

        // 1. Process Video File Upload (if admin selected a file from their file manager)
        if (isset($_FILES['ad_video_file']) && $_FILES['ad_video_file']['error'] === UPLOAD_ERR_OK) {
            $file     = $_FILES['ad_video_file'];
            $fileName = $file['name'];
            $tmpPath  = $file['tmp_name'];
            $fileSize = (int)$file['size'];
            $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExts = ['mp4', 'webm', 'ogg', 'mov'];
            if (!in_array($ext, $allowedExts, true)) {
                header('Location: dashboard.php?status=error&message=' . urlencode('Invalid video format. Please upload MP4, WebM, or MOV.') . '#ads');
                exit;
            }

            // Limit to 100MB
            if ($fileSize > 100 * 1024 * 1024) {
                header('Location: dashboard.php?status=error&message=' . urlencode('Video file exceeds maximum limit of 100MB.') . '#ads');
                exit;
            }

            $uploadDir = __DIR__ . '/../uploads/ads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $safeName   = 'ad_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $targetPath = $uploadDir . $safeName;

            if (move_uploaded_file($tmpPath, $targetPath)) {
                $videoUrl = 'uploads/ads/' . $safeName;
            } else {
                header('Location: dashboard.php?status=error&message=' . urlencode('Failed to write uploaded video file to server disk.') . '#ads');
                exit;
            }
        } elseif (isset($_FILES['ad_video_file']) && $_FILES['ad_video_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE   => 'Uploaded video exceeds server upload_max_filesize limit.',
                UPLOAD_ERR_FORM_SIZE  => 'Uploaded video exceeds MAX_FILE_SIZE limit.',
                UPLOAD_ERR_PARTIAL    => 'Video was only partially uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to save file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension interrupted video upload.',
            ];
            $errText = $uploadErrors[$_FILES['ad_video_file']['error']] ?? 'File upload error occurred.';
            header('Location: dashboard.php?status=error&message=' . urlencode($errText) . '#ads');
            exit;
        }

        // 2. Handle fallback video URL if no file was uploaded
        if ($videoUrl === '') {
            $videoUrl = 'assets/ad/sample_ad.mp4';
        }

        $stmt = $pdo->prepare("INSERT INTO sponsor_ads (sponsor_name, campaign_title, video_url, click_url, cpm_rate, status) 
                               VALUES (:name, :title, :video, :click, :cpm, :status)");
        $stmt->execute([
            ':name'   => htmlspecialchars($sponsorName),
            ':title'  => htmlspecialchars($campaignTitle),
            ':video'  => htmlspecialchars($videoUrl),
            ':click'  => $clickUrl !== '' ? htmlspecialchars($clickUrl) : null,
            ':cpm'    => $cpmRate,
            ':status' => $status
        ]);

        $msg = "New sponsor ad campaign '{$campaignTitle}' by {$sponsorName} created successfully.";
        header('Location: dashboard.php?status=success&message=' . urlencode($msg) . '#ads');
        exit;

    } elseif ($action === 'toggle_status') {
        $adId = (int)($_POST['ad_id'] ?? 0);
        if ($adId <= 0) {
            header('Location: dashboard.php?status=error&message=' . urlencode('Invalid campaign ID.') . '#ads');
            exit;
        }

        $stmtCheck = $pdo->prepare("SELECT id, status, campaign_title FROM sponsor_ads WHERE id = :id LIMIT 1");
        $stmtCheck->execute([':id' => $adId]);
        $ad = $stmtCheck->fetch();

        if (!$ad) {
            header('Location: dashboard.php?status=error&message=' . urlencode('Campaign not found.') . '#ads');
            exit;
        }

        $newStatus = $ad['status'] === 'active' ? 'paused' : 'active';
        $stmtUpdate = $pdo->prepare("UPDATE sponsor_ads SET status = :status WHERE id = :id");
        $stmtUpdate->execute([':status' => $newStatus, ':id' => $adId]);

        $msg = "Campaign '{$ad['campaign_title']}' is now " . ucfirst($newStatus) . ".";
        header('Location: dashboard.php?status=success&message=' . urlencode($msg) . '#ads');
        exit;

    } elseif ($action === 'delete_ad') {
        $adId = (int)($_POST['ad_id'] ?? 0);
        if ($adId <= 0) {
            header('Location: dashboard.php?status=error&message=' . urlencode('Invalid campaign ID.') . '#ads');
            exit;
        }

        // Check if ad exists and if video file was uploaded locally
        $stmtCheck = $pdo->prepare("SELECT id, video_url, campaign_title FROM sponsor_ads WHERE id = :id LIMIT 1");
        $stmtCheck->execute([':id' => $adId]);
        $ad = $stmtCheck->fetch();

        if ($ad) {
            // Delete uploaded file if stored in uploads/ads/
            if (!empty($ad['video_url']) && str_starts_with($ad['video_url'], 'uploads/ads/')) {
                $filePath = __DIR__ . '/../' . $ad['video_url'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $stmtDelete = $pdo->prepare("DELETE FROM sponsor_ads WHERE id = :id");
            $stmtDelete->execute([':id' => $adId]);
        }

        header('Location: dashboard.php?status=success&message=' . urlencode('Sponsor campaign deleted successfully.') . '#ads');
        exit;

    } elseif ($action === 'update_settings') {
        $defaultCpm       = max(0.001, (float)($_POST['default_ad_cpm'] ?? 0.0500));
        $instructorShare  = max(1, min(99, (int)($_POST['instructor_rev_share_percent'] ?? 70)));
        $platformShare    = 100 - $instructorShare;
        $adInterval       = max(1, min(60, (int)($_POST['ad_interval_minutes'] ?? 5)));

        $settings = [
            'default_ad_cpm'               => (string)$defaultCpm,
            'instructor_rev_share_percent' => (string)$instructorShare,
            'platform_rev_share_percent'   => (string)$platformShare,
            'ad_interval_minutes'          => (string)$adInterval,
        ];

        $stmtSet = $pdo->prepare("INSERT INTO platform_settings (setting_key, setting_value) 
                                 VALUES (:k, :v) 
                                 ON DUPLICATE KEY UPDATE setting_value = :v2");
        foreach ($settings as $k => $v) {
            $stmtSet->execute([':k' => $k, ':v' => $v, ':v2' => $v]);
        }

        header('Location: dashboard.php?status=success&message=' . urlencode('Platform monetization settings updated successfully.') . '#ads');
        exit;

    } else {
        header('Location: dashboard.php?status=error&message=' . urlencode('Unknown action requested.') . '#ads');
        exit;
    }

} catch (PDOException $e) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Database error: ' . $e->getMessage()) . '#ads');
    exit;
}
