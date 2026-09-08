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
			<a href="../logout.php" class="instructor-btn-logout">
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

				<a href="../logout.php" class="btn-sidebar-logout">
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
														<div style="margin-top: 3px; display: flex; align-items: center; gap: 6px;">
															<?php $cStatus = $c['status'] ?? 'published'; ?>
															<?php if ($cStatus === 'published'): ?>
																<span style="font-size: 0.7rem; font-weight: 700; color: #15803d; background-color: #dcfce7; padding: 1px 7px; border-radius: 9999px;">● Live</span>
															<?php elseif ($cStatus === 'pending_review'): ?>
																<span style="font-size: 0.7rem; font-weight: 700; color: #b45309; background-color: #fef3c7; padding: 1px 7px; border-radius: 9999px;">⏳ In Review</span>
															<?php elseif ($cStatus === 'rejected'): ?>
																<span style="font-size: 0.7rem; font-weight: 700; color: #b91c1c; background-color: #fee2e2; padding: 1px 7px; border-radius: 9999px;">⚠️ Revisions</span>
															<?php endif; ?>
															<span style="font-size: 0.78rem; color: #64748b;"><?= htmlspecialchars(substr($c['description'], 0, 45)) ?>...</span>
														</div>
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
														<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
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
											<td style="text-align: right;">
												<?php if ($sub['status'] === 'approved'): ?>
													<span class="status-approved-pill">✓ Approved</span>
												<?php elseif ($sub['status'] === 'revision_needed'): ?>
													<span class="status-revision-pill">↺ Revision Needed</span>
												<?php else: ?>
													<span class="status-pending-pill">
														<img src="../assets/icons/clock.svg" width="12" height="12" alt="Pending">
														Pending Review
													</span>
												<?php endif; ?>
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
														<div style="margin-top: 5px; display: flex; align-items: center; gap: 6px;">
															<?php $cStatus = $c['status'] ?? 'published'; ?>
															<?php if ($cStatus === 'published'): ?>
																<span style="font-size: 0.72rem; font-weight: 700; color: #15803d; background-color: #dcfce7; padding: 2px 8px; border-radius: 9999px;">● Live in Catalog</span>
															<?php elseif ($cStatus === 'pending_review'): ?>
																<span style="font-size: 0.72rem; font-weight: 700; color: #b45309; background-color: #fef3c7; padding: 2px 8px; border-radius: 9999px;">⏳ Pending Admin Review</span>
															<?php elseif ($cStatus === 'rejected'): ?>
																<span style="font-size: 0.72rem; font-weight: 700; color: #b91c1c; background-color: #fee2e2; padding: 2px 8px; border-radius: 9999px;">⚠️ Revisions Requested</span>
															<?php endif; ?>
														</div>
														<?php if ($cStatus === 'rejected' && !empty($c['rejection_reason'])): ?>
															<div style="font-size: 0.76rem; color: #dc2626; margin-top: 4px; background-color: #fff1f2; padding: 4px 8px; border-radius: 4px; border-left: 3px solid #e11d48; max-width: 320px;">
																<strong>Admin Feedback:</strong> <?= htmlspecialchars($c['rejection_reason']) ?>
															</div>
														<?php endif; ?>
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
														<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
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
										<th>Status</th>
										<th style="text-align: right;">Action</th>
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
											<td>
												<?php if ($sub['status'] === 'approved'): ?>
													<span class="status-approved-pill">✓ Approved</span>
												<?php elseif ($sub['status'] === 'revision_needed'): ?>
													<span class="status-revision-pill">↺ Revision Needed</span>
												<?php else: ?>
													<span class="status-pending-pill">
														<img src="../assets/icons/clock.svg" width="12" height="12" alt="Pending">
														Pending Review
													</span>
												<?php endif; ?>
											</td>
											<td style="text-align: right;">
												<button type="button" class="btn-review-modal <?= $sub['status'] !== 'pending' ? 'btn-review-modal--outline' : '' ?>" onclick='openReviewModal(<?= json_encode([
													'id' => (int)$sub['id'],
													'student_name' => $sub['student_name'],
													'student_email' => $sub['student_email'],
													'course_title' => $sub['course_title'],
													'submission_type' => $sub['submission_type'],
													'submission_value' => $sub['submission_value'],
													'notes' => $sub['notes'] ?? '',
													'instructor_feedback' => $sub['instructor_feedback'] ?? '',
													'status' => $sub['status'],
													'submitted_at' => date('M d, Y h:i A', strtotime($sub['submitted_at'])),
													'reviewed_at' => !empty($sub['reviewed_at']) ? date('M d, Y h:i A', strtotime($sub['reviewed_at'])) : '',
													'certificate_code' => $sub['certificate_code'] ?? ''
												], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
													<img src="../assets/icons/award.svg" width="13" height="13" alt="Review" <?= $sub['status'] === 'pending' ? 'style="filter: brightness(0) invert(1);"' : '' ?>>
													<span><?= $sub['status'] === 'pending' ? 'Review &amp; Grade' : 'View / Edit Feedback' ?></span>
												</button>
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
							<div style="margin-top: 18px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
								<?php $availBal = (float)($wallet['available_balance'] ?? 0); ?>
								<button type="button" class="btn-withdraw-funds" onclick="openPayoutModal(<?= $availBal ?>)" <?= $availBal < 5.00 ? 'style="opacity: 0.85;"' : '' ?>>
									<img src="../assets/icons/dollar-sign.svg" width="16" height="16" alt="Withdraw" style="filter: brightness(0);">
									<span>Withdraw Funds</span>
									<span class="payout-min-badge">Min. $5.00</span>
								</button>
								<?php if ($availBal < 5.00): ?>
									<span style="font-size: 0.82rem; color: #94a3b8;">
										(Minimum cash-out threshold is $5.00)
									</span>
								<?php endif; ?>
							</div>
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

					<!-- Payout & Cash-out Requests History -->
					<div style="margin-top: 40px;">
						<div class="section-header">
							<div>
								<h3 class="section-title" style="font-size: 1.15rem;">
									<img src="../assets/icons/dollar-sign.svg" width="20" height="20" alt="Payouts">
									Payout &amp; Cash-Out Requests
								</h3>
								<p class="section-subtitle">History of your withdrawal requests and disbursement status.</p>
							</div>
							<button type="button" class="btn-review-modal" onclick="openPayoutModal(<?= (float)($wallet['available_balance'] ?? 0) ?>)" style="padding: 6px 14px; font-size: 0.82rem;">
								<img src="../assets/icons/plus.svg" width="13" height="13" alt="Plus" style="filter: brightness(0) invert(1);">
								<span>New Withdrawal</span>
							</button>
						</div>

						<div class="table-responsive">
							<?php if (!empty($payoutRequests)): ?>
								<table class="data-table">
									<thead>
										<tr>
											<th>Request ID</th>
											<th>Date Requested</th>
											<th>Amount</th>
											<th>Payout Method</th>
											<th>Destination Details</th>
											<th>Reference / Admin Notes</th>
											<th style="text-align: right;">Status</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($payoutRequests as $pr): ?>
											<?php 
												$details = json_decode($pr['payout_details'], true) ?: [];
											?>
											<tr>
												<td><strong>#<?= $pr['id'] ?></strong></td>
												<td><?= date('M d, Y h:i A', strtotime($pr['created_at'])) ?></td>
												<td>
													<strong style="color: #0f172a; font-size: 0.95rem;">
														$<?= number_format((float)$pr['amount'], 2) ?>
													</strong>
												</td>
												<td>
													<?php if ($pr['payout_method'] === 'paypal'): ?>
														<span class="deliverable-badge deliverable-badge--demo" style="background-color: #eff6ff; color: #1d4ed8; border-color: #bfdbfe;">
															PayPal
														</span>
													<?php elseif ($pr['payout_method'] === 'gcash'): ?>
														<span class="deliverable-badge deliverable-badge--file" style="background-color: #ecfdf5; color: #059669; border-color: #a7f3d0;">
															GCash
														</span>
													<?php else: ?>
														<span class="deliverable-badge deliverable-badge--github" style="background-color: #f8fafc; color: #334155; border: 1px solid #cbd5e1;">
															Bank Transfer
														</span>
													<?php endif; ?>
												</td>
												<td>
													<div style="font-size: 0.85rem; color: #334155;">
														<?php if ($pr['payout_method'] === 'paypal'): ?>
															<span><?= htmlspecialchars($details['email'] ?? '—') ?></span>
														<?php elseif ($pr['payout_method'] === 'gcash'): ?>
															<strong><?= htmlspecialchars($details['account_name'] ?? '') ?></strong>
															<div style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($details['mobile_number'] ?? '') ?></div>
														<?php else: ?>
															<strong><?= htmlspecialchars($details['bank_name'] ?? '') ?></strong> - <?= htmlspecialchars($details['account_name'] ?? '') ?>
															<div style="font-size: 0.8rem; color: #64748b;">Acct: <?= htmlspecialchars($details['account_number'] ?? '') ?></div>
														<?php endif; ?>
													</div>
												</td>
												<td>
													<div style="font-size: 0.84rem; color: #475569; max-width: 250px;">
														<?php if (!empty($pr['transaction_reference'])): ?>
															<div style="font-weight: 700; color: #166534;">Ref: <?= htmlspecialchars($pr['transaction_reference']) ?></div>
														<?php endif; ?>
														<?php if (!empty($pr['admin_notes'])): ?>
															<div style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($pr['admin_notes']) ?></div>
														<?php elseif (empty($pr['transaction_reference'])): ?>
															<span style="color: #94a3b8;">Awaiting admin review</span>
														<?php endif; ?>
													</div>
												</td>
												<td style="text-align: right;">
													<?php if ($pr['status'] === 'completed'): ?>
														<span class="status-completed-pill">✓ Disbursed</span>
													<?php elseif ($pr['status'] === 'rejected'): ?>
														<span class="status-rejected-pill">✕ Rejected</span>
													<?php else: ?>
														<span class="status-pending-pill">
															<img src="../assets/icons/clock.svg" width="12" height="12" alt="Pending">
															Pending Review
														</span>
													<?php endif; ?>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							<?php else: ?>
								<div class="empty-state-box" style="text-align: center; padding: 40px 20px; background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 12px;">
									<img src="../assets/icons/award.svg" width="36" height="36" alt="Empty" style="opacity: 0.35; margin-bottom: 12px;">
									<h4 style="margin: 0 0 6px 0; color: #0f172a; font-weight: 800;">No Payout Requests Yet</h4>
									<p style="margin: 0; color: #64748b; font-size: 0.88rem;">Once your balance reaches $5.00, request a cash-out and track your disbursement status here.</p>
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

	<!-- ==========================================================
	     SUBMISSION REVIEW & GRADING MODAL
	     ========================================================== -->
	<div id="reviewModal" class="review-modal-backdrop" style="display: none;" onclick="handleModalBackdropClick(event)">
		<div class="review-modal-dialog">
			<div class="review-modal-header">
				<div class="review-modal-header-text">
					<h3 class="review-modal-title">
						<img src="../assets/icons/award.svg" width="20" height="20" alt="Review">
						Review Student Assessment
					</h3>
					<p class="review-modal-subtitle">Evaluate the learner's deliverable, request revisions, or issue their certificate.</p>
				</div>
				<button type="button" class="review-modal-close" onclick="closeReviewModal()" aria-label="Close modal">&times;</button>
			</div>

			<div class="review-modal-body">
				<!-- Student & Course Meta Info -->
				<div class="review-meta-card">
					<div class="review-meta-item">
						<span class="review-meta-label">Student</span>
						<strong id="modalStudentName" class="review-meta-value">-</strong>
						<span id="modalStudentEmail" class="review-meta-sub">-</span>
					</div>
					<div class="review-meta-item">
						<span class="review-meta-label">Course</span>
						<strong id="modalCourseTitle" class="review-meta-value">-</strong>
					</div>
					<div class="review-meta-item">
						<span class="review-meta-label">Status</span>
						<div id="modalStatusBadge" style="margin-top: 4px;">-</div>
					</div>
					<div class="review-meta-item">
						<span class="review-meta-label">Submitted Date</span>
						<span id="modalSubmittedAt" class="review-meta-value" style="font-size: 0.88rem;">-</span>
					</div>
				</div>

				<!-- Student Deliverable Section -->
				<div class="review-section">
					<label class="review-label">Student Deliverable Link / Upload</label>
					<div class="review-deliverable-box">
						<div class="review-deliverable-left">
							<span id="modalDeliverableTypeBadge" class="deliverable-badge deliverable-badge--github">Repo</span>
							<span id="modalDeliverablePreviewText" style="font-family: monospace; font-size: 0.88rem; color: #334155; word-break: break-all;">-</span>
						</div>
						<div id="modalDeliverableLinkContainer">
							<!-- Dynamic Link / Download button populated via JS -->
						</div>
					</div>
				</div>

				<!-- Student Notes (if any) -->
				<div id="modalStudentNotesWrapper" class="review-section">
					<label class="review-label">Student Notes</label>
					<div id="modalStudentNotes" class="review-notes-box">-</div>
				</div>

				<!-- Grading Form -->
				<form id="reviewGradingForm" method="POST" action="review_submission_function.php">
					<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
					<input type="hidden" name="submission_id" id="modalSubmissionId" value="">
					<input type="hidden" name="action" id="modalActionInput" value="">
					<input type="hidden" name="redirect_to" value="dashboard.php#submissions">

					<div class="review-section">
						<label for="modalInstructorFeedback" class="review-label">
							Instructor Feedback &amp; Review Comments
							<span style="font-weight: 500; color: #64748b; font-size: 0.82rem;">(Visible to student)</span>
						</label>
						<textarea 
							name="instructor_feedback" 
							id="modalInstructorFeedback" 
							rows="4" 
							class="review-textarea" 
							placeholder="Provide encouraging comments, actionable guidance, or explanation if revisions are required..."
						></textarea>
						<div style="font-size: 0.8rem; color: #64748b; margin-top: 4px;">
							Feedback is required when requesting revisions so learners know how to improve.
						</div>
					</div>

					<div class="review-modal-footer">
						<button type="button" class="btn-modal-cancel" onclick="closeReviewModal()">Cancel</button>
						
						<button type="button" class="btn-modal-revision" onclick="submitReviewAction('request_revision')">
							<img src="../assets/icons/alert-circle.svg" width="15" height="15" alt="Revision">
							<span>Request Revision</span>
						</button>

						<button type="button" class="btn-modal-approve" onclick="submitReviewAction('approve')">
							<img src="../assets/icons/check-circle.svg" width="16" height="16" alt="Approve" style="filter: brightness(0) invert(1);">
							<span>Pass &amp; Issue Certificate</span>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Reusable UI & Client-side Helpers -->
	<script src="../assets/js/adsity-ui.js"></script>
	<script>
		function switchTab(tabId) {
			AdsityUI.switchTab(tabId, {
				buttonSelector: '.sidebar-nav-item[data-tab]',
				panelSelector: '.instructor-tab-panel'
			});
		}

		function toggleSidebar() {
			AdsityUI.toggleSidebar('instructorSidebar', 'sidebarBackdrop');
		}

		function closeSidebar() {
			AdsityUI.closeSidebar('instructorSidebar', 'sidebarBackdrop');
		}

		// Modal Logic
		function openReviewModal(data) {
			document.getElementById('modalSubmissionId').value = data.id;
			document.getElementById('modalStudentName').textContent = data.student_name;
			document.getElementById('modalStudentEmail').textContent = data.student_email;
			document.getElementById('modalCourseTitle').textContent = data.course_title;
			document.getElementById('modalSubmittedAt').textContent = data.submitted_at;
			document.getElementById('modalInstructorFeedback').value = data.instructor_feedback || '';

			// Status Badge
			var statusBadgeHtml = '';
			if (data.status === 'approved') {
				statusBadgeHtml = '<span class="status-approved-pill">✓ Approved &amp; Certified</span>';
			} else if (data.status === 'revision_needed') {
				statusBadgeHtml = '<span class="status-revision-pill">↺ Revision Needed</span>';
			} else {
				statusBadgeHtml = '<span class="status-pending-pill"><img src="../assets/icons/clock.svg" width="12" height="12" alt="Pending"> Pending Review</span>';
			}
			document.getElementById('modalStatusBadge').innerHTML = statusBadgeHtml;

			// Deliverable Type & Link
			var typeBadge = document.getElementById('modalDeliverableTypeBadge');
			var linkContainer = document.getElementById('modalDeliverableLinkContainer');
			var previewText = document.getElementById('modalDeliverablePreviewText');

			if (data.submission_type === 'github_repo') {
				typeBadge.className = 'deliverable-badge deliverable-badge--github';
				typeBadge.innerHTML = '<img src="../assets/icons/github.svg" width="12" height="12" alt="GitHub"> GitHub Repo';
				previewText.textContent = data.submission_value;
				linkContainer.innerHTML = '<a href="' + encodeURI(data.submission_value) + '" target="_blank" rel="noopener noreferrer" class="link-deliverable link-deliverable--blue" style="font-weight:700;">Open Repo ↗</a>';
			} else if (data.submission_type === 'file_upload') {
				typeBadge.className = 'deliverable-badge deliverable-badge--file';
				typeBadge.innerHTML = '<img src="../assets/icons/file-text.svg" width="12" height="12" alt="File"> File Upload';
				previewText.textContent = data.submission_value;
				linkContainer.innerHTML = '<a href="../assets/submissions/' + encodeURIComponent(data.submission_value) + '" download class="link-deliverable link-deliverable--green" style="font-weight:700;"><img src="../assets/icons/download.svg" width="14" height="14" alt="Download"> Download ↗</a>';
			} else {
				typeBadge.className = 'deliverable-badge deliverable-badge--demo';
				typeBadge.innerHTML = '<img src="../assets/icons/external-link.svg" width="12" height="12" alt="Live"> Live Demo';
				previewText.textContent = data.submission_value;
				linkContainer.innerHTML = '<a href="' + encodeURI(data.submission_value) + '" target="_blank" rel="noopener noreferrer" class="link-deliverable link-deliverable--purple" style="font-weight:700;">Visit Demo ↗</a>';
			}

			// Notes
			var notesWrapper = document.getElementById('modalStudentNotesWrapper');
			var notesContent = document.getElementById('modalStudentNotes');
			if (data.notes && data.notes.trim().length > 0) {
				notesContent.textContent = data.notes;
				notesWrapper.style.display = 'block';
			} else {
				notesWrapper.style.display = 'none';
			}

			AdsityUI.openModal('reviewModal');
		}

		function closeReviewModal() {
			AdsityUI.closeModal('reviewModal');
		}

		function handleModalBackdropClick(event) {
			AdsityUI.handleBackdropClick(event, 'reviewModal');
		}

		function submitReviewAction(action) {
			var feedback = document.getElementById('modalInstructorFeedback').value.trim();
			if (action === 'request_revision') {
				if (!feedback) {
					alert('Please provide feedback explaining what the student needs to revise before requesting a revision.');
					document.getElementById('modalInstructorFeedback').focus();
					return;
				}
				if (!confirm('Are you sure you want to request a revision for this project? The student will be notified and asked to resubmit.')) {
					return;
				}
			} else if (action === 'approve') {
				if (!confirm('Are you sure you want to approve this project? This will issue the student\'s official course certificate.')) {
					return;
				}
			}

			document.getElementById('modalActionInput').value = action;
			document.getElementById('reviewGradingForm').submit();
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

		// Payout Modal Logic
		function openPayoutModal(availableBalance) {
			if (availableBalance < 5.00) {
				AdsityUI.showToast('Your available balance is $' + availableBalance.toFixed(2) + '. You must have at least $5.00 to request a withdrawal.', 'warning');
				return;
			}
			AdsityUI.openModal('payoutModal');
		}

		function closePayoutModal() {
			AdsityUI.closeModal('payoutModal');
		}

		function handlePayoutModalBackdrop(event) {
			AdsityUI.handleBackdropClick(event, 'payoutModal');
		}

		function setWithdrawAll(amt) {
			var input = document.getElementById('payoutAmountInput');
			if (input) {
				input.value = parseFloat(amt).toFixed(2);
			}
		}

		function selectPayoutMethod(method) {
			['gcash', 'paypal', 'bank_transfer'].forEach(function(m) {
				var card = document.getElementById('card-' + m);
				var fields = document.getElementById('fields-' + m);
				if (card) {
					if (m === method) {
						card.classList.add('active');
						var radio = card.querySelector('input[type="radio"]');
						if (radio) radio.checked = true;
					} else {
						card.classList.remove('active');
					}
				}
				if (fields) {
					fields.style.display = (m === method) ? 'block' : 'none';
				}
			});
		}
	</script>

	<!-- ==========================================================
	     INSTRUCTOR PAYOUT / WITHDRAWAL MODAL
	     ========================================================== -->
	<div id="payoutModal" class="review-modal-backdrop" style="display: none;" onclick="handlePayoutModalBackdrop(event)">
		<div class="review-modal-dialog" style="max-width: 540px;">
			<div class="review-modal-header">
				<div class="review-modal-header-text">
					<h3 class="review-modal-title">
						<img src="../assets/icons/dollar-sign.svg" width="22" height="22" alt="Withdraw">
						Request Funds Withdrawal
					</h3>
					<p class="review-modal-subtitle">Transfer your earned ad revenue to your preferred payout account.</p>
				</div>
				<button type="button" class="review-modal-close" onclick="closePayoutModal()" aria-label="Close modal">&times;</button>
			</div>

			<form action="request_payout_function.php" method="POST" id="payoutForm" class="review-modal-body">
				<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
				<!-- Available Balance Card -->
				<div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between;">
					<div>
						<div style="font-size: 0.76rem; font-weight: 800; color: #15803d; text-transform: uppercase;">Available Balance</div>
						<div style="font-size: 1.5rem; font-weight: 800; color: #166534;" id="payoutModalBalDisplay">$<?= number_format((float)($wallet['available_balance'] ?? 0), 2) ?></div>
					</div>
					<button type="button" onclick="setWithdrawAll(<?= (float)($wallet['available_balance'] ?? 0) ?>)" class="btn-review-modal btn-review-modal--outline" style="font-size: 0.8rem; padding: 6px 12px;">
						Withdraw All
					</button>
				</div>

				<!-- Amount Input -->
				<div class="review-section">
					<label for="payoutAmountInput" class="review-label">
						<span>Withdrawal Amount ($ USD)</span>
						<span style="font-weight: 500; font-size: 0.8rem; color: #64748b;">Minimum $5.00</span>
					</label>
					<input 
						type="number" 
						id="payoutAmountInput" 
						name="amount" 
						step="0.01" 
						min="5.00" 
						max="<?= (float)($wallet['available_balance'] ?? 0) ?>" 
						value="<?= (float)($wallet['available_balance'] ?? 0) >= 5.00 ? number_format((float)($wallet['available_balance'] ?? 0), 2, '.', '') : '5.00' ?>" 
						required 
						class="form-input" 
						style="width: 100%; padding: 12px 14px; border: 1px solid var(--border-light); border-radius: 10px; font-size: 1.05rem; font-weight: 700; box-sizing: border-box;"
					>
				</div>

				<!-- Payout Channel Selection -->
				<div class="review-section">
					<label class="review-label">Select Mode of Transfer / Payout Method</label>
					<div class="payout-methods-grid">
						<label class="payout-method-card active" id="card-gcash" onclick="selectPayoutMethod('gcash')">
							<input type="radio" name="payout_method" value="gcash" checked>
							<div style="font-size: 1.2rem;">📱</div>
							<div class="payout-method-name">GCash</div>
						</label>
						<label class="payout-method-card" id="card-paypal" onclick="selectPayoutMethod('paypal')">
							<input type="radio" name="payout_method" value="paypal">
							<div style="font-size: 1.2rem;">🅿️</div>
							<div class="payout-method-name">PayPal</div>
						</label>
						<label class="payout-method-card" id="card-bank_transfer" onclick="selectPayoutMethod('bank_transfer')">
							<input type="radio" name="payout_method" value="bank_transfer">
							<div style="font-size: 1.2rem;">🏦</div>
							<div class="payout-method-name">Bank Transfer</div>
						</label>
					</div>
				</div>

				<!-- Dynamic Destination Fields -->
				<!-- 1. GCash Fields -->
				<div id="fields-gcash" class="payout-method-fields">
					<div class="review-section" style="margin-bottom: 12px;">
						<label for="gcash_name" class="review-label">GCash Registered Account Name</label>
						<input type="text" id="gcash_name" name="gcash_name" placeholder="Juan Dela Cruz" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 10px; box-sizing: border-box;">
					</div>
					<div class="review-section">
						<label for="gcash_number" class="review-label">GCash Mobile Number</label>
						<input type="text" id="gcash_number" name="gcash_number" placeholder="09171234567" maxlength="13" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 10px; box-sizing: border-box;">
					</div>
				</div>

				<!-- 2. PayPal Fields -->
				<div id="fields-paypal" class="payout-method-fields" style="display: none;">
					<div class="review-section">
						<label for="paypal_email" class="review-label">PayPal Account Email</label>
						<input type="email" id="paypal_email" name="paypal_email" placeholder="instructor@paypal.com" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 10px; box-sizing: border-box;">
					</div>
				</div>

				<!-- 3. Bank Transfer Fields -->
				<div id="fields-bank_transfer" class="payout-method-fields" style="display: none;">
					<div class="review-section" style="margin-bottom: 12px;">
						<label for="bank_name" class="review-label">Bank Name</label>
						<input type="text" id="bank_name" name="bank_name" placeholder="e.g. BDO, BPI, UnionBank, Metrobank..." class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 10px; box-sizing: border-box;">
					</div>
					<div class="review-section" style="margin-bottom: 12px;">
						<label for="bank_account_name" class="review-label">Account Holder Full Name</label>
						<input type="text" id="bank_account_name" name="bank_account_name" placeholder="Account Full Name" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 10px; box-sizing: border-box;">
					</div>
					<div class="review-section">
						<label for="bank_account_number" class="review-label">Account Number</label>
						<input type="text" id="bank_account_number" name="bank_account_number" placeholder="Account Number" class="form-input" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-light); border-radius: 10px; box-sizing: border-box;">
					</div>
				</div>

				<!-- Optional Instructor Notes -->
				<div class="review-section">
					<label for="payoutInstructorNotes" class="review-label">
						Notes for Admin Processor <span style="font-weight: 500; font-size: 0.8rem; color: #64748b;">(Optional)</span>
					</label>
					<textarea id="payoutInstructorNotes" name="instructor_notes" rows="2" placeholder="Any specific payout instructions or notes..." class="review-textarea"></textarea>
				</div>

				<div class="review-modal-footer">
					<button type="button" class="btn-modal-cancel" onclick="closePayoutModal()">Cancel</button>
					<button type="submit" class="btn-modal-approve" style="background-color: var(--primary-green); color: #0b1410;">
						<img src="../assets/icons/dollar-sign.svg" width="16" height="16" alt="Confirm">
						<span>Submit Withdrawal Request</span>
					</button>
				</div>
			</form>
		</div>
	</div>

	<script src="../assets/js/logout_modal.js"></script>
</body>
</html>

