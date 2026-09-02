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
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<style>
		.admin-container {
			max-width: 1200px;
			margin: 40px auto;
			padding: 0 20px;
		}

		.admin-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 30px;
			flex-wrap: wrap;
			gap: 16px;
		}

		.admin-title {
			font-size: 2rem;
			font-weight: 800;
			color: var(--text-dark);
		}

		.admin-subtitle {
			font-size: 0.95rem;
			color: var(--text-muted);
			margin-top: 4px;
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
			border-radius: 14px;
			padding: 24px;
			display: flex;
			align-items: center;
			gap: 18px;
			box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
		}

		.stat-icon-box {
			width: 52px;
			height: 52px;
			border-radius: 12px;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.stat-icon-box--blue {
			background-color: #e0f2fe;
		}

		.stat-icon-box--green {
			background-color: #e6f9ed;
		}

		.stat-icon-box--purple {
			background-color: #f3e8ff;
		}

		.stat-number {
			font-size: 1.75rem;
			font-weight: 800;
			color: var(--text-dark);
			line-height: 1.1;
		}

		.stat-label {
			font-size: 0.85rem;
			color: var(--text-muted);
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.05em;
			margin-top: 4px;
		}

		/* Table Section */
		.dashboard-section {
			background-color: #ffffff;
			border: 1px solid #e2e8f0;
			border-radius: 16px;
			padding: 28px;
			margin-bottom: 36px;
			box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
		}

		.section-heading {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 20px;
		}

		.section-heading h2 {
			font-size: 1.35rem;
			font-weight: 700;
			color: var(--text-dark);
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
			font-size: 0.95rem;
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

		.user-badge-instructor {
			display: inline-block;
			background-color: #e0f2fe;
			color: #0369a1;
			font-size: 0.78rem;
			font-weight: 700;
			padding: 3px 10px;
			border-radius: 6px;
		}

		.user-badge-student {
			display: inline-block;
			background-color: #dcfce7;
			color: #15803d;
			font-size: 0.78rem;
			font-weight: 700;
			padding: 3px 10px;
			border-radius: 6px;
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
			padding: 40px 20px;
			color: #94a3b8;
			font-size: 0.95rem;
		}
	</style>
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
			<a href="../logout.php" class="btn-login" style="background-color: #475569;">Log Out</a>
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
