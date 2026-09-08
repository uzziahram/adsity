<?php

require_once __DIR__ . '/../validation.php';
requireAuth('admin', '../login.php?status=error&message=' . urlencode('Unauthorized: Administrator access required.'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
    redirect('dashboard.php?status=error&message=' . urlencode('Invalid ad management request.') . '#ads');
}

verifyCsrfOrRedirect('dashboard.php?status=error&message=' . urlencode('Invalid security token. Please refresh the page and try again.') . '#ads');

require_once __DIR__ . '/../database/config.php';

$action = trim($_POST['action']);

try {
    $pdo = getConnection();

    if ($action === 'add_ad') {
        header('Location: dashboard.php?status=error&message=' . urlencode('Manual ad creation is disabled. All advertisements are delivered exclusively via Google AdSense (mock_adsense).') . '#ads');
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
        header('Location: dashboard.php?status=error&message=' . urlencode('Google AdSense network campaigns cannot be deleted. You may pause or activate the feed instead.') . '#ads');
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
    error_log('Admin manage ads error: ' . $e->getMessage());
    header('Location: dashboard.php?status=error&message=' . urlencode('An error occurred while updating ad monetization settings. Please try again.') . '#ads');
    exit;
}
