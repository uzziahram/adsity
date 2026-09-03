<?php
$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
$id      = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Log In - Adsity</title>
	<link rel="stylesheet" href="style.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body class="auth-page">

	<!-- Navigation Header -->
	<header class="navbar">
		<div class="nav-left">
			<a href="index.php" class="logo">
				<span class="logo-icon">
					<img class="icon" src="./assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo">
				</span>
			</a>
			<a href="courses.php" class="explore-btn">Explore</a>
			<form action="courses.php" method="GET" class="search-bar">
				<button type="submit" style="background: none; border: none; cursor: pointer; padding: 0;" class="search-icon">&#128269;</button>
				<input type="text" name="search" placeholder="Search for Courses">
			</form>
		</div>

		<div class="nav-right">
			<a href="teach.php" class="teach-link">Teach on Adsity</a>
			<a href="login.php" class="btn-login">Log In</a>
			<a href="signup.php" class="btn-signup">Sign Up</a>
			<div class="lang-globe" title="Change Language">
				<img src="./assets/icons/globe.svg" width="18" height="18" alt="Language" style="display: block; filter: brightness(0) invert(1);">
			</div>
		</div>
	</header>

	<!-- Main Auth Container (Full-Screen Split Layout) -->
	<main class="auth-split-wrapper">
		<!-- Left Image Hero Side -->
		<div class="auth-image-side">
			<img src="./assets/student_sign_up.jpg" alt="Student Learning on Adsity" class="auth-image-hero-bg">
			<div class="auth-image-overlay"></div>

			<div class="auth-image-top">
				<a href="index.php" class="auth-brand-back-btn">
					<img src="./assets/icons/arrow-left.svg" width="16" height="16" alt="Back" style="filter: brightness(0) invert(1);">
					Back to Home
				</a>
			</div>

			<div class="auth-image-bottom">
				<div class="auth-image-glass-card">
					<h2 class="auth-image-card-title">Welcome back to Adsity</h2>
					<p class="auth-image-card-desc">Resume your enrolled courses, track multi-video lesson modules, and download your earned certificates.</p>
					<div class="auth-image-card-badges">
						<span class="auth-image-card-badge">🎓 100% Free Learning</span>
						<span class="auth-image-card-badge">📜 Verified Certificates</span>
						<span class="auth-image-card-badge">📺 Ad-Sponsored</span>
					</div>
				</div>
			</div>
		</div>

		<!-- Right Auth Form Side -->
		<div class="auth-form-side">
			<div class="auth-form-container">
				<div class="auth-form-header">
					<h2 class="auth-title">Log In to Your Account</h2>
					<p class="auth-subtitle">Enter your credentials below to access your student or instructor dashboard.</p>
				</div>

				<?php if ($status === 'success'): ?>
					<div class="alert alert--success">
						<img src="./assets/icons/check-circle.svg" width="20" height="20" alt="Success">
						<span><?= htmlspecialchars($message ?? 'Logged in successfully!') ?></span>
					</div>
				<?php elseif ($status === 'error'): ?>
					<div class="alert alert--error">
						<img src="./assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
						<span><?= htmlspecialchars($message ?? 'Invalid credentials.') ?></span>
					</div>
				<?php endif; ?>

				<form action="login_function.php" method="POST" class="auth-form">
					<div class="form-group">
						<label for="email" class="form-label">Email Address</label>
						<div class="form-input-wrapper">
							<input 
								type="email" 
								id="email" 
								name="email" 
								class="form-input form-input--blue" 
								placeholder="name@example.com" 
								required
							>
						</div>
					</div>

					<div class="form-group">
						<label for="password" class="form-label">Password</label>
						<div class="form-input-wrapper">
							<input 
								type="password" 
								id="password" 
								name="password" 
								class="form-input form-input--blue" 
								placeholder="Enter your password" 
								required
							>
						</div>
					</div>

					<button type="submit" name="login" class="btn-auth-submit btn-auth-submit--blue">
						Log In to Dashboard
						<img src="./assets/icons/arrow-right.svg" width="18" height="18" alt="Arrow" style="filter: brightness(0) invert(1);">
					</button>
				</form>

				<div class="auth-form-footer">
					Don't have an account yet? <a href="signup.php">Create a free student account</a>
				</div>
			</div>
		</div>
	</main>

	<!-- Footer -->
	<footer class="footer">
		<div class="footer-bottom">
			<div class="footer-bottom-inner">
				<img src="./assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo" class="footer-bottom-logo-img">
				<p class="footer-copyright">&copy; 2026 Adsity, Org. All rights reserved.</p>
			</div>
		</div>
	</footer>

</body>
</html>
