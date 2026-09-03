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
	<link rel="stylesheet" href="submit_exam.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body class="student-dashboard-body">

	<!-- Navigation Header -->
	<header class="student-navbar">
		<div class="student-nav-left">
			<a href="../index.php" class="student-nav-logo" title="Adsity Home">
				<img src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo">
			</a>
			<span style="font-weight: 800; color: #0f172a; font-size: 1.05rem;">Final Project Assessment</span>
		</div>

		<div class="student-nav-right">
			<a href="dashboard.php" class="student-btn-dashboard">
				<img src="../assets/icons/arrow-left.svg" width="16" height="16" alt="Back" style="filter: brightness(0) saturate(100%) invert(42%) sepia(85%) saturate(2891%) hue-rotate(182deg) brightness(94%) contrast(87%);">
				Back to Dashboard
			</a>
			<a href="../logout.php" class="student-btn-logout" onclick="return confirm('Are you sure you want to log out?');">
				<img src="../assets/icons/arrow-left.svg" width="14" height="14" alt="Logout" style="filter: brightness(0) saturate(100%) invert(42%) sepia(13%) saturate(1072%) hue-rotate(182deg) brightness(94%) contrast(87%);">
				<span>Log Out</span>
			</a>
		</div>
	</header>

	<main class="exam-wrapper">
		<div class="exam-card">
			<a href="dashboard.php" class="back-home-link">
				<img src="../assets/icons/arrow-left.svg" width="16" height="16" alt="Back">
				Back to Learning Portal
			</a>

			<div style="margin-top: 20px; margin-bottom: 8px;">
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

				<h1 style="font-size: 1.85rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; letter-spacing: -0.01em;">
					<?= htmlspecialchars($course['title'] ?? 'Final Assessment') ?>
				</h1>
				<p style="color: #64748b; font-size: 0.95rem; line-height: 1.6;">
					Complete your instructor's deliverable requirements below to verify your practical skills and unlock your official Adsity Certificate.
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
				<div class="alert alert--error" style="margin-bottom: 24px;">
					<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
					<span><?= htmlspecialchars($message ?? 'An error occurred during submission.') ?></span>
				</div>
			<?php endif; ?>

			<form action="submit_exam.php" method="POST" enctype="multipart/form-data" class="auth-form">
				<input type="hidden" name="course_id" value="<?= htmlspecialchars($course['id']) ?>">

				<?php if (($course['assessment_type'] ?? '') === 'github_repo'): ?>
					<div class="form-group" style="margin-bottom: 20px;">
						<label for="github_url" class="form-label" style="font-weight: 700; color: #0f172a; margin-bottom: 8px; display: block;">GitHub Repository URL</label>
						<div class="form-input-wrapper">
							<input 
								type="url" 
								id="github_url" 
								name="github_url" 
								class="form-input" 
								placeholder="https://github.com/your-username/your-repository" 
								required
								style="width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.92rem;"
							>
						</div>
					</div>

				<?php elseif (($course['assessment_type'] ?? '') === 'file_upload'): ?>
					<div class="form-group" style="margin-bottom: 20px;">
						<label for="project_file" class="form-label" style="font-weight: 700; color: #0f172a; margin-bottom: 8px; display: block;">Upload Project File (ZIP, PDF, RAR, PNG, JPG)</label>
						<div class="form-input-wrapper">
							<input 
								type="file" 
								id="project_file" 
								name="project_file" 
								class="form-input" 
								accept=".zip,.rar,.tar,.gz,.pdf,.png,.jpg,.jpeg,.txt,.json" 
								required
								style="width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.92rem; background-color: #f8fafc;"
							>
						</div>
					</div>

				<?php else: ?>
					<div class="form-group" style="margin-bottom: 20px;">
						<label for="live_url" class="form-label" style="font-weight: 700; color: #0f172a; margin-bottom: 8px; display: block;">Live Project / Website Demo URL</label>
						<div class="form-input-wrapper">
							<input 
								type="url" 
								id="live_url" 
								name="live_url" 
								class="form-input" 
								placeholder="https://your-project.vercel.app or https://yourdomain.com" 
								required
								style="width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.92rem;"
							>
						</div>
					</div>
				<?php endif; ?>

				<div class="form-group" style="margin-bottom: 28px;">
					<label for="notes" class="form-label" style="font-weight: 700; color: #0f172a; margin-bottom: 8px; display: block;">Submission Notes &amp; Comments (Optional)</label>
					<div class="form-input-wrapper">
						<textarea 
							id="notes" 
							name="notes" 
							class="form-input" 
							rows="3" 
							placeholder="Mention any credentials, setup instructions, or notes for your instructor..." 
							style="width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.92rem; resize: vertical;"
						></textarea>
					</div>
				</div>

				<button type="submit" name="submit_assessment" class="btn-submit-assessment">
					<img src="../assets/icons/award.svg" width="18" height="18" alt="Award" style="filter: brightness(0);">
					Submit Project &amp; Claim Certificate
				</button>
			</form>
		</div>
	</main>

	<!-- Footer -->
	<footer class="student-footer" style="padding: 24px 32px; background-color: #ffffff;">
		<p>&copy; 2026 Adsity Education. All rights reserved.</p>
		<div class="student-footer-links">
			<a href="dashboard.php">My Dashboard</a>
			<a href="../courses.php">Explore Courses</a>
			<a href="../index.php">Home</a>
		</div>
	</footer>

</body>
</html>
