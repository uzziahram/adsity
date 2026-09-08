<?php
require_once __DIR__ . '/validation.php';
ensureSessionStarted();

$status         = $_GET['status'] ?? null;
$message        = $_GET['message'] ?? null;
$id             = $_GET['id'] ?? null;
$redirectCourse = isset($_GET['redirect_course']) ? (int)$_GET['redirect_course'] : 0;
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


	<!-- Main Auth Container (Full-Screen Split Layout) -->
	<main class="auth-split-wrapper">
		<!-- Left Image Hero Side -->
		<div class="auth-image-side">
			<img src="./assets/adsity_assets/pexels-armin-rimoldi-5553731.jpg" alt="Adsity Login" class="auth-image-hero-bg">
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
					<a href="index.php" class="auth-logo-link" title="Return to Adsity Home">
						<img src="./assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo" class="auth-logo-img">
					</a>
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
					<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
					<?php if ($redirectCourse > 0): ?>
						<input type="hidden" name="redirect_course" value="<?= $redirectCourse ?>">
					<?php endif; ?>
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
					Don't have an account yet? <a href="signup.php<?= $redirectCourse > 0 ? '?redirect_course=' . $redirectCourse : '' ?>">Create a free student account</a>
				</div>
			</div>
		</div>
	</main>


</body>
</html>
