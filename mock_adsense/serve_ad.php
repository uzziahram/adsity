<?php
/**
 * Mock Google AdSense for Video API Endpoint
 * 
 * Simulates the Google Interactive Media Ads (IMA) / VAST 3.0 auction engine.
 * Receives ad requests from video classrooms, runs an automated auction across
 * registered global sponsors (Nike, Spotify, Google Cloud, JetBrains, Duolingo),
 * and serves high-definition video pre-roll payloads with dynamic CPM rates.
 */

// Allow cross-origin requests from the Adsity classroom player
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$inventoryFile = __DIR__ . '/inventory.json';
if (!file_exists($inventoryFile)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'status'  => 'error',
        'message' => 'Ad inventory database not found.'
    ]);
    exit;
}

$inventoryData = json_decode(file_get_contents($inventoryFile), true) ?: [];
$activeAds = array_filter($inventoryData, function ($ad) {
    return ($ad['status'] ?? '') === 'active';
});

if (empty($activeAds)) {
    http_response_code(204); // No content / No fill
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'status'  => 'no_fill',
        'message' => 'No active sponsor campaigns available in auction.'
    ]);
    exit;
}

// Support optional query parameters
$format    = strtolower(trim($_GET['format'] ?? 'json'));
$reqAdId   = isset($_GET['ad_id']) ? (int)$_GET['ad_id'] : null;
$category  = trim($_GET['category'] ?? '');
$courseId  = (int)($_GET['course_id'] ?? 0);
$lessonId  = (int)($_GET['lesson_id'] ?? 0);

// Filter by ad_id if requested
if ($reqAdId !== null) {
    $matched = array_filter($activeAds, function ($ad) use ($reqAdId) {
        return (int)($ad['id'] ?? 0) === $reqAdId;
    });
    if (!empty($matched)) {
        $activeAds = $matched;
    }
}

// Filter by category if matched
if ($category !== '') {
    $catMatched = array_filter($activeAds, function ($ad) use ($category) {
        return stripos($ad['category'] ?? '', $category) !== false;
    });
    if (!empty($catMatched)) {
        $activeAds = $catMatched;
    }
}

// Re-index and run simulated auction: pick random or weighted by CPM bid
$activeAds = array_values($activeAds);
$winningAd = $activeAds[array_rand($activeAds)];

// Generate simulated auction tracking tokens
$auctionId = 'auc_' . date('Ymd') . '_' . substr(md5(uniqid('', true)), 0, 10);
$adUnitId  = $winningAd['ad_unit_id'] ?? 'ca-video-pub-default';

// Construct clean URLs
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/mock_adsense/serve_ad.php');
$baseUri   = rtrim($protocol . $host . '/' . ltrim(str_replace('\\', '/', $scriptDir), '/'), '/');

$assetFilename = basename($winningAd['video_url'] ?? 'sample_ad.mp4');
$fullVideoUrl  = $baseUri . '/assets/' . $assetFilename;
$studentRelativePath = '../mock_adsense/assets/' . $assetFilename;

// Check requested format: VAST 3.0 XML vs JSON
if ($format === 'vast' || $format === 'xml') {
    header('Content-Type: application/xml; charset=UTF-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    ?>
    <VAST version="3.0">
        <Ad id="<?= htmlspecialchars($winningAd['ad_unit_id']) ?>">
            <InLine>
                <AdSystem version="2.0">Google AdSense Mock Network</AdSystem>
                <AdTitle><?= htmlspecialchars($winningAd['campaign_title']) ?></AdTitle>
                <Description>Ethical Ad-Funded Education Linear Pre-Roll</Description>
                <Advertiser><?= htmlspecialchars($winningAd['sponsor_name']) ?></Advertiser>
                <Pricing model="CPM" currency="USD"><?= number_format((float)$winningAd['cpm_bid'], 4) ?></Pricing>
                <Impression><?= htmlspecialchars($baseUri . '/serve_ad.php?beacon=impression&auction=' . $auctionId) ?></Impression>
                <Creatives>
                    <Creative id="creative_<?= htmlspecialchars($winningAd['id']) ?>" sequence="1">
                        <Linear>
                            <Duration>00:00:15</Duration>
                            <TrackingEvents>
                                <Tracking event="start"><?= htmlspecialchars($baseUri . '/serve_ad.php?beacon=start&auction=' . $auctionId) ?></Tracking>
                                <Tracking event="midpoint"><?= htmlspecialchars($baseUri . '/serve_ad.php?beacon=midpoint&auction=' . $auctionId) ?></Tracking>
                                <Tracking event="complete"><?= htmlspecialchars($baseUri . '/serve_ad.php?beacon=complete&auction=' . $auctionId) ?></Tracking>
                            </TrackingEvents>
                            <VideoClicks>
                                <ClickThrough><?= htmlspecialchars($winningAd['click_url']) ?></ClickThrough>
                            </VideoClicks>
                            <MediaFiles>
                                <MediaFile delivery="progressive" type="video/mp4" width="1280" height="720" scalable="true" maintainAspectRatio="true">
                                    <?= htmlspecialchars($fullVideoUrl) ?>
                                </MediaFile>
                            </MediaFiles>
                        </Linear>
                    </Creative>
                </Creatives>
            </InLine>
        </Ad>
    </VAST>
    <?php
    exit;
}

// Default response: Rich JSON for frontend player consumption
header('Content-Type: application/json; charset=UTF-8');
echo json_encode([
    'status'              => 'success',
    'network'             => 'Google AdSense for Video (Mock Service)',
    'protocol'            => 'VAST 3.0 Compatible',
    'auction_id'          => $auctionId,
    'publisher_id'        => 'pub-8492018374920194',
    'timestamp'           => date('c'),
    'ad' => [
        'id'              => (int)$winningAd['id'],
        'ad_unit_id'      => $winningAd['ad_unit_id'],
        'sponsor_name'    => $winningAd['sponsor_name'],
        'campaign_title'  => $winningAd['campaign_title'],
        'video_url'       => $fullVideoUrl,
        'relative_path'   => $studentRelativePath,
        'click_url'       => $winningAd['click_url'],
        'cpm_rate'        => (float)$winningAd['cpm_bid'],
        'duration'        => (int)($winningAd['duration'] ?? 15),
        'category'        => $winningAd['category'] ?? 'General Education',
        'format'          => 'linear_preroll'
    ],
    'tracking' => [
        'auction_id'      => $auctionId,
        'beacon_url'      => $baseUri . '/serve_ad.php?beacon=complete&auction=' . $auctionId
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit;
