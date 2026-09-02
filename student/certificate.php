<?php
require_once __DIR__ . '/certificate_function.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Verified Certificate - Adsity</title>
	<link rel="stylesheet" href="../style.css">
	<link rel="stylesheet" href="certificate.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Raleway:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="cert-page">

	<div class="cert-actions">
		<a href="dashboard.php" class="btn-back">
			<img src="../assets/icons/arrow-left.svg" width="16" height="16" alt="Back" style="filter: brightness(0) invert(1);">
			Back to Dashboard
		</a>
		<button class="btn-print" onclick="window.print()">
			<img src="../assets/icons/download.svg" width="16" height="16" alt="Download" style="filter: brightness(0) invert(1);">
			Print / Save Certificate
		</button>
	</div>

	<?php if ($certificate): ?>
		<div class="cert-frame">
			<img src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo" class="cert-logo">
			<h1 class="cert-header">Certificate of Completion</h1>
			<div class="cert-subtitle">Adsity Verified Credentials</div>

			<p class="cert-presented">This certificate is proudly awarded to</p>
			<div class="cert-recipient"><?= htmlspecialchars($certificate['student_name']) ?></div>

			<p class="cert-text">
				for successfully completing all assessments, practical modules, and required learning units in 
				<br><span class="cert-course"><?= htmlspecialchars($certificate['course_title']) ?></span>.
			</p>

			<div class="cert-footer">
				<div class="cert-detail">
					<div class="cert-label">Issue Date</div>
					<div class="cert-value"><?= date('F d, Y', strtotime($certificate['issued_at'])) ?></div>
					<div class="cert-label" style="margin-top: 8px;">Certificate ID</div>
					<div class="cert-value" style="font-family: monospace; color: #0284c7;"><?= htmlspecialchars($certificate['certificate_code']) ?></div>
				</div>

				<div class="cert-seal">
					<div class="seal-badge">VERIFIED</div>
					<span style="font-size: 0.75rem; color: #16a34a; font-weight: 700;">Funded by Ads</span>
				</div>

				<div class="cert-detail" style="text-align: right;">
					<div class="cert-label">Issued By</div>
					<div class="cert-value">Adsity Education, Org.</div>
					<div class="cert-label" style="margin-top: 8px;">Accreditation</div>
					<div class="cert-value">100% Free &amp; Shareable</div>
				</div>
			</div>
		</div>
	<?php else: ?>
		<div style="background-color: #ffffff; padding: 40px; border-radius: 12px; text-align: center; max-width: 500px;">
			<h2 style="color: #e11d48; margin-bottom: 12px;">Certificate Not Found</h2>
			<p style="color: #64748b; margin-bottom: 20px;">The requested certificate code could not be verified.</p>
			<a href="dashboard.php" class="btn-green" style="text-decoration: none;">Return to Dashboard</a>
		</div>
	<?php endif; ?>

</body>
</html>
