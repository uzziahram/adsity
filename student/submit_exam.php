<?php
require_once __DIR__ . '/submit_exam_function.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Submit Final Project - Adsity</title>
	<link rel="stylesheet" href="../style.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<style>
		.exam-wrapper {
			min-height: calc(100vh - 160px);
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 40px 20px;
			position: relative;
		}

		.exam-card {
			background-color: #ffffff;
			border: 1px solid #e2e8f0;
			border-radius: 20px;
			padding: 36px;
			max-width: 640px;
			width: 100%;
			box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
		}

		.exam-badge-type {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			background-color: #e0f2fe;
			color: #0284c7;
			font-size: 0.8rem;
			font-weight: 700;
			padding: 5px 14px;
			border-radius: 9999px;
			text-transform: uppercase;
			letter-spacing: 0.05em;
			margin-bottom: 12px;
		}

		.instructions-box {
			background-color: #f8fafc;
			border-left: 4px solid #27D662;
			border-radius: 8px;
			padding: 18px 20px;
			margin: 20px 0 24px 0;
		}

		.instructions-title {
			font-size: 0.85rem;
			font-weight: 800;
			color: #0f172a;
			text-transform: uppercase;
			letter-spacing: 0.05em;
			margin-bottom: 6px;
			display: flex;
			align-items: center;
			gap: 6px;
		}

		.instructions-content {
			font-size: 0.95rem;
			color: #334155;
			line-height: 1.6;
		}
	</style>
</head>

<body class="student-dashboard-body">

	<!-- Navigation Header -->
	<header class="navbar">
		<div class="nav-left">
			<a href="../index.php" class="logo">
				<span class="logo-icon">
					<img class="icon" src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo">
				</span>
			</a>
			<span style="font-weight: 700; color: #0f172a; font-size: 1.05rem;">Final Project Assessment</span>
		</div>

		<div class="nav-right">
			<a href="dashboard.php" class="teach-link">Back to Dashboard</a>
			<a href="../logout.php" class="btn-login" style="background-color: #475569;" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
		</div>
	</header>

	<main class="exam-wrapper">
		<div class="exam-card">
			<a href="dashboard.php" class="back-home-link">
				<img src="../assets/icons/arrow-left.svg" width="16" height="16" alt="Back">
				Back to Dashboard
			</a>

			<div style="margin-top: 16px; margin-bottom: 8px;">
				<?php if (($course['assessment_type'] ?? '') === 'github_repo'): ?>
					<span class="exam-badge-type">
						<img src="../assets/icons/github.svg" width="14" height="14" alt="GitHub">
						GitHub Repository Assessment
					</span>
				<?php elseif (($course['assessment_type'] ?? '') === 'file_upload'): ?>
					<span class="exam-badge-type" style="background-color: #dcfce7; color: #15803d;">
						<img src="../assets/icons/file-text.svg" width="14" height="14" alt="File">
						Project File Upload Assessment
					</span>
				<?php else: ?>
					<span class="exam-badge-type" style="background-color: #f3e8ff; color: #7e22ce;">
						<img src="../assets/icons/external-link.svg" width="14" height="14" alt="Live Demo">
						Live Project URL Assessment
					</span>
				<?php endif; ?>

				<h1 style="font-size: 1.85rem; font-weight: 800; color: #0f172a; margin-bottom: 6px;">
					<?= htmlspecialchars($course['title'] ?? 'Final Assessment') ?>
				</h1>
				<p style="color: #64748b; font-size: 0.95rem;">
					Complete your instructor's project requirements below to verify your skills and unlock your official Adsity Certificate.
				</p>
			</div>

			<!-- Instructor Instructions Box -->
			<div class="instructions-box">
				<div class="instructions-title">
					<img src="../assets/icons/graduation-cap.svg" width="16" height="16" alt="Instructor">
					Instructor's Deliverable Instructions
				</div>
				<div class="instructions-content">
					<?= nl2br(htmlspecialchars($course['assessment_instructions'] ?? 'Please complete all required components of your project and submit your deliverable below.')) ?>
				</div>
			</div>

			<!-- Status Alerts -->
			<?php if ($status === 'error'): ?>
				<div class="alert alert--error" style="margin-bottom: 20px;">
					<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
					<span><?= htmlspecialchars($message ?? 'An error occurred during submission.') ?></span>
				</div>
			<?php endif; ?>

			<form action="submit_exam.php" method="POST" enctype="multipart/form-data" class="auth-form">
				<input type="hidden" name="course_id" value="<?= htmlspecialchars($course['id']) ?>">

				<?php if (($course['assessment_type'] ?? '') === 'github_repo'): ?>
					<div class="form-group">
						<label for="github_url" class="form-label">GitHub Repository URL</label>
						<div class="form-input-wrapper">
							<input 
								type="url" 
								id="github_url" 
								name="github_url" 
								class="form-input" 
								placeholder="https://github.com/your-username/your-repository" 
								required
							>
						</div>
					</div>

				<?php elseif (($course['assessment_type'] ?? '') === 'file_upload'): ?>
					<div class="form-group">
						<label for="project_file" class="form-label">Upload Project File (ZIP, PDF, RAR, PNG, JPG)</label>
						<div class="form-input-wrapper">
							<input 
								type="file" 
								id="project_file" 
								name="project_file" 
								class="form-input" 
								accept=".zip,.rar,.tar,.gz,.pdf,.png,.jpg,.jpeg,.txt,.json" 
								style="padding: 10px;" 
								required
							>
						</div>
					</div>

				<?php else: ?>
					<div class="form-group">
						<label for="live_url" class="form-label">Live Project / Website Demo URL</label>
						<div class="form-input-wrapper">
							<input 
								type="url" 
								id="live_url" 
								name="live_url" 
								class="form-input" 
								placeholder="https://your-project.vercel.app or https://yourdomain.com" 
								required
							>
						</div>
					</div>
				<?php endif; ?>

				<div class="form-group">
					<label for="notes" class="form-label">Submission Notes &amp; Comments (Optional)</label>
					<div class="form-input-wrapper">
						<textarea 
							id="notes" 
							name="notes" 
							class="form-input" 
							rows="3" 
							placeholder="Mention any credentials, setup instructions, or notes for your instructor..." 
							style="resize: vertical;"
						></textarea>
					</div>
				</div>

				<button type="submit" name="submit_assessment" class="btn-auth-submit" style="background-color: var(--primary-green);">
					<img src="../assets/icons/award.svg" width="18" height="18" alt="Award" style="filter: brightness(0) invert(1);">
					Submit Project &amp; Claim Certificate
				</button>
			</form>
		</div>
	</main>

	<!-- Footer -->
	<footer class="footer">
		<div class="footer-bottom">
			<div class="footer-bottom-inner">
				<img src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo" class="footer-bottom-logo-img">
				<p class="footer-copyright">&copy; 2026 Adsity Education. All rights reserved.</p>
			</div>
		</div>
	</footer>

</body>
</html>
