<?php
require_once __DIR__ . '/dashboard_function.php';
$adminInitial = strtoupper(substr($adminProfile['full_name'] ?? 'A', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Control Center - Adsity</title>
	<link rel="stylesheet" href="../style.css">
	<link rel="stylesheet" href="admindashboard.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body class="admin-dashboard-body">

	<!-- ==========================================================
	     TOP HEADER NAVIGATION BAR
	     ========================================================== -->
	<header class="admin-top-navbar">
		<div class="admin-top-left">
			<button type="button" class="admin-sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle Navigation Menu">
				<span></span>
				<span></span>
				<span></span>
			</button>

			<a href="../index.php" class="admin-top-logo" title="Adsity Home">
				<img src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo">
			</a>
			<span class="admin-brand-title">
				<span>Admin Control Center</span>
				<span class="admin-badge-pill">Portal</span>
			</span>
		</div>

		<div class="admin-top-right">
			<button type="button" class="admin-top-btn admin-top-btn--secondary" onclick="openAdminExportModal()">
				<img src="../assets/icons/download.svg" width="14" height="14" alt="Export">
				<span>Export CSV</span>
			</button>
			<a href="../index.php" target="_blank" class="admin-top-btn admin-top-btn--secondary">
				<img src="../assets/icons/external-link.svg" width="14" height="14" alt="Live">
				<span>View Website</span>
			</a>
			<div class="admin-user-pill">
				<div class="admin-user-avatar"><?= $adminInitial ?></div>
				<span class="admin-user-name"><?= htmlspecialchars($adminProfile['full_name']) ?></span>
			</div>
			<a href="../logout.php" class="admin-top-btn admin-top-btn--logout" onclick="event.preventDefault(); openLogoutModal('../logout.php');">
				<img src="../assets/icons/log-out.svg" width="14" height="14" alt="Logout">
				<span>Log Out</span>
			</a>
		</div>
	</header>

	<!-- ==========================================================
	     ADMIN LAYOUT: LEFT NAVBAR SIDEBAR + MAIN CONTENT AREA
	     ========================================================== -->
	<div class="admin-layout">

		<!-- Mobile Overlay Backdrop -->
		<div class="admin-sidebar-overlay" id="sidebarOverlay" onclick="closeSidebarMobile()"></div>

		<!-- ======================================================
		     LEFT SIDEBAR NAVBAR
		     ====================================================== -->
		<aside class="admin-sidebar" id="adminSidebar">
			<div class="admin-sidebar-inner">

				<!-- Admin Profile Card -->
				<div class="admin-profile-box">
					<div class="admin-profile-avatar"><?= $adminInitial ?></div>
					<div class="admin-profile-details">
						<div class="admin-profile-name" title="<?= htmlspecialchars($adminProfile['full_name']) ?>">
							<?= htmlspecialchars($adminProfile['full_name']) ?>
						</div>
						<div class="admin-profile-role">
							<span class="admin-profile-role-dot"></span>
							System Administrator
						</div>
					</div>
				</div>

				<!-- Navigation Links -->
				<nav class="admin-nav-group">
					<div class="admin-nav-label">Management Views</div>

					<button type="button" class="admin-nav-item active" data-tab="overview" onclick="switchTab('overview')">
						<div class="admin-nav-item-left">
							<img src="../assets/icons/layout.svg" class="admin-nav-icon" alt="Overview">
							<span>Dashboard Overview</span>
						</div>
					</button>

					<button type="button" class="admin-nav-item" data-tab="courses" onclick="switchTab('courses')">
						<div class="admin-nav-item-left">
							<img src="../assets/icons/book-open.svg" class="admin-nav-icon" alt="Courses">
							<span>Course Moderation</span>
						</div>
						<span class="admin-nav-badge <?= $pendingCoursesCount > 0 ? 'admin-nav-badge--amber' : '' ?>">
							<?= $pendingCoursesCount > 0 ? $pendingCoursesCount . ' pending' : $totalCourses ?>
						</span>
					</button>

					<button type="button" class="admin-nav-item" data-tab="payouts" onclick="switchTab('payouts')">
						<div class="admin-nav-item-left">
							<img src="../assets/icons/dollar-sign.svg" class="admin-nav-icon" alt="Payouts">
							<span>Cash-Out &amp; Payouts</span>
						</div>
						<span class="admin-nav-badge <?= $pendingPayoutCount > 0 ? 'admin-nav-badge--amber' : '' ?>">
							<?= $pendingPayoutCount ?>
						</span>
					</button>

					<button type="button" class="admin-nav-item" data-tab="ads" onclick="switchTab('ads')">
						<div class="admin-nav-item-left">
							<img src="../assets/icons/video.svg" class="admin-nav-icon" alt="Sponsor Ads">
							<span>Sponsor Ad Engine</span>
						</div>
						<span class="admin-nav-badge" style="background-color: #dcfce7; color: #15803d; font-weight: 700;">
							<?= $activeSponsorAds ?> Active
						</span>
					</button>

					<button type="button" class="admin-nav-item" data-tab="users" onclick="switchTab('users')">
						<div class="admin-nav-item-left">
							<img src="../assets/icons/users.svg" class="admin-nav-icon" alt="Users">
							<span>User Directory</span>
						</div>
						<span class="admin-nav-badge"><?= $totalUsers ?></span>
					</button>

					<button type="button" class="admin-nav-item" data-tab="analytics" onclick="switchTab('analytics')">
						<div class="admin-nav-item-left">
							<img src="../assets/icons/award.svg" class="admin-nav-icon" alt="Certificates">
							<span>Certificates &amp; Logs</span>
						</div>
						<span class="admin-nav-badge"><?= $totalCertificates ?></span>
					</button>
				</nav>

				<!-- Quick Admin Shortcuts -->
				<div class="admin-nav-group" style="margin-top: 4px;">
					<div class="admin-nav-label">Data &amp; Reports</div>

					<button type="button" class="admin-sidebar-action-btn admin-sidebar-action-btn--secondary" onclick="openAdminExportModal()">
						<img src="../assets/icons/download.svg" width="15" height="15" alt="Export">
						<span>Export Platform Data</span>
					</button>
				</div>

			</div>

			<!-- Sidebar Footer -->
			<div class="admin-sidebar-footer">
				<a href="../logout.php" class="admin-top-btn admin-top-btn--logout" style="width: 100%; justify-content: center; padding: 10px 14px;" onclick="event.preventDefault(); openLogoutModal('../logout.php');">
					<img src="../assets/icons/log-out.svg" width="15" height="15" alt="Logout">
					<span>Log Out</span>
				</a>
			</div>
		</aside>

		<!-- ======================================================
		     MAIN CONTENT PANELS
		     ====================================================== -->
		<main class="admin-main-content">

			<!-- Flash Status Alerts -->
			<?php if ($status === 'success'): ?>
				<div class="alert alert--success" style="margin-bottom: 24px;">
					<img src="../assets/icons/check-circle.svg" width="20" height="20" alt="Success">
					<span><?= htmlspecialchars($message ?? 'Operation completed successfully.') ?></span>
				</div>
			<?php elseif ($status === 'error'): ?>
				<div class="alert alert--error" style="margin-bottom: 24px;">
					<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
					<span><?= htmlspecialchars($message ?? 'An error occurred during processing.') ?></span>
				</div>
			<?php endif; ?>

			<!-- ==================================================
			     TAB 1: DASHBOARD OVERVIEW & ANALYTICS
			     ================================================== -->
			<section id="tab-overview" class="admin-tab-panel active">
				<div class="panel-header">
					<div>
						<h1 class="panel-title">Operations &amp; Monetization Overview</h1>
						<p class="panel-subtitle">Platform health metrics, ad impression revenue, cash-out requests, and active learners.</p>
					</div>
					<div class="panel-header-actions">
						<button type="button" class="admin-top-btn admin-top-btn--secondary" onclick="openAdminExportModal()">
							<img src="../assets/icons/download.svg" width="14" height="14" alt="Export">
							<span>Quick Export</span>
						</button>
					</div>
				</div>

				<!-- 6 KPI Metric Cards -->
				<div class="stats-grid-6">
					<div class="stat-card" onclick="switchTab('users')" style="cursor: pointer;">
						<div class="stat-icon-box stat-icon-box--blue">
							<img src="../assets/icons/users.svg" width="26" height="26" alt="Users">
						</div>
						<div>
							<div class="stat-number"><?= number_format($totalUsers) ?></div>
							<div class="stat-label">Total Users</div>
							<div class="stat-subtext"><?= $totalStudents ?> Students &bull; <?= $totalInstructors ?> Teachers</div>
						</div>
					</div>

					<div class="stat-card" onclick="switchTab('courses')" style="cursor: pointer;">
						<div class="stat-icon-box stat-icon-box--purple">
							<img src="../assets/icons/book-open.svg" width="26" height="26" alt="Courses">
						</div>
						<div>
							<div class="stat-number"><?= number_format($totalCourses) ?></div>
							<div class="stat-label">Active Courses</div>
							<div class="stat-subtext"><?= count($courseCategories) ?> Topic Categories</div>
						</div>
					</div>

					<div class="stat-card" onclick="switchTab('analytics')" style="cursor: pointer;">
						<div class="stat-icon-box stat-icon-box--emerald">
							<img src="../assets/icons/video.svg" width="26" height="26" alt="Impressions">
						</div>
						<div>
							<div class="stat-number"><?= number_format($totalAdImpressions) ?></div>
							<div class="stat-label">Ad Impressions</div>
							<div class="stat-subtext">15s Sponsor Sessions Logged</div>
						</div>
					</div>

					<div class="stat-card" onclick="switchTab('analytics')" style="cursor: pointer;">
						<div class="stat-icon-box stat-icon-box--green">
							<img src="../assets/icons/dollar-sign.svg" width="26" height="26" alt="Ad Revenue">
						</div>
						<div>
							<div class="stat-number">$<?= number_format($totalAdRevenue, 2) ?></div>
							<div class="stat-label">Ad Revenue Generated</div>
							<div class="stat-subtext">$0.05 per completed view</div>
						</div>
					</div>

					<div class="stat-card" onclick="switchTab('payouts')" style="cursor: pointer;">
						<div class="stat-icon-box stat-icon-box--amber">
							<img src="../assets/icons/dollar-sign.svg" width="26" height="26" alt="Payouts">
						</div>
						<div>
							<div class="stat-number">$<?= number_format($pendingPayoutAmount, 2) ?></div>
							<div class="stat-label"><?= $pendingPayoutCount ?> Pending Payout<?= $pendingPayoutCount === 1 ? '' : 's' ?></div>
							<div class="stat-subtext">$<?= number_format($totalDisbursed, 2) ?> Total Disbursed</div>
						</div>
					</div>

					<div class="stat-card" onclick="switchTab('analytics')" style="cursor: pointer;">
						<div class="stat-icon-box stat-icon-box--indigo">
							<img src="../assets/icons/award.svg" width="26" height="26" alt="Certificates">
						</div>
						<div>
							<div class="stat-number"><?= number_format($totalCertificates) ?></div>
							<div class="stat-label">Certificates Issued</div>
							<div class="stat-subtext">Verified Platform Graduates</div>
						</div>
					</div>
				</div>

				<!-- Platform Health & Launchpad Grid -->
				<div class="platform-health-grid">
					<!-- Learning & Completion Rate -->
					<div class="health-card">
						<div class="health-card-header">
							<h3 class="health-card-title">
								<img src="../assets/icons/award.svg" width="20" height="20" alt="Learning">
								Learning &amp; Completion Rate
							</h3>
							<span class="badge-count" style="font-weight: 800; color: #15803d; background-color: #dcfce7;">
								<?= $platformCompletionRate ?>% Rate
							</span>
						</div>

						<div class="progress-bar-container">
							<div class="progress-bar-fill" style="width: <?= min(100, max(5, $platformCompletionRate)) ?>%;"></div>
						</div>

						<div>
							<div class="health-metric-row">
								<span class="health-metric-label">Total Course Enrollments</span>
								<span class="health-metric-value"><?= number_format($totalEnrollments) ?></span>
							</div>
							<div class="health-metric-row">
								<span class="health-metric-label">Completed Courses</span>
								<span class="health-metric-value"><?= number_format($completedEnrollments) ?></span>
							</div>
							<div class="health-metric-row">
								<span class="health-metric-label">Verified Certificates Issued</span>
								<span class="health-metric-value"><?= number_format($totalCertificates) ?></span>
							</div>
						</div>
					</div>

					<!-- Financial Overview -->
					<div class="health-card">
						<div class="health-card-header">
							<h3 class="health-card-title">
								<img src="../assets/icons/dollar-sign.svg" width="20" height="20" alt="Financial">
								Financial &amp; Disbursement Summary
							</h3>
							<span class="badge-count">$<?= number_format($totalDisbursed, 2) ?> Paid</span>
						</div>

						<div>
							<div class="health-metric-row">
								<span class="health-metric-label">Total Ad Revenue Credited</span>
								<span class="health-metric-value" style="color: #16a34a;">$<?= number_format($totalAdRevenue, 2) ?></span>
							</div>
							<div class="health-metric-row">
								<span class="health-metric-label">Total Disbursed to Instructors</span>
								<span class="health-metric-value">$<?= number_format($totalDisbursed, 2) ?></span>
							</div>
							<div class="health-metric-row">
								<span class="health-metric-label">Awaiting Payout Review</span>
								<span class="health-metric-value" style="color: #d97706;">$<?= number_format($pendingPayoutAmount, 2) ?> (<?= $pendingPayoutCount ?>)</span>
							</div>
						</div>
					</div>

					<!-- Quick Action Launchpad -->
					<div class="health-card">
						<div class="health-card-header">
							<h3 class="health-card-title">
								<img src="../assets/icons/layout.svg" width="20" height="20" alt="Launchpad">
								Admin Control Launchpad
							</h3>
						</div>
						<div class="launchpad-grid" style="grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));">
							<button type="button" class="launchpad-btn" onclick="switchTab('courses')">
								<img src="../assets/icons/book-open.svg" width="20" height="20" alt="Courses">
								<span>Moderate Courses</span>
								<?php if ($pendingCoursesCount > 0): ?>
									<span class="badge-count" style="background-color: #fef3c7; color: #b45309; font-size: 0.7rem; margin-top: 2px;"><?= $pendingCoursesCount ?> Pending</span>
								<?php endif; ?>
							</button>
							<button type="button" class="launchpad-btn" onclick="switchTab('ads')">
								<img src="../assets/icons/video.svg" width="20" height="20" alt="Sponsor Ads">
								<span>Sponsor Ad Engine</span>
							</button>
							<button type="button" class="launchpad-btn" onclick="switchTab('analytics')">
								<img src="../assets/icons/award.svg" width="20" height="20" alt="Certificates">
								<span>Certificate Registry</span>
							</button>
							<button type="button" class="launchpad-btn" onclick="switchTab('payouts')">
								<img src="../assets/icons/dollar-sign.svg" width="20" height="20" alt="Payouts">
								<span>Review Payouts</span>
							</button>
							<button type="button" class="launchpad-btn" onclick="switchTab('users')">
								<img src="../assets/icons/users.svg" width="20" height="20" alt="Users">
								<span>User Directory</span>
							</button>
							<button type="button" class="launchpad-btn" onclick="openAdminExportModal()">
								<img src="../assets/icons/download.svg" width="20" height="20" alt="Export">
								<span>Export CSV</span>
							</button>
						</div>
					</div>
				</div>

				<!-- Recent Payout Requests Preview -->
				<div class="dashboard-section">
					<div class="section-heading">
						<h2>
							<img src="../assets/icons/dollar-sign.svg" width="22" height="22" alt="Payouts">
							Recent Instructor Cash-Out Requests
						</h2>
						<button type="button" class="btn-table-action btn-table-action--preview" onclick="switchTab('payouts')">
							View All (<?= count($payoutRequests) ?>) &rarr;
						</button>
					</div>

					<div class="table-responsive">
						<?php 
						$recentPayoutSlice = array_slice($payoutRequests, 0, 5);
						if (!empty($recentPayoutSlice)): 
						?>
							<table class="data-table">
								<thead>
									<tr>
										<th>Request ID</th>
										<th>Instructor</th>
										<th>Amount</th>
										<th>Method</th>
										<th>Status</th>
										<th>Date</th>
										<th style="text-align: right;">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($recentPayoutSlice as $pr): ?>
										<?php $details = json_decode($pr['payout_details'], true) ?: []; ?>
										<tr>
											<td><strong>#<?= $pr['id'] ?></strong></td>
											<td>
												<strong><?= htmlspecialchars($pr['instructor_name']) ?></strong>
												<div style="font-size: 0.78rem; color: #64748b;"><?= htmlspecialchars($pr['instructor_email']) ?></div>
											</td>
											<td><strong style="color: #0f172a;">$<?= number_format((float)$pr['amount'], 2) ?></strong></td>
											<td>
												<span class="category-tag"><?= strtoupper($pr['payout_method']) ?></span>
											</td>
											<td>
												<?php if ($pr['status'] === 'completed'): ?>
													<span class="status-badge-completed">✓ Disbursed</span>
												<?php elseif ($pr['status'] === 'rejected'): ?>
													<span class="status-badge-rejected">✕ Rejected</span>
												<?php else: ?>
													<span class="status-badge-pending">⏳ Pending</span>
												<?php endif; ?>
											</td>
											<td><?= date('M d, Y', strtotime($pr['created_at'])) ?></td>
											<td style="text-align: right;">
												<button type="button" class="btn-process-payout <?= $pr['status'] !== 'pending' ? 'btn-process-payout--view' : '' ?>" onclick='openAdminPayoutModal(<?= json_encode([
													'id' => (int)$pr['id'],
													'instructor_name' => $pr['instructor_name'],
													'instructor_email' => $pr['instructor_email'],
													'amount' => number_format((float)$pr['amount'], 2),
													'payout_method' => $pr['payout_method'],
													'payout_details' => $details,
													'instructor_notes' => $pr['instructor_notes'] ?? '',
													'admin_notes' => $pr['admin_notes'] ?? '',
													'transaction_reference' => $pr['transaction_reference'] ?? '',
													'status' => $pr['status'],
													'created_at' => date('M d, Y h:i A', strtotime($pr['created_at'])),
													'processed_at' => !empty($pr['processed_at']) ? date('M d, Y h:i A', strtotime($pr['processed_at'])) : '',
													'processed_by_name' => $pr['processed_by_name'] ?? ''
												], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
													<span><?= $pr['status'] === 'pending' ? 'Review' : 'View' ?></span>
												</button>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<div class="empty-state">No withdrawal requests submitted yet.</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- Recent Verified Certificates Preview -->
				<div class="dashboard-section">
					<div class="section-heading">
						<h2>
							<img src="../assets/icons/award.svg" width="22" height="22" alt="Certificates">
							Recent Verified Certificates Issued
						</h2>
						<button type="button" class="btn-table-action btn-table-action--preview" onclick="switchTab('analytics')">
							View All (<?= $totalCertificates ?>) &rarr;
						</button>
					</div>

					<div class="table-responsive">
						<?php 
						$recentCertSlice = array_slice($recentCertificates, 0, 5);
						if (!empty($recentCertSlice)): 
						?>
							<table class="data-table">
								<thead>
									<tr>
										<th>Certificate ID</th>
										<th>Student Name</th>
										<th>Course Completed</th>
										<th>Instructor</th>
										<th>Issue Date</th>
										<th style="text-align: right;">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($recentCertSlice as $cert): ?>
										<tr>
											<td><code style="font-family: monospace; font-size: 0.88rem; font-weight: 700; color: #0284c7;"><?= htmlspecialchars($cert['certificate_code']) ?></code></td>
											<td>
												<strong><?= htmlspecialchars($cert['student_name']) ?></strong>
												<div style="font-size: 0.78rem; color: #64748b;"><?= htmlspecialchars($cert['student_email']) ?></div>
											</td>
											<td><strong><?= htmlspecialchars($cert['course_title']) ?></strong></td>
											<td><?= htmlspecialchars($cert['instructor_name'] ?? 'Instructor') ?></td>
											<td><?= date('M d, Y', strtotime($cert['issued_at'])) ?></td>
											<td style="text-align: right;">
												<a href="../student/certificate.php?code=<?= urlencode($cert['certificate_code']) ?>" target="_blank" class="btn-table-action btn-table-action--verify">
													<img src="../assets/icons/external-link.svg" width="13" height="13" alt="Verify">
													<span>Verify</span>
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<div class="empty-state">No certificates issued yet. Students receive verifiable certificates upon course project approval.</div>
						<?php endif; ?>
					</div>
				</div>
			</section>

			<!-- ==================================================
			     TAB 2: COURSE OVERSIGHT & MODERATION
			     ================================================== -->
			<section id="tab-courses" class="admin-tab-panel">
				<div class="panel-header">
					<div>
						<h1 class="panel-title">Course Catalog &amp; Moderation</h1>
						<p class="panel-subtitle">Review multi-video curricula, monitor enrollment counts, and moderate published courses.</p>
					</div>
					<div class="panel-header-actions">
						<a href="../courses.php" target="_blank" class="admin-top-btn admin-top-btn--secondary">
							<img src="../assets/icons/external-link.svg" width="14" height="14" alt="Explore">
							<span>Public Catalog</span>
						</a>
						<a href="export_data.php?type=courses" class="admin-top-btn admin-top-btn--secondary">
							<img src="../assets/icons/download.svg" width="14" height="14" alt="Export">
							<span>Export Courses</span>
						</a>
					</div>
				</div>

				<div class="dashboard-section">
					<!-- Course Status Filter Pills -->
					<div class="user-role-filter-group" style="margin-bottom: 16px;">
						<button type="button" class="filter-pill course-status-pill active" onclick="filterCoursesByStatus('all')">
							All Courses (<?= $totalCourses ?>)
						</button>
						<button type="button" class="filter-pill course-status-pill" onclick="filterCoursesByStatus('pending_review')">
							Pending Review (<?= $pendingCoursesCount ?>)
						</button>
						<button type="button" class="filter-pill course-status-pill" onclick="filterCoursesByStatus('published')">
							Published (<?= $publishedCoursesCount ?>)
						</button>
						<button type="button" class="filter-pill course-status-pill" onclick="filterCoursesByStatus('rejected')">
							Revisions Needed (<?= $rejectedCoursesCount ?>)
						</button>
					</div>

					<!-- Search and Category Filter Toolbar -->
					<div class="table-toolbar">
						<div class="table-toolbar-left">
							<input type="text" id="courseSearchInput" placeholder="Search by course title or instructor..." class="table-search-input" onkeyup="filterCoursesTable()">
							<select id="courseCategoryFilter" class="table-select-filter" onchange="filterCoursesTable()">
								<option value="">All Categories</option>
								<?php foreach ($courseCategories as $cat): ?>
									<option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="badge-count" id="coursesCountDisplay"><?= count($allCourses) ?> Total Courses</div>
					</div>

					<!-- Courses Table -->
					<div class="table-responsive">
						<?php if (!empty($allCourses)): ?>
							<table class="data-table" id="adminCoursesTable">
								<thead>
									<tr>
										<th>Course</th>
										<th>Instructor</th>
										<th>Category</th>
										<th>Status</th>
										<th>Curriculum</th>
										<th>Enrolled</th>
										<th>Graduates</th>
										<th>Ad Revenue</th>
										<th>Created</th>
										<th style="text-align: right;">Moderation</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($allCourses as $course): ?>
										<tr class="course-row" data-title="<?= htmlspecialchars(strtolower($course['title'])) ?>" data-instructor="<?= htmlspecialchars(strtolower($course['instructor_name'] ?? '')) ?>" data-category="<?= htmlspecialchars($course['category'] ?? '') ?>" data-status="<?= htmlspecialchars($course['status'] ?? 'published') ?>">
											<td>
												<div style="display: flex; align-items: center; gap: 12px;">
													<?php if (!empty($course['thumbnail'])): ?>
														<img src="../<?= htmlspecialchars($course['thumbnail']) ?>" alt="Thumb" style="width: 44px; height: 32px; border-radius: 6px; object-fit: cover; border: 1px solid #e2e8f0;">
													<?php else: ?>
														<div style="width: 44px; height: 32px; border-radius: 6px; background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: #94a3b8; font-weight: 700;">VID</div>
													<?php endif; ?>
													<div>
														<strong style="color: #0f172a; font-size: 0.95rem; display: block; max-width: 230px; line-height: 1.3;">
															<?= htmlspecialchars($course['title']) ?>
														</strong>
														<span style="font-size: 0.75rem; color: #64748b;">ID #<?= $course['id'] ?></span>
													</div>
												</div>
											</td>
											<td>
												<strong><?= htmlspecialchars($course['instructor_name'] ?? 'Unassigned') ?></strong>
												<div style="font-size: 0.76rem; color: #64748b;"><?= htmlspecialchars($course['instructor_email'] ?? '—') ?></div>
											</td>
											<td>
												<span class="category-tag"><?= htmlspecialchars($course['category'] ?? 'General') ?></span>
											</td>
											<td>
												<?php $cStatus = $course['status'] ?? 'published'; ?>
												<span class="status-badge-<?= htmlspecialchars($cStatus) ?>">
													<?= $cStatus === 'pending_review' ? 'In Review' : ucfirst($cStatus) ?>
												</span>
												<?php if (!empty($course['rejection_reason'])): ?>
													<div style="font-size: 0.72rem; color: #dc2626; margin-top: 3px; max-width: 140px; line-height: 1.2;" title="<?= htmlspecialchars($course['rejection_reason']) ?>">
														<?= htmlspecialchars(mb_strimwidth($course['rejection_reason'], 0, 26, '...')) ?>
													</div>
												<?php endif; ?>
											</td>
											<td>
												<strong style="color: #0f172a;"><?= $course['actual_lessons_count'] ?></strong>
												<span style="color: #94a3b8; font-size: 0.78rem;">/ <?= $course['total_lessons'] ?> lessons</span>
											</td>
											<td>
												<span class="user-badge-student"><?= number_format($course['total_enrolled']) ?></span>
											</td>
											<td>
												<span class="badge-count" style="font-weight: 800;"><?= number_format($course['total_graduates']) ?></span>
											</td>
											<td>
												<strong style="color: #16a34a;">$<?= number_format((float)$course['course_ad_revenue'], 2) ?></strong>
											</td>
											<td><?= date('M d, Y', strtotime($course['created_at'])) ?></td>
											<td style="text-align: right;">
												<div style="display: inline-flex; align-items: center; gap: 6px;">
													<?php if (($course['status'] ?? 'published') !== 'published'): ?>
														<form method="POST" action="moderate_course.php" style="display: inline;">
															<input type="hidden" name="course_id" value="<?= (int)$course['id'] ?>">
															<input type="hidden" name="action" value="approve">
															<button type="submit" class="btn-table-action btn-table-action--approve" title="Approve & Publish to Public Catalog">
																<img src="../assets/icons/check-circle.svg" width="13" height="13" alt="Approve">
																<span>Approve</span>
															</button>
														</form>
													<?php endif; ?>

													<?php if (($course['status'] ?? 'published') !== 'rejected'): ?>
														<button type="button" class="btn-table-action btn-table-action--reject" onclick="openAdminRejectCourseModal(<?= (int)$course['id'] ?>, '<?= htmlspecialchars(addslashes($course['title'])) ?>')" title="Request Revisions / Reject">
															<img src="../assets/icons/alert-circle.svg" width="13" height="13" alt="Reject">
															<span>Reject</span>
														</button>
													<?php endif; ?>

													<a href="../course_details.php?id=<?= $course['id'] ?>" target="_blank" class="btn-table-action btn-table-action--preview" title="Preview Public Page">
														<img src="../assets/icons/eye.svg" width="13" height="13" alt="View">
														<span>Preview</span>
													</a>
													<button type="button" class="btn-table-action btn-table-action--delete" onclick="openAdminDeleteCourseModal(<?= (int)$course['id'] ?>, '<?= htmlspecialchars(addslashes($course['title'])) ?>')" title="Delete Course">
														<img src="../assets/icons/trash.svg" width="13" height="13" alt="Delete">
														<span>Delete</span>
													</button>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<div class="empty-state">No courses published on Adsity yet.</div>
						<?php endif; ?>
					</div>
				</div>
			</section>

			<!-- ==================================================
			     TAB 3: CASH-OUT & PAYOUT REQUESTS
			     ================================================== -->
			<section id="tab-payouts" class="admin-tab-panel">
				<div class="panel-header">
					<div>
						<h1 class="panel-title">Instructor Cash-Out &amp; Payout Requests</h1>
						<p class="panel-subtitle">Review withdrawal requests, verify destination accounts, and disburse ad revenue shares.</p>
					</div>
					<div class="panel-header-actions">
						<a href="export_data.php?type=payouts" class="admin-top-btn admin-top-btn--secondary">
							<img src="../assets/icons/download.svg" width="14" height="14" alt="Export">
							<span>Export Payouts</span>
						</a>
					</div>
				</div>

				<div class="dashboard-section">
					<!-- Payout Filter Toolbar -->
					<div class="table-toolbar">
						<div class="filter-pill-group">
							<button type="button" class="filter-pill active" onclick="filterPayoutsTable('all')">All Requests</button>
							<button type="button" class="filter-pill" onclick="filterPayoutsTable('pending')">Pending Review (<?= $pendingPayoutCount ?>)</button>
							<button type="button" class="filter-pill" onclick="filterPayoutsTable('completed')">Disbursed</button>
							<button type="button" class="filter-pill" onclick="filterPayoutsTable('rejected')">Rejected</button>
						</div>
						<div class="badge-count" style="background-color: #fef3c7; color: #b45309; font-weight: 800;">
							$<?= number_format($pendingPayoutAmount, 2) ?> Pending Review
						</div>
					</div>

					<div class="table-responsive">
						<?php if (!empty($payoutRequests)): ?>
							<table class="data-table" id="adminPayoutsTable">
								<thead>
									<tr>
										<th>Request ID</th>
										<th>Instructor</th>
										<th>Amount</th>
										<th>Transfer Mode</th>
										<th>Destination Account</th>
										<th>Requested Date</th>
										<th>Status</th>
										<th style="text-align: right;">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($payoutRequests as $pr): ?>
										<?php $details = json_decode($pr['payout_details'], true) ?: []; ?>
										<tr class="payout-row" data-status="<?= htmlspecialchars($pr['status']) ?>">
											<td><strong>#<?= $pr['id'] ?></strong></td>
											<td>
												<strong><?= htmlspecialchars($pr['instructor_name']) ?></strong>
												<div style="font-size: 0.78rem; color: #64748b;"><?= htmlspecialchars($pr['instructor_email']) ?></div>
											</td>
											<td>
												<strong style="font-size: 1.05rem; color: #0f172a;">
													$<?= number_format((float)$pr['amount'], 2) ?>
												</strong>
											</td>
											<td>
												<?php if ($pr['payout_method'] === 'gcash'): ?>
													<span class="user-badge-student" style="background-color: #ecfdf5; color: #059669; font-weight: 800;">📱 GCash</span>
												<?php elseif ($pr['payout_method'] === 'paypal'): ?>
													<span class="user-badge-instructor" style="font-weight: 800;">🅿️ PayPal</span>
												<?php else: ?>
													<span class="badge-count" style="font-weight: 800;">🏦 Bank Transfer</span>
												<?php endif; ?>
											</td>
											<td>
												<div style="font-size: 0.85rem; color: #334155;">
													<?php if ($pr['payout_method'] === 'paypal'): ?>
														<span><?= htmlspecialchars($details['email'] ?? '—') ?></span>
													<?php elseif ($pr['payout_method'] === 'gcash'): ?>
														<strong><?= htmlspecialchars($details['account_name'] ?? '') ?></strong>
														<div style="font-size: 0.78rem; color: #64748b;"><?= htmlspecialchars($details['mobile_number'] ?? '') ?></div>
													<?php else: ?>
														<strong><?= htmlspecialchars($details['bank_name'] ?? '') ?></strong>
														<div style="font-size: 0.78rem; color: #64748b;"><?= htmlspecialchars($details['account_name'] ?? '') ?> (<?= htmlspecialchars($details['account_number'] ?? '') ?>)</div>
													<?php endif; ?>
												</div>
											</td>
											<td><?= date('M d, Y h:i A', strtotime($pr['created_at'])) ?></td>
											<td>
												<?php if ($pr['status'] === 'completed'): ?>
													<span class="status-badge-completed">✓ Disbursed</span>
												<?php elseif ($pr['status'] === 'rejected'): ?>
													<span class="status-badge-rejected">✕ Rejected</span>
												<?php else: ?>
													<span class="status-badge-pending">⏳ Pending Review</span>
												<?php endif; ?>
											</td>
											<td style="text-align: right;">
												<button type="button" class="btn-process-payout <?= $pr['status'] !== 'pending' ? 'btn-process-payout--view' : '' ?>" onclick='openAdminPayoutModal(<?= json_encode([
													'id' => (int)$pr['id'],
													'instructor_name' => $pr['instructor_name'],
													'instructor_email' => $pr['instructor_email'],
													'amount' => number_format((float)$pr['amount'], 2),
													'payout_method' => $pr['payout_method'],
													'payout_details' => $details,
													'instructor_notes' => $pr['instructor_notes'] ?? '',
													'admin_notes' => $pr['admin_notes'] ?? '',
													'transaction_reference' => $pr['transaction_reference'] ?? '',
													'status' => $pr['status'],
													'created_at' => date('M d, Y h:i A', strtotime($pr['created_at'])),
													'processed_at' => !empty($pr['processed_at']) ? date('M d, Y h:i A', strtotime($pr['processed_at'])) : '',
													'processed_by_name' => $pr['processed_by_name'] ?? ''
												], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
													<span><?= $pr['status'] === 'pending' ? 'Review &amp; Process' : 'View Details' ?></span>
												</button>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<div class="empty-state">No instructor withdrawal requests submitted yet.</div>
						<?php endif; ?>
					</div>
				</div>
			</section>

			<!-- ==================================================
			     TAB 4: USER DIRECTORY & MANAGEMENT
			     ================================================== -->
			<section id="tab-users" class="admin-tab-panel">
				<div class="panel-header">
					<div>
						<h1 class="panel-title">User Management &amp; Directory</h1>
						<p class="panel-subtitle">Manage student accounts, verify instructor studio profiles, and assign administrative roles.</p>
					</div>
					<div class="panel-header-actions">
						<a href="export_data.php?type=users" class="admin-top-btn admin-top-btn--secondary">
							<img src="../assets/icons/download.svg" width="14" height="14" alt="Export">
							<span>Export Users</span>
						</a>
					</div>
				</div>

				<div class="dashboard-section">
					<!-- Search & Role Filter Toolbar -->
					<div class="table-toolbar">
						<div class="table-toolbar-left">
							<input type="text" id="userSearchInput" placeholder="Search users by name or email..." class="table-search-input" onkeyup="filterUsersTable()">
							<div class="filter-pill-group">
								<button type="button" class="filter-pill active" onclick="filterUsersByRole('all')">All Users (<?= $totalUsers ?>)</button>
								<button type="button" class="filter-pill" onclick="filterUsersByRole('instructor')">Teachers (<?= $totalInstructors ?>)</button>
								<button type="button" class="filter-pill" onclick="filterUsersByRole('student')">Students (<?= $totalStudents ?>)</button>
								<button type="button" class="filter-pill" onclick="filterUsersByRole('admin')">Admins (<?= $totalAdmins ?>)</button>
							</div>
						</div>
						<div class="badge-count" id="usersCountDisplay"><?= $totalUsers ?> Total Accounts</div>
					</div>

					<div class="table-responsive">
						<table class="data-table" id="adminUsersTable">
							<thead>
								<tr>
									<th>User</th>
									<th>Email Address</th>
									<th>Role</th>
									<th>Activity Summary</th>
									<th>Joined Date</th>
									<th style="text-align: right;">Actions</th>
								</tr>
							</thead>
							<tbody>
								<!-- Administrators -->
								<?php foreach ($adminsList as $adm): ?>
									<tr class="user-row" data-role="admin" data-name="<?= htmlspecialchars(strtolower($adm['full_name'])) ?>" data-email="<?= htmlspecialchars(strtolower($adm['email'])) ?>">
										<td>
											<strong style="color: #0f172a;"><?= htmlspecialchars($adm['full_name']) ?></strong>
											<div style="font-size: 0.75rem; color: #64748b;">ID #<?= $adm['id'] ?></div>
										</td>
										<td><?= htmlspecialchars($adm['email']) ?></td>
										<td><span class="user-badge-admin">Admin</span></td>
										<td><span style="color: #64748b; font-size: 0.85rem;">System Management Access</span></td>
										<td><?= date('M d, Y', strtotime($adm['created_at'])) ?></td>
										<td style="text-align: right;">
											<button type="button" class="btn-table-action btn-table-action--inspect" onclick='openAdminUserInspectModal(<?= json_encode([
												'id' => (int)$adm['id'],
												'name' => $adm['full_name'],
												'email' => $adm['email'],
												'role' => 'Administrator',
												'created_at' => date('M d, Y', strtotime($adm['created_at'])),
												'extra' => ['Access' => 'Full Administrative Access']
											], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
												Inspect
											</button>
										</td>
									</tr>
								<?php endforeach; ?>

								<!-- Instructors -->
								<?php foreach ($instructors as $inst): ?>
									<tr class="user-row" data-role="instructor" data-name="<?= htmlspecialchars(strtolower($inst['full_name'])) ?>" data-email="<?= htmlspecialchars(strtolower($inst['email'])) ?>">
										<td>
											<strong style="color: #0f172a;"><?= htmlspecialchars($inst['full_name']) ?></strong>
											<div style="font-size: 0.75rem; color: #64748b;">ID #<?= $inst['id'] ?></div>
										</td>
										<td><?= htmlspecialchars($inst['email']) ?></td>
										<td><span class="user-badge-instructor">Instructor</span></td>
										<td>
											<span style="font-weight: 700; color: #0284c7;"><?= $inst['courses_count'] ?> Courses</span> &bull; 
											<span style="color: #16a34a; font-weight: 700;">$<?= number_format((float)$inst['available_balance'], 2) ?> Balance</span>
										</td>
										<td><?= date('M d, Y', strtotime($inst['created_at'])) ?></td>
										<td style="text-align: right;">
											<div style="display: inline-flex; align-items: center; gap: 6px;">
												<button type="button" class="btn-table-action btn-table-action--inspect" onclick='openAdminUserInspectModal(<?= json_encode([
													'id' => (int)$inst['id'],
													'name' => $inst['full_name'],
													'email' => $inst['email'],
													'role' => 'Instructor',
													'created_at' => date('M d, Y', strtotime($inst['created_at'])),
													'extra' => [
														'Courses Published' => $inst['courses_count'],
														'Available Balance' => '$' . number_format((float)$inst['available_balance'], 2),
														'Total Ad Earnings' => '$' . number_format((float)$inst['total_earned'], 2),
														'Total Withdrawn' => '$' . number_format((float)$inst['total_withdrawn'], 2)
													]
												], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
													Inspect
												</button>

												<button type="button" class="btn-table-action btn-table-action--role" onclick="openAdminChangeRoleModal(<?= (int)$inst['id'] ?>, '<?= htmlspecialchars(addslashes($inst['full_name'])) ?>', 2)">
													Role
												</button>

												<form method="POST" action="delete_user.php" onsubmit="return confirm('Are you sure you want to delete instructor <?= htmlspecialchars(addslashes($inst['full_name'])) ?>?');" style="display: inline;">
													<input type="hidden" name="user_id" value="<?= htmlspecialchars($inst['id']) ?>">
													<button type="submit" name="delete_user" class="btn-table-action btn-table-action--delete">
														<img src="../assets/icons/trash.svg" width="12" height="12" alt="Delete">
													</button>
												</form>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>

								<!-- Students -->
								<?php foreach ($students as $stu): ?>
									<tr class="user-row" data-role="student" data-name="<?= htmlspecialchars(strtolower($stu['full_name'])) ?>" data-email="<?= htmlspecialchars(strtolower($stu['email'])) ?>">
										<td>
											<strong style="color: #0f172a;"><?= htmlspecialchars($stu['full_name']) ?></strong>
											<div style="font-size: 0.75rem; color: #64748b;">ID #<?= $stu['id'] ?></div>
										</td>
										<td><?= htmlspecialchars($stu['email']) ?></td>
										<td><span class="user-badge-student">Student</span></td>
										<td>
											<span style="font-weight: 700; color: #334155;"><?= $stu['enrolled_count'] ?> Enrolled</span> &bull; 
											<span style="color: #15803d; font-weight: 700;"><?= $stu['certs_count'] ?> Certificates</span>
										</td>
										<td><?= date('M d, Y', strtotime($stu['created_at'])) ?></td>
										<td style="text-align: right;">
											<div style="display: inline-flex; align-items: center; gap: 6px;">
												<button type="button" class="btn-table-action btn-table-action--inspect" onclick='openAdminUserInspectModal(<?= json_encode([
													'id' => (int)$stu['id'],
													'name' => $stu['full_name'],
													'email' => $stu['email'],
													'role' => 'Student',
													'created_at' => date('M d, Y', strtotime($stu['created_at'])),
													'extra' => [
														'Enrolled Courses' => $stu['enrolled_count'],
														'Verified Certificates' => $stu['certs_count']
													]
												], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
													Inspect
												</button>

												<button type="button" class="btn-table-action btn-table-action--role" onclick="openAdminChangeRoleModal(<?= (int)$stu['id'] ?>, '<?= htmlspecialchars(addslashes($stu['full_name'])) ?>', 3)">
													Role
												</button>

												<form method="POST" action="delete_user.php" onsubmit="return confirm('Are you sure you want to delete student <?= htmlspecialchars(addslashes($stu['full_name'])) ?>?');" style="display: inline;">
													<input type="hidden" name="user_id" value="<?= htmlspecialchars($stu['id']) ?>">
													<button type="submit" name="delete_user" class="btn-table-action btn-table-action--delete">
														<img src="../assets/icons/trash.svg" width="12" height="12" alt="Delete">
													</button>
												</form>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</section>

			<!-- ==================================================
			     TAB 5: SPONSOR ADS & MONETIZATION ENGINE
			     ================================================== -->
			<section id="tab-ads" class="admin-tab-panel">
				<div class="panel-header">
					<div>
						<h1 class="panel-title">Sponsor Ad Engine &amp; Monetization</h1>
						<p class="panel-subtitle">Configure sponsor video campaigns, Cost-Per-Mille (CPM) rates, and instructor revenue-share splits.</p>
					</div>
					<div class="panel-header-actions">
						<button type="button" class="admin-top-btn admin-top-btn--primary" onclick="openAdminNewAdModal()">
							<img src="../assets/icons/plus.svg" width="14" height="14" alt="New Ad" style="filter: brightness(0) invert(1);">
							<span>+ New Sponsor Ad</span>
						</button>
						<a href="export_data.php?type=ad_logs" class="admin-top-btn admin-top-btn--secondary">
							<img src="../assets/icons/download.svg" width="14" height="14" alt="Export">
							<span>Export Ad Logs</span>
						</a>
					</div>
				</div>

				<!-- Monetization Engine Platform Settings Card -->
				<div class="dashboard-section" style="margin-bottom: 24px;">
					<div class="section-heading">
						<h2>
							<img src="../assets/icons/dollar-sign.svg" width="22" height="22" alt="Rules">
							Platform Monetization &amp; Revenue-Share Split
						</h2>
						<span class="badge-count" style="background-color: #ecfdf5; color: #15803d; font-weight: 800;">
							Current Default: $<?= number_format((float)($platformSettings['default_ad_cpm'] ?? 0.05), 4) ?> CPM
						</span>
					</div>

					<form method="POST" action="manage_ads.php" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px;">
						<input type="hidden" name="action" value="update_settings">
						<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; align-items: flex-end;">
							<div>
								<label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
									Default CPM Rate ($)
								</label>
								<input type="number" step="0.001" min="0.001" name="default_ad_cpm" value="<?= htmlspecialchars($platformSettings['default_ad_cpm'] ?? '0.0500') ?>" required class="table-search-input" style="width: 100%; height: 42px; font-weight: 700;">
								<small style="color: #64748b; font-size: 0.72rem;">Advertiser cost per 15s break</small>
							</div>

							<div>
								<label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
									Instructor Share (%)
								</label>
								<input type="number" step="1" min="1" max="99" name="instructor_rev_share_percent" id="instructorRevShareInput" value="<?= htmlspecialchars($platformSettings['instructor_rev_share_percent'] ?? '70') ?>" required class="table-search-input" style="width: 100%; height: 42px; font-weight: 700;" oninput="updatePlatformShareDisplay()">
								<small style="color: #64748b; font-size: 0.72rem;">Credited to instructor wallet</small>
							</div>

							<div>
								<label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
									Adsity Platform Margin (%)
								</label>
								<input type="number" id="platformRevShareDisplay" value="<?= htmlspecialchars($platformSettings['platform_rev_share_percent'] ?? '30') ?>" readonly class="table-search-input" style="width: 100%; height: 42px; font-weight: 700; background-color: #e2e8f0; color: #475569; cursor: not-allowed;">
								<small style="color: #64748b; font-size: 0.72rem;">Auto-balanced platform revenue</small>
							</div>

							<div>
								<label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
									Anti-Spam Cooldown (Mins)
								</label>
								<input type="number" step="1" min="1" max="60" name="ad_interval_minutes" value="<?= htmlspecialchars($platformSettings['ad_interval_minutes'] ?? '5') ?>" required class="table-search-input" style="width: 100%; height: 42px; font-weight: 700;">
								<small style="color: #64748b; font-size: 0.72rem;">Min interval between repeat impressions</small>
							</div>

							<div>
								<button type="submit" class="btn-process-payout" style="width: 100%; height: 42px; justify-content: center; background-color: #16a34a; font-size: 0.9rem;">
									<img src="../assets/icons/check-circle.svg" width="16" height="16" alt="Save" style="filter: brightness(0) invert(1);">
									<span>Save Monetization Rules</span>
								</button>
							</div>
						</div>
					</form>
				</div>

				<!-- Active Sponsor Video Inventory -->
				<div class="dashboard-section">
					<div class="section-heading">
						<h2>
							<img src="../assets/icons/video.svg" width="22" height="22" alt="Inventory">
							Sponsor Ad Inventory &amp; Campaigns
						</h2>
						<span class="badge-count"><?= $activeSponsorAds ?> Active / <?= $totalSponsorAds ?> Total</span>
					</div>

					<div class="table-responsive">
						<?php if (!empty($allSponsorAds)): ?>
							<table class="data-table">
								<thead>
									<tr>
										<th>Campaign &amp; Sponsor</th>
										<th>Video Asset</th>
										<th>Target URL</th>
										<th>CPM Rate</th>
										<th>Impressions</th>
										<th>Status</th>
										<th>Created</th>
										<th style="text-align: right;">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($allSponsorAds as $ad): ?>
										<tr>
											<td>
												<strong style="color: #0f172a; font-size: 0.95rem;"><?= htmlspecialchars($ad['campaign_title']) ?></strong>
												<div style="font-size: 0.78rem; color: #4338ca; font-weight: 700; margin-top: 2px;">
													<?= htmlspecialchars($ad['sponsor_name']) ?>
												</div>
											</td>
											<td>
												<code style="font-size: 0.78rem; color: #475569; background-color: #f1f5f9; padding: 2px 6px; border-radius: 4px;">
													<?= htmlspecialchars($ad['video_url']) ?>
												</code>
											</td>
											<td>
												<?php if (!empty($ad['click_url'])): ?>
													<a href="<?= htmlspecialchars($ad['click_url']) ?>" target="_blank" rel="noopener noreferrer" style="color: #0284c7; font-size: 0.85rem; text-decoration: underline;">
														<?= htmlspecialchars(mb_strimwidth($ad['click_url'], 0, 24, '...')) ?>
													</a>
												<?php else: ?>
													<span style="color: #94a3b8; font-size: 0.8rem;">None</span>
												<?php endif; ?>
											</td>
											<td>
												<strong style="color: #16a34a;">$<?= number_format((float)$ad['cpm_rate'], 4) ?></strong>
											</td>
											<td>
												<span class="badge-count" style="font-weight: 800;">
													<?= number_format((int)$ad['total_impressions']) ?> views
												</span>
											</td>
											<td>
												<span class="status-badge-<?= htmlspecialchars($ad['status']) ?>">
													<?= ucfirst($ad['status']) ?>
												</span>
											</td>
											<td><?= date('M d, Y', strtotime($ad['created_at'])) ?></td>
											<td style="text-align: right;">
												<div style="display: inline-flex; align-items: center; gap: 6px;">
													<form method="POST" action="manage_ads.php" style="display: inline;">
														<input type="hidden" name="action" value="toggle_status">
														<input type="hidden" name="ad_id" value="<?= (int)$ad['id'] ?>">
														<?php if ($ad['status'] === 'active'): ?>
															<button type="submit" class="btn-table-action btn-table-action--reject" title="Pause Campaign">
																<span>Pause</span>
															</button>
														<?php else: ?>
															<button type="submit" class="btn-table-action btn-table-action--approve" title="Activate Campaign">
																<span>Activate</span>
															</button>
														<?php endif; ?>
													</form>

													<form method="POST" action="manage_ads.php" style="display: inline;" onsubmit="return confirm('Permanently delete campaign \'<?= htmlspecialchars(addslashes($ad['campaign_title'])) ?>\'?');">
														<input type="hidden" name="action" value="delete_ad">
														<input type="hidden" name="ad_id" value="<?= (int)$ad['id'] ?>">
														<button type="submit" class="btn-table-action btn-table-action--delete" title="Delete Campaign">
															<img src="../assets/icons/trash.svg" width="13" height="13" alt="Delete">
														</button>
													</form>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<div class="empty-state">No sponsor ad campaigns created yet. Click "+ New Sponsor Ad" to add your first campaign.</div>
						<?php endif; ?>
					</div>
				</div>
			</section>

			<!-- ==================================================
			     TAB 6: CERTIFICATES & AD ACTIVITY LOGS
			     ================================================== -->
			<section id="tab-analytics" class="admin-tab-panel">
				<div class="panel-header">
					<div>
						<h1 class="panel-title">Certificates &amp; Ad Monetization Logs</h1>
						<p class="panel-subtitle">Audited record of verified credentials issued to graduates and live sponsor ad sessions.</p>
					</div>
					<div class="panel-header-actions">
						<a href="export_data.php?type=certificates" class="admin-top-btn admin-top-btn--secondary">
							<img src="../assets/icons/download.svg" width="14" height="14" alt="Export">
							<span>Export Certificates</span>
						</a>
						<a href="export_data.php?type=ad_logs" class="admin-top-btn admin-top-btn--secondary">
							<img src="../assets/icons/download.svg" width="14" height="14" alt="Export">
							<span>Export Ad Logs</span>
						</a>
					</div>
				</div>

				<!-- Verified Certificates Audit Log -->
				<div class="dashboard-section">
					<div class="section-heading">
						<h2>
							<img src="../assets/icons/award.svg" width="22" height="22" alt="Certificates">
							Verified Certificates Audit Log
						</h2>
						<div class="badge-count" style="display: flex; gap: 8px;">
							<span style="color: #15803d; font-weight: 700;"><?= $validCertificates ?> Valid</span>
							&bull;
							<span style="color: #dc2626; font-weight: 700;"><?= $revokedCertificates ?> Revoked</span>
						</div>
					</div>

					<!-- Search Toolbar for Certificates -->
					<div class="table-toolbar">
						<div class="table-toolbar-left">
							<input type="text" id="certSearchInput" placeholder="Search by certificate code or student name..." class="table-search-input" onkeyup="filterCertificatesTable()" style="max-width: 380px;">
						</div>
						<div class="badge-count" id="certsCountDisplay"><?= count($recentCertificates) ?> Certificates Logged</div>
					</div>

					<div class="table-responsive">
						<?php if (!empty($recentCertificates)): ?>
							<table class="data-table" id="adminCertificatesTable">
								<thead>
									<tr>
										<th>Certificate Code</th>
										<th>Student Name</th>
										<th>Course Completed</th>
										<th>Instructor</th>
										<th>Status</th>
										<th>Issue Date</th>
										<th style="text-align: right;">Actions</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($recentCertificates as $cert): ?>
										<?php $certStatus = $cert['status'] ?? 'valid'; ?>
										<tr class="cert-row" data-code="<?= htmlspecialchars(strtolower($cert['certificate_code'])) ?>" data-student="<?= htmlspecialchars(strtolower($cert['student_name'])) ?>" data-status="<?= htmlspecialchars($certStatus) ?>">
											<td>
												<code style="font-family: monospace; font-size: 0.88rem; font-weight: 700; color: <?= $certStatus === 'revoked' ? '#dc2626' : '#0284c7' ?>; background-color: <?= $certStatus === 'revoked' ? '#fee2e2' : '#f0f9ff' ?>; padding: 3px 6px; border-radius: 4px;">
													<?= htmlspecialchars($cert['certificate_code']) ?>
												</code>
											</td>
											<td>
												<strong><?= htmlspecialchars($cert['student_name']) ?></strong>
												<div style="font-size: 0.78rem; color: #64748b;"><?= htmlspecialchars($cert['student_email']) ?></div>
											</td>
											<td><strong><?= htmlspecialchars($cert['course_title']) ?></strong></td>
											<td><?= htmlspecialchars($cert['instructor_name'] ?? 'Instructor') ?></td>
											<td>
												<span class="status-badge-<?= htmlspecialchars($certStatus) ?>">
													<?= ucfirst($certStatus) ?>
												</span>
												<?php if ($certStatus === 'revoked' && !empty($cert['revocation_reason'])): ?>
													<div style="font-size: 0.72rem; color: #dc2626; margin-top: 3px; max-width: 140px; line-height: 1.2;" title="<?= htmlspecialchars($cert['revocation_reason']) ?>">
														<?= htmlspecialchars(mb_strimwidth($cert['revocation_reason'], 0, 24, '...')) ?>
													</div>
												<?php endif; ?>
											</td>
											<td><?= date('M d, Y h:i A', strtotime($cert['issued_at'])) ?></td>
											<td style="text-align: right;">
												<div style="display: inline-flex; align-items: center; gap: 6px;">
													<a href="../student/certificate.php?code=<?= urlencode($cert['certificate_code']) ?>" target="_blank" class="btn-table-action btn-table-action--verify" title="Verify / View Certificate">
														<img src="../assets/icons/external-link.svg" width="13" height="13" alt="Verify">
														<span>View</span>
													</a>

													<?php if ($certStatus === 'valid'): ?>
														<button type="button" class="btn-table-action btn-table-action--revoke" onclick="openAdminRevokeCertModal(<?= (int)$cert['id'] ?>, '<?= htmlspecialchars(addslashes($cert['certificate_code'])) ?>', '<?= htmlspecialchars(addslashes($cert['student_name'])) ?>')" title="Revoke Certificate">
															<img src="../assets/icons/alert-circle.svg" width="13" height="13" alt="Revoke">
															<span>Revoke</span>
														</button>
													<?php else: ?>
														<form method="POST" action="revoke_certificate.php" style="display: inline;" onsubmit="return confirm('Reinstate certificate \'<?= htmlspecialchars(addslashes($cert['certificate_code'])) ?>\' to Valid?');">
															<input type="hidden" name="action" value="restore">
															<input type="hidden" name="certificate_id" value="<?= (int)$cert['id'] ?>">
															<button type="submit" class="btn-table-action btn-table-action--restore" title="Restore / Reinstate Certificate">
																<img src="../assets/icons/check-circle.svg" width="13" height="13" alt="Restore">
																<span>Restore</span>
															</button>
														</form>
													<?php endif; ?>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<div class="empty-state">No certificates generated yet. As students complete practical assessments and instructors approve them, verified credentials will be logged here.</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- Live Ad Impressions Stream -->
				<div class="dashboard-section">
					<div class="section-heading">
						<h2>
							<img src="../assets/icons/video.svg" width="22" height="22" alt="Ad Logs">
							Live Sponsor Ad Impression Stream (Recent 25)
						</h2>
						<span class="badge-count" style="background-color: #dcfce7; color: #15803d; font-weight: 800;">
							<?= number_format($totalAdImpressions) ?> Total Sessions
						</span>
					</div>

					<div class="table-responsive">
						<?php if (!empty($recentAdLogs)): ?>
							<table class="data-table">
								<thead>
									<tr>
										<th>Log ID</th>
										<th>Student Viewer</th>
										<th>Course &amp; Lesson</th>
										<th>Instructor Beneficiary</th>
										<th>Duration</th>
										<th>Revenue Credited</th>
										<th>Timestamp</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($recentAdLogs as $log): ?>
										<tr>
											<td>#<?= $log['id'] ?></td>
											<td><strong><?= htmlspecialchars($log['student_name'] ?? 'Registered Student') ?></strong></td>
											<td>
												<strong><?= htmlspecialchars($log['course_title']) ?></strong>
												<div style="font-size: 0.78rem; color: #64748b;">Lesson <?= htmlspecialchars($log['lesson_number'] ?? '1') ?>: <?= htmlspecialchars($log['lesson_title'] ?? '') ?></div>
											</td>
											<td><?= htmlspecialchars($log['instructor_name'] ?? 'Instructor') ?></td>
											<td><?= $log['ad_duration_seconds'] ?>s break</td>
											<td>
												<strong style="color: #16a34a; font-size: 0.95rem;">+$<?= number_format((float)$log['amount_earned'], 4) ?></strong>
											</td>
											<td><?= date('M d, Y h:i:s A', strtotime($log['created_at'])) ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else: ?>
							<div class="empty-state">No sponsor ad sessions recorded yet. Ad activity logs track every 15-second sponsor break completed in video classrooms.</div>
						<?php endif; ?>
					</div>
				</div>
			</section>

			<!-- Admin Dashboard Footer -->
			<footer class="admin-footer">
				<div>&copy; 2026 Adsity Admin Portal. Ethical Ad-Funded Education.</div>
				<div>System Version 2.4 &bull; Administrator Mode</div>
			</footer>

		</main>
	</div>

	<!-- ==========================================================
	     MODAL 1: ADMIN PAYOUT REVIEW & DISBURSEMENT MODAL
	     ========================================================== -->
	<div id="adminPayoutModal" class="admin-modal-backdrop" style="display: none;" onclick="handleAdminModalBackdrop(event)">
		<div class="admin-modal-dialog">
			<div class="admin-modal-header">
				<div>
					<h3 class="admin-modal-title">
						<img src="../assets/icons/dollar-sign.svg" width="20" height="20" alt="Review">
						Review Cash-Out Request <span id="modalPayoutIdDisplay">#0</span>
					</h3>
					<p class="admin-modal-subtitle">Verify the instructor's payout destination and disburse earnings.</p>
				</div>
				<button type="button" class="admin-modal-close" onclick="closeAdminPayoutModal()" aria-label="Close modal">&times;</button>
			</div>

			<div class="admin-modal-body">
				<!-- Meta Overview Card -->
				<div class="admin-meta-card">
					<div class="admin-meta-item">
						<span class="admin-meta-label">Instructor</span>
						<strong id="modalInstructorName" class="admin-meta-value">-</strong>
						<span id="modalInstructorEmail" style="font-size: 0.78rem; color: #64748b;">-</span>
					</div>
					<div class="admin-meta-item">
						<span class="admin-meta-label">Requested Amount</span>
						<strong id="modalAmountDisplay" style="font-size: 1.3rem; color: #16a34a;">$0.00</strong>
					</div>
					<div class="admin-meta-item">
						<span class="admin-meta-label">Status</span>
						<div id="modalStatusBadge" style="margin-top: 4px;">-</div>
					</div>
					<div class="admin-meta-item">
						<span class="admin-meta-label">Date Requested</span>
						<span id="modalCreatedAt" style="font-size: 0.85rem; color: #0f172a;">-</span>
					</div>
				</div>

				<!-- Destination Details Card -->
				<div>
					<label class="admin-meta-label" style="display: block; margin-bottom: 6px;">Transfer Destination &amp; Account Details</label>
					<div class="admin-details-box" id="modalDestinationBox">
						<!-- Populated via JS -->
					</div>
				</div>

				<!-- Instructor Notes Box (if any) -->
				<div id="modalInstructorNotesWrapper" style="display: none;">
					<label class="admin-meta-label" style="display: block; margin-bottom: 6px;">Instructor Notes</label>
					<div style="background-color: #f1f5f9; border-left: 3px solid #0284c7; padding: 10px 14px; border-radius: 6px; font-size: 0.88rem; color: #334155;" id="modalInstructorNotes">-</div>
				</div>

				<!-- Processing History (if already completed or rejected) -->
				<div id="modalProcessedHistoryWrapper" style="display: none; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px;">
					<div style="font-size: 0.78rem; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 6px;">Processing Log</div>
					<div style="font-size: 0.88rem; color: #334155; margin-bottom: 4px;">
						<strong>Reference ID:</strong> <span id="modalHistoryRef">-</span>
					</div>
					<div style="font-size: 0.88rem; color: #334155; margin-bottom: 4px;">
						<strong>Admin Notes:</strong> <span id="modalHistoryNotes">-</span>
					</div>
					<div style="font-size: 0.78rem; color: #94a3b8;" id="modalHistoryMeta">Processed on ...</div>
				</div>

				<!-- Active Processing Form (Only shown when pending) -->
				<form id="adminPayoutActionForm" method="POST" action="process_payout_function.php">
					<input type="hidden" name="payout_id" id="modalPayoutIdInput" value="">
					<input type="hidden" name="action" id="modalActionInput" value="">

					<div id="modalActionFields">
						<div style="margin-bottom: 16px;">
							<label for="modalTransactionRef" class="admin-meta-label" style="display: block; margin-bottom: 6px;">
								Disbursement Reference ID / Receipt Code <span style="font-weight: 500; font-size: 0.78rem; color: #64748b;">(Optional for Approval)</span>
							</label>
							<input type="text" id="modalTransactionRef" name="transaction_reference" placeholder="e.g. PayPal TXN ID, GCash Reference No., or Bank Trace #" class="form-input">
						</div>

						<div style="margin-bottom: 20px;">
							<label for="modalAdminNotes" class="admin-meta-label" style="display: block; margin-bottom: 6px;">
								Admin Notes &amp; Comments <span style="font-weight: 500; font-size: 0.78rem; color: #dc2626;">(Required if rejecting)</span>
							</label>
							<textarea id="modalAdminNotes" name="admin_notes" rows="3" placeholder="Provide reason if rejecting, or confirmation notes for the instructor..." class="review-textarea"></textarea>
						</div>

						<div class="admin-modal-footer">
							<button type="button" class="btn-table-action btn-table-action--preview" onclick="closeAdminPayoutModal()">Close</button>
							<button type="button" class="btn-table-action btn-table-action--delete" onclick="submitAdminPayoutAction('reject')">
								<img src="../assets/icons/alert-circle.svg" width="14" height="14" alt="Reject">
								<span>Reject &amp; Refund Balance</span>
							</button>
							<button type="button" class="btn-process-payout" style="background-color: #16a34a;" onclick="submitAdminPayoutAction('approve')">
								<img src="../assets/icons/check-circle.svg" width="15" height="15" alt="Approve" style="filter: brightness(0) invert(1);">
								<span>Approve &amp; Mark Paid</span>
							</button>
						</div>
					</div>

					<div id="modalCloseOnlyFooter" class="admin-modal-footer" style="display: none;">
						<button type="button" class="btn-process-payout btn-process-payout--view" onclick="closeAdminPayoutModal()">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- ==========================================================
	     MODAL 2: USER ACTIVITY INSPECTOR MODAL
	     ========================================================== -->
	<div id="adminUserInspectModal" class="admin-modal-backdrop" style="display: none;" onclick="handleInspectModalBackdrop(event)">
		<div class="admin-modal-dialog">
			<div class="admin-modal-header">
				<div>
					<h3 class="admin-modal-title">
						<img src="../assets/icons/users.svg" width="20" height="20" alt="Inspect">
						User Account Details
					</h3>
					<p class="admin-modal-subtitle">Activity profile, enrollment records, and wallet balances.</p>
				</div>
				<button type="button" class="admin-modal-close" onclick="closeAdminUserInspectModal()" aria-label="Close modal">&times;</button>
			</div>

			<div class="admin-modal-body">
				<div class="admin-meta-card">
					<div class="admin-meta-item">
						<span class="admin-meta-label">User ID</span>
						<strong id="inspectUserId" class="admin-meta-value">#0</strong>
					</div>
					<div class="admin-meta-item">
						<span class="admin-meta-label">Account Role</span>
						<strong id="inspectUserRole" class="admin-meta-value">-</strong>
					</div>
					<div class="admin-meta-item">
						<span class="admin-meta-label">Member Since</span>
						<strong id="inspectUserJoined" class="admin-meta-value">-</strong>
					</div>
				</div>

				<div class="admin-details-box">
					<div style="margin-bottom: 6px;">
						<span class="admin-meta-label">Full Name:</span>
						<strong id="inspectUserName" style="font-size: 1rem; color: #0f172a;">-</strong>
					</div>
					<div>
						<span class="admin-meta-label">Email Address:</span>
						<span id="inspectUserEmail" style="color: #475569;">-</span>
					</div>
				</div>

				<div>
					<label class="admin-meta-label" style="display: block; margin-bottom: 6px;">Activity Breakdown</label>
					<div id="inspectUserActivityBox" class="admin-details-box">
						<!-- Populated via JS -->
					</div>
				</div>

				<div class="admin-modal-footer">
					<button type="button" class="btn-process-payout btn-process-payout--view" onclick="closeAdminUserInspectModal()">Done</button>
				</div>
			</div>
		</div>
	</div>

	<!-- ==========================================================
	     MODAL 3: CHANGE USER ROLE MODAL
	     ========================================================== -->
	<div id="adminChangeRoleModal" class="admin-modal-backdrop" style="display: none;" onclick="handleChangeRoleModalBackdrop(event)">
		<div class="admin-modal-dialog" style="max-width: 480px;">
			<div class="admin-modal-header">
				<div>
					<h3 class="admin-modal-title">
						<img src="../assets/icons/user-check.svg" width="20" height="20" alt="Role">
						Change User Role
					</h3>
					<p class="admin-modal-subtitle">Promote student or change instructor permissions.</p>
				</div>
				<button type="button" class="admin-modal-close" onclick="closeAdminChangeRoleModal()" aria-label="Close modal">&times;</button>
			</div>

			<form method="POST" action="change_role_function.php">
				<input type="hidden" name="change_role" value="1">
				<input type="hidden" name="user_id" id="changeRoleUserId" value="">

				<div class="admin-modal-body">
					<div>
						<label class="admin-meta-label" style="display: block; margin-bottom: 4px;">User Account</label>
						<strong id="changeRoleUserName" style="font-size: 1.05rem; color: #0f172a;">-</strong>
					</div>

					<div>
						<label for="changeRoleSelect" class="admin-meta-label" style="display: block; margin-bottom: 6px;">Assign New Role</label>
						<select name="new_role_id" id="changeRoleSelect" class="table-select-filter" style="width: 100%; padding: 10px 14px;">
							<?php foreach ($allRoles as $r): ?>
								<option value="<?= $r['id'] ?>"><?= ucfirst($r['name']) ?> (<?= htmlspecialchars($r['description'] ?? '') ?>)</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="admin-modal-footer">
						<button type="button" class="btn-table-action btn-table-action--preview" onclick="closeAdminChangeRoleModal()">Cancel</button>
						<button type="submit" class="btn-process-payout" style="background-color: #16a34a;">
							Update Role
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- ==========================================================
	     MODAL 4: DELETE COURSE CONFIRMATION MODAL
	     ========================================================== -->
	<div id="adminDeleteCourseModal" class="admin-modal-backdrop" style="display: none;" onclick="handleDeleteCourseModalBackdrop(event)">
		<div class="admin-modal-dialog" style="max-width: 480px;">
			<div class="admin-modal-header">
				<div>
					<h3 class="admin-modal-title" style="color: #dc2626;">
						<img src="../assets/icons/trash.svg" width="20" height="20" alt="Delete">
						Delete Course Confirmation
					</h3>
					<p class="admin-modal-subtitle">Moderation action will delete lessons and student enrollments.</p>
				</div>
				<button type="button" class="admin-modal-close" onclick="closeAdminDeleteCourseModal()" aria-label="Close modal">&times;</button>
			</div>

			<form method="POST" action="delete_course.php">
				<input type="hidden" name="delete_course" value="1">
				<input type="hidden" name="course_id" id="deleteCourseIdInput" value="">

				<div class="admin-modal-body">
					<p style="margin: 0; color: #334155; line-height: 1.5;">
						Are you sure you want to permanently delete:
						<br>
						<strong id="deleteCourseTitleDisplay" style="color: #0f172a; font-size: 1.05rem; display: block; margin-top: 6px;"></strong>
					</p>

					<div style="background-color: #fee2e2; border-left: 4px solid #dc2626; padding: 12px; border-radius: 6px; font-size: 0.85rem; color: #991b1b;">
						<strong>Warning:</strong> Deleting this course will automatically cascade and remove its video lessons, student progress completions, project submissions, and issued certificates.
					</div>

					<div class="admin-modal-footer">
						<button type="button" class="btn-table-action btn-table-action--preview" onclick="closeAdminDeleteCourseModal()">Cancel</button>
						<button type="submit" class="btn-table-action btn-table-action--delete" style="padding: 9px 16px;">
							Confirm Permanent Delete
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- ==========================================================
	     MODAL 6: EXPORT PLATFORM DATA MODAL
	     ========================================================== -->
	<div id="adminExportModal" class="admin-modal-backdrop" style="display: none;" onclick="handleExportModalBackdrop(event)">
		<div class="admin-modal-dialog" style="max-width: 480px;">
			<div class="admin-modal-header">
				<div>
					<h3 class="admin-modal-title">
						<img src="../assets/icons/download.svg" width="20" height="20" alt="Export">
						Export Platform Data (CSV)
					</h3>
					<p class="admin-modal-subtitle">Download spreadsheet reports for external bookkeeping.</p>
				</div>
				<button type="button" class="admin-modal-close" onclick="closeAdminExportModal()" aria-label="Close modal">&times;</button>
			</div>

			<div class="admin-modal-body">
				<p style="margin: 0; color: #64748b; font-size: 0.9rem;">
					Select the dataset you would like to export as a CSV document:
				</p>

				<div style="display: flex; flex-direction: column; gap: 10px;">
					<a href="export_data.php?type=users" class="export-option-link">
						<span>👥 All Users (Students, Instructors, Admins)</span>
						<span style="color: #16a34a; font-weight: 800;">&darr; CSV</span>
					</a>
					<a href="export_data.php?type=courses" class="export-option-link">
						<span>📚 Courses Catalog &amp; Metrics</span>
						<span style="color: #16a34a; font-weight: 800;">&darr; CSV</span>
					</a>
					<a href="export_data.php?type=payouts" class="export-option-link">
						<span>💰 Instructor Cash-Out &amp; Payout Requests</span>
						<span style="color: #16a34a; font-weight: 800;">&darr; CSV</span>
					</a>
					<a href="export_data.php?type=certificates" class="export-option-link">
						<span>📜 Verified Certificates Log</span>
						<span style="color: #16a34a; font-weight: 800;">&darr; CSV</span>
					</a>
					<a href="export_data.php?type=ad_logs" class="export-option-link">
						<span>📺 Sponsor Ad Impression Stream</span>
						<span style="color: #16a34a; font-weight: 800;">&darr; CSV</span>
					</a>
					<a href="export_data.php?type=sponsor_ads" class="export-option-link">
						<span>💼 Sponsor Campaigns &amp; Video Inventory</span>
						<span style="color: #16a34a; font-weight: 800;">&darr; CSV</span>
					</a>
				</div>

				<div class="admin-modal-footer">
					<button type="button" class="btn-process-payout btn-process-payout--view" onclick="closeAdminExportModal()">Close</button>
				</div>
			</div>
		</div>
	</div>
	     MODAL 7: REJECT / REVISION COURSE MODAL
	     ========================================================== -->
	<div id="adminRejectCourseModal" class="admin-modal-backdrop" style="display: none;" onclick="handleRejectCourseModalBackdrop(event)">
		<div class="admin-modal-dialog" style="max-width: 500px;">
			<div class="admin-modal-header">
				<div>
					<h3 class="admin-modal-title" style="color: #e11d48;">
						<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Reject">
						Request Course Revisions
					</h3>
					<p class="admin-modal-subtitle">Provide constructive feedback so the instructor can update their curriculum.</p>
				</div>
				<button type="button" class="admin-modal-close" onclick="closeAdminRejectCourseModal()" aria-label="Close modal">&times;</button>
			</div>

			<form method="POST" action="moderate_course.php">
				<input type="hidden" name="action" value="reject">
				<input type="hidden" name="course_id" id="modalRejectCourseId" value="">

				<div class="admin-modal-body">
					<p style="margin: 0 0 12px 0; color: #334155; font-size: 0.95rem;">
						Course Title: <strong id="modalRejectCourseTitle" style="color: #0f172a;"></strong>
					</p>

					<div style="margin-bottom: 16px;">
						<label for="modalRejectReason" style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
							Revision Feedback / Reason <span style="color: #ef4444;">*</span>
						</label>
						<textarea id="modalRejectReason" name="rejection_reason" rows="4" required placeholder="Describe what lessons or assessments need adjustment before this course can be approved..." style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.9rem; resize: vertical; box-sizing: border-box;"></textarea>
					</div>

					<div class="admin-modal-footer">
						<button type="button" class="btn-table-action btn-table-action--preview" onclick="closeAdminRejectCourseModal()">Cancel</button>
						<button type="submit" class="btn-table-action btn-table-action--reject" style="padding: 9px 18px; font-weight: 700;">
							Submit Rejection Note
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- ==========================================================
	     MODAL 8: REVOKE CERTIFICATE MODAL
	     ========================================================== -->
	<div id="adminRevokeCertModal" class="admin-modal-backdrop" style="display: none;" onclick="handleRevokeCertModalBackdrop(event)">
		<div class="admin-modal-dialog" style="max-width: 500px;">
			<div class="admin-modal-header">
				<div>
					<h3 class="admin-modal-title" style="color: #dc2626;">
						<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Revoke">
						Revoke Verified Certificate
					</h3>
					<p class="admin-modal-subtitle">Invalidate public credential verification code due to policy violation.</p>
				</div>
				<button type="button" class="admin-modal-close" onclick="closeAdminRevokeCertModal()" aria-label="Close modal">&times;</button>
			</div>

			<form method="POST" action="revoke_certificate.php">
				<input type="hidden" name="action" value="revoke">
				<input type="hidden" name="certificate_id" id="modalRevokeCertId" value="">

				<div class="admin-modal-body">
					<div style="background-color: #fee2e2; border-left: 4px solid #dc2626; padding: 12px; border-radius: 6px; font-size: 0.85rem; color: #991b1b; margin-bottom: 14px;">
						<strong>Warning:</strong> Revoking will display a permanent invalidation notice and watermark on the student's public certificate verification page.
					</div>

					<p style="margin: 0 0 6px 0; font-size: 0.9rem; color: #475569;">
						Student: <strong id="modalRevokeStudentName" style="color: #0f172a;"></strong>
					</p>
					<p style="margin: 0 0 16px 0; font-size: 0.9rem; color: #475569;">
						Certificate ID: <code id="modalRevokeCertCode" style="color: #dc2626; font-weight: 700; font-family: monospace;"></code>
					</p>

					<div style="margin-bottom: 16px;">
						<label for="modalRevokeReason" style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
							Reason for Revocation <span style="color: #ef4444;">*</span>
						</label>
						<textarea id="modalRevokeReason" name="revocation_reason" rows="3" required placeholder="State violation (e.g. plagiarized GitHub assessment, cheating, fraud)..." style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.9rem; resize: vertical; box-sizing: border-box;"></textarea>
					</div>

					<div class="admin-modal-footer">
						<button type="button" class="btn-table-action btn-table-action--preview" onclick="closeAdminRevokeCertModal()">Cancel</button>
						<button type="submit" class="btn-table-action btn-table-action--revoke" style="padding: 9px 18px; font-weight: 700;">
							Confirm Revocation
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- ==========================================================
	     MODAL 9: NEW SPONSOR AD CAMPAIGN MODAL
	     ========================================================== -->
	<div id="adminNewAdModal" class="admin-modal-backdrop" style="display: none;" onclick="handleNewAdModalBackdrop(event)">
		<div class="admin-modal-dialog" style="max-width: 520px;">
			<div class="admin-modal-header">
				<div>
					<h3 class="admin-modal-title">
						<img src="../assets/icons/video.svg" width="20" height="20" alt="New Ad">
						New Sponsor Video Campaign
					</h3>
					<p class="admin-modal-subtitle">Add a sponsor advertisement to run during video lessons.</p>
				</div>
				<button type="button" class="admin-modal-close" onclick="closeAdminNewAdModal()" aria-label="Close modal">&times;</button>
			</div>

			<form method="POST" action="manage_ads.php" enctype="multipart/form-data">
				<input type="hidden" name="action" value="add_ad">

				<div class="admin-modal-body">
					<div style="margin-bottom: 14px;">
						<label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
							Sponsor Brand / Company Name <span style="color: #ef4444;">*</span>
						</label>
						<input type="text" name="sponsor_name" required placeholder="e.g. JetBrains, Google Cloud, Figma" class="table-search-input" style="width: 100%; box-sizing: border-box;">
					</div>

					<div style="margin-bottom: 14px;">
						<label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
							Campaign Headline / Title <span style="color: #ef4444;">*</span>
						</label>
						<input type="text" name="campaign_title" required placeholder="e.g. Free Developer Pack for Students" class="table-search-input" style="width: 100%; box-sizing: border-box;">
					</div>

					<!-- Video File Picker from File Manager -->
					<div style="margin-bottom: 14px;">
						<label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
							Sponsor Video File (Pick from your computer)
						</label>
						<div id="adFileDropzone" onclick="document.getElementById('adVideoFileInput').click()" style="border: 2px dashed #94a3b8; border-radius: 10px; padding: 18px 14px; text-align: center; background-color: #f8fafc; cursor: pointer; transition: all 0.2s ease;">
							<input type="file" id="adVideoFileInput" name="ad_video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime,.mp4,.webm,.ogg,.mov" style="display: none;" onchange="handleAdFileSelected(this)">
							<img src="../assets/icons/video.svg" width="28" height="28" alt="Video File" style="opacity: 0.7; margin-bottom: 4px;">
							<div id="adFilePickerTitle" style="font-size: 0.92rem; font-weight: 700; color: #1e293b;">
								Click to open File Manager &amp; select Video
							</div>
							<div id="adFilePickerSub" style="font-size: 0.75rem; color: #64748b; margin-top: 3px;">
								Supports MP4, WebM, MOV (Max 100MB) &bull; Recommended 15-second sponsor spot
							</div>
						</div>

						<div style="margin-top: 8px; display: flex; align-items: center; justify-content: space-between;">
							<span style="font-size: 0.74rem; color: #64748b;">Or use existing path / web URL:</span>
							<button type="button" onclick="toggleAdUrlInput()" style="background: none; border: none; font-size: 0.74rem; color: #4f46e5; text-decoration: underline; cursor: pointer;">
								Toggle Custom Path
							</button>
						</div>
						<input type="text" id="adVideoUrlInput" name="video_url" value="assets/ad/sample_ad.mp4" placeholder="assets/ad/sample_ad.mp4 or https://..." class="table-search-input" style="width: 100%; box-sizing: border-box; margin-top: 4px; display: none; font-size: 0.85rem;">
					</div>

					<div style="margin-bottom: 14px;">
						<label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
							Sponsor Website / Click-Through URL
						</label>
						<input type="url" name="click_url" placeholder="https://example.com/student-offer" class="table-search-input" style="width: 100%; box-sizing: border-box;">
					</div>

					<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
						<div>
							<label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
								CPM Rate ($ per impression)
							</label>
							<input type="number" step="0.0001" min="0.0001" name="cpm_rate" value="0.0500" class="table-search-input" style="width: 100%; box-sizing: border-box; font-weight: 700;">
						</div>
						<div>
							<label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
								Initial Status
							</label>
							<select name="status" class="table-select-filter" style="width: 100%; height: 42px;">
								<option value="active" selected>Active (Serving)</option>
								<option value="paused">Paused</option>
							</select>
						</div>
					</div>

					<div class="admin-modal-footer">
						<button type="button" class="btn-table-action btn-table-action--preview" onclick="closeAdminNewAdModal()">Cancel</button>
						<button type="submit" class="btn-process-payout" style="background-color: #16a34a; padding: 9px 18px;">
							Create Sponsor Campaign
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- ==========================================================
	     LOGOUT CONFIRMATION MODAL SCRIPT
	     ========================================================== -->
	<script src="../assets/js/logout_modal.js"></script>

	<!-- ==========================================================
	     INTERACTIVE ADMIN CONTROLLER JS
	     ========================================================== -->
	<script>
		// Tab Switching Logic with URL hash persistence
		function switchTab(tabId) {
			const validTabs = ['overview', 'courses', 'payouts', 'users', 'ads', 'analytics'];
			if (!validTabs.includes(tabId)) {
				tabId = 'overview';
			}

			// Update Tab Panels
			document.querySelectorAll('.admin-tab-panel').forEach(function(panel) {
				panel.classList.remove('active');
			});
			const targetPanel = document.getElementById('tab-' + tabId);
			if (targetPanel) {
				targetPanel.classList.add('active');
			}

			// Update Left Sidebar Nav Items
			document.querySelectorAll('.admin-nav-item').forEach(function(navItem) {
				if (navItem.getAttribute('data-tab') === tabId) {
					navItem.classList.add('active');
				} else if (navItem.getAttribute('data-tab')) {
					navItem.classList.remove('active');
				}
			});

			// Update URL hash without jump
			if (history.pushState) {
				history.pushState(null, null, '#' + tabId);
			} else {
				location.hash = '#' + tabId;
			}

			// Close mobile sidebar if open
			closeSidebarMobile();
			window.scrollTo({ top: 0, behavior: 'smooth' });
		}

		// Check hash on initial page load
		window.addEventListener('DOMContentLoaded', function() {
			const hash = window.location.hash.replace('#', '');
			if (hash) {
				switchTab(hash);
			}

			// Mobile sidebar toggle
			const toggleBtn = document.getElementById('sidebarToggle');
			if (toggleBtn) {
				toggleBtn.addEventListener('click', toggleSidebarMobile);
			}
		});

		function toggleSidebarMobile() {
			const sidebar = document.getElementById('adminSidebar');
			const overlay = document.getElementById('sidebarOverlay');
			if (sidebar) sidebar.classList.toggle('is-open');
			if (overlay) overlay.classList.toggle('is-open');
		}

		function closeSidebarMobile() {
			const sidebar = document.getElementById('adminSidebar');
			const overlay = document.getElementById('sidebarOverlay');
			if (sidebar) sidebar.classList.remove('is-open');
			if (overlay) overlay.classList.remove('is-open');
		}

		// --- Real-time Course Table Filtering ---
		let currentCourseStatusFilter = 'all';

		function filterCoursesByStatus(status) {
			currentCourseStatusFilter = status;
			document.querySelectorAll('.course-status-pill').forEach(function(pill) {
				pill.classList.remove('active');
			});
			if (window.event && window.event.currentTarget) {
				window.event.currentTarget.classList.add('active');
			}
			filterCoursesTable();
		}

		function filterCoursesTable() {
			const search = document.getElementById('courseSearchInput').value.toLowerCase().trim();
			const category = document.getElementById('courseCategoryFilter').value.toLowerCase().trim();
			const rows = document.querySelectorAll('.course-row');
			let visibleCount = 0;

			rows.forEach(function(row) {
				const title = row.getAttribute('data-title') || '';
				const instructor = row.getAttribute('data-instructor') || '';
				const cat = (row.getAttribute('data-category') || '').toLowerCase();
				const status = row.getAttribute('data-status') || '';

				const matchesSearch = !search || title.includes(search) || instructor.includes(search);
				const matchesCat = !category || cat === category;
				const matchesStatus = currentCourseStatusFilter === 'all' || status === currentCourseStatusFilter;

				if (matchesSearch && matchesCat && matchesStatus) {
					row.style.display = '';
					visibleCount++;
				} else {
					row.style.display = 'none';
				}
			});

			const countDisplay = document.getElementById('coursesCountDisplay');
			if (countDisplay) {
				countDisplay.textContent = visibleCount + ' Courses Shown';
			}
		}

		// --- Real-time Certificate Table Filtering ---
		function filterCertificatesTable() {
			const search = document.getElementById('certSearchInput').value.toLowerCase().trim();
			const rows = document.querySelectorAll('.cert-row');
			let visibleCount = 0;

			rows.forEach(function(row) {
				const code = row.getAttribute('data-code') || '';
				const student = row.getAttribute('data-student') || '';

				const matchesSearch = !search || code.includes(search) || student.includes(search);

				if (matchesSearch) {
					row.style.display = '';
					visibleCount++;
				} else {
					row.style.display = 'none';
				}
			});

			const countDisplay = document.getElementById('certsCountDisplay');
			if (countDisplay) {
				countDisplay.textContent = visibleCount + ' Certificates Shown';
			}
		}

		// Platform Revenue Share Dynamic Calculation
		function updatePlatformShareDisplay() {
			const instructorInput = document.getElementById('instructorRevShareInput');
			const platformDisplay = document.getElementById('platformRevShareDisplay');
			if (instructorInput && platformDisplay) {
				let val = parseInt(instructorInput.value, 10) || 70;
				if (val < 1) val = 1;
				if (val > 99) val = 99;
				platformDisplay.value = 100 - val;
			}
		}

		// --- Real-time User Table Filtering ---
		let currentRoleFilter = 'all';

		function filterUsersByRole(role) {
			currentRoleFilter = role;
			document.querySelectorAll('#tab-users .filter-pill').forEach(function(pill) {
				pill.classList.remove('active');
			});
			if (event && event.currentTarget) {
				event.currentTarget.classList.add('active');
			}
			filterUsersTable();
		}

		function filterUsersTable() {
			const search = document.getElementById('userSearchInput').value.toLowerCase().trim();
			const rows = document.querySelectorAll('.user-row');
			let visibleCount = 0;

			rows.forEach(function(row) {
				const role = row.getAttribute('data-role') || '';
				const name = row.getAttribute('data-name') || '';
				const email = row.getAttribute('data-email') || '';

				const matchesRole = currentRoleFilter === 'all' || role === currentRoleFilter;
				const matchesSearch = !search || name.includes(search) || email.includes(search);

				if (matchesRole && matchesSearch) {
					row.style.display = '';
					visibleCount++;
				} else {
					row.style.display = 'none';
				}
			});

			const countDisplay = document.getElementById('usersCountDisplay');
			if (countDisplay) {
				countDisplay.textContent = visibleCount + ' Users Shown';
			}
		}

		// --- Real-time Payout Table Filtering ---
		function filterPayoutsTable(status) {
			document.querySelectorAll('#tab-payouts .filter-pill').forEach(function(pill) {
				pill.classList.remove('active');
			});
			if (event && event.currentTarget) {
				event.currentTarget.classList.add('active');
			}

			const rows = document.querySelectorAll('.payout-row');
			rows.forEach(function(row) {
				const rowStatus = row.getAttribute('data-status');
				if (status === 'all' || rowStatus === status) {
					row.style.display = '';
				} else {
					row.style.display = 'none';
				}
			});
		}

		// --- Modal 1: Payout Review & Process Modal ---
		function openAdminPayoutModal(data) {
			document.getElementById('modalPayoutIdDisplay').textContent = '#' + data.id;
			document.getElementById('modalPayoutIdInput').value = data.id;
			document.getElementById('modalInstructorName').textContent = data.instructor_name;
			document.getElementById('modalInstructorEmail').textContent = data.instructor_email;
			document.getElementById('modalAmountDisplay').textContent = '$' + data.amount;
			document.getElementById('modalCreatedAt').textContent = data.created_at;

			// Status badge
			var statusHtml = '';
			if (data.status === 'completed') {
				statusHtml = '<span class="status-badge-completed">✓ Disbursed</span>';
			} else if (data.status === 'rejected') {
				statusHtml = '<span class="status-badge-rejected">✕ Rejected</span>';
			} else {
				statusHtml = '<span class="status-badge-pending">⏳ Pending Review</span>';
			}
			document.getElementById('modalStatusBadge').innerHTML = statusHtml;

			// Destination Details
			var destHtml = '';
			var details = data.payout_details || {};
			if (data.payout_method === 'paypal') {
				destHtml = '<div style="font-weight: 800; color: #1d4ed8; margin-bottom: 4px;">🅿️ PayPal Transfer</div>' +
				           '<div><strong>Account Email:</strong> ' + escapeHtml(details.email || '—') + '</div>';
			} else if (data.payout_method === 'gcash') {
				destHtml = '<div style="font-weight: 800; color: #059669; margin-bottom: 4px;">📱 GCash Transfer</div>' +
				           '<div><strong>Account Name:</strong> ' + escapeHtml(details.account_name || '—') + '</div>' +
				           '<div><strong>Mobile Number:</strong> ' + escapeHtml(details.mobile_number || '—') + '</div>';
			} else {
				destHtml = '<div style="font-weight: 800; color: #334155; margin-bottom: 4px;">🏦 Bank Transfer</div>' +
				           '<div><strong>Bank:</strong> ' + escapeHtml(details.bank_name || '—') + '</div>' +
				           '<div><strong>Account Name:</strong> ' + escapeHtml(details.account_name || '—') + '</div>' +
				           '<div><strong>Account Number:</strong> ' + escapeHtml(details.account_number || '—') + '</div>';
			}
			document.getElementById('modalDestinationBox').innerHTML = destHtml;

			// Instructor notes
			var notesWrapper = document.getElementById('modalInstructorNotesWrapper');
			if (data.instructor_notes && data.instructor_notes.trim().length > 0) {
				document.getElementById('modalInstructorNotes').textContent = data.instructor_notes;
				notesWrapper.style.display = 'block';
			} else {
				notesWrapper.style.display = 'none';
			}

			// Active form vs history
			var actionFields = document.getElementById('modalActionFields');
			var historyWrapper = document.getElementById('modalProcessedHistoryWrapper');
			var closeOnlyFooter = document.getElementById('modalCloseOnlyFooter');

			if (data.status === 'pending') {
				actionFields.style.display = 'block';
				historyWrapper.style.display = 'none';
				closeOnlyFooter.style.display = 'none';
				document.getElementById('modalTransactionRef').value = '';
				document.getElementById('modalAdminNotes').value = '';
			} else {
				actionFields.style.display = 'none';
				historyWrapper.style.display = 'block';
				closeOnlyFooter.style.display = 'flex';
				document.getElementById('modalHistoryRef').textContent = data.transaction_reference || 'None specified';
				document.getElementById('modalHistoryNotes').textContent = data.admin_notes || 'No comments';
				document.getElementById('modalHistoryMeta').textContent = 'Processed on ' + (data.processed_at || '—') + (data.processed_by_name ? ' by ' + data.processed_by_name : '');
			}

			var modal = document.getElementById('adminPayoutModal');
			modal.style.display = 'flex';
			document.body.style.overflow = 'hidden';
		}

		function closeAdminPayoutModal() {
			var modal = document.getElementById('adminPayoutModal');
			if (modal) {
				modal.style.display = 'none';
				document.body.style.overflow = '';
			}
		}

		function handleAdminModalBackdrop(event) {
			if (event.target && event.target.id === 'adminPayoutModal') {
				closeAdminPayoutModal();
			}
		}

		function submitAdminPayoutAction(action) {
			var notes = document.getElementById('modalAdminNotes').value.trim();
			if (action === 'reject') {
				if (!notes) {
					alert('Please enter an administrative reason explaining why this withdrawal request is being rejected.');
					document.getElementById('modalAdminNotes').focus();
					return;
				}
				if (!confirm('Are you sure you want to reject this payout request? The amount will be refunded back to the instructor\'s available balance.')) {
					return;
				}
			} else if (action === 'approve') {
				if (!confirm('Confirm disbursement: Have you transferred the funds or are you ready to approve this cash-out request?')) {
					return;
				}
			}

			document.getElementById('modalActionInput').value = action;
			document.getElementById('adminPayoutActionForm').submit();
		}

		// --- Modal 2: User Inspector Modal ---
		function openAdminUserInspectModal(data) {
			document.getElementById('inspectUserId').textContent = '#' + data.id;
			document.getElementById('inspectUserName').textContent = data.name;
			document.getElementById('inspectUserEmail').textContent = data.email;
			document.getElementById('inspectUserRole').textContent = data.role;
			document.getElementById('inspectUserJoined').textContent = data.created_at;

			let html = '';
			const extra = data.extra || {};
			for (const [key, val] of Object.entries(extra)) {
				html += '<div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9;">' +
				        '<span style="color: #64748b; font-weight: 600;">' + escapeHtml(key) + '</span>' +
				        '<strong style="color: #0f172a;">' + escapeHtml(String(val)) + '</strong>' +
				        '</div>';
			}
			document.getElementById('inspectUserActivityBox').innerHTML = html;

			const modal = document.getElementById('adminUserInspectModal');
			modal.style.display = 'flex';
			document.body.style.overflow = 'hidden';
		}

		function closeAdminUserInspectModal() {
			const modal = document.getElementById('adminUserInspectModal');
			if (modal) {
				modal.style.display = 'none';
				document.body.style.overflow = '';
			}
		}

		function handleInspectModalBackdrop(event) {
			if (event.target && event.target.id === 'adminUserInspectModal') {
				closeAdminUserInspectModal();
			}
		}

		// --- Modal 3: Change User Role Modal ---
		function openAdminChangeRoleModal(userId, userName, currentRoleId) {
			document.getElementById('changeRoleUserId').value = userId;
			document.getElementById('changeRoleUserName').textContent = userName;
			document.getElementById('changeRoleSelect').value = currentRoleId;

			const modal = document.getElementById('adminChangeRoleModal');
			modal.style.display = 'flex';
			document.body.style.overflow = 'hidden';
		}

		function closeAdminChangeRoleModal() {
			const modal = document.getElementById('adminChangeRoleModal');
			if (modal) {
				modal.style.display = 'none';
				document.body.style.overflow = '';
			}
		}

		function handleChangeRoleModalBackdrop(event) {
			if (event.target && event.target.id === 'adminChangeRoleModal') {
				closeAdminChangeRoleModal();
			}
		}

		// --- Modal 4: Delete Course Modal ---
		function openAdminDeleteCourseModal(courseId, courseTitle) {
			document.getElementById('deleteCourseIdInput').value = courseId;
			document.getElementById('deleteCourseTitleDisplay').textContent = courseTitle + ' (ID #' + courseId + ')';

			const modal = document.getElementById('adminDeleteCourseModal');
			modal.style.display = 'flex';
			document.body.style.overflow = 'hidden';
		}

		function closeAdminDeleteCourseModal() {
			const modal = document.getElementById('adminDeleteCourseModal');
			if (modal) {
				modal.style.display = 'none';
				document.body.style.overflow = '';
			}
		}

		function handleDeleteCourseModalBackdrop(event) {
			if (event.target && event.target.id === 'adminDeleteCourseModal') {
				closeAdminDeleteCourseModal();
			}
		}

		// --- Modal 5: Export Platform Data Modal ---
		function openAdminExportModal() {
			const modal = document.getElementById('adminExportModal');
			modal.style.display = 'flex';
			document.body.style.overflow = 'hidden';
		}

		function closeAdminExportModal() {
			const modal = document.getElementById('adminExportModal');
			if (modal) {
				modal.style.display = 'none';
				document.body.style.overflow = '';
			}
		}

		function handleExportModalBackdrop(event) {
			if (event.target && event.target.id === 'adminExportModal') {
				closeAdminExportModal();
			}
		}

		// --- Modal 6: Reject Course Modal ---
		function openAdminRejectCourseModal(courseId, courseTitle) {
			document.getElementById('modalRejectCourseId').value = courseId;
			document.getElementById('modalRejectCourseTitle').textContent = courseTitle + ' (ID #' + courseId + ')';
			document.getElementById('modalRejectReason').value = '';

			const modal = document.getElementById('adminRejectCourseModal');
			modal.style.display = 'flex';
			document.body.style.overflow = 'hidden';
		}

		function closeAdminRejectCourseModal() {
			const modal = document.getElementById('adminRejectCourseModal');
			if (modal) {
				modal.style.display = 'none';
				document.body.style.overflow = '';
			}
		}

		function handleRejectCourseModalBackdrop(event) {
			if (event.target && event.target.id === 'adminRejectCourseModal') {
				closeAdminRejectCourseModal();
			}
		}

		// --- Modal 7: Revoke Certificate Modal ---
		function openAdminRevokeCertModal(certId, certCode, studentName) {
			document.getElementById('modalRevokeCertId').value = certId;
			document.getElementById('modalRevokeCertCode').textContent = certCode;
			document.getElementById('modalRevokeStudentName').textContent = studentName;
			document.getElementById('modalRevokeReason').value = '';

			const modal = document.getElementById('adminRevokeCertModal');
			modal.style.display = 'flex';
			document.body.style.overflow = 'hidden';
		}

		function closeAdminRevokeCertModal() {
			const modal = document.getElementById('adminRevokeCertModal');
			if (modal) {
				modal.style.display = 'none';
				document.body.style.overflow = '';
			}
		}

		function handleRevokeCertModalBackdrop(event) {
			if (event.target && event.target.id === 'adminRevokeCertModal') {
				closeAdminRevokeCertModal();
			}
		}

		// --- Modal 8: New Sponsor Ad Modal ---
		function openAdminNewAdModal() {
			const modal = document.getElementById('adminNewAdModal');
			modal.style.display = 'flex';
			document.body.style.overflow = 'hidden';
		}

		function closeAdminNewAdModal() {
			const modal = document.getElementById('adminNewAdModal');
			if (modal) {
				modal.style.display = 'none';
				document.body.style.overflow = '';
				resetAdFilePicker();
			}
		}

		function handleNewAdModalBackdrop(event) {
			if (event.target && event.target.id === 'adminNewAdModal') {
				closeAdminNewAdModal();
			}
		}

		function handleAdFileSelected(input) {
			const file = input.files && input.files[0];
			if (file) {
				const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
				const titleEl = document.getElementById('adFilePickerTitle');
				const subEl = document.getElementById('adFilePickerSub');
				const dropzone = document.getElementById('adFileDropzone');
				if (titleEl) {
					titleEl.innerHTML = 'Selected: <span style="color: #15803d; word-break: break-all;">' + escapeHtml(file.name) + '</span>';
				}
				if (subEl) {
					subEl.textContent = sizeMB + ' MB \u2022 Ready to upload (Click to change file)';
				}
				if (dropzone) {
					dropzone.style.borderColor = '#16a34a';
					dropzone.style.backgroundColor = '#f0fdf4';
				}
				const urlInput = document.getElementById('adVideoUrlInput');
				if (urlInput) {
					urlInput.value = '';
				}
			}
		}

		function toggleAdUrlInput() {
			const urlInput = document.getElementById('adVideoUrlInput');
			if (urlInput) {
				const isHidden = (urlInput.style.display === 'none' || getComputedStyle(urlInput).display === 'none');
				urlInput.style.display = isHidden ? 'block' : 'none';
				if (isHidden) {
					urlInput.focus();
				}
			}
		}

		function resetAdFilePicker() {
			const fileInput = document.getElementById('adVideoFileInput');
			if (fileInput) {
				fileInput.value = '';
			}
			const titleEl = document.getElementById('adFilePickerTitle');
			if (titleEl) {
				titleEl.textContent = 'Click to open File Manager & select Video';
			}
			const subEl = document.getElementById('adFilePickerSub');
			if (subEl) {
				subEl.textContent = 'Supports MP4, WebM, MOV (Max 100MB) \u2022 Recommended 15-second sponsor spot';
			}
			const dropzone = document.getElementById('adFileDropzone');
			if (dropzone) {
				dropzone.style.borderColor = '#94a3b8';
				dropzone.style.backgroundColor = '#f8fafc';
			}
			const urlInput = document.getElementById('adVideoUrlInput');
			if (urlInput && !urlInput.value) {
				urlInput.value = 'assets/ad/sample_ad.mp4';
			}
		}

		// Drag and drop support for ad file dropzone
		document.addEventListener('DOMContentLoaded', function() {
			const dropzone = document.getElementById('adFileDropzone');
			const fileInput = document.getElementById('adVideoFileInput');
			if (dropzone && fileInput) {
				['dragenter', 'dragover'].forEach(function(eventName) {
					dropzone.addEventListener(eventName, function(e) {
						e.preventDefault();
						e.stopPropagation();
						dropzone.style.borderColor = '#16a34a';
						dropzone.style.backgroundColor = '#f0fdf4';
					}, false);
				});

				['dragleave', 'dragend'].forEach(function(eventName) {
					dropzone.addEventListener(eventName, function(e) {
						e.preventDefault();
						e.stopPropagation();
						if (!fileInput.files || !fileInput.files.length) {
							dropzone.style.borderColor = '#94a3b8';
							dropzone.style.backgroundColor = '#f8fafc';
						}
					}, false);
				});

				dropzone.addEventListener('drop', function(e) {
					e.preventDefault();
					e.stopPropagation();
					if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
						fileInput.files = e.dataTransfer.files;
						handleAdFileSelected(fileInput);
					}
				}, false);
			}
		});

		// Keyboard ESC listener to close open modals
		window.addEventListener('keydown', function(event) {
			if (event.key === 'Escape') {
				closeAdminPayoutModal();
				closeAdminUserInspectModal();
				closeAdminChangeRoleModal();
				closeAdminDeleteCourseModal();
				closeAdminExportModal();
				closeAdminRejectCourseModal();
				closeAdminRevokeCertModal();
				closeAdminNewAdModal();
				closeSidebarMobile();
			}
		});

		function escapeHtml(str) {
			var div = document.createElement('div');
			div.appendChild(document.createTextNode(str));
			return div.innerHTML;
		}
	</script>
</body>
</html>
