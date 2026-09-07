<?php
/**
 * Mock Google AdSense for Video - Publisher & Developer Sandbox
 * 
 * Interactive developer sandbox simulating Google AdSense for Video and 
 * the Google Interactive Media Ads (IMA) ad server.
 */

$inventoryFile = __DIR__ . '/inventory.json';
$inventory = file_exists($inventoryFile) ? (json_decode(file_get_contents($inventoryFile), true) ?: []) : [];
$totalCampaigns = count($inventory);
$avgCpm = 0;
if ($totalCampaigns > 0) {
    $sum = array_sum(array_column($inventory, 'cpm_bid'));
    $avgCpm = $sum / $totalCampaigns;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google AdSense for Video (Mock Service) | Adsity Sandbox</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Roboto:wght@400;500;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --google-blue: #1a73e8;
            --google-blue-dark: #1557b0;
            --google-red: #ea4335;
            --google-yellow: #fbbc04;
            --google-green: #34a853;
            --surface-bg: #f8f9fa;
            --card-bg: #ffffff;
            --text-dark: #202124;
            --text-muted: #5f6368;
            --border-color: #dadce0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--surface-bg);
            color: var(--text-dark);
            line-height: 1.5;
            padding: 30px 20px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        /* Top Header */
        .adsense-header {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(60,64,67,0.08);
        }

        .brand-block {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            font-family: 'Google Sans', sans-serif;
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .logo-g { color: #4285f4; }
        .logo-o1 { color: #ea4335; }
        .logo-o2 { color: #fbbc05; }
        .logo-g2 { color: #4285f4; }
        .logo-l { color: #34a853; }
        .logo-e { color: #ea4335; }
        .logo-adsense { color: #5f6368; margin-left: 8px; font-weight: 400; }

        .sandbox-badge {
            background-color: #e8f0fe;
            color: var(--google-blue);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 16px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.88rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-nav--secondary {
            background-color: #ffffff;
            color: var(--google-blue);
            border: 1px solid var(--border-color);
        }
        .btn-nav--secondary:hover {
            background-color: #f1f3f4;
        }

        .btn-nav--primary {
            background-color: var(--google-blue);
            color: #ffffff;
            border: 1px solid transparent;
        }
        .btn-nav--primary:hover {
            background-color: var(--google-blue-dark);
        }

        /* Metrics Row */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .metric-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 1px 2px rgba(60,64,67,0.06);
        }

        .metric-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .metric-value {
            font-family: 'Google Sans', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .metric-sub {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Content Sections */
        .section-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(60,64,67,0.06);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .section-title {
            font-family: 'Google Sans', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        /* Inventory Table */
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }

        .inventory-table th {
            text-align: left;
            padding: 12px 14px;
            background-color: #f8f9fa;
            color: var(--text-muted);
            font-weight: 700;
            border-bottom: 2px solid var(--border-color);
            font-size: 0.78rem;
            text-transform: uppercase;
        }

        .inventory-table td {
            padding: 14px;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
        }

        .badge-cpm {
            display: inline-block;
            background-color: #e6f4ea;
            color: #137333;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
        }

        .tag-active {
            display: inline-block;
            background-color: #e6f4ea;
            color: #137333;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
        }

        /* Interactive Simulator */
        .simulator-box {
            background-color: #202124;
            border-radius: 10px;
            padding: 20px;
            color: #f1f3f4;
            margin-top: 14px;
        }

        .sim-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .endpoint-pill {
            font-family: 'JetBrains Mono', monospace;
            background-color: #303134;
            color: #8ab4f8;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.82rem;
        }

        .btn-test {
            background-color: var(--google-blue);
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            font-size: 0.85rem;
        }
        .btn-test:hover {
            background-color: var(--google-blue-dark);
        }

        .console-output {
            background-color: #171717;
            border: 1px solid #3c4043;
            border-radius: 8px;
            padding: 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            color: #e8eaed;
            max-height: 280px;
            overflow-y: auto;
            white-space: pre-wrap;
            line-height: 1.45;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Header -->
        <header class="adsense-header">
            <div class="brand-block">
                <div class="brand-logo">
                    <span class="logo-g">G</span><span class="logo-o1">o</span><span class="logo-o2">o</span><span class="logo-g2">g</span><span class="logo-l">l</span><span class="logo-e">e</span>
                    <span class="logo-adsense">AdSense for Video</span>
                </div>
                <span class="sandbox-badge">Simulated Network</span>
            </div>
            <div class="header-actions">
                <a href="../admin/dashboard.php#ads" class="btn-nav btn-nav--secondary">&larr; Adsity Admin</a>
                <a href="../student/dashboard.php" class="btn-nav btn-nav--primary">Open Classroom</a>
            </div>
        </header>

        <!-- KPI Metrics -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Publisher Account ID</div>
                <div class="metric-value" style="font-size: 1.4rem; color: #1a73e8;">pub-849201837492</div>
                <div class="metric-sub">Linked to Adsity Platform Sandbox</div>
            </div>

            <div class="metric-card">
                <div class="metric-label">Active Advertisers</div>
                <div class="metric-value"><?= $totalCampaigns ?> <?= $totalCampaigns === 1 ? 'Active Campaign' : 'Active Campaigns' ?></div>
                <div class="metric-sub">Coca-Cola Real Magic &bull; 15s Pre-Roll</div>
            </div>

            <div class="metric-card">
                <div class="metric-label">Average Auction Bid</div>
                <div class="metric-value">$<?= number_format($avgCpm, 4) ?></div>
                <div class="metric-sub">Standard CPM per 15s pre-roll view</div>
            </div>

            <div class="metric-card">
                <div class="metric-label">Engine Protocol</div>
                <div class="metric-value" style="font-size: 1.35rem; color: #137333;">VAST 3.0 &amp; JSON</div>
                <div class="metric-sub">Google IMA SDK compatible endpoint</div>
            </div>
        </div>

        <!-- Global Ad Inventory Section -->
        <div class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Google AdSense Simulated Advertiser Inventory</h2>
                    <p style="font-size: 0.84rem; color: var(--text-muted); margin-top: 3px;">
                        Active Coca-Cola commercial pre-roll currently serving in the Adsity video classroom auction.
                    </p>
                </div>
                <button type="button" class="btn-nav btn-nav--secondary" onclick="runAuctionTest('json')">
                    ↻ Test Real-Time Auction
                </button>
            </div>

            <div style="overflow-x: auto;">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Ad Unit ID</th>
                            <th>Brand / Sponsor</th>
                            <th>Campaign Headline</th>
                            <th>Category</th>
                            <th>Auction Bid (CPM)</th>
                            <th>Format</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventory as $ad): ?>
                            <tr>
                                <td><code style="font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; color: #1a73e8;"><?= htmlspecialchars($ad['ad_unit_id']) ?></code></td>
                                <td><strong><?= htmlspecialchars($ad['sponsor_name']) ?></strong></td>
                                <td><?= htmlspecialchars($ad['campaign_title']) ?></td>
                                <td><?= htmlspecialchars($ad['category']) ?></td>
                                <td>
                                    <span class="badge-cpm">$<?= number_format((float)$ad['cpm_bid'], 4) ?></span>
                                </td>
                                <td><?= (int)$ad['duration'] ?>s Linear Pre-roll</td>
                                <td><span class="tag-active">Active (Serving)</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Live Auction Simulator -->
        <div class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Live Ad Auction &amp; VAST Tag Simulator</h2>
                    <p style="font-size: 0.84rem; color: var(--text-muted); margin-top: 3px;">
                        Test how Adsity's video classroom queries the Mock AdSense API and inspect the live response.
                    </p>
                </div>
            </div>

            <div class="simulator-box">
                <div class="sim-header">
                    <span class="endpoint-pill" id="endpointDisplay">GET /mock_adsense/serve_ad.php</span>
                    <div style="display: flex; gap: 8px;">
                        <button type="button" class="btn-test" onclick="runAuctionTest('json')">Fetch JSON Ad</button>
                        <button type="button" class="btn-test" style="background-color: #34a853;" onclick="runAuctionTest('vast')">Fetch VAST 3.0 XML</button>
                    </div>
                </div>

                <div class="console-output" id="consoleOutput">Click "Fetch JSON Ad" or "Fetch VAST 3.0 XML" above to simulate an ad auction call from the Adsity video classroom...</div>
            </div>
        </div>
    </div>

    <script>
        function runAuctionTest(format) {
            const display = document.getElementById('endpointDisplay');
            const output = document.getElementById('consoleOutput');
            const url = 'serve_ad.php?format=' + format;

            display.textContent = 'GET ' + url + ' (Running auction...)';
            output.textContent = 'Fetching auction payload from Google AdSense Mock Network...';

            fetch(url)
                .then(function(res) {
                    return res.text();
                })
                .then(function(data) {
                    display.textContent = 'GET ' + url + ' (200 OK)';
                    output.textContent = data;
                })
                .catch(function(err) {
                    display.textContent = 'GET ' + url + ' (Error)';
                    output.textContent = 'Error querying ad server: ' + err.message;
                });
        }
    </script>
</body>
</html>
