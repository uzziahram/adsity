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
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<style>
		.student-dashboard-body {
			background-color: #f8fafc;
			min-height: 100vh;
		}

		.student-container {
			max-width: 1200px;
			margin: 36px auto;
			padding: 0 24px;
		}

		/* Welcome Header */
		.student-welcome {
			background: linear-gradient(135deg, #14221b 0%, #1e3a2f 100%);
			border-radius: 18px;
			padding: 36px 32px;
			color: #ffffff;
			margin-bottom: 32px;
			position: relative;
			overflow: hidden;
			box-shadow: 0 10px 25px -5px rgba(20, 34, 27, 0.25);
		}

		.welcome-badge {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			background-color: rgba(39, 214, 98, 0.15);
			border: 1px solid rgba(39, 214, 98, 0.4);
			color: #27D662;
			font-size: 0.8rem;
			font-weight: 700;
			padding: 4px 12px;
			border-radius: 9999px;
			margin-bottom: 12px;
			text-transform: uppercase;
			letter-spacing: 0.05em;
		}

		.welcome-title {
			font-size: 2.2rem;
			font-weight: 800;
			margin-bottom: 8px;
			letter-spacing: -0.02em;
		}

		.welcome-subtitle {
			font-size: 1rem;
			color: #cbd5e1;
			max-width: 650px;
			line-height: 1.5;
		}

		/* Stats Grid */
		.stats-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
			gap: 20px;
			margin-bottom: 40px;
		}

		.stat-card {
			background-color: #ffffff;
			border: 1px solid #e2e8f0;
			border-radius: 16px;
			padding: 24px;
			display: flex;
			align-items: center;
			gap: 18px;
			box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
			transition: transform 0.2s ease, box-shadow 0.2s ease;
		}

		.stat-card:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.08);
		}

		.stat-icon-box {
			width: 52px;
			height: 52px;
			border-radius: 12px;
			display: flex;
			align-items: center;
			justify-content: center;
			flex-shrink: 0;
		}

		.stat-icon-box--green {
			background-color: #dcfce7;
		}

		.stat-icon-box--blue {
			background-color: #e0f2fe;
		}

		.stat-icon-box--amber {
			background-color: #fef3c7;
		}

		.stat-number {
			font-size: 1.85rem;
			font-weight: 800;
			color: #0f172a;
			line-height: 1.1;
		}

		.stat-label {
			font-size: 0.85rem;
			color: #64748b;
			font-weight: 600;
			margin-top: 4px;
		}

		/* Section Styles */
		.section-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 22px;
		}

		.section-title {
			font-size: 1.45rem;
			font-weight: 800;
			color: #0f172a;
			display: flex;
			align-items: center;
			gap: 10px;
		}

		.section-badge-count {
			background-color: #e2e8f0;
			color: #475569;
			font-size: 0.8rem;
			font-weight: 700;
			padding: 2px 10px;
			border-radius: 9999px;
		}

		.courses-grid {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
			gap: 24px;
			margin-bottom: 48px;
		}

		/* Course Card */
		.course-card {
			background-color: #ffffff;
			border: 1px solid #e2e8f0;
			border-radius: 16px;
			overflow: hidden;
			display: flex;
			flex-direction: column;
			box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
			transition: transform 0.2s ease, box-shadow 0.2s ease;
		}

		.course-card:hover {
			transform: translateY(-3px);
			box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.08);
		}

		.course-thumb-wrapper {
			position: relative;
			height: 160px;
			background-color: #0f172a;
			overflow: hidden;
		}

		.course-thumb-img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}

		.course-category-tag {
			position: absolute;
			top: 12px;
			left: 12px;
			background-color: rgba(15, 23, 42, 0.85);
			backdrop-filter: blur(4px);
			color: #ffffff;
			font-size: 0.75rem;
			font-weight: 700;
			padding: 4px 10px;
			border-radius: 6px;
			text-transform: uppercase;
			letter-spacing: 0.05em;
		}

		.course-card-content {
			padding: 20px;
			display: flex;
			flex-direction: column;
			flex: 1;
		}

		.course-card-title {
			font-size: 1.15rem;
			font-weight: 700;
			color: #0f172a;
			margin-bottom: 8px;
			line-height: 1.35;
		}

		.course-card-desc {
			font-size: 0.88rem;
			color: #64748b;
			line-height: 1.5;
			margin-bottom: 16px;
			flex: 1;
		}

		/* Progress Bar */
		.progress-container {
			margin-bottom: 16px;
		}

		.progress-header {
			display: flex;
			justify-content: space-between;
			font-size: 0.8rem;
			font-weight: 700;
			margin-bottom: 6px;
		}

		.progress-text {
			color: #64748b;
		}

		.progress-percent {
			color: #16a34a;
		}

		.progress-bar-bg {
			height: 8px;
			background-color: #f1f5f9;
			border-radius: 9999px;
			overflow: hidden;
		}

		.progress-bar-fill {
			height: 100%;
			background: linear-gradient(90deg, #27D662 0%, #10b981 100%);
			border-radius: 9999px;
			transition: width 0.4s ease;
		}

		.btn-continue-course {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			gap: 8px;
			background-color: #27D662;
			color: #ffffff;
			font-weight: 700;
			font-size: 0.9rem;
			padding: 10px 18px;
			border-radius: 10px;
			text-decoration: none;
			transition: background-color 0.2s ease;
		}

		.btn-continue-course:hover {
			background-color: #21b854;
		}

		.btn-view-cert {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			gap: 8px;
			background-color: #0284c7;
			color: #ffffff;
			font-weight: 700;
			font-size: 0.9rem;
			padding: 10px 18px;
			border-radius: 10px;
			text-decoration: none;
			transition: background-color 0.2s ease;
		}

		.btn-view-cert:hover {
			background-color: #0369a1;
		}

		/* Certificate Card */
		.cert-card {
			background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
			border: 2px solid #fef08a;
			border-radius: 16px;
			padding: 24px;
			display: flex;
			flex-direction: column;
			box-shadow: 0 4px 10px rgba(234, 179, 8, 0.08);
			position: relative;
			overflow: hidden;
		}

		.cert-card-badge-verified {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			color: #854d0e;
			background-color: #fef9c3;
			font-size: 0.75rem;
			font-weight: 700;
			padding: 4px 10px;
			border-radius: 9999px;
			margin-bottom: 12px;
			width: fit-content;
		}

		.cert-card-title {
			font-size: 1.15rem;
			font-weight: 800;
			color: #0f172a;
			margin-bottom: 6px;
		}

		.cert-card-code {
			font-family: monospace;
			font-size: 0.85rem;
			color: #0284c7;
			background-color: #f0f9ff;
			padding: 3px 8px;
			border-radius: 4px;
			display: inline-block;
			margin-bottom: 12px;
			width: fit-content;
		}

		.cert-card-date {
			font-size: 0.82rem;
			color: #64748b;
			margin-bottom: 20px;
		}

		.empty-box {
			background-color: #ffffff;
			border: 2px dashed #e2e8f0;
			border-radius: 16px;
			padding: 48px 24px;
			text-align: center;
			color: #64748b;
			margin-bottom: 48px;
		}

		.empty-box-title {
			font-size: 1.15rem;
			font-weight: 700;
			color: #334155;
			margin-top: 12px;
			margin-bottom: 6px;
		}

		.empty-box-desc {
			font-size: 0.9rem;
			color: #94a3b8;
			margin-bottom: 18px;
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
			<a href="../logout.php" class="btn-login" style="background-color: #475569;">Log Out</a>
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

								<a href="../index.php" class="btn-continue-course">
									<img src="../assets/icons/play.svg" width="16" height="16" alt="Play" style="filter: brightness(0) invert(1);">
									Continue Learning
								</a>
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
