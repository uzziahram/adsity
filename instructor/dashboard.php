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
	<link rel="stylesheet" href="instructordashboard.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
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

		<!-- Student Project Submissions Section -->
		<section class="dashboard-section">
			<div class="section-header">
				<h2 class="section-title">
					<img src="../assets/icons/award.svg" width="22" height="22" alt="Submissions">
					Student Project Submissions &amp; Deliverables
				</h2>
				<span class="badge-count"><?= count($submissions) ?> Submissions</span>
			</div>

			<div class="table-responsive">
				<?php if (!empty($submissions)): ?>
					<table class="data-table">
						<thead>
							<tr>
								<th>Student</th>
								<th>Course</th>
								<th>Exam Deliverable Type</th>
								<th>Project Link / Deliverable</th>
								<th>Student Notes</th>
								<th>Submitted Date</th>
								<th style="text-align: right;">Status</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($submissions as $sub): ?>
								<tr>
									<td>
										<strong><?= htmlspecialchars($sub['student_name']) ?></strong>
										<div style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($sub['student_email']) ?></div>
									</td>
									<td>
										<strong><?= htmlspecialchars($sub['course_title']) ?></strong>
									</td>
									<td>
										<?php if ($sub['submission_type'] === 'github_repo'): ?>
											<span style="background-color: #f1f5f9; color: #0f172a; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
												<img src="../assets/icons/github.svg" width="12" height="12" alt="GitHub">
												GitHub Repo
											</span>
										<?php elseif ($sub['submission_type'] === 'file_upload'): ?>
											<span style="background-color: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
												<img src="../assets/icons/file-text.svg" width="12" height="12" alt="File">
												File Upload
											</span>
										<?php else: ?>
											<span style="background-color: #f3e8ff; color: #7e22ce; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
												<img src="../assets/icons/external-link.svg" width="12" height="12" alt="Live">
												Live Demo
											</span>
										<?php endif; ?>
									</td>
									<td>
										<?php if ($sub['submission_type'] === 'github_repo'): ?>
											<a href="<?= htmlspecialchars($sub['submission_value']) ?>" target="_blank" rel="noopener noreferrer" style="color: #0284c7; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
												<span>Open Repository</span>
												<img src="../assets/icons/external-link.svg" width="12" height="12" alt="Open">
											</a>
										<?php elseif ($sub['submission_type'] === 'file_upload'): ?>
											<a href="../assets/submissions/<?= htmlspecialchars($sub['submission_value']) ?>" download style="color: #16a34a; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
												<img src="../assets/icons/download.svg" width="14" height="14" alt="Download">
												<span>Download <?= htmlspecialchars(pathinfo($sub['submission_value'], PATHINFO_EXTENSION)) ?></span>
											</a>
										<?php else: ?>
											<a href="<?= htmlspecialchars($sub['submission_value']) ?>" target="_blank" rel="noopener noreferrer" style="color: #7e22ce; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
												<span>Visit Live Site</span>
												<img src="../assets/icons/external-link.svg" width="12" height="12" alt="Open">
											</a>
										<?php endif; ?>
									</td>
									<td>
										<div style="font-size: 0.85rem; color: #475569; max-width: 200px;">
											<?= htmlspecialchars($sub['notes'] ?? 'No notes provided') ?>
										</div>
									</td>
									<td><?= date('M d, Y', strtotime($sub['submitted_at'])) ?></td>
									<td style="text-align: right;">
										<span style="background-color: #dcfce7; color: #16a34a; font-weight: 700; font-size: 0.8rem; padding: 4px 10px; border-radius: 9999px;">
											✓ Approved &amp; Certified
										</span>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else: ?>
					<div class="empty-state" style="padding: 32px 16px;">
						<p style="color: #94a3b8; margin: 0;">No student project submissions yet.</p>
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
