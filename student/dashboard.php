<?php
require_once __DIR__ . '/dashboard_function.php';
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

	<!-- Navigation Header -->
	<header class="navbar">
		<div class="nav-left">
			<a href="../index.php" class="logo">
				<span class="logo-icon">
					<img class="icon" src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo">
				</span>
			</a>
			<a href="../courses.php" class="explore-btn">Explore Courses</a>
			<form action="../courses.php" method="GET" class="search-bar">
				<button type="submit" style="background: none; border: none; cursor: pointer; padding: 0;" class="search-icon">&#128269;</button>
				<input type="text" name="search" placeholder="Search for Topics">
			</form>
		</div>

		<div class="nav-right">
			<a href="../teach.php" class="teach-link">Teach on Adsity</a>
			<span style="font-weight: 700; color: var(--primary-green); font-size: 0.9rem;">
				👤 <?= htmlspecialchars($student['full_name'] ?? 'Student') ?>
			</span>
			<a href="../logout.php" class="btn-login" style="background-color: #475569;" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
		</div>
	</header>

	<main class="student-container">

		<!-- Welcome Banner -->
		<section class="student-welcome">
			<span class="welcome-badge">
				<img src="../assets/icons/shield-check.svg" width="14" height="14" alt="Verified" style="filter: brightness(0) saturate(100%) invert(69%) sepia(57%) saturate(548%) hue-rotate(88deg) brightness(97%) contrast(92%);">
				100% Free Ad-Supported Learning
			</span>
			<h1 class="welcome-title">Welcome back, <?= htmlspecialchars($student['full_name'] ?? 'Student') ?> 👋</h1>
			<p class="welcome-subtitle">
				Continue your learning journey, master in-demand technology skills, and earn accredited certificates funded by brief ad breaks.
			</p>
		</section>

		<!-- Status Alerts -->
		<?php if ($status === 'success'): ?>
			<div class="alert alert--success" style="margin-bottom: 30px;">
				<img src="../assets/icons/check-circle.svg" width="20" height="20" alt="Success">
				<span><?= htmlspecialchars($message ?? 'Welcome to your student portal!') ?></span>
			</div>
		<?php elseif ($status === 'error'): ?>
			<div class="alert alert--error" style="margin-bottom: 30px;">
				<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
				<span><?= htmlspecialchars($message ?? 'An error occurred.') ?></span>
			</div>
		<?php endif; ?>

		<!-- KPI Metric Cards -->
		<div class="stats-grid">
			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--blue">
					<img src="../assets/icons/clock.svg" width="24" height="24" alt="In Progress">
				</div>
				<div>
					<div class="stat-number"><?= $inProgressCount ?></div>
					<div class="stat-label">In Progress Courses</div>
				</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--green">
					<img src="../assets/icons/check-circle.svg" width="24" height="24" alt="Completed">
				</div>
				<div>
					<div class="stat-number"><?= $completedCount ?></div>
					<div class="stat-label">Completed Courses</div>
				</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--amber">
					<img src="../assets/icons/award.svg" width="24" height="24" alt="Certificates">
				</div>
				<div>
					<div class="stat-number"><?= $certCount ?></div>
					<div class="stat-label">Certificates Earned</div>
				</div>
			</div>
		</div>

		<!-- 1. In-Progress Courses Section -->
		<section>
			<div class="section-header">
				<h2 class="section-title">
					<img src="../assets/icons/book-open.svg" width="22" height="22" alt="Courses">
					In Progress Courses
				</h2>
				<span class="section-badge-count"><?= $inProgressCount ?> Courses</span>
			</div>

			<?php if (!empty($inProgress)): ?>
				<div class="courses-grid">
					<?php foreach ($inProgress as $course): ?>
						<article class="course-card">
							<div class="course-thumb-wrapper">
								<img src="../assets/adsity_assets/<?= htmlspecialchars($course['thumbnail']) ?>" alt="<?= htmlspecialchars($course['title']) ?>" class="course-thumb-img">
								<span class="course-category-tag"><?= htmlspecialchars($course['category']) ?></span>
							</div>

							<div class="course-card-content">
								<h3 class="course-card-title"><?= htmlspecialchars($course['title']) ?></h3>
								<p class="course-card-desc"><?= htmlspecialchars($course['description']) ?></p>

								<div class="progress-container">
									<div class="progress-header">
										<span class="progress-text"><?= $course['completed_lessons'] ?> of <?= $course['total_lessons'] ?> Lessons</span>
										<span class="progress-percent"><?= $course['progress_percent'] ?>%</span>
									</div>
									<div class="progress-bar-bg">
										<div class="progress-bar-fill" style="width: <?= $course['progress_percent'] ?>%;"></div>
									</div>
								</div>

								<div style="display: flex; gap: 8px; margin-top: auto;">
									<a href="../courses.php" class="btn-continue-course" style="flex: 1;">
										<img src="../assets/icons/play.svg" width="16" height="16" alt="Play" style="filter: brightness(0) invert(1);">
										Learn
									</a>
									<a href="submit_exam.php?course_id=<?= urlencode($course['course_id']) ?>" class="btn-view-cert" style="flex: 1.3; background-color: #0284c7;">
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
					<img src="../assets/icons/book-open.svg" width="36" height="36" alt="Book" style="opacity: 0.4;">
					<div class="empty-box-title">No Courses in Progress</div>
					<p class="empty-box-desc">You haven't enrolled in any active courses yet.</p>
					<a href="../courses.php" class="explore-btn" style="display: inline-block;">Browse Free Courses</a>
				</div>
			<?php endif; ?>
		</section>

		<!-- 2. Completed Courses Section -->
		<section>
			<div class="section-header">
				<h2 class="section-title">
					<img src="../assets/icons/check-circle.svg" width="22" height="22" alt="Completed">
					Completed Courses
				</h2>
				<span class="section-badge-count"><?= $completedCount ?> Courses</span>
			</div>

			<?php if (!empty($completedCourses)): ?>
				<div class="courses-grid">
					<?php foreach ($completedCourses as $completed): ?>
						<article class="course-card">
							<div class="course-thumb-wrapper">
								<img src="../assets/adsity_assets/<?= htmlspecialchars($completed['thumbnail']) ?>" alt="<?= htmlspecialchars($completed['title']) ?>" class="course-thumb-img">
								<span class="course-category-tag" style="background-color: #16a34a;">Completed</span>
							</div>

							<div class="course-card-content">
								<h3 class="course-card-title"><?= htmlspecialchars($completed['title']) ?></h3>
								<p class="course-card-desc"><?= htmlspecialchars($completed['description']) ?></p>

								<div style="font-size: 0.82rem; color: #64748b; margin-bottom: 16px;">
									Completed on <?= date('M d, Y', strtotime($completed['completed_at'])) ?>
								</div>

								<?php if (!empty($completed['certificate_code'])): ?>
									<a href="certificate.php?code=<?= urlencode($completed['certificate_code']) ?>" class="btn-view-cert">
										<img src="../assets/icons/award.svg" width="16" height="16" alt="Certificate" style="filter: brightness(0) invert(1);">
										View Certificate
									</a>
								<?php else: ?>
									<span style="font-size: 0.85rem; color: #16a34a; font-weight: 700;">100% Finished</span>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<div class="empty-box">
					<img src="../assets/icons/check-circle.svg" width="36" height="36" alt="Check" style="opacity: 0.4;">
					<div class="empty-box-title">No Completed Courses Yet</div>
					<p class="empty-box-desc">Finish watching lessons and assessments to earn completion status and certificates.</p>
				</div>
			<?php endif; ?>
		</section>

		<!-- 3. My Certificates Section -->
		<section>
			<div class="section-header">
				<h2 class="section-title">
					<img src="../assets/icons/award.svg" width="22" height="22" alt="Certificates">
					My Verified Certificates
				</h2>
				<span class="section-badge-count"><?= $certCount ?> Earned</span>
			</div>

			<?php if (!empty($certificates)): ?>
				<div class="courses-grid">
					<?php foreach ($certificates as $cert): ?>
						<article class="cert-card">
							<span class="cert-card-badge-verified">
								<img src="../assets/icons/award.svg" width="14" height="14" alt="Verified">
								Official Adsity Credential
							</span>

							<h3 class="cert-card-title"><?= htmlspecialchars($cert['course_title']) ?></h3>
							<div class="cert-card-code">Code: <?= htmlspecialchars($cert['certificate_code']) ?></div>
							<div class="cert-card-date">Issued on <?= date('F d, Y', strtotime($cert['issued_at'])) ?></div>

							<div style="display: flex; gap: 10px; margin-top: auto;">
								<a href="certificate.php?code=<?= urlencode($cert['certificate_code']) ?>" class="btn-view-cert" style="flex: 1;">
									<img src="../assets/icons/eye.svg" width="16" height="16" alt="View" style="filter: brightness(0) invert(1);">
									View
								</a>
								<a href="certificate.php?code=<?= urlencode($cert['certificate_code']) ?>" class="btn-continue-course" style="background-color: #334155;">
									<img src="../assets/icons/download.svg" width="16" height="16" alt="Download" style="filter: brightness(0) invert(1);">
								</a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<div class="empty-box">
					<img src="../assets/icons/award.svg" width="36" height="36" alt="Award" style="opacity: 0.4;">
					<div class="empty-box-title">No Certificates Earned Yet</div>
					<p class="empty-box-desc">Complete 100% of a course to unlock your free industry certificate.</p>
				</div>
			<?php endif; ?>
		</section>

	</main>

	<!-- Footer -->
	<footer class="footer">
		<div class="footer-bottom">
			<div class="footer-bottom-inner">
				<img src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo" class="footer-bottom-logo-img">
				<p class="footer-copyright">&copy; 2026 Adsity Student Portal. All rights reserved.</p>
			</div>
		</div>
	</footer>

</body>
</html>
