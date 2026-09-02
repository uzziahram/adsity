<?php
require_once __DIR__ . '/dashboard_function.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Instructor Dashboard - Adsity</title>
	<link rel="stylesheet" href="../style.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<style>
		.instructor-dashboard-body {
			background-color: #f8fafc;
			min-height: 100vh;
		}

		.instructor-container {
			max-width: 1200px;
			margin: 36px auto;
			padding: 0 24px;
		}

		/* Welcome Header */
		.instructor-welcome {
			background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
			border-radius: 18px;
			padding: 36px 32px;
			color: #ffffff;
			margin-bottom: 32px;
			display: flex;
			justify-content: space-between;
			align-items: center;
			flex-wrap: wrap;
			gap: 20px;
			box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
		}

		.instructor-welcome-content {
			max-width: 650px;
		}

		.instructor-badge {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			background-color: rgba(2, 132, 199, 0.2);
			border: 1px solid rgba(2, 132, 199, 0.5);
			color: #38bdf8;
			font-size: 0.8rem;
			font-weight: 700;
			padding: 4px 12px;
			border-radius: 9999px;
			margin-bottom: 12px;
			text-transform: uppercase;
			letter-spacing: 0.05em;
		}

		.instructor-title {
			font-size: 2.1rem;
			font-weight: 800;
			margin-bottom: 8px;
			letter-spacing: -0.02em;
		}

		.instructor-subtitle {
			font-size: 0.95rem;
			color: #94a3b8;
			line-height: 1.5;
		}

		.btn-create-course-hero {
			background-color: #0284c7;
			color: #ffffff;
			font-weight: 700;
			font-size: 0.95rem;
			padding: 12px 24px;
			border-radius: 10px;
			text-decoration: none;
			display: inline-flex;
			align-items: center;
			gap: 8px;
			transition: background-color 0.2s ease, transform 0.2s ease;
		}

		.btn-create-course-hero:hover {
			background-color: #0369a1;
			transform: translateY(-2px);
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

		.stat-icon-box--blue {
			background-color: #e0f2fe;
		}

		.stat-icon-box--green {
			background-color: #dcfce7;
		}

		.stat-icon-box--amber {
			background-color: #fef3c7;
		}

		.stat-icon-box--purple {
			background-color: #f3e8ff;
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
		.dashboard-section {
			background-color: #ffffff;
			border: 1px solid #e2e8f0;
			border-radius: 16px;
			padding: 28px;
			margin-bottom: 36px;
			box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
		}

		.section-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 22px;
		}

		.section-title {
			font-size: 1.35rem;
			font-weight: 800;
			color: #0f172a;
			display: flex;
			align-items: center;
			gap: 10px;
		}

		.badge-count {
			background-color: #f1f5f9;
			color: #475569;
			font-size: 0.8rem;
			font-weight: 700;
			padding: 3px 10px;
			border-radius: 9999px;
		}

		.table-responsive {
			overflow-x: auto;
		}

		.data-table {
			width: 100%;
			border-collapse: collapse;
			text-align: left;
			font-size: 0.92rem;
		}

		.data-table th {
			background-color: #f8fafc;
			color: #475569;
			font-weight: 600;
			padding: 14px 18px;
			border-bottom: 1px solid #e2e8f0;
			text-transform: uppercase;
			font-size: 0.78rem;
			letter-spacing: 0.05em;
		}

		.data-table td {
			padding: 16px 18px;
			border-bottom: 1px solid #f1f5f9;
			color: #334155;
			vertical-align: middle;
		}

		.data-table tr:hover {
			background-color: #f8fafc;
		}

		.course-table-cell {
			display: flex;
			align-items: center;
			gap: 14px;
		}

		.course-table-img {
			width: 48px;
			height: 36px;
			border-radius: 6px;
			object-fit: cover;
		}

		.btn-delete {
			background-color: #fee2e2;
			color: #dc2626;
			border: 1px solid #fecaca;
			padding: 6px 12px;
			border-radius: 6px;
			font-size: 0.82rem;
			font-weight: 600;
			cursor: pointer;
			display: inline-flex;
			align-items: center;
			gap: 6px;
			transition: background-color 0.2s, color 0.2s;
		}

		.btn-delete:hover {
			background-color: #dc2626;
			color: #ffffff;
		}

		.empty-state {
			text-align: center;
			padding: 48px 24px;
			color: #64748b;
		}
	</style>
</head>

<body class="instructor-dashboard-body">

	<!-- Navigation Header -->
	<header class="navbar">
		<div class="nav-left">
			<a href="../index.php" class="logo">
				<span class="logo-icon">
					<img class="icon" src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo">
				</span>
			</a>
			<span style="font-weight: 700; color: #0f172a; font-size: 1.05rem;">Instructor Studio</span>
		</div>

		<div class="nav-right">
			<a href="../courses.php" class="teach-link">Explore Courses</a>
			<span style="font-weight: 700; color: #0284c7; font-size: 0.9rem;">
				👨‍🏫 <?= htmlspecialchars($instructor['full_name'] ?? 'Instructor') ?>
			</span>
			<a href="../logout.php" class="btn-login" style="background-color: #475569;" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
		</div>
	</header>

	<main class="instructor-container">

		<!-- Welcome Header -->
		<section class="instructor-welcome">
			<div class="instructor-welcome-content">
				<span class="instructor-badge">
					<img src="../assets/icons/graduation-cap.svg" width="14" height="14" alt="Instructor" style="filter: brightness(0) invert(1);">
					Ad Revenue Share Partner
				</span>
				<h1 class="instructor-title">Welcome, <?= htmlspecialchars($instructor['full_name'] ?? 'Instructor') ?> 🎓</h1>
				<p class="instructor-subtitle">
					Publish free courses funded by ads, educate students worldwide, and track your audience engagement and certificates.
				</p>
			</div>

			<div>
				<a href="create_course.php" class="btn-create-course-hero">
					<img src="../assets/icons/plus.svg" width="16" height="16" alt="Create" style="filter: brightness(0) invert(1);">
					Publish New Course
				</a>
			</div>
		</section>

		<!-- Status Alerts -->
		<?php if ($status === 'success'): ?>
			<div class="alert alert--success" style="margin-bottom: 30px;">
				<img src="../assets/icons/check-circle.svg" width="20" height="20" alt="Success">
				<span><?= htmlspecialchars($message ?? 'Operation completed successfully!') ?></span>
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
					<img src="../assets/icons/book-open.svg" width="24" height="24" alt="Courses">
				</div>
				<div>
					<div class="stat-number"><?= $totalCourses ?></div>
					<div class="stat-label">Published Courses</div>
				</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--green">
					<img src="../assets/icons/users.svg" width="24" height="24" alt="Students">
				</div>
				<div>
					<div class="stat-number"><?= $totalStudents ?></div>
					<div class="stat-label">Enrolled Students</div>
				</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--amber">
					<img src="../assets/icons/award.svg" width="24" height="24" alt="Certificates">
				</div>
				<div>
					<div class="stat-number"><?= $totalCertificates ?></div>
					<div class="stat-label">Certificates Awarded</div>
				</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--purple">
					<img src="../assets/icons/dollar-sign.svg" width="24" height="24" alt="Revenue">
				</div>
				<div>
					<div class="stat-number">70%</div>
					<div class="stat-label">Ad Revenue Share</div>
				</div>
			</div>
		</div>

		<!-- My Published Courses Table Section -->
		<section class="dashboard-section">
			<div class="section-header">
				<h2 class="section-title">
					<img src="../assets/icons/book-open.svg" width="22" height="22" alt="Courses">
					My Published Curriculum
				</h2>
				<span class="badge-count"><?= $totalCourses ?> Courses</span>
			</div>

			<div class="table-responsive">
				<?php if (!empty($courses)): ?>
					<table class="data-table">
						<thead>
							<tr>
								<th>Course Title</th>
								<th>Category</th>
								<th>Lessons</th>
								<th>Enrolled Students</th>
								<th>Certificates Issued</th>
								<th>Published Date</th>
								<th style="text-align: right;">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($courses as $c): ?>
								<tr>
									<td>
										<div class="course-table-cell">
											<img src="../assets/adsity_assets/<?= htmlspecialchars($c['thumbnail']) ?>" alt="<?= htmlspecialchars($c['title']) ?>" class="course-table-img">
											<div>
												<strong><?= htmlspecialchars($c['title']) ?></strong>
												<div style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars(substr($c['description'], 0, 60)) ?>...</div>
											</div>
										</div>
									</td>
									<td>
										<span style="background-color: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.82rem;">
											<?= htmlspecialchars($c['category']) ?>
										</span>
									</td>
									<td><?= $c['total_lessons'] ?> Lessons</td>
									<td>
										<strong><?= $c['enrolled_students'] ?></strong> Learners
									</td>
									<td>
										<span style="color: #16a34a; font-weight: 700;">🎓 <?= $c['certificates_issued'] ?></span>
									</td>
									<td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
									<td style="text-align: right;">
										<form method="POST" action="delete_course.php" onsubmit="return confirm('Are you sure you want to delete this course?');" style="display: inline;">
											<input type="hidden" name="course_id" value="<?= htmlspecialchars($c['id']) ?>">
											<button type="submit" name="delete_course" class="btn-delete">
												<img src="../assets/icons/trash.svg" width="14" height="14" alt="Delete">
												Delete
											</button>
										</form>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else: ?>
					<div class="empty-state">
						<img src="../assets/icons/book-open.svg" width="48" height="48" alt="No courses" style="opacity: 0.35; margin-bottom: 12px;">
						<h3 style="font-size: 1.2rem; font-weight: 700; color: #334155; margin-bottom: 6px;">No Courses Published Yet</h3>
						<p style="color: #94a3b8; margin-bottom: 20px;">Publish your first ad-funded course to start reaching students and earning revenue.</p>
						<a href="create_course.php" class="btn-create-course-hero" style="display: inline-flex;">
							<img src="../assets/icons/plus.svg" width="16" height="16" alt="Plus" style="filter: brightness(0) invert(1);">
							Publish Your First Course
						</a>
					</div>
				<?php endif; ?>
			</div>
		</section>

	</main>

	<!-- Footer -->
	<footer class="footer">
		<div class="footer-bottom">
			<div class="footer-bottom-inner">
				<img src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo" class="footer-bottom-logo-img">
				<p class="footer-copyright">&copy; 2026 Adsity Instructor Studio. All rights reserved.</p>
			</div>
		</div>
	</footer>

</body>
</html>
