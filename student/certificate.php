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
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Raleway:wght@400;600;700;800&display=swap" rel="stylesheet">
	<style>
		.cert-page {
			background-color: #0b1410;
			min-height: 100vh;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			padding: 40px 20px;
		}

		.cert-actions {
			display: flex;
			gap: 16px;
			margin-bottom: 24px;
		}

		.btn-back {
			background-color: #334155;
			color: #ffffff;
			padding: 10px 20px;
			border-radius: 8px;
			text-decoration: none;
			font-weight: 600;
			display: inline-flex;
			align-items: center;
			gap: 8px;
		}

		.btn-print {
			background-color: var(--primary-green);
			color: #ffffff;
			border: none;
			padding: 10px 24px;
			border-radius: 8px;
			font-weight: 700;
			cursor: pointer;
			display: inline-flex;
			align-items: center;
			gap: 8px;
			font-family: inherit;
		}

		/* Certificate Frame */
		.cert-frame {
			width: 100%;
			max-width: 850px;
			background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
			border: 12px solid #14221b;
			outline: 3px solid #27D662;
			border-radius: 8px;
			padding: 48px;
			position: relative;
			box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
			text-align: center;
		}

		.cert-logo {
			height: 48px;
			margin-bottom: 16px;
		}

		.cert-header {
			font-family: 'Cinzel', serif;
			font-size: 2.2rem;
			font-weight: 900;
			color: #0f172a;
			letter-spacing: 0.08em;
			text-transform: uppercase;
			margin-bottom: 8px;
		}

		.cert-subtitle {
			font-size: 1rem;
			color: #64748b;
			text-transform: uppercase;
			letter-spacing: 0.15em;
			margin-bottom: 24px;
		}

		.cert-presented {
			font-size: 0.95rem;
			color: #475569;
			font-style: italic;
			margin-bottom: 8px;
		}

		.cert-recipient {
			font-family: 'Raleway', sans-serif;
			font-size: 2.3rem;
			font-weight: 800;
			color: #0284c7;
			border-bottom: 2px solid #e2e8f0;
			display: inline-block;
			padding: 0 30px 8px 30px;
			margin-bottom: 20px;
		}

		.cert-text {
			font-size: 1.05rem;
			color: #334155;
			max-width: 600px;
			margin: 0 auto 30px auto;
			line-height: 1.6;
		}

		.cert-course {
			font-weight: 800;
			color: #0f172a;
		}

		.cert-footer {
			display: flex;
			justify-content: space-between;
			align-items: flex-end;
			margin-top: 40px;
			padding-top: 24px;
			border-top: 1px solid #e2e8f0;
		}

		.cert-detail {
			text-align: left;
		}

		.cert-label {
			font-size: 0.75rem;
			text-transform: uppercase;
			color: #94a3b8;
			letter-spacing: 0.05em;
		}

		.cert-value {
			font-size: 0.9rem;
			font-weight: 700;
			color: #0f172a;
		}

		.cert-seal {
			display: flex;
			flex-direction: column;
			align-items: center;
			gap: 6px;
		}

		.seal-badge {
			width: 60px;
			height: 60px;
			background-color: #27D662;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #ffffff;
			font-weight: 800;
			font-size: 0.75rem;
			border: 3px dashed #ffffff;
			box-shadow: 0 0 0 4px #27D662;
		}

		@media print {
			.cert-actions {
				display: none;
			}
			.cert-page {
				background-color: transparent;
				padding: 0;
			}
			.cert-frame {
				box-shadow: none;
				border-width: 6px;
			}
		}
	</style>
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
