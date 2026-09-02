<?php
require_once __DIR__ . '/dashboard_function.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Dashboard - Adsity</title>
	<link rel="stylesheet" href="../style.css">
	<link rel="stylesheet" href="admindashboard.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body style="background-color: #f8fafc;">

	<!-- Navigation Header -->
	<header class="navbar">
		<div class="nav-left">
			<a href="../index.php" class="logo">
				<span class="logo-icon">
					<img class="icon" src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo">
				</span>
			</a>
			<span style="font-weight: 700; color: #0f172a; font-size: 1.05rem;">Admin Control Center</span>
		</div>

		<div class="nav-right">
			<a href="../index.php" class="teach-link">View Website</a>
			<a href="../logout.php" class="btn-login" style="background-color: #475569;" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
		</div>
	</header>

	<main class="admin-container">

		<!-- Admin Header Title -->
		<div class="admin-header">
			<div>
				<h1 class="admin-title">User Monitoring &amp; Management</h1>
				<p class="admin-subtitle">Live overview of registered teachers, instructors, and students across Adsity.</p>
			</div>
		</div>

		<!-- Status Alerts -->
		<?php if ($status === 'success'): ?>
			<div class="alert alert--success">
				<img src="../assets/icons/check-circle.svg" width="20" height="20" alt="Success">
				<span><?= htmlspecialchars($message ?? 'Operation successful!') ?></span>
			</div>
		<?php elseif ($status === 'error'): ?>
			<div class="alert alert--error">
				<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
				<span><?= htmlspecialchars($message ?? 'An error occurred.') ?></span>
			</div>
		<?php endif; ?>

		<!-- KPI Metric Cards -->
		<div class="stats-grid">
			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--blue">
					<img src="../assets/icons/users.svg" width="24" height="24" alt="Users">
				</div>
				<div>
					<div class="stat-number"><?= $totalUsers ?></div>
					<div class="stat-label">Total Users</div>
				</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--purple">
					<img src="../assets/icons/graduation-cap.svg" width="24" height="24" alt="Instructors">
				</div>
				<div>
					<div class="stat-number"><?= $totalInstructors ?></div>
					<div class="stat-label">Teachers / Instructors</div>
				</div>
			</div>

			<div class="stat-card">
				<div class="stat-icon-box stat-icon-box--green">
					<img src="../assets/icons/user-check.svg" width="24" height="24" alt="Students">
				</div>
				<div>
					<div class="stat-number"><?= $totalStudents ?></div>
					<div class="stat-label">Registered Students</div>
				</div>
			</div>
		</div>

		<!-- Teachers / Instructors Table -->
		<section class="dashboard-section">
			<div class="section-heading">
				<h2>
					<img src="../assets/icons/graduation-cap.svg" width="20" height="20" alt="Instructors">
					Teachers &amp; Instructors
				</h2>
				<span class="badge-count"><?= $totalInstructors ?> Total</span>
			</div>

			<div class="table-responsive">
				<?php if (!empty($instructors)): ?>
					<table class="data-table">
						<thead>
							<tr>
								<th>ID</th>
								<th>Instructor Name</th>
								<th>Email Address</th>
								<th>Role</th>
								<th>Joined Date</th>
								<th style="text-align: right;">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($instructors as $teacher): ?>
								<tr>
									<td>#<?= htmlspecialchars($teacher['id']) ?></td>
									<td>
										<strong><?= htmlspecialchars($teacher['full_name']) ?></strong>
									</td>
									<td><?= htmlspecialchars($teacher['email']) ?></td>
									<td>
										<span class="user-badge-instructor">Instructor</span>
									</td>
									<td><?= htmlspecialchars(date('M d, Y', strtotime($teacher['created_at']))) ?></td>
									<td style="text-align: right;">
										<form method="POST" action="delete_user.php" onsubmit="return confirm('Are you sure you want to delete this instructor?');" style="display: inline;">
											<input type="hidden" name="user_id" value="<?= htmlspecialchars($teacher['id']) ?>">
											<button type="submit" name="delete_user" class="btn-delete">
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
						No instructors registered yet.
					</div>
				<?php endif; ?>
			</div>
		</section>

		<!-- Students Table -->
		<section class="dashboard-section">
			<div class="section-heading">
				<h2>
					<img src="../assets/icons/user-check.svg" width="20" height="20" alt="Students">
					Students
				</h2>
				<span class="badge-count"><?= $totalStudents ?> Total</span>
			</div>

			<div class="table-responsive">
				<?php if (!empty($students)): ?>
					<table class="data-table">
						<thead>
							<tr>
								<th>ID</th>
								<th>Student Name</th>
								<th>Email Address</th>
								<th>Role</th>
								<th>Joined Date</th>
								<th style="text-align: right;">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($students as $student): ?>
								<tr>
									<td>#<?= htmlspecialchars($student['id']) ?></td>
									<td>
										<strong><?= htmlspecialchars($student['full_name']) ?></strong>
									</td>
									<td><?= htmlspecialchars($student['email']) ?></td>
									<td>
										<span class="user-badge-student">Student</span>
									</td>
									<td><?= htmlspecialchars(date('M d, Y', strtotime($student['created_at']))) ?></td>
									<td style="text-align: right;">
										<form method="POST" action="delete_user.php" onsubmit="return confirm('Are you sure you want to delete this student?');" style="display: inline;">
											<input type="hidden" name="user_id" value="<?= htmlspecialchars($student['id']) ?>">
											<button type="submit" name="delete_user" class="btn-delete">
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
						No students registered yet.
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
				<p class="footer-copyright">&copy; 2026 Adsity Admin Portal. All rights reserved.</p>
			</div>
		</div>
	</footer>

</body>
</html>
