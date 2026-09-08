<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $_SESSION['full_name'] ?? '';
$userRole   = $_SESSION['role_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Adsity - Learn for Free</title>
	<link rel="stylesheet" href="style.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body>

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
			<?php if ($isLoggedIn): ?>
				<?php if ($userRole === 'admin'): ?>
					<a href="admin/dashboard.php" class="btn-login" style="background-color: #0284c7;">Admin Portal</a>
				<?php elseif ($userRole === 'instructor'): ?>
					<a href="instructor/dashboard.php" class="btn-login" style="background-color: #0284c7;">Instructor Studio</a>
				<?php else: ?>
					<a href="student/dashboard.php" class="btn-login" style="background-color: var(--primary-green);">My Dashboard</a>
				<?php endif; ?>
				<a href="logout.php" class="btn-signup" style="background-color: #475569;">Log Out</a>
			<?php else: ?>
				<a href="login.php" class="btn-login">Log In</a>
				<a href="signup.php" class="btn-signup">Sign Up</a>
			<?php endif; ?>
			<div class="lang-globe" title="Change Language">
				<img src="./assets/icons/globe.svg" width="18" height="18" alt="Language" style="display: block; filter: brightness(0) invert(1);">
			</div>
		</div>
	</header>

	<!-- Hero Section -->
	<section class="hero">
		<div class="hero-grid">
			<!-- Hero Left: Image -->
			<div class="hero-image-container">
				<img class="hero-img" src="./assets/adsity_assets/hero_image.jpg" alt="Hero Image">
			</div>

			<!-- Hero Right: Text & CTA -->
			<div class="hero-content">
				<h1 class="hero-title">
					Credentials Funded by ads,<br>
					Earned by You
				</h1>
				<p class="hero-description">
					Watch a few ads, pass the assessment, and unlock shareable certificates to boost your resume for free.
				</p>
				<a href="signup.php" class="btn-green">Start Learning for free</a>
			</div>
		</div>
	</section>

	<!-- Build Essential Resume Power Section -->
	<section class="resume-section">
		<div class="resume-grid">
			<!-- Text Description -->
			<div class="resume-text">
				<h2>Build essential resume power</h2>
				<p>Access top-tier courses and claim industry recognized certificates for zero cost, powered by brief ad breaks.</p>
			</div>

			<!-- Carousel Cards -->
			<div class="resume-carousel">
				<div class="carousel-cards">
					<!-- Card 1 -->
					<div class="course-card">
						<img src="./assets/adsity_assets/Fullstack_Web_Development.jpg" alt="Full-Stack Web Development" class="course-card-img">
						<div class="course-card-body">
							<h3 class="course-card-title">Full-Stack Web Development</h3>
							<p class="course-card-tags">Next.js | MongoDB | Redis<br>Docker | Kubernetes</p>
						</div>
					</div>

					<!-- Card 2 -->
					<div class="course-card">
						<img src="./assets/adsity_assets/Logo_Design.jpg" alt="Logo Design" class="course-card-img">
						<div class="course-card-body">
							<h3 class="course-card-title">Logo Design</h3>
							<p class="course-card-tags">Adobe Illustrator<br>Adobe Photoshop</p>
						</div>
					</div>

					<!-- Card 3 -->
					<div class="course-card">
						<img src="./assets/adsity_assets/Web_Development_Basics.png" alt="Web Development Basics" class="course-card-img">
						<div class="course-card-body">
							<h3 class="course-card-title">Web Development Basics</h3>
							<p class="course-card-tags">Html | CSS | JavaScript</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<div class="green-divider"></div>

	<!-- Trending Courses Section -->
	<section class="trending-section">
		<div class="trending-header">
			<h2 class="trending-title">Trending Courses</h2>
		</div>

		<div class="trending-grid-wrapper">
			<div class="trending-grid">
				<!-- Card 1 -->
				<div class="trending-card-frame">
					<div class="trending-card">
						<div class="trending-card-header">
							<img src="./assets/adsity_assets/CyberSecurityFundamentals.png" alt="Cybersecurity Fundamentals" class="trending-card-img">
						</div>
						<div class="trending-card-body">
							<div class="trending-card-content">
								<h3 class="trending-card-title">Cybersecurity Fundamentals</h3>
								<p class="trending-card-desc">Threat models, basic encryption, identity management, and securing network traffic.</p>
							</div>
							<div class="trending-card-footer">
								<button class="btn-start-now">Start Now</button>
								<span class="rating"><span class="rating-star">&#9733;</span><span class="rating-score"> 4.8</span></span>
							</div>
						</div>
					</div>
				</div>

				<!-- Card 2 -->
				<div class="trending-card-frame">
					<div class="trending-card">
						<div class="trending-card-header">
							<img src="./assets/adsity_assets/sql_and_database_management.png" alt="SQL &amp; Database Management" class="trending-card-img">
						</div>
						<div class="trending-card-body">
							<div class="trending-card-content">
								<h3 class="trending-card-title">SQL &amp; Database Management</h3>
								<p class="trending-card-desc">Designing relational databases, writing queries, and managing data with SQL.</p>
							</div>
							<div class="trending-card-footer">
								<button class="btn-start-now">Start Now</button>
								<span class="rating"><span class="rating-star">&#9733;</span><span class="rating-score"> 4.8</span></span>
							</div>
						</div>
					</div>
				</div>

				<!-- Card 3 -->
				<div class="trending-card-frame">
					<div class="trending-card">
						<div class="trending-card-header">
							<img src="./assets/adsity_assets/Computer_Networking_Fundamentals.png" alt="Computer Networking Fundamentals" class="trending-card-img">
						</div>
						<div class="trending-card-body">
							<div class="trending-card-content">
								<h3 class="trending-card-title">Computer Networking Fundamentals</h3>
								<p class="trending-card-desc">How devices communicate, IP addressing, DNS, TCP/IP stack, routers, and switches.</p>
							</div>
							<div class="trending-card-footer">
								<button class="btn-start-now">Start Now</button>
								<span class="rating"><span class="rating-star">&#9733;</span><span class="rating-score"> 4.8</span></span>
							</div>
						</div>
					</div>
				</div>

				<!-- Card 4 -->
				<div class="trending-card-frame">
					<div class="trending-card">
						<div class="trending-card-header">
							<img src="./assets/adsity_assets/Clound_Computing.png" alt="Cloud Computing" class="trending-card-img">
						</div>
						<div class="trending-card-body">
							<div class="trending-card-content">
								<h3 class="trending-card-title">Cloud Computing</h3>
								<p class="trending-card-desc">Cloud environments, deployment basics, and configuration.</p>
							</div>
							<div class="trending-card-footer">
								<button class="btn-start-now">Start Now</button>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</section>

	<div class="blue-divider"></div>

	<!-- Popular Skills Section -->
	<section class="skills-section">
		<div class="skills-container">
			<header class="skills-header">
				<h2 class="skills-title">Popular Skills</h2>
				<p class="skills-subtitle">Master in-demand technologies and validate your expertise with our free learning pathways.</p>
			</header>

			<div class="skills-grid">
				<!-- Row 1: Green Accent (3 Cards) -->
				<!-- Python -->
				<article class="skill-card skill-card--green">
					<div class="skill-card__surface">
						<div class="skill-card__header">
							<span class="skill-card__badge">
								<img src="./assets/icons/code.svg" width="16" height="16" alt="Code" class="skill-card__icon">
								<h3 class="skill-card__title">Python</h3>
							</span>
						</div>
						<div class="skill-card__panel skill-card__panel--white">
							<img src="./assets/adsity_assets/python_logo.png" alt="Python logo" class="skill-card__img">
						</div>
						<p class="skill-card__desc">Build data pipelines, automate workflows, and power AI models with a versatile language.</p>
					</div>
				</article>

				<!-- JavaScript -->
				<article class="skill-card skill-card--green">
					<div class="skill-card__surface">
						<div class="skill-card__header">
							<span class="skill-card__badge">
								<img src="./assets/icons/code.svg" width="16" height="16" alt="Code" class="skill-card__icon">
								<h3 class="skill-card__title">JavaScript</h3>
							</span>
						</div>
						<div class="skill-card__panel skill-card__panel--white">
							<img src="./assets/adsity_assets/javascript_logo.jpg" alt="JavaScript logo" class="skill-card__img">
						</div>
						<p class="skill-card__desc">Drive web frontends, backends, and desktop apps with a language that runs everywhere.</p>
					</div>
				</article>

				<!-- React -->
				<article class="skill-card skill-card--green">
					<div class="skill-card__surface">
						<div class="skill-card__header">
							<span class="skill-card__badge">
								<img src="./assets/icons/code.svg" width="16" height="16" alt="Code" class="skill-card__icon">
								<h3 class="skill-card__title">React</h3>
							</span>
						</div>
						<div class="skill-card__panel skill-card__panel--black">
							<img src="./assets/adsity_assets/react_logo.jpg" alt="React logo" class="skill-card__img">
						</div>
						<p class="skill-card__desc">Build fast, reusable UI components for web applications with a popular JavaScript library.</p>
					</div>
				</article>

				<!-- Row 2: Crimson Red Accent (3 Cards) -->
				<!-- AWS -->
				<article class="skill-card skill-card--red">
					<div class="skill-card__surface">
						<div class="skill-card__header">
							<span class="skill-card__badge">
								<img src="./assets/icons/cloud.svg" width="16" height="16" alt="Cloud" class="skill-card__icon">
								<h3 class="skill-card__title">AWS</h3>
							</span>
						</div>
						<div class="skill-card__panel skill-card__panel--navy">
							<img src="./assets/adsity_assets/aws_logo.jpg" alt="AWS logo" class="skill-card__img">
						</div>
						<p class="skill-card__desc">Deploy scalable infrastructure, services, and applications on a leading cloud platform.</p>
					</div>
				</article>

				<!-- UI / UX design -->
				<article class="skill-card skill-card--red">
					<div class="skill-card__surface">
						<div class="skill-card__header">
							<span class="skill-card__badge">
								<img src="./assets/icons/layout.svg" width="16" height="16" alt="Design" class="skill-card__icon">
								<h3 class="skill-card__title">UI / UX design</h3>
							</span>
						</div>
						<div class="skill-card__panel skill-card__panel--dark-muted">
							<img src="./assets/adsity_assets/ui_ux_design.jpg" alt="UI / UX design mockups" class="skill-card__img">
						</div>
						<p class="skill-card__desc">Craft intuitive interfaces that delight users and drive conversion through research and testing.</p>
					</div>
				</article>

				<!-- Docker -->
				<article class="skill-card skill-card--red">
					<div class="skill-card__surface">
						<div class="skill-card__header">
							<span class="skill-card__badge">
								<img src="./assets/icons/box.svg" width="16" height="16" alt="Container" class="skill-card__icon">
								<h3 class="skill-card__title">Docker</h3>
							</span>
						</div>
						<div class="skill-card__panel skill-card__panel--white">
							<img src="./assets/adsity_assets/docker_logo.jpg" alt="Docker logo" class="skill-card__img">
						</div>
						<p class="skill-card__desc">Package applications into consistent, portable containers for reliable delivery.</p>
					</div>
				</article>

				<!-- Row 3: Cyan / Electric Blue Accent (2 Centered Cards) -->
				<!-- Cyber Security -->
				<article class="skill-card skill-card--blue skill-card--row3-left">
					<div class="skill-card__surface">
						<div class="skill-card__header">
							<span class="skill-card__badge">
								<img src="./assets/icons/shield.svg" width="16" height="16" alt="Shield" class="skill-card__icon">
								<h3 class="skill-card__title">Cyber Security</h3>
							</span>
						</div>
						<div class="skill-card__panel skill-card__panel--deep-blue">
							<img src="./assets/adsity_assets/cybersecurity_shield.jpg" alt="Cyber Security logo" class="skill-card__img">
						</div>
						<p class="skill-card__desc">Protect systems, networks, and data from threats with secure design and incident response.</p>
					</div>
				</article>

				<!-- Blockchain -->
				<article class="skill-card skill-card--blue skill-card--row3-right">
					<div class="skill-card__surface">
						<div class="skill-card__header">
							<span class="skill-card__badge">
								<img src="./assets/icons/link.svg" width="16" height="16" alt="Link" class="skill-card__icon">
								<h3 class="skill-card__title">Blockchain</h3>
							</span>
						</div>
						<div class="skill-card__panel skill-card__panel--vivid-blue">
							<img src="./assets/adsity_assets/blockchain_logo.png" alt="Blockchain logo" class="skill-card__img">
						</div>
						<p class="skill-card__desc">Build decentralized applications, smart contracts, and secure ledger systems.</p>
					</div>
				</article>
			</div>
		</div>
	</section>

	<!-- Enroll Now CTA Section -->
	<section class="cta-section">
		<!-- Diagonal Rotated Colored Rectangles (Side by Side, Equal Sizes) -->
		<div class="cta-stripes-container" aria-hidden="true">
			<div class="cta-stripe cta-stripe--red"></div>
			<div class="cta-stripe cta-stripe--blue"></div>
			<div class="cta-stripe cta-stripe--green"></div>
		</div>

		<div class="cta-container">
			<div class="cta-card">
				<div class="cta-card__image-wrapper">
					<img src="./assets/adsity_assets/0c605f5725146674637fe3342783ecf4 1.jpg" alt="Students collaborating around a laptop" class="cta-card__image">
				</div>
				<div class="cta-card__body">
					<h2 class="cta-card__title">Enroll Now!</h2>
					<p class="cta-card__desc">Ready to start learning? Enroll now and get instant access to free courses, certificates, and a supportive community. No credit card required-just sign up and start building the skills you need.</p>
					<a href="signup.php" class="cta-card__btn">Enroll Now</a>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="footer">
		<!-- Top Decorative Accent Bar -->

		<!-- Main Footer Content -->
		<div class="footer-main">
			<!-- Left Column: Logo + Newsletter -->
			<div class="footer-left">
				<a href="index.php" class="footer-logo">
					<img src="./assets/adsity_assets/Adsity_Logo.png" alt="Adsity" class="footer-logo-img">
				</a>
				<div class="footer-newsletter">
					<label class="footer-newsletter-label">Sign up for a newsletter:</label>
					<form class="footer-newsletter-form">
						<input type="email" placeholder="Email" class="footer-newsletter-input" required>
						<button type="submit" class="footer-newsletter-btn">Subscribe</button>
					</form>
				</div>
			</div>

			<!-- Right Column: 3 Link Columns -->
			<div class="footer-right">
				<!-- About us -->
				<div class="footer-col">
					<h4 class="footer-col-title">About us</h4>
					<ul class="footer-links">
						<li><a href="#">About us</a></li>
						<li><a href="#">Careers</a></li>
						<li><a href="#">Blog</a></li>
						<li><a href="#">Find More on Adsity</a></li>
					</ul>
				</div>

				<!-- Discover Adsity -->
				<div class="footer-col">
					<h4 class="footer-col-title">Discover Adsity</h4>
					<ul class="footer-links">
						<li><a href="#">Get the app</a></li>
						<li><a href="teach.php">Teach on Adsity</a></li>
						<li><a href="#">Courses</a></li>
						<li><a href="#">Affiliate</a></li>
						<li><a href="#">Help and Support</a></li>
					</ul>
				</div>

				<!-- Legal & Accessibility -->
				<div class="footer-col">
					<h4 class="footer-col-title">Legal &amp; Accessibility</h4>
					<ul class="footer-links">
						<li><a href="#">Accessibility Statement</a></li>
						<li><a href="#">Privacy Policy</a></li>
						<li><a href="#">Sitemap</a></li>
						<li><a href="#">Terms</a></li>
					</ul>
				</div>
			</div>
		</div>

		<!-- Bottom Copyright Bar -->
		<div class="footer-bottom">
			<div class="footer-bottom-inner">
				<img src="./assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo" class="footer-bottom-logo-img">
				<p class="footer-copyright">&copy; 2026 Adsity, Org.</p>
			</div>
		</div>
	</footer>

	<script src="assets/js/adsity-ui.js"></script>
	<script src="assets/js/logout_modal.js"></script>
</body>

</html>