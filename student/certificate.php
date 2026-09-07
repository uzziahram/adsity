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
		<a href="<?= htmlspecialchars($backUrl) ?>" class="btn-back">
			<img src="../assets/icons/arrow-left.svg" width="16" height="16" alt="Back" style="filter: brightness(0) invert(1);">
			<?= htmlspecialchars($backLabel) ?>
		</a>
		<button class="btn-print" onclick="window.print()">
			<img src="../assets/icons/download.svg" width="16" height="16" alt="Download" style="filter: brightness(0) invert(1);">
			Print / Save Certificate
		</button>
	</div>

	<?php if ($certificate): ?>
		<?php $isRevoked = ($certificate['status'] ?? 'valid') === 'revoked'; ?>

		<?php if ($isRevoked): ?>
			<div style="background-color: #fef2f2; border: 2px solid #ef4444; border-radius: 12px; padding: 16px 24px; max-width: 900px; width: 92%; margin: 0 auto 24px auto; color: #991b1b; text-align: center; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.12);">
				<div style="font-weight: 800; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; gap: 8px; letter-spacing: 0.5px;">
					<img src="../assets/icons/alert-circle.svg" width="22" height="22" alt="Revoked" style="filter: invert(18%) sepia(85%) saturate(4646%) hue-rotate(345deg) brightness(85%) contrast(92%);">
					OFFICIAL AUDIT NOTICE: THIS CERTIFICATE HAS BEEN REVOKED
				</div>
				<div style="font-size: 0.9rem; margin-top: 8px; color: #b91c1c; line-height: 1.5;">
					<strong>Revocation Date:</strong> <?= !empty($certificate['revoked_at']) ? date('F d, Y', strtotime($certificate['revoked_at'])) : 'Recently' ?> &bull; 
					<strong>Reason:</strong> <em><?= htmlspecialchars($certificate['revocation_reason'] ?? 'Credential invalidated following academic integrity investigation.') ?></em>
				</div>
				<div style="font-size: 0.8rem; color: #7f1d1d; margin-top: 4px;">
					This credential is void and cannot be verified for professional or employment purposes.
				</div>
			</div>
		<?php endif; ?>

		<div class="cert-frame" style="position: relative; overflow: hidden; <?= $isRevoked ? 'border-color: #fca5a5; opacity: 0.95;' : '' ?>">
			<?php if ($isRevoked): ?>
				<div style="position: absolute; top: 48%; left: 50%; transform: translate(-50%, -50%) rotate(-25deg); font-size: 5.5rem; font-weight: 900; color: rgba(239, 68, 68, 0.16); letter-spacing: 14px; pointer-events: none; text-transform: uppercase; border: 8px solid rgba(239, 68, 68, 0.22); padding: 12px 48px; border-radius: 16px; z-index: 5; user-select: none;">
					REVOKED
				</div>
			<?php endif; ?>

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
					<div class="cert-value" style="font-family: monospace; color: <?= $isRevoked ? '#dc2626' : '#0284c7' ?>; font-weight: 700;"><?= htmlspecialchars($certificate['certificate_code']) ?></div>
				</div>

				<div class="cert-seal">
					<?php if ($isRevoked): ?>
						<div class="seal-badge" style="background-color: #ef4444; border-color: #b91c1c; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);">REVOKED</div>
						<span style="font-size: 0.75rem; color: #dc2626; font-weight: 800;">Invalid Credential</span>
					<?php else: ?>
						<div class="seal-badge">VERIFIED</div>
						<span style="font-size: 0.75rem; color: #16a34a; font-weight: 700;">Funded by Ads</span>
					<?php endif; ?>
				</div>

				<div class="cert-detail" style="text-align: right;">
					<div class="cert-label">Issued By</div>
					<div class="cert-value">Adsity Education, Org.</div>
					<div class="cert-label" style="margin-top: 8px;">Accreditation</div>
					<div class="cert-value"><?= $isRevoked ? '<span style="color: #dc2626; font-weight: 700;">Revoked / Void</span>' : '100% Free &amp; Shareable' ?></div>
				</div>
			</div>
		</div>
	<?php else: ?>
		<div style="background-color: #ffffff; padding: 40px; border-radius: 12px; text-align: center; max-width: 500px;">
			<h2 style="color: #e11d48; margin-bottom: 12px;">Certificate Not Found</h2>
			<p style="color: #64748b; margin-bottom: 20px;">The requested certificate code could not be verified.</p>
			<a href="<?= htmlspecialchars($backUrl) ?>" class="btn-green" style="text-decoration: none;"><?= htmlspecialchars($backLabel) ?></a>
		</div>
	<?php endif; ?>

</body>
</html>
