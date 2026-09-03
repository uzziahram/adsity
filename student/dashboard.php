<?php
require_once __DIR__ . '/dashboard_function.php';
$initials = strtoupper(substr($student['full_name'] ?? 'S', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Student Dashboard - Adsity</title>
	<link rel="stylesheet" href="../style.css">
	<link rel="stylesheet" href="studentdashboard.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body class="student-dashboard-body">

	<!-- Top Navigation Header -->
	<header class="student-navbar">
		<div class="student-nav-left">
			<a href="../index.php" class="student-nav-logo" title="Adsity Home">
				<img src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo">
			</a>
			<a href="../courses.php" class="student-nav-explore">
				<img src="../assets/icons/book-open.svg" width="16" height="16" alt="Explore" style="opacity: 0.8;">
				Explore Courses
			</a>
			<form action="../courses.php" method="GET" class="student-search-form">
				<button type="submit" class="student-search-btn" title="Search">&#128269;</button>
				<input type="text" name="search" class="student-search-input" placeholder="Search topics (e.g. Python, Web, Cloud)...">
			</form>
		</div>

		<div class="student-nav-right">
			<a href="../teach.php" class="student-nav-teach">Teach on Adsity</a>
			<div class="student-user-pill">
				<div class="student-user-pill-avatar"><?= $initials ?></div>
				<span><?= htmlspecialchars($student['full_name'] ?? 'Student') ?></span>
			</div>
			<a href="../logout.php" class="student-btn-logout" onclick="return confirm('Are you sure you want to log out?');">
				<img src="../assets/icons/arrow-left.svg" width="14" height="14" alt="Logout" style="filter: brightness(0) saturate(100%) invert(42%) sepia(13%) saturate(1072%) hue-rotate(182deg) brightness(94%) contrast(87%);">
				<span>Log Out</span>
			</a>
		</div>
	</header>

	<!-- App Layout: Left Sidebar + Right Fluid Content -->
	<div class="student-dashboard-layout">

		<!-- ==========================================
		     LEFT SIDEBAR NAVBAR
		     ========================================== -->
		<aside class="student-left-nav">
			<div>
				<!-- Student Profile Info Card -->
				<div class="nav-profile-card">
					<div class="nav-profile-avatar"><?= $initials ?></div>
					<div class="nav-profile-info">
						<div class="nav-profile-name" title="<?= htmlspecialchars($student['full_name'] ?? 'Student') ?>">
							<?= htmlspecialchars($student['full_name'] ?? 'Student') ?>
						</div>
						<div class="nav-profile-status">
							<span class="nav-status-dot"></span>
							Active Learner
						</div>
					</div>
				</div>

				<!-- Navigation Tabs -->
				<nav class="nav-menu-group">
					<div class="nav-group-label">Learning Portal</div>

					<button type="button" class="nav-tab-btn active" data-tab="overview" onclick="switchTab('overview')">
						<div class="nav-tab-btn-left">
							<img src="../assets/icons/layout.svg" class="nav-tab-icon" alt="Overview">
							<span>Overview</span>
						</div>
					</button>

					<button type="button" class="nav-tab-btn" data-tab="in-progress" onclick="switchTab('in-progress')">
						<div class="nav-tab-btn-left">
							<img src="../assets/icons/clock.svg" class="nav-tab-icon" alt="In Progress">
							<span>In Progress</span>
						</div>
						<span class="nav-badge-pill"><?= $inProgressCount ?></span>
					</button>

					<button type="button" class="nav-tab-btn" data-tab="completed" onclick="switchTab('completed')">
						<div class="nav-tab-btn-left">
							<img src="../assets/icons/check-circle.svg" class="nav-tab-icon" alt="Completed">
							<span>Completed</span>
						</div>
						<span class="nav-badge-pill"><?= $completedCount ?></span>
					</button>

					<button type="button" class="nav-tab-btn" data-tab="certificates" onclick="switchTab('certificates')">
						<div class="nav-tab-btn-left">
							<img src="../assets/icons/award.svg" class="nav-tab-icon" alt="Certificates">
							<span>My Certificates</span>
						</div>
						<span class="nav-badge-pill"><?= $certCount ?></span>
					</button>

					<div class="nav-group-label" style="margin-top: 18px;">Discovery</div>

					<a href="../courses.php" class="nav-tab-btn">
						<div class="nav-tab-btn-left">
							<img src="../assets/icons/book-open.svg" class="nav-tab-icon" alt="Browse">
							<span>Browse Catalog</span>
						</div>
					</a>

					<a href="../teach.php" class="nav-tab-btn">
						<div class="nav-tab-btn-left">
							<img src="../assets/icons/graduation-cap.svg" class="nav-tab-icon" alt="Teach">
							<span>Teach on Adsity</span>
						</div>
					</a>
				</nav>
			</div>

			<!-- Sidebar Footer -->
			<div class="nav-sidebar-footer">
				<a href="../logout.php" class="btn-sidebar-logout" onclick="return confirm('Are you sure you want to log out?');">
					<img src="../assets/icons/arrow-left.svg" width="16" height="16" alt="Logout" style="filter: brightness(0) saturate(100%) invert(32%) sepia(85%) saturate(2891%) hue-rotate(344deg) brightness(98%) contrast(92%);">
					<span>Log Out</span>
				</a>
			</div>
		</aside>

		<!-- ==========================================
		     RIGHT MAIN FLUID CONTENT
		     ========================================== -->
		<main class="student-content-area">

			<!-- Status Alerts -->
			<?php if ($status === 'success'): ?>
				<div class="alert alert--success">
					<img src="../assets/icons/check-circle.svg" width="20" height="20" alt="Success">
					<span><?= htmlspecialchars($message ?? 'Welcome to your student portal!') ?></span>
				</div>
			<?php elseif ($status === 'error'): ?>
				<div class="alert alert--error">
					<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
					<span><?= htmlspecialchars($message ?? 'An error occurred.') ?></span>
				</div>
			<?php endif; ?>

			<!-- ==========================================================
			     TAB 1: OVERVIEW PANEL
			     ========================================================== -->
			<div id="tab-overview" class="tab-panel active">
				<!-- Welcome Hero Banner -->
				<section class="student-welcome">
					<div class="welcome-content">
						<span class="welcome-badge">
							<img src="../assets/icons/shield-check.svg" width="14" height="14" alt="Verified" style="filter: brightness(0) saturate(100%) invert(69%) sepia(57%) saturate(548%) hue-rotate(88deg) brightness(97%) contrast(92%);">
							100% Free Ad-Funded Education
						</span>
						<h1 class="welcome-title">Welcome back, <?= htmlspecialchars($student['full_name'] ?? 'Student') ?> 👋</h1>
						<p class="welcome-subtitle">
							Build real-world programming and technology skills, complete practical projects, and earn accredited shareable certificates funded by brief ad breaks.
						</p>
					</div>
					<div class="welcome-actions">
						<a href="../courses.php" class="btn-welcome-explore">
							<img src="../assets/icons/book-open.svg" width="16" height="16" alt="Courses" style="filter: brightness(0);">
							Explore Courses
						</a>
					</div>
				</section>

				<!-- KPI Metric Cards -->
				<div class="stats-grid">
					<div class="stat-card stat-card--blue" onclick="switchTab('in-progress')">
						<div class="stat-icon-box stat-icon-box--blue">
							<img src="../assets/icons/clock.svg" width="24" height="24" alt="In Progress">
						</div>
						<div>
							<div class="stat-number"><?= $inProgressCount ?></div>
							<div class="stat-label">In Progress Courses</div>
						</div>
					</div>

					<div class="stat-card stat-card--green" onclick="switchTab('completed')">
						<div class="stat-icon-box stat-icon-box--green">
							<img src="../assets/icons/check-circle.svg" width="24" height="24" alt="Completed">
						</div>
						<div>
							<div class="stat-number"><?= $completedCount ?></div>
							<div class="stat-label">Completed Courses</div>
						</div>
					</div>

					<div class="stat-card stat-card--amber" onclick="switchTab('certificates')">
						<div class="stat-icon-box stat-icon-box--amber">
							<img src="../assets/icons/award.svg" width="24" height="24" alt="Certificates">
						</div>
						<div>
							<div class="stat-number"><?= $certCount ?></div>
							<div class="stat-label">Verified Certificates</div>
						</div>
					</div>
				</div>

				<!-- Quick Resume Section -->
				<div class="section-header">
					<h2 class="section-title">
						<img src="../assets/icons/play.svg" width="22" height="22" alt="Play">
						Continue Learning
					</h2>
					<?php if (!empty($inProgress)): ?>
						<button type="button" onclick="switchTab('in-progress')" class="btn-view-all-link">
							<span>View All (<?= $inProgressCount ?>)</span>
							&rarr;
						</button>
					<?php endif; ?>
				</div>

				<?php if (!empty($inProgress)): ?>
					<div class="courses-grid">
						<?php foreach (array_slice($inProgress, 0, 3) as $course): ?>
							<article class="course-card">
								<div class="course-thumb-wrapper">
									<img 
										src="../assets/adsity_assets/<?= htmlspecialchars($course['thumbnail'] ?: 'Web_Development_Basics.png') ?>" 
										alt="<?= htmlspecialchars($course['title']) ?>" 
										class="course-thumb-img"
										onerror="this.src='../assets/adsity_assets/Web_Development_Basics.png'"
									>
									<span class="course-category-tag"><?= htmlspecialchars($course['category'] ?? 'Course') ?></span>
								</div>

								<div class="course-card-content">
									<h3 class="course-card-title"><?= htmlspecialchars($course['title']) ?></h3>
									<p class="course-card-desc"><?= htmlspecialchars($course['description'] ?? 'Comprehensive practical learning course.') ?></p>

									<div class="progress-container">
										<div class="progress-header">
											<span class="progress-text">
												<img src="../assets/icons/clock.svg" width="14" height="14" alt="Lessons">
												<?= (int)($course['completed_lessons'] ?? 0) ?> of <?= (int)($course['total_lessons'] ?? 10) ?> Lessons
											</span>
											<span class="progress-percent"><?= (int)($course['progress_percent'] ?? 0) ?>%</span>
										</div>
										<div class="progress-bar-bg">
											<div class="progress-bar-fill" style="width: <?= (int)($course['progress_percent'] ?? 0) ?>%;"></div>
										</div>
									</div>

									<div class="card-actions-row">
										<a href="../courses.php" class="btn-continue-course">
											<img src="../assets/icons/play.svg" width="16" height="16" alt="Play" style="filter: brightness(0);">
											Learn
										</a>
										<a href="submit_exam.php?course_id=<?= urlencode($course['course_id']) ?>" class="btn-submit-project">
											<img src="../assets/icons/award.svg" width="16" height="16" alt="Exam" style="filter: brightness(0) invert(1);">
											Submit Project
										</a>
									</div>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				<?php else: ?>
					<div class="empty-box">
						<div class="empty-icon-circle">
							<img src="../assets/icons/book-open.svg" width="36" height="36" alt="Book" style="opacity: 0.45;">
						</div>
						<div class="empty-box-title">No Courses in Progress</div>
						<p class="empty-box-desc">Explore our full catalog of free ad-supported courses to start building in-demand skills today.</p>
						<a href="../courses.php" class="btn-empty-action">
							<img src="../assets/icons/book-open.svg" width="16" height="16" alt="Explore" style="filter: brightness(0);">
							Explore Course Catalog
						</a>
					</div>
				<?php endif; ?>
			</div>

			<!-- ==========================================================
			     TAB 2: IN-PROGRESS COURSES PANEL
			     ========================================================== -->
			<div id="tab-in-progress" class="tab-panel">
				<div class="section-header">
					<h2 class="section-title">
						<img src="../assets/icons/clock.svg" width="22" height="22" alt="In Progress">
						In Progress Courses
					</h2>
					<span class="section-badge-count"><?= $inProgressCount ?> Active</span>
				</div>

				<?php if (!empty($inProgress)): ?>
					<div class="courses-grid">
						<?php foreach ($inProgress as $course): ?>
							<article class="course-card">
								<div class="course-thumb-wrapper">
									<img 
										src="../assets/adsity_assets/<?= htmlspecialchars($course['thumbnail'] ?: 'Web_Development_Basics.png') ?>" 
										alt="<?= htmlspecialchars($course['title']) ?>" 
										class="course-thumb-img"
										onerror="this.src='../assets/adsity_assets/Web_Development_Basics.png'"
									>
									<span class="course-category-tag"><?= htmlspecialchars($course['category'] ?? 'Course') ?></span>
								</div>

								<div class="course-card-content">
									<h3 class="course-card-title"><?= htmlspecialchars($course['title']) ?></h3>
									<p class="course-card-desc"><?= htmlspecialchars($course['description'] ?? 'Comprehensive practical learning course.') ?></p>

									<div class="progress-container">
										<div class="progress-header">
											<span class="progress-text">
												<img src="../assets/icons/clock.svg" width="14" height="14" alt="Lessons">
												<?= (int)($course['completed_lessons'] ?? 0) ?> of <?= (int)($course['total_lessons'] ?? 10) ?> Lessons
											</span>
											<span class="progress-percent"><?= (int)($course['progress_percent'] ?? 0) ?>%</span>
										</div>
										<div class="progress-bar-bg">
											<div class="progress-bar-fill" style="width: <?= (int)($course['progress_percent'] ?? 0) ?>%;"></div>
										</div>
									</div>

									<div class="card-actions-row">
										<a href="../courses.php" class="btn-continue-course">
											<img src="../assets/icons/play.svg" width="16" height="16" alt="Play" style="filter: brightness(0);">
											Learn
										</a>
										<a href="submit_exam.php?course_id=<?= urlencode($course['course_id']) ?>" class="btn-submit-project">
											<img src="../assets/icons/award.svg" width="16" height="16" alt="Exam" style="filter: brightness(0) invert(1);">
											Submit Project
										</a>
									</div>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				<?php else: ?>
					<div class="empty-box">
						<div class="empty-icon-circle">
							<img src="../assets/icons/clock.svg" width="36" height="36" alt="Clock" style="opacity: 0.45;">
						</div>
						<div class="empty-box-title">No Active Enrollments</div>
						<p class="empty-box-desc">You are not currently enrolled in any active courses. Enroll in any course for free to begin learning!</p>
						<a href="../courses.php" class="btn-empty-action">
							<img src="../assets/icons/book-open.svg" width="16" height="16" alt="Browse" style="filter: brightness(0);">
							Browse Free Courses
						</a>
					</div>
				<?php endif; ?>
			</div>

			<!-- ==========================================================
			     TAB 3: COMPLETED COURSES PANEL
			     ========================================================== -->
			<div id="tab-completed" class="tab-panel">
				<div class="section-header">
					<h2 class="section-title">
						<img src="../assets/icons/check-circle.svg" width="22" height="22" alt="Completed">
						Completed Courses
					</h2>
					<span class="section-badge-count"><?= $completedCount ?> Finished</span>
				</div>

				<?php if (!empty($completedCourses)): ?>
					<div class="courses-grid">
						<?php foreach ($completedCourses as $completed): ?>
							<article class="course-card">
								<div class="course-thumb-wrapper">
									<img 
										src="../assets/adsity_assets/<?= htmlspecialchars($completed['thumbnail'] ?: 'Web_Development_Basics.png') ?>" 
										alt="<?= htmlspecialchars($completed['title']) ?>" 
										class="course-thumb-img"
										onerror="this.src='../assets/adsity_assets/Web_Development_Basics.png'"
									>
									<span class="course-category-tag course-category-tag--completed">Completed ✓</span>
								</div>

								<div class="course-card-content">
									<h3 class="course-card-title"><?= htmlspecialchars($completed['title']) ?></h3>
									<p class="course-card-desc"><?= htmlspecialchars($completed['description'] ?? 'Course completed successfully.') ?></p>

									<div class="course-completed-date">
										<img src="../assets/icons/check-circle.svg" width="16" height="16" alt="Completed">
										Completed on <?= date('M d, Y', strtotime($completed['completed_at'] ?? 'now')) ?>
									</div>

									<?php if (!empty($completed['certificate_code'])): ?>
										<a href="certificate.php?code=<?= urlencode($completed['certificate_code']) ?>" class="btn-view-cert">
											<img src="../assets/icons/award.svg" width="16" height="16" alt="Certificate" style="filter: brightness(0);">
											View Official Certificate
										</a>
									<?php else: ?>
										<a href="submit_exam.php?course_id=<?= urlencode($completed['course_id']) ?>" class="btn-submit-project">
											<img src="../assets/icons/award.svg" width="16" height="16" alt="Certificate" style="filter: brightness(0) invert(1);">
											Claim Your Certificate
										</a>
									<?php endif; ?>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				<?php else: ?>
					<div class="empty-box">
						<div class="empty-icon-circle">
							<img src="../assets/icons/check-circle.svg" width="36" height="36" alt="Check" style="opacity: 0.45;">
						</div>
						<div class="empty-box-title">No Completed Courses Yet</div>
						<p class="empty-box-desc">Complete all video lessons and submit your practical project deliverables to earn official verified credentials.</p>
					</div>
				<?php endif; ?>
			</div>

			<!-- ==========================================================
			     TAB 4: MY CERTIFICATES PANEL
			     ========================================================== -->
			<div id="tab-certificates" class="tab-panel">
				<div class="section-header">
					<h2 class="section-title">
						<img src="../assets/icons/award.svg" width="22" height="22" alt="Certificates">
						My Verified Certificates
					</h2>
					<span class="section-badge-count"><?= $certCount ?> Issued</span>
				</div>

				<?php if (!empty($certificates)): ?>
					<div class="courses-grid">
						<?php foreach ($certificates as $cert): ?>
							<article class="cert-card">
								<span class="cert-card-badge-verified">
									<img src="../assets/icons/award.svg" width="14" height="14" alt="Verified">
									Official Adsity Credential
								</span>

								<h3 class="cert-card-title"><?= htmlspecialchars($cert['course_title'] ?? 'Verified Course') ?></h3>

								<div class="cert-card-code-box">
									<span class="cert-card-code"><?= htmlspecialchars($cert['certificate_code']) ?></span>
									<button type="button" class="btn-copy-code" onclick="copyCertCode('<?= htmlspecialchars($cert['certificate_code']) ?>', this)">
										Copy Code
									</button>
								</div>

								<div class="cert-card-date">
									<img src="../assets/icons/clock.svg" width="14" height="14" alt="Issued">
									Issued on <?= date('F d, Y', strtotime($cert['issued_at'] ?? 'now')) ?>
								</div>

								<div class="cert-card-actions">
									<a href="certificate.php?code=<?= urlencode($cert['certificate_code']) ?>" class="btn-cert-view">
										<img src="../assets/icons/eye.svg" width="16" height="16" alt="View" style="filter: brightness(0);">
										View Certificate
									</a>
									<a href="certificate.php?code=<?= urlencode($cert['certificate_code']) ?>" class="btn-cert-download" title="Print / Download PDF">
										<img src="../assets/icons/download.svg" width="16" height="16" alt="Download" style="filter: brightness(0) invert(1);">
									</a>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				<?php else: ?>
					<div class="empty-box">
						<div class="empty-icon-circle">
							<img src="../assets/icons/award.svg" width="36" height="36" alt="Award" style="opacity: 0.45;">
						</div>
						<div class="empty-box-title">No Certificates Earned Yet</div>
						<p class="empty-box-desc">Submit your final assessment project deliverables for completed courses to unlock verifiable certificates.</p>
					</div>
				<?php endif; ?>
			</div>

			<!-- Compact Footer -->
			<footer class="student-footer">
				<p>&copy; 2026 Adsity Student Learning Studio. All rights reserved.</p>
				<div class="student-footer-links">
					<a href="../courses.php">Course Catalog</a>
					<a href="../teach.php">Instructor Studio</a>
					<a href="../index.php">Home</a>
				</div>
			</footer>

		</main>
	</div>

	<!-- Toast Notification Container -->
	<div id="toast-notice" class="toast-notice">
		<img src="../assets/icons/check-circle.svg" width="18" height="18" alt="Check" style="filter: brightness(0) saturate(100%) invert(69%) sepia(57%) saturate(548%) hue-rotate(88deg) brightness(97%) contrast(92%);">
		<span id="toast-text">Certificate code copied!</span>
	</div>

	<!-- Interactive Tab Switching & Clipboard Scripts -->
	<script>
		function switchTab(tabId) {
			// Update Tab Buttons
			document.querySelectorAll('.nav-tab-btn').forEach(btn => {
				if (btn.dataset.tab === tabId) {
					btn.classList.add('active');
				} else {
					btn.classList.remove('active');
				}
			});

			// Update Tab Panels
			document.querySelectorAll('.tab-panel').forEach(panel => {
				if (panel.id === 'tab-' + tabId) {
					panel.classList.add('active');
				} else {
					panel.classList.remove('active');
				}
			});

			// Update URL hash without jumping
			if (history.replaceState) {
				history.replaceState(null, null, '#' + tabId);
			}
			window.scrollTo({ top: 0, behavior: 'smooth' });
		}

		// Copy certificate code to clipboard
		function copyCertCode(code, btnElement) {
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(code).then(() => {
					showToast('Certificate code copied to clipboard!');
					if (btnElement) {
						const originalText = btnElement.innerText;
						btnElement.innerText = 'Copied! ✓';
						setTimeout(() => { btnElement.innerText = originalText; }, 2000);
					}
				});
			} else {
				// Fallback
				const tempInput = document.createElement('input');
				tempInput.value = code;
				document.body.appendChild(tempInput);
				tempInput.select();
				document.execCommand('copy');
				document.body.removeChild(tempInput);
				showToast('Certificate code copied!');
			}
		}

		function showToast(message) {
			const toast = document.getElementById('toast-notice');
			const toastText = document.getElementById('toast-text');
			if (toast && toastText) {
				toastText.innerText = message;
				toast.classList.add('show');
				setTimeout(() => {
					toast.classList.remove('show');
				}, 2600);
			}
		}

		// Check hash on page load (e.g. #in-progress, #completed, #certificates)
		window.addEventListener('DOMContentLoaded', () => {
			const hash = window.location.hash.replace('#', '');
			if (hash && ['overview', 'in-progress', 'completed', 'certificates'].includes(hash)) {
				switchTab(hash);
			}
		});
	</script>

</body>
</html>
