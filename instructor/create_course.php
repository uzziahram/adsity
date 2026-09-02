<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'instructor') {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in with an instructor account.'));
    exit;
}

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Create New Course - Adsity Instructor</title>
	<link rel="stylesheet" href="../style.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body class="auth-page">

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
			<a href="dashboard.php" class="teach-link">Back to Dashboard</a>
			<a href="../logout.php" class="btn-login" style="background-color: #475569;" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
		</div>
	</header>

	<!-- Main Form Container -->
	<main class="auth-wrapper">
		<div class="auth-bg-blob auth-bg-blob--green"></div>
		<div class="auth-bg-blob auth-bg-blob--blue"></div>

		<div class="auth-card" style="max-width: 580px;">
			<a href="dashboard.php" class="back-home-link">
				<img src="../assets/icons/arrow-left.svg" width="16" height="16" alt="Back">
				Back to Dashboard
			</a>

			<div class="auth-card__header">
				<span class="auth-badge" style="background-color: #e0f2fe; color: #0284c7;">
					<img src="../assets/icons/plus.svg" width="14" height="14" alt="Plus">
					Publish Curriculum
				</span>
				<h1 class="auth-title">Create New Course</h1>
				<p class="auth-subtitle">Publish a course funded by ad breaks. Share your expertise and earn ad revenue.</p>
			</div>

			<?php if ($status === 'error'): ?>
				<div class="alert alert--error">
					<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
					<span><?= htmlspecialchars($message ?? 'An error occurred.') ?></span>
				</div>
			<?php endif; ?>

			<form action="create_course_function.php" method="POST" class="auth-form">
				<div class="form-group">
					<label for="title" class="form-label">Course Title</label>
					<div class="form-input-wrapper">
						<input 
							type="text" 
							id="title" 
							name="title" 
							class="form-input" 
							placeholder="e.g. Modern TypeScript &amp; React Deep Dive" 
							required
						>
					</div>
				</div>

				<div class="form-group">
					<label for="category" class="form-label">Category</label>
					<div class="form-input-wrapper">
						<select id="category" name="category" class="form-input" style="background-color: #ffffff;" required>
							<option value="Development">Development</option>
							<option value="Security">Security</option>
							<option value="Networking">Networking</option>
							<option value="Cloud & DevOps">Cloud &amp; DevOps</option>
							<option value="Data & Backend">Data &amp; Backend</option>
							<option value="Design">Design / UI/UX</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="total_lessons" class="form-label">Total Lessons</label>
					<div class="form-input-wrapper">
						<input 
							type="number" 
							id="total_lessons" 
							name="total_lessons" 
							class="form-input" 
							placeholder="10" 
							value="10" 
							min="1" 
							max="100" 
							required
						>
					</div>
				</div>

				<div class="form-group">
					<label for="thumbnail" class="form-label">Course Thumbnail Artwork</label>
					<div class="form-input-wrapper">
						<select id="thumbnail" name="thumbnail" class="form-input" style="background-color: #ffffff;" required>
							<option value="Fullstack_Web_Development.jpg">Fullstack Web Development</option>
							<option value="CyberSecurityFundamentals.png">Cybersecurity Fundamentals</option>
							<option value="Clound_Computing.png">Cloud Computing</option>
							<option value="sql_and_database_management.png">SQL &amp; Database Management</option>
							<option value="Computer_Networking_Fundamentals.png">Networking Fundamentals</option>
							<option value="Web_Development_Basics.png">Web Development Basics</option>
							<option value="Logo_Design.jpg">Logo &amp; Brand Design</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="description" class="form-label">Course Description &amp; Overview</label>
					<div class="form-input-wrapper">
						<textarea 
							id="description" 
							name="description" 
							class="form-input" 
							rows="4" 
							placeholder="Describe what students will learn, projects built, and prerequisites..." 
							style="resize: vertical; min-height: 100px;" 
							required
						></textarea>
					</div>
				</div>

				<button type="submit" name="create_course" class="btn-auth-submit" style="background-color: #0284c7;">
					Publish Course
					<img src="../assets/icons/arrow-right.svg" width="18" height="18" alt="Submit" style="filter: brightness(0) invert(1);">
				</button>
			</form>
		</div>
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
