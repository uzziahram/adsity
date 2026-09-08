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
			<a href="../logout.php" class="student-btn-logout">
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
			<?php if ($status === 'success'): ?>
				<div class="alert alert--success" style="margin-bottom: 24px;">
					<img src="../assets/icons/check-circle.svg" width="20" height="20" alt="Success">
					<span><?= htmlspecialchars($message ?? 'Deliverable submitted successfully!') ?></span>
				</div>
			<?php elseif ($status === 'error'): ?>
				<div class="alert alert--error" style="margin-bottom: 24px;">
					<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
					<span><?= htmlspecialchars($message ?? 'An error occurred during submission.') ?></span>
				</div>
			<?php endif; ?>

			<?php if (!$isCourseFinished): ?>
				<div class="locked-submission-card" style="background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 14px; padding: 40px 24px; text-align: center; margin-top: 24px;">
					<div style="width: 64px; height: 64px; border-radius: 50%; background-color: #fef2f2; border: 1px solid #fee2e2; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
						<img src="../assets/icons/lock.svg" width="30" height="30" alt="Locked" style="filter: brightness(0) saturate(100%) invert(32%) sepia(85%) saturate(2891%) hue-rotate(344deg) brightness(98%) contrast(92%);">
					</div>
					<h2 style="font-size: 1.4rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Final Project Submission is Locked</h2>
					<p style="color: #64748b; font-size: 0.98rem; max-width: 520px; margin: 0 auto 18px auto; line-height: 1.6;">
						You must finish watching all lessons in this course before you can submit your final project deliverable.
					</p>
					<div style="display: inline-flex; align-items: center; gap: 8px; background-color: #f1f5f9; padding: 8px 18px; border-radius: 9999px; font-size: 0.88rem; font-weight: 700; color: #334155; margin-bottom: 24px;">
						<span>Current Progress: <?= (int)$enrollment['progress_percent'] ?>% Completed</span>
					</div>
					<div>
						<a href="learn.php?course_id=<?= urlencode($course['id']) ?>" class="btn-submit-assessment" style="display: inline-flex; width: auto; text-decoration: none; padding: 12px 28px; background-color: #0284c7; color: #ffffff;">
							<img src="../assets/icons/play.svg" width="16" height="16" alt="Play" style="filter: brightness(0) invert(1);">
							<span>Resume Course Lessons</span>
						</a>
					</div>
				</div>

			<?php elseif ($latestSubmission && $latestSubmission['status'] === 'approved'): ?>
				<!-- Assessment Approved State -->
				<div class="submission-status-card" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 16px; padding: 32px 28px; margin-top: 24px;">
					<div style="display: flex; align-items: flex-start; gap: 18px;">
						<div style="width: 52px; height: 52px; border-radius: 50%; background-color: #dcfce7; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(34, 197, 94, 0.2);">
							<img src="../assets/icons/award.svg" width="28" height="28" alt="Passed">
						</div>
						<div style="flex: 1;">
							<span style="background-color: #dcfce7; color: #15803d; font-size: 0.78rem; font-weight: 800; padding: 4px 12px; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 8px;">
								✓ Assessment Passed &amp; Certified
							</span>
							<h2 style="font-size: 1.45rem; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">
								Congratulations! Your Project Has Been Approved
							</h2>
							<p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0 0 20px 0;">
								Your instructor has evaluated and approved your capstone project deliverable. Your official Adsity Certificate of Completion has been issued!
							</p>

							<!-- Instructor Feedback Box -->
							<div style="background-color: #ffffff; border: 1px solid #bbf7d0; border-left: 4px solid #16a34a; border-radius: 10px; padding: 16px 18px; margin-bottom: 24px;">
								<div style="font-size: 0.78rem; font-weight: 800; color: #166534; text-transform: uppercase; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
									<img src="../assets/icons/graduation-cap.svg" width="15" height="15" alt="Feedback">
									Instructor Feedback
								</div>
								<div style="font-size: 0.94rem; color: #1e293b; line-height: 1.6; white-space: pre-wrap;"><?= htmlspecialchars($latestSubmission['instructor_feedback'] ?: 'Great job! Your project satisfies all assessment criteria.') ?></div>
								<?php if (!empty($latestSubmission['reviewed_at'])): ?>
									<div style="font-size: 0.78rem; color: #64748b; margin-top: 8px;">
										Reviewed on <?= date('M d, Y h:i A', strtotime($latestSubmission['reviewed_at'])) ?>
									</div>
								<?php endif; ?>
							</div>

							<!-- Deliverable Summary -->
							<div style="font-size: 0.88rem; color: #64748b; margin-bottom: 24px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px;">
								<div style="margin-bottom: 6px;">
									<strong style="color: #0f172a;">Your Approved Deliverable:</strong>
									<?php if ($latestSubmission['submission_type'] === 'file_upload'): ?>
										<a href="../assets/submissions/<?= htmlspecialchars($latestSubmission['submission_value']) ?>" download style="color: #0284c7; font-weight: 700; text-decoration: none;">Download Project Archive ↗</a>
									<?php else: ?>
										<a href="<?= htmlspecialchars($latestSubmission['submission_value']) ?>" target="_blank" rel="noopener noreferrer" style="color: #0284c7; font-weight: 700; text-decoration: none;"><?= htmlspecialchars($latestSubmission['submission_value']) ?> ↗</a>
									<?php endif; ?>
								</div>
								<?php if (!empty($latestSubmission['notes'])): ?>
									<div style="font-size: 0.84rem; color: #475569; margin-top: 6px; padding-top: 6px; border-top: 1px dashed #e2e8f0;">
										<strong>Submission Notes:</strong> <?= htmlspecialchars($latestSubmission['notes']) ?>
									</div>
								<?php endif; ?>
							</div>

							<div style="display: flex; gap: 14px; flex-wrap: wrap;">
								<?php if (!empty($existingCertificate['certificate_code'])): ?>
									<a href="certificate.php?code=<?= urlencode($existingCertificate['certificate_code']) ?>" target="_blank" class="btn-submit-assessment" style="width: auto; text-decoration: none; padding: 12px 24px; font-size: 0.95rem;">
										<img src="../assets/icons/award.svg" width="18" height="18" alt="Certificate" style="filter: brightness(0);">
										<span>View Official Certificate (<?= htmlspecialchars($existingCertificate['certificate_code']) ?>)</span>
									</a>
								<?php endif; ?>
								<a href="dashboard.php" class="back-home-link" style="padding: 12px 20px; font-size: 0.9rem;">
									<span>Return to Learning Dashboard</span>
								</a>
							</div>
						</div>
					</div>
				</div>

			<?php elseif ($latestSubmission && $latestSubmission['status'] === 'pending'): ?>
				<!-- Assessment Under Review State -->
				<div class="submission-status-card" style="background-color: #fefce8; border: 1px solid #fef08a; border-radius: 16px; padding: 32px 28px; margin-top: 24px;">
					<div style="display: flex; align-items: flex-start; gap: 18px;">
						<div style="width: 52px; height: 52px; border-radius: 50%; background-color: #fef9c3; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(234, 179, 8, 0.15);">
							<img src="../assets/icons/clock.svg" width="28" height="28" alt="Pending">
						</div>
						<div style="flex: 1;">
							<span style="background-color: #fef08a; color: #854d0e; font-size: 0.78rem; font-weight: 800; padding: 4px 12px; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 8px;">
								⏳ Assessment Under Instructor Review
							</span>
							<h2 style="font-size: 1.45rem; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">
								Your Deliverable Has Been Received
							</h2>
							<p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin: 0 0 20px 0;">
								Your project deliverable has been recorded and added to your instructor's grading queue. Once your instructor evaluates your work, your certificate will be issued or revision guidance will appear right here.
							</p>

							<!-- Current Submission Details -->
							<div style="background-color: #ffffff; border: 1px solid #fef08a; border-radius: 10px; padding: 16px 18px; margin-bottom: 24px;">
								<div style="font-size: 0.9rem; color: #334155; margin-bottom: 8px;">
									<strong style="color: #0f172a;">Submitted Deliverable:</strong>
									<?php if ($latestSubmission['submission_type'] === 'file_upload'): ?>
										<a href="../assets/submissions/<?= htmlspecialchars($latestSubmission['submission_value']) ?>" download style="color: #0284c7; font-weight: 700; text-decoration: none;">Download Submitted Archive ↗</a>
									<?php else: ?>
										<a href="<?= htmlspecialchars($latestSubmission['submission_value']) ?>" target="_blank" rel="noopener noreferrer" style="color: #0284c7; font-weight: 700; text-decoration: none;"><?= htmlspecialchars($latestSubmission['submission_value']) ?> ↗</a>
									<?php endif; ?>
								</div>

								<?php if (!empty($latestSubmission['notes'])): ?>
									<div style="font-size: 0.86rem; color: #475569; margin-top: 8px; border-top: 1px dashed #e2e8f0; padding-top: 8px;">
										<strong>Your Notes:</strong> <?= htmlspecialchars($latestSubmission['notes']) ?>
									</div>
								<?php endif; ?>

								<div style="font-size: 0.78rem; color: #94a3b8; margin-top: 10px;">
									Submitted on <?= date('M d, Y h:i A', strtotime($latestSubmission['submitted_at'])) ?>
								</div>
							</div>

							<div>
								<a href="dashboard.php" class="back-home-link" style="padding: 12px 20px; font-size: 0.9rem;">
									<span>Return to Learning Dashboard</span>
								</a>
							</div>
						</div>
					</div>
				</div>

			<?php else: ?>
				<!-- Revision Needed or Initial Submission Form -->

				<?php if ($latestSubmission && $latestSubmission['status'] === 'revision_needed'): ?>
					<!-- Revision Requested Alert Box -->
					<div style="background-color: #fff7ed; border: 1px solid #fed7aa; border-radius: 14px; padding: 24px 20px; margin-top: 24px; margin-bottom: 28px;">
						<div style="display: flex; align-items: flex-start; gap: 16px;">
							<div style="width: 48px; height: 48px; border-radius: 50%; background-color: #ffedd5; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(234, 88, 12, 0.15);">
								<img src="../assets/icons/alert-circle.svg" width="24" height="24" alt="Revision" style="filter: brightness(0) saturate(100%) invert(43%) sepia(97%) saturate(1450%) hue-rotate(357deg) brightness(96%) contrast(92%);">
							</div>
							<div style="flex: 1;">
								<span style="background-color: #ffedd5; color: #c2410c; font-size: 0.78rem; font-weight: 800; padding: 4px 12px; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 6px;">
									↺ Revision Requested by Instructor
								</span>
								<h3 style="font-size: 1.25rem; font-weight: 800; color: #9a3412; margin: 0 0 6px 0;">
									Changes Requested Before Certificate Can Be Issued
								</h3>
								<p style="color: #7c2d12; font-size: 0.92rem; line-height: 1.5; margin: 0 0 16px 0;">
									Your instructor reviewed your project and requested revisions. Please read the comments below, update your work, and resubmit using the form.
								</p>

								<!-- Instructor Feedback Box -->
								<div style="background-color: #ffffff; border: 1px solid #fed7aa; border-left: 4px solid #ea580c; border-radius: 8px; padding: 14px 16px;">
									<div style="font-size: 0.78rem; font-weight: 800; color: #9a3412; text-transform: uppercase; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
										<img src="../assets/icons/graduation-cap.svg" width="14" height="14" alt="Instructor">
										Instructor Revision Feedback
									</div>
									<div style="font-size: 0.92rem; color: #1e293b; line-height: 1.6; white-space: pre-wrap;"><?= htmlspecialchars($latestSubmission['instructor_feedback'] ?? 'Please review your project deliverables against the course requirements and resubmit.') ?></div>
									<?php if (!empty($latestSubmission['reviewed_at'])): ?>
										<div style="font-size: 0.78rem; color: #94a3b8; margin-top: 8px;">
											Feedback given on <?= date('M d, Y h:i A', strtotime($latestSubmission['reviewed_at'])) ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<form action="submit_exam.php" method="POST" enctype="multipart/form-data" class="auth-form" style="margin-top: 24px;">
					<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
					<input type="hidden" name="course_id" value="<?= htmlspecialchars($course['id']) ?>">

					<?php if (($course['assessment_type'] ?? '') === 'github_repo'): ?>
						<div class="form-group" style="margin-bottom: 20px;">
							<label for="github_url" class="form-label" style="font-weight: 700; color: #0f172a; margin-bottom: 8px; display: block;">
								GitHub Repository URL <?= ($latestSubmission && $latestSubmission['status'] === 'revision_needed') ? '(Updated Link)' : '' ?>
							</label>
							<div class="form-input-wrapper">
								<input 
									type="url" 
									id="github_url" 
									name="github_url" 
									class="form-input" 
									placeholder="https://github.com/your-username/your-repository" 
									value="<?= ($latestSubmission && $latestSubmission['status'] === 'revision_needed' && $latestSubmission['submission_type'] === 'github_repo') ? htmlspecialchars($latestSubmission['submission_value']) : '' ?>"
									required
									style="width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.92rem;"
								>
							</div>
						</div>

					<?php elseif (($course['assessment_type'] ?? '') === 'file_upload'): ?>
						<div class="form-group" style="margin-bottom: 20px;">
							<label for="project_file" class="form-label" style="font-weight: 700; color: #0f172a; margin-bottom: 8px; display: block;">
								Upload Project File <?= ($latestSubmission && $latestSubmission['status'] === 'revision_needed') ? '(New Archive Revision)' : '(ZIP, PDF, RAR, PNG, JPG)' ?>
							</label>
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
							<label for="live_url" class="form-label" style="font-weight: 700; color: #0f172a; margin-bottom: 8px; display: block;">
								Live Project / Website Demo URL <?= ($latestSubmission && $latestSubmission['status'] === 'revision_needed') ? '(Updated URL)' : '' ?>
							</label>
							<div class="form-input-wrapper">
								<input 
									type="url" 
									id="live_url" 
									name="live_url" 
									class="form-input" 
									placeholder="https://your-project.vercel.app or https://yourdomain.com" 
									value="<?= ($latestSubmission && $latestSubmission['status'] === 'revision_needed' && $latestSubmission['submission_type'] === 'live_url') ? htmlspecialchars($latestSubmission['submission_value']) : '' ?>"
									required
									style="width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.92rem;"
								>
							</div>
						</div>
					<?php endif; ?>

					<div class="form-group" style="margin-bottom: 28px;">
						<label for="notes" class="form-label" style="font-weight: 700; color: #0f172a; margin-bottom: 8px; display: block;">
							Submission Notes <?= ($latestSubmission && $latestSubmission['status'] === 'revision_needed') ? '(Explanation of Changes Made)' : '(Optional)' ?>
						</label>
						<div class="form-input-wrapper">
							<textarea 
								id="notes" 
								name="notes" 
								class="form-input" 
								rows="3" 
								placeholder="Mention what you revised, credentials, setup instructions, or notes for your instructor..." 
								style="width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 10px; font-family: inherit; font-size: 0.92rem; resize: vertical;"
							></textarea>
						</div>
					</div>

					<button type="submit" name="submit_assessment" class="btn-submit-assessment">
						<img src="../assets/icons/award.svg" width="18" height="18" alt="Award" style="filter: brightness(0);">
						<span><?= ($latestSubmission && $latestSubmission['status'] === 'revision_needed') ? 'Resubmit Project for Review' : 'Submit Project for Review' ?></span>
					</button>
				</form>
			<?php endif; ?>
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

	<script src="../assets/js/adsity-ui.js"></script>
	<script src="../assets/js/logout_modal.js"></script>
</body>
</html>
