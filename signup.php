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
	<title>Sign Up - Adsity</title>
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
					<h2 class="auth-image-card-title">100% Free Tech Education</h2>
					<p class="auth-image-card-desc">Watch brief ads, complete real-world project submissions, and earn industry-recognized verified certificates.</p>
					<div class="auth-image-card-badges">
						<span class="auth-image-card-badge">💻 Hands-on Code</span>
						<span class="auth-image-card-badge">📜 Verifiable Certificates</span>
						<span class="auth-image-card-badge">⚡ Zero Tuition</span>
					</div>
				</div>
			</div>
		</div>

		<!-- Right Auth Form Side -->
		<div class="auth-form-side">
			<div class="auth-form-container">
				<div class="auth-form-header">
					<h2 class="auth-title">Create Your Student Account</h2>
					<p class="auth-subtitle">Get started in less than a minute and begin learning immediately.</p>
				</div>

				<?php if ($status === 'success'): ?>
					<div class="alert alert--success">
						<img src="./assets/icons/check-circle.svg" width="20" height="20" alt="Success">
						<span><?= htmlspecialchars($message ?? 'Student registered successfully!') ?></span>
					</div>
				<?php elseif ($status === 'error'): ?>
					<div class="alert alert--error">
						<img src="./assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
						<span><?= htmlspecialchars($message ?? 'An error occurred.') ?></span>
					</div>
				<?php endif; ?>

				<form action="signup_function.php" method="POST" class="auth-form">
					<div class="form-group">
						<label for="full_name" class="form-label">Full Name</label>
						<div class="form-input-wrapper">
							<input 
								type="text" 
								id="full_name" 
								name="full_name" 
								class="form-input" 
								placeholder="John Doe" 
								required
							>
						</div>
					</div>

					<div class="form-group">
						<label for="email" class="form-label">Email Address</label>
						<div class="form-input-wrapper">
							<input 
								type="email" 
								id="email" 
								name="email" 
								class="form-input" 
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
								class="form-input" 
								placeholder="At least 6 characters" 
								required
							>
						</div>
					</div>

					<div class="form-group">
						<label for="confirm_password" class="form-label">Confirm Password</label>
						<div class="form-input-wrapper">
							<input 
								type="password" 
								id="confirm_password" 
								name="confirm_password" 
								class="form-input" 
								placeholder="Repeat your password" 
								required
							>
						</div>
					</div>

					<label class="form-checkbox-label">
						<input type="checkbox" name="terms" required checked>
						<span>I agree to Adsity's <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
					</label>

					<button type="submit" name="signup" class="btn-auth-submit">
						Sign Up for Free
						<img src="./assets/icons/arrow-right.svg" width="18" height="18" alt="Arrow" style="filter: brightness(0) invert(1);">
					</button>
				</form>

				<div class="auth-form-footer">
					Already have an account? <a href="login.php">Log In</a>
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
