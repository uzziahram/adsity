<?php
require_once __DIR__ . '/dashboard_function.php';
$initials = strtoupper(substr($instructor['full_name'] ?? 'I', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Instructor Studio - Adsity</title>
	<link rel="stylesheet" href="../style.css">
	<link rel="stylesheet" href="instructordashboard.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body class="instructor-dashboard-body">

	<!-- Top Navigation Header -->
	<header class="instructor-navbar">
		<div class="instructor-nav-left">
			<button type="button" class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle Navigation Menu">
				<span class="hamburger-line"></span>
				<span class="hamburger-line"></span>
				<span class="hamburger-line"></span>
			</button>

			<a href="../index.php" class="instructor-nav-logo" title="Adsity Home">
				<img src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo">
			</a>
			<span class="studio-badge-title">Instructor Studio</span>
		</div>

		<div class="instructor-nav-right">
			<a href="create_course.php" class="nav-btn-create">
				<img src="../assets/icons/plus.svg" width="15" height="15" alt="Create" style="filter: brightness(0) invert(1);">
				<span>Create Course</span>
			</a>
			<a href="../courses.php" class="instructor-nav-link">Explore Catalog</a>
			<div class="instructor-user-pill">
				<div class="instructor-user-avatar"><?= $initials ?></div>
				<span class="instructor-user-name"><?= htmlspecialchars($instructor['full_name'] ?? 'Instructor') ?></span>
			</div>
			<a href="../logout.php" class="instructor-btn-logout" onclick="return confirm('Are you sure you want to log out?');">
				<img src="../assets/icons/arrow-left.svg" width="14" height="14" alt="Logout">
				<span>Log Out</span>
			</a>
		</div>
	</header>

	<!-- App Layout: Left Sidebar Navbar + Main Content Area -->
	<div class="instructor-dashboard-layout">

		<!-- ==========================================
		     LEFT SIDEBAR NAVBAR (ALL INSTRUCTOR ACTIONS)
		     ========================================== -->
		<aside class="instructor-sidebar" id="instructorSidebar">
			<div class="sidebar-top-section">

				<!-- Instructor Profile Summary Card -->
				<div class="instructor-profile-card">
					<div class="instructor-profile-avatar"><?= $initials ?></div>
					<div class="instructor-profile-info">
						<div class="instructor-profile-name" title="<?= htmlspecialchars($instructor['full_name'] ?? 'Instructor') ?>">
							<?= htmlspecialchars($instructor['full_name'] ?? 'Instructor') ?>
						</div>
						<div class="instructor-profile-status">
							<span class="status-indicator-dot"></span>
							Ad Revenue Partner
						</div>
					</div>
				</div>

				<!-- Prominent Primary Action Button -->
				<div class="sidebar-cta-wrapper">
					<a href="create_course.php" class="btn-sidebar-create">
						<img src="../assets/icons/plus.svg" width="16" height="16" alt="Add" style="filter: brightness(0) invert(1);">
						<span>Publish New Course</span>
					</a>
				</div>

				<!-- Navigation Actions & Tabs -->
				<nav class="sidebar-nav-group">
					<div class="sidebar-group-label">Studio Actions</div>

					<button type="button" class="sidebar-nav-item active" data-tab="overview" onclick="switchTab('overview')">
						<div class="sidebar-nav-item-left">
							<img src="../assets/icons/layout.svg" class="sidebar-nav-icon" alt="Overview">
							<span>Dashboard Overview</span>
						</div>
					</button>

					<button type="button" class="sidebar-nav-item" data-tab="courses" onclick="switchTab('courses')">
						<div class="sidebar-nav-item-left">
							<img src="../assets/icons/book-open.svg" class="sidebar-nav-icon" alt="Courses">
							<span>My Courses</span>
						</div>
						<span class="sidebar-badge-count"><?= $totalCourses ?></span>
					</button>

					<button type="button" class="sidebar-nav-item" data-tab="submissions" onclick="switchTab('submissions')">
						<div class="sidebar-nav-item-left">
							<img src="../assets/icons/award.svg" class="sidebar-nav-icon" alt="Submissions">
							<span>Student Submissions</span>
						</div>
						<span class="sidebar-badge-count"><?= count($submissions) ?></span>
					</button>

					<button type="button" class="sidebar-nav-item" data-tab="revenue" onclick="switchTab('revenue')">
						<div class="sidebar-nav-item-left">
							<img src="../assets/icons/dollar-sign.svg" class="sidebar-nav-icon" alt="Revenue">
							<span>Ad Revenue &amp; Logs</span>
						</div>
						<span class="sidebar-badge-share">$<?= number_format((float)($wallet['total_earned'] ?? 0), 2) ?></span>
					</button>

					<div class="sidebar-group-label" style="margin-top: 22px;">Explore Platform</div>

					<a href="../courses.php" class="sidebar-nav-item">
						<div class="sidebar-nav-item-left">
							<img src="../assets/icons/globe.svg" class="sidebar-nav-icon" alt="Courses">
							<span>Course Catalog</span>
						</div>
					</a>

					<a href="../index.php" class="sidebar-nav-item">
						<div class="sidebar-nav-item-left">
							<img src="../assets/icons/external-link.svg" class="sidebar-nav-icon" alt="Home">
							<span>Platform Homepage</span>
						</div>
					</a>
				</nav>
			</div>

			<!-- Sidebar Footer -->
			<div class="sidebar-bottom-section">
				<div class="partner-perk-box">
					<div class="perk-title">
						<img src="../assets/icons/shield-check.svg" width="16" height="16" alt="Partner">
						<span>Partner Status</span>
					</div>
					<p class="perk-desc">Enjoying 70% ad revenue split for all published video lessons.</p>
				</div>

				<a href="../logout.php" class="btn-sidebar-logout" onclick="return confirm('Are you sure you want to log out?');">
					<img src="../assets/icons/arrow-left.svg" width="15" height="15" alt="Logout">
					<span>Log Out</span>
				</a>
			</div>
		</aside>

		<!-- Backdrop for mobile sidebar drawer -->
		<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

		<!-- ==========================================
		     RIGHT MAIN FLUID CONTENT
		     ========================================== -->
		<main class="instructor-main-content">

			<!-- Status Alerts -->
			<?php if ($status === 'success'): ?>
				<div class="alert alert--success">
					<img src="../assets/icons/check-circle.svg" width="20" height="20" alt="Success">
					<span><?= htmlspecialchars($message ?? 'Operation completed successfully!') ?></span>
				</div>
			<?php elseif ($status === 'error'): ?>
				<div class="alert alert--error">
					<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
					<span><?= htmlspecialchars($message ?? 'An error occurred.') ?></span>
				</div>
			<?php endif; ?>

			<!-- ==========================================================
			     TAB 1: OVERVIEW TAB
			     ========================================================== -->
			<div id="tab-overview" class="instructor-tab-panel active">

				<!-- Welcome Banner -->
				<section class="instructor-welcome">
					<div class="instructor-welcome-content">
						<span class="instructor-badge">
							<img src="../assets/icons/graduation-cap.svg" width="14" height="14" alt="Instructor" style="filter: brightness(0) invert(1);">
							Ad Revenue Share Partner
						</span>
						<h1 class="instructor-title">Welcome, <?= htmlspecialchars($instructor['full_name'] ?? 'Instructor') ?> 🎓</h1>
						<p class="instructor-subtitle">
							Publish multi-video courses funded by sponsor ads, empower learners worldwide, and track student achievements.
						</p>
					</div>

					<div class="welcome-actions-group">
						<a href="create_course.php" class="btn-create-course-hero">
							<img src="../assets/icons/plus.svg" width="16" height="16" alt="Create" style="filter: brightness(0) invert(1);">
							Publish New Course
						</a>
					</div>
				</section>

				<!-- KPI Metrics Grid -->
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
							<div class="stat-label">Enrolled Learners</div>
						</div>
					</div>

					<div class="stat-card">
						<div class="stat-icon-box stat-icon-box--amber">
							<img src="../assets/icons/video.svg" width="24" height="24" alt="Ad Views">
						</div>
						<div>
							<div class="stat-number"><?= number_format($totalAdViews) ?></div>
							<div class="stat-label">Ad Views Logged</div>
						</div>
					</div>

					<div class="stat-card">
						<div class="stat-icon-box stat-icon-box--purple">
							<img src="../assets/icons/dollar-sign.svg" width="24" height="24" alt="Revenue">
						</div>
						<div>
							<div class="stat-number">$<?= number_format((float)($wallet['total_earned'] ?? 0), 2) ?></div>
							<div class="stat-label">Total Ad Earnings</div>
						</div>
					</div>
				</div>

				<!-- Quick Overview: Recent Courses -->
				<section class="dashboard-section">
					<div class="section-header">
						<h2 class="section-title">
							<img src="../assets/icons/book-open.svg" width="22" height="22" alt="Courses">
							Recent Published Courses
						</h2>
						<div class="section-header-actions">
							<button type="button" class="btn-text-link" onclick="switchTab('courses')">View All Courses &rarr;</button>
						</div>
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
									<?php foreach (array_slice($courses, 0, 5) as $c): ?>
										<tr>
											<td>
												<div class="course-table-cell">
													<?php
													$thumbSrc = !empty($c['thumbnail']) && str_starts_with($c['thumbnail'], 'uploads/')
														? '../' . $c['thumbnail']
														: '../assets/adsity_assets/' . ($c['thumbnail'] ?: 'Web_Development_Basics.png');
													?>
													<a href="course_overview.php?id=<?= $c['id'] ?>" class="course-table-img-link" title="View <?= htmlspecialchars($c['title']) ?>">
														<img src="<?= htmlspecialchars($thumbSrc) ?>" alt="<?= htmlspecialchars($c['title']) ?>" class="course-table-img">
													</a>
													<div>
														<a href="course_overview.php?id=<?= $c['id'] ?>" class="course-table-link">
															<strong><?= htmlspecialchars($c['title']) ?></strong>
														</a>
														<div style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars(substr($c['description'], 0, 55)) ?>...</div>
													</div>
												</div>
											</td>
											<td>
												<span class="category-pill"><?= htmlspecialchars($c['category']) ?></span>
											</td>
											<td><?= $c['total_lessons'] ?> Lessons</td>
											<td>
												<strong><?= $c['enrolled_students'] ?></strong> Learners
											</td>
											<td>
												<span class="text-success-badge">🎓 <?= $c['certificates_issued'] ?></span>
											</td>
											<td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
											<td style="text-align: right;">
												<div class="btn-action-group" style="justify-content: flex-end;">
													<a href="course_overview.php?id=<?= $c['id'] ?>" class="btn-view-course">
														<img src="../assets/icons/eye.svg" width="13" height="13" alt="View">
														View
													</a>
													<form method="POST" action="delete_course.php" onsubmit="return confirm('Are you sure you want to delete this course?');" style="display: inline;">
														<input type="hidden" name="course_id" value="<?= htmlspecialchars($c['id']) ?>">
														<button type="submit" name="delete_course" class="btn-delete">
															<img src="../assets/icons/trash.svg" width="13" height="13" alt="Delete">
															Delete
														</button>
													</form>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<div class="empty-state">
								<img src="../assets/icons/book-open.svg" width="48" height="48" alt="No courses" style="opacity: 0.35; margin-bottom: 12px;">
								<h3 style="font-size: 1.2rem; font-weight: 700; color: #334155; margin-bottom: 6px;">No Courses Published Yet</h3>
								<p style="color: #94a3b8; margin-bottom: 20px;">Publish your first ad-funded course to start reaching students worldwide.</p>
								<a href="create_course.php" class="btn-create-course-hero" style="display: inline-flex;">
									<img src="../assets/icons/plus.svg" width="16" height="16" alt="Plus" style="filter: brightness(0) invert(1);">
									Publish Your First Course
								</a>
							</div>
						<?php endif; ?>
					</div>
				</section>

				<!-- Quick Overview: Recent Submissions -->
				<section class="dashboard-section">
					<div class="section-header">
						<h2 class="section-title">
							<img src="../assets/icons/award.svg" width="22" height="22" alt="Submissions">
							Latest Student Project Deliverables
						</h2>
						<div class="section-header-actions">
							<button type="button" class="btn-text-link" onclick="switchTab('submissions')">View All Submissions &rarr;</button>
						</div>
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
										<th>Submitted Date</th>
										<th style="text-align: right;">Status</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach (array_slice($submissions, 0, 5) as $sub): ?>
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
													<span class="deliverable-badge deliverable-badge--github">
														<img src="../assets/icons/github.svg" width="12" height="12" alt="GitHub">
														GitHub Repo
													</span>
												<?php elseif ($sub['submission_type'] === 'file_upload'): ?>
													<span class="deliverable-badge deliverable-badge--file">
														<img src="../assets/icons/file-text.svg" width="12" height="12" alt="File">
														File Upload
													</span>
												<?php else: ?>
													<span class="deliverable-badge deliverable-badge--demo">
														<img src="../assets/icons/external-link.svg" width="12" height="12" alt="Live">
														Live Demo
													</span>
												<?php endif; ?>
											</td>
											<td>
												<?php if ($sub['submission_type'] === 'github_repo'): ?>
													<a href="<?= htmlspecialchars($sub['submission_value']) ?>" target="_blank" rel="noopener noreferrer" class="link-deliverable link-deliverable--blue">
														<span>Open Repository</span>
														<img src="../assets/icons/external-link.svg" width="12" height="12" alt="Open">
													</a>
												<?php elseif ($sub['submission_type'] === 'file_upload'): ?>
													<a href="../assets/submissions/<?= htmlspecialchars($sub['submission_value']) ?>" download class="link-deliverable link-deliverable--green">
														<img src="../assets/icons/download.svg" width="14" height="14" alt="Download">
														<span>Download File</span>
													</a>
												<?php else: ?>
													<a href="<?= htmlspecialchars($sub['submission_value']) ?>" target="_blank" rel="noopener noreferrer" class="link-deliverable link-deliverable--purple">
														<span>Visit Live Site</span>
														<img src="../assets/icons/external-link.svg" width="12" height="12" alt="Open">
													</a>
												<?php endif; ?>
											</td>
											<td><?= date('M d, Y', strtotime($sub['submitted_at'])) ?></td>
											<td style="text-align: right;">
												<span class="status-approved-pill">✓ Approved &amp; Certified</span>
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
			</div>

			<!-- ==========================================================
			     TAB 2: MY COURSES TAB
			     ========================================================== -->
			<div id="tab-courses" class="instructor-tab-panel">
				<section class="dashboard-section">
					<div class="section-header">
						<div>
							<h2 class="section-title">
								<img src="../assets/icons/book-open.svg" width="22" height="22" alt="Courses">
								My Curriculum &amp; Published Courses
							</h2>
							<p class="section-subtitle">Manage your sequential multi-video courses and view learner engagement.</p>
						</div>
						<div class="section-header-actions">
							<a href="create_course.php" class="btn-create-course-hero" style="font-size: 0.9rem; padding: 10px 18px;">
								<img src="../assets/icons/plus.svg" width="14" height="14" alt="Plus" style="filter: brightness(0) invert(1);">
								Create New Course
							</a>
						</div>
					</div>

					<div class="table-responsive">
						<?php if (!empty($courses)): ?>
							<table class="data-table">
								<thead>
									<tr>
										<th>Course Details</th>
										<th>Category</th>
										<th>Video Lessons</th>
										<th>Enrolled Students</th>
										<th>Certificates Awarded</th>
										<th>Date Published</th>
										<th style="text-align: right;">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($courses as $c): ?>
										<tr>
											<td>
												<div class="course-table-cell">
													<?php
													$thumbSrc = !empty($c['thumbnail']) && str_starts_with($c['thumbnail'], 'uploads/')
														? '../' . $c['thumbnail']
														: '../assets/adsity_assets/' . ($c['thumbnail'] ?: 'Web_Development_Basics.png');
													?>
													<a href="course_overview.php?id=<?= $c['id'] ?>" class="course-table-img-link" title="View <?= htmlspecialchars($c['title']) ?>">
														<img src="<?= htmlspecialchars($thumbSrc) ?>" alt="<?= htmlspecialchars($c['title']) ?>" class="course-table-img">
													</a>
													<div>
														<a href="course_overview.php?id=<?= $c['id'] ?>" class="course-table-link">
															<strong style="font-size: 1rem;"><?= htmlspecialchars($c['title']) ?></strong>
														</a>
														<div style="font-size: 0.82rem; color: #64748b; margin-top: 3px; max-width: 320px;"><?= htmlspecialchars($c['description']) ?></div>
													</div>
												</div>
											</td>
											<td>
												<span class="category-pill"><?= htmlspecialchars($c['category']) ?></span>
											</td>
											<td>
												<div class="course-lessons-info">
													<img src="../assets/icons/video.svg" width="14" height="14" alt="Video">
													<?= $c['total_lessons'] ?> Lessons
												</div>
											</td>
											<td>
												<strong><?= $c['enrolled_students'] ?></strong> Learners
											</td>
											<td>
												<span class="text-success-badge">🎓 <?= $c['certificates_issued'] ?> Issued</span>
											</td>
											<td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
											<td style="text-align: right;">
												<div class="btn-action-group" style="justify-content: flex-end;">
													<a href="course_overview.php?id=<?= $c['id'] ?>" class="btn-view-course">
														<img src="../assets/icons/eye.svg" width="13" height="13" alt="View">
														View
													</a>
													<form method="POST" action="delete_course.php" onsubmit="return confirm('Are you sure you want to delete this course? This cannot be undone.');" style="display: inline;">
														<input type="hidden" name="course_id" value="<?= htmlspecialchars($c['id']) ?>">
														<button type="submit" name="delete_course" class="btn-delete">
															<img src="../assets/icons/trash.svg" width="13" height="13" alt="Delete">
															Delete
														</button>
													</form>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<div class="empty-state">
								<img src="../assets/icons/book-open.svg" width="56" height="56" alt="No courses" style="opacity: 0.35; margin-bottom: 14px;">
								<h3 style="font-size: 1.25rem; font-weight: 700; color: #334155; margin-bottom: 8px;">No Courses Published Yet</h3>
								<p style="color: #94a3b8; margin-bottom: 24px;">Publish your first multi-video course to start educating learners and earning ad revenue.</p>
								<a href="create_course.php" class="btn-create-course-hero" style="display: inline-flex;">
									<img src="../assets/icons/plus.svg" width="16" height="16" alt="Plus" style="filter: brightness(0) invert(1);">
									Publish Your First Course
								</a>
							</div>
						<?php endif; ?>
					</div>
				</section>
			</div>

			<!-- ==========================================================
			     TAB 3: STUDENT SUBMISSIONS TAB
			     ========================================================== -->
			<div id="tab-submissions" class="instructor-tab-panel">
				<section class="dashboard-section">
					<div class="section-header">
						<div>
							<h2 class="section-title">
								<img src="../assets/icons/award.svg" width="22" height="22" alt="Submissions">
								Student Project Submissions &amp; Deliverables
							</h2>
							<p class="section-subtitle">Review submitted capstone projects, repositories, and deliverables from your learners.</p>
						</div>
						<span class="badge-count"><?= count($submissions) ?> Total Submissions</span>
					</div>

					<div class="table-responsive">
						<?php if (!empty($submissions)): ?>
							<table class="data-table">
								<thead>
									<tr>
										<th>Student Name</th>
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
													<span class="deliverable-badge deliverable-badge--github">
														<img src="../assets/icons/github.svg" width="12" height="12" alt="GitHub">
														GitHub Repo
													</span>
												<?php elseif ($sub['submission_type'] === 'file_upload'): ?>
													<span class="deliverable-badge deliverable-badge--file">
														<img src="../assets/icons/file-text.svg" width="12" height="12" alt="File">
														File Upload
													</span>
												<?php else: ?>
													<span class="deliverable-badge deliverable-badge--demo">
														<img src="../assets/icons/external-link.svg" width="12" height="12" alt="Live">
														Live Demo
													</span>
												<?php endif; ?>
											</td>
											<td>
												<?php if ($sub['submission_type'] === 'github_repo'): ?>
													<a href="<?= htmlspecialchars($sub['submission_value']) ?>" target="_blank" rel="noopener noreferrer" class="link-deliverable link-deliverable--blue">
														<span>Open Repository</span>
														<img src="../assets/icons/external-link.svg" width="12" height="12" alt="Open">
													</a>
												<?php elseif ($sub['submission_type'] === 'file_upload'): ?>
													<a href="../assets/submissions/<?= htmlspecialchars($sub['submission_value']) ?>" download class="link-deliverable link-deliverable--green">
														<img src="../assets/icons/download.svg" width="14" height="14" alt="Download">
														<span>Download <?= htmlspecialchars(pathinfo($sub['submission_value'], PATHINFO_EXTENSION)) ?></span>
													</a>
												<?php else: ?>
													<a href="<?= htmlspecialchars($sub['submission_value']) ?>" target="_blank" rel="noopener noreferrer" class="link-deliverable link-deliverable--purple">
														<span>Visit Live Site</span>
														<img src="../assets/icons/external-link.svg" width="12" height="12" alt="Open">
													</a>
												<?php endif; ?>
											</td>
											<td>
												<div style="font-size: 0.85rem; color: #475569; max-width: 240px;">
													<?= htmlspecialchars($sub['notes'] ?? 'No notes provided') ?>
												</div>
											</td>
											<td><?= date('M d, Y', strtotime($sub['submitted_at'])) ?></td>
											<td style="text-align: right;">
												<span class="status-approved-pill">
													✓ Approved &amp; Certified
												</span>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<div class="empty-state" style="padding: 48px 24px;">
								<img src="../assets/icons/award.svg" width="48" height="48" alt="No submissions" style="opacity: 0.3; margin-bottom: 12px;">
								<h3 style="font-size: 1.15rem; font-weight: 700; color: #334155; margin-bottom: 4px;">No Project Submissions Yet</h3>
								<p style="color: #94a3b8; margin: 0;">When enrolled students complete and submit their course deliverables, they will appear here.</p>
							</div>
						<?php endif; ?>
					</div>
				</section>
			</div>

			<!-- ==========================================================
			     TAB 4: REVENUE & ANALYTICS TAB
			     ========================================================== -->
			<div id="tab-revenue" class="instructor-tab-panel">
				<section class="dashboard-section">
					<div class="section-header">
						<div>
							<h2 class="section-title">
								<img src="../assets/icons/dollar-sign.svg" width="22" height="22" alt="Revenue">
								Ad Revenue &amp; Activity Logs
							</h2>
							<p class="section-subtitle">Track your earned sponsor revenue from student ad views in real time.</p>
						</div>
					</div>

					<!-- Wallet Balance Hero Card -->
					<div class="revenue-hero-card">
						<div class="revenue-hero-content">
							<span class="partner-tier-pill">Creator Monetization Wallet</span>
							<h3 class="revenue-hero-heading">$<?= number_format((float)($wallet['available_balance'] ?? 0), 2) ?> Available Balance</h3>
							<p class="revenue-hero-text">
								You earn <strong>$0.05</strong> for every completed 15-second sponsor ad session watched by students before each lesson. Courses stay 100% free for learners while rewarding your educational content.
							</p>
						</div>
						<div class="revenue-metric-badge">
							<div class="metric-big-value">$<?= number_format((float)($wallet['total_earned'] ?? 0), 2) ?></div>
							<div class="metric-sub-label">Total Lifetime Earned</div>
						</div>
					</div>

					<!-- Analytics Breakdown Grid -->
					<div class="analytics-grid">
						<div class="analytic-card">
							<div class="analytic-card-header">
								<img src="../assets/icons/dollar-sign.svg" width="20" height="20" alt="Rate">
								<span>Fixed Payout Rate</span>
							</div>
							<p class="analytic-card-text">
								<strong>$0.05 / Ad Session</strong> earned automatically upon every verified student ad completion.
							</p>
						</div>

						<div class="analytic-card">
							<div class="analytic-card-header">
								<img src="../assets/icons/video.svg" width="20" height="20" alt="Views">
								<span>Ad Views Logged</span>
							</div>
							<p class="analytic-card-text">
								<strong><?= number_format($totalAdViews) ?></strong> total verified ad sessions completed by your enrolled students.
							</p>
						</div>

						<div class="analytic-card">
							<div class="analytic-card-header">
								<img src="../assets/icons/award.svg" width="20" height="20" alt="Paid">
								<span>Withdrawn to Date</span>
							</div>
							<p class="analytic-card-text">
								<strong>$<?= number_format((float)($wallet['total_withdrawn'] ?? 0), 2) ?></strong> total funds disbursed from your wallet account.
							</p>
						</div>
					</div>

					<!-- Real-Time Ad Activity Logs Table -->
					<div style="margin-top: 36px;">
						<div class="section-header">
							<div>
								<h3 class="section-title" style="font-size: 1.15rem;">
									<img src="../assets/icons/file-text.svg" width="20" height="20" alt="Logs">
									Recent Ad Activity Logs
								</h3>
								<p class="section-subtitle">Itemized audit log of student ad sessions credited to your wallet.</p>
							</div>
						</div>

						<div class="table-responsive">
							<?php if (!empty($adActivityLogs)): ?>
								<table class="data-table">
									<thead>
										<tr>
											<th>Timestamp</th>
											<th>Student</th>
											<th>Course</th>
											<th>Lesson</th>
											<th>Duration</th>
											<th>Amount Credited</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($adActivityLogs as $log): ?>
											<tr>
												<td><?= date('M d, Y h:i A', strtotime($log['created_at'])) ?></td>
												<td>
													<strong><?= htmlspecialchars($log['student_name']) ?></strong>
												</td>
												<td><?= htmlspecialchars($log['course_title']) ?></td>
												<td>Lesson <?= str_pad($log['lesson_number'], 2, '0', STR_PAD_LEFT) ?></td>
												<td><?= (int)$log['ad_duration_seconds'] ?>s</td>
												<td>
													<strong style="color: #16a34a;">+$<?= number_format((float)$log['amount_earned'], 2) ?></strong>
												</td>
												<td>
													<span class="badge badge--success" style="background-color: #dcfce7; color: #166534; font-weight: 800; padding: 4px 10px; border-radius: 9999px; font-size: 0.78rem;">✓ Settled</span>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							<?php else: ?>
								<div class="empty-state-box" style="text-align: center; padding: 40px 20px; background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 12px;">
									<img src="../assets/icons/dollar-sign.svg" width="36" height="36" alt="Empty" style="opacity: 0.35; margin-bottom: 12px;">
									<h4 style="margin: 0 0 6px 0; color: #0f172a; font-weight: 800;">No Ad Activities Logged Yet</h4>
									<p style="margin: 0; color: #64748b; font-size: 0.88rem;">When students watch video lessons in your courses, their verified ad views will log here automatically.</p>
								</div>
							<?php endif; ?>
						</div>
					</div>

				</section>
			</div>

		</main>
	</div>

	<!-- Bottom Footer -->
	<footer class="footer">
		<div class="footer-bottom">
			<div class="footer-bottom-inner">
				<img src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo" class="footer-bottom-logo-img">
				<p class="footer-copyright">&copy; 2026 Adsity Instructor Studio. All rights reserved.</p>
			</div>
		</div>
	</footer>

	<!-- Tab Switching & Mobile Drawer Script -->
	<script>
		function switchTab(tabId) {
			// Update active tab buttons
			document.querySelectorAll('.sidebar-nav-item[data-tab]').forEach(function(btn) {
				if (btn.getAttribute('data-tab') === tabId) {
					btn.classList.add('active');
				} else {
					btn.classList.remove('active');
				}
			});

			// Update active panels
			document.querySelectorAll('.instructor-tab-panel').forEach(function(panel) {
				panel.classList.remove('active');
			});

			var targetPanel = document.getElementById('tab-' + tabId);
			if (targetPanel) {
				targetPanel.classList.add('active');
			}

			// Update URL hash without jumping
			if (history.pushState) {
				history.pushState(null, null, '#' + tabId);
			} else {
				location.hash = '#' + tabId;
			}

			// Close mobile drawer if open
			closeSidebar();
		}

		function toggleSidebar() {
			var sidebar = document.getElementById('instructorSidebar');
			var backdrop = document.getElementById('sidebarBackdrop');
			if (sidebar && backdrop) {
				sidebar.classList.toggle('open');
				backdrop.classList.toggle('open');
			}
		}

		function closeSidebar() {
			var sidebar = document.getElementById('instructorSidebar');
			var backdrop = document.getElementById('sidebarBackdrop');
			if (sidebar && backdrop) {
				sidebar.classList.remove('open');
				backdrop.classList.remove('open');
			}
		}

		document.addEventListener('DOMContentLoaded', function() {
			var toggleBtn = document.getElementById('sidebarToggle');
			if (toggleBtn) {
				toggleBtn.addEventListener('click', toggleSidebar);
			}

			// Handle initial hash in URL
			var hash = window.location.hash.replace('#', '');
			if (hash && document.getElementById('tab-' + hash)) {
				switchTab(hash);
			}
		});
	</script>
</body>
</html>

