<?php
require_once __DIR__ . '/courses_function.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Explore Courses - Adsity</title>
	<link rel="stylesheet" href="style.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<style>
		.explore-page-body {
			background-color: #f8fafc;
			min-height: 100vh;
		}

		.explore-hero {
			background: linear-gradient(135deg, #14221b 0%, #1a382c 100%);
			padding: 56px 24px;
			color: #ffffff;
			text-align: center;
			position: relative;
			overflow: hidden;
		}

		.explore-hero-inner {
			max-width: 800px;
			margin: 0 auto;
		}

		.explore-badge {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			background-color: rgba(39, 214, 98, 0.15);
			border: 1px solid rgba(39, 214, 98, 0.4);
			color: #27D662;
			font-size: 0.8rem;
			font-weight: 700;
			padding: 4px 14px;
			border-radius: 9999px;
			margin-bottom: 16px;
			text-transform: uppercase;
			letter-spacing: 0.05em;
		}

		.explore-title {
			font-size: 2.5rem;
			font-weight: 800;
			margin-bottom: 12px;
			letter-spacing: -0.02em;
		}

		.explore-subtitle {
			font-size: 1.05rem;
			color: #cbd5e1;
			line-height: 1.6;
		}

		/* Search & Filter Section */
		.explore-controls-wrapper {
			max-width: 1200px;
			margin: -24px auto 36px auto;
			padding: 0 24px;
			position: relative;
			z-index: 10;
		}

		.explore-controls-card {
			background-color: #ffffff;
			border: 1px solid #e2e8f0;
			border-radius: 16px;
			padding: 20px 24px;
			box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
			display: flex;
			flex-direction: column;
			gap: 16px;
		}

		.search-filter-form {
			display: flex;
			gap: 12px;
			width: 100%;
		}

		.search-input-field {
			flex: 1;
			background-color: #f1f5f9;
			border: 1px solid #cbd5e1;
			border-radius: 10px;
			padding: 12px 18px;
			font-size: 0.95rem;
			font-family: inherit;
			color: #0f172a;
			outline: none;
			transition: border-color 0.2s ease, background-color 0.2s ease;
		}

		.search-input-field:focus {
			border-color: var(--primary-green);
			background-color: #ffffff;
		}

		.btn-search-submit {
			background-color: var(--primary-green);
			color: #ffffff;
			border: none;
			padding: 12px 24px;
			border-radius: 10px;
			font-weight: 700;
			font-family: inherit;
			font-size: 0.95rem;
			cursor: pointer;
			display: inline-flex;
			align-items: center;
			gap: 8px;
			transition: background-color 0.2s ease;
		}

		.btn-search-submit:hover {
			background-color: #21b854;
		}

		/* Category Filter Pills */
		.category-pills-list {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
		}

		.category-pill {
			background-color: #f8fafc;
			border: 1px solid #e2e8f0;
			color: #475569;
			padding: 6px 14px;
			border-radius: 9999px;
			font-size: 0.82rem;
			font-weight: 600;
			text-decoration: none;
			transition: all 0.2s ease;
		}

		.category-pill:hover {
			border-color: #cbd5e1;
			color: #0f172a;
		}

		.category-pill--active {
			background-color: #14221b;
			border-color: #14221b;
			color: #27D662;
			font-weight: 700;
		}

		/* Courses Grid */
		.explore-container {
			max-width: 1200px;
			margin: 0 auto 60px auto;
			padding: 0 24px;
		}

		.results-count-text {
			font-size: 0.9rem;
			color: #64748b;
			margin-bottom: 24px;
			font-weight: 600;
		}

		.explore-courses-grid {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
			gap: 28px;
		}

		.explore-course-card {
			background-color: #ffffff;
			border: 1px solid #e2e8f0;
			border-radius: 16px;
			overflow: hidden;
			display: flex;
			flex-direction: column;
			box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
			transition: transform 0.2s ease, box-shadow 0.2s ease;
		}

		.explore-course-card:hover {
			transform: translateY(-4px);
			box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.09);
		}

		.explore-course-thumb {
			position: relative;
			height: 180px;
			background-color: #0f172a;
			overflow: hidden;
		}

		.explore-course-thumb img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}

		.explore-course-category {
			position: absolute;
			top: 12px;
			left: 12px;
			background-color: rgba(15, 23, 42, 0.85);
			backdrop-filter: blur(4px);
			color: #ffffff;
			font-size: 0.75rem;
			font-weight: 700;
			padding: 4px 10px;
			border-radius: 6px;
			text-transform: uppercase;
			letter-spacing: 0.05em;
		}

		.explore-course-body {
			padding: 22px;
			display: flex;
			flex-direction: column;
			flex: 1;
		}

		.explore-course-title {
			font-size: 1.2rem;
			font-weight: 800;
			color: #0f172a;
			margin-bottom: 8px;
			line-height: 1.35;
		}

		.explore-course-desc {
			font-size: 0.9rem;
			color: #64748b;
			line-height: 1.55;
			margin-bottom: 20px;
			flex: 1;
		}

		.explore-course-meta {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding-top: 14px;
			border-top: 1px solid #f1f5f9;
			margin-bottom: 16px;
			font-size: 0.82rem;
			color: #64748b;
			font-weight: 600;
		}

		.meta-tag-free {
			color: #16a34a;
			font-weight: 700;
			display: flex;
			align-items: center;
			gap: 4px;
		}

		.btn-enroll-course {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			gap: 8px;
			background-color: #27D662;
			color: #ffffff;
			font-weight: 700;
			font-size: 0.92rem;
			padding: 12px 18px;
			border-radius: 10px;
			text-decoration: none;
			transition: background-color 0.2s ease;
		}

		.btn-enroll-course:hover {
			background-color: #21b854;
		}

		.empty-explore-box {
			background-color: #ffffff;
			border: 2px dashed #cbd5e1;
			border-radius: 16px;
			padding: 56px 24px;
			text-align: center;
			color: #64748b;
		}
	</style>
</head>

<body class="explore-page-body">

	<!-- Navigation Header -->
	<header class="navbar">
		<div class="nav-left">
			<a href="index.php" class="logo">
				<span class="logo-icon">
					<img class="icon" src="./assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo">
				</span>
			</a>
			<a href="courses.php" class="explore-btn" style="color: var(--primary-green); font-weight: 800;">Explore</a>
			<div class="search-bar">
				<span class="search-icon">&#128269;</span>
				<input type="text" placeholder="Search for Courses">
			</div>
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
				<a href="logout.php" class="btn-signup" style="background-color: #475569;" onclick="return confirm('Are you sure you want to log out?');">Log Out</a>
			<?php else: ?>
				<a href="login.php" class="btn-login">Log In</a>
				<a href="signup.php" class="btn-signup">Sign Up</a>
			<?php endif; ?>
			<div class="lang-globe" title="Change Language">
				<img src="./assets/icons/globe.svg" width="18" height="18" alt="Language" style="display: block; filter: brightness(0) invert(1);">
			</div>
		</div>
	</header>

	<!-- Hero Header -->
	<section class="explore-hero">
		<div class="explore-hero-inner">
			<span class="explore-badge">
				<img src="./assets/icons/shield-check.svg" width="14" height="14" alt="Shield" style="filter: brightness(0) saturate(100%) invert(69%) sepia(57%) saturate(548%) hue-rotate(88deg) brightness(97%) contrast(92%);">
				Ad-Supported Free Education
			</span>
			<h1 class="explore-title">Explore All Available Courses</h1>
			<p class="explore-subtitle">
				Learn high-demand technology, programming, and cloud computing skills. Complete courses for free and earn industry-recognized verified certificates.
			</p>
		</div>
	</section>

	<!-- Search & Filter Controls -->
	<div class="explore-controls-wrapper">
		<div class="explore-controls-card">
			<form action="courses.php" method="GET" class="search-filter-form">
				<?php if ($category !== ''): ?>
					<input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
				<?php endif; ?>
				<input 
					type="text" 
					name="search" 
					class="search-input-field" 
					placeholder="Search courses by keyword (e.g. Python, Cloud, Security)..." 
					value="<?= htmlspecialchars($search) ?>"
				>
				<button type="submit" class="btn-search-submit">
					<span>Search</span>
					<img src="./assets/icons/arrow-right.svg" width="16" height="16" alt="Search" style="filter: brightness(0) invert(1);">
				</button>
			</form>

			<!-- Category Pills -->
			<div class="category-pills-list">
				<a href="courses.php<?= $search ? '?search=' . urlencode($search) : '' ?>" class="category-pill <?= ($category === '' || $category === 'all') ? 'category-pill--active' : '' ?>">
					All Categories
				</a>
				<?php foreach ($categories as $cat): ?>
					<a href="courses.php?category=<?= urlencode($cat) ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="category-pill <?= ($category === $cat) ? 'category-pill--active' : '' ?>">
						<?= htmlspecialchars($cat) ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<!-- Courses Grid -->
	<main class="explore-container">
		<div class="results-count-text">
			Showing <?= count($courses) ?> available <?= count($courses) === 1 ? 'course' : 'courses' ?>
			<?php if ($category): ?> in <strong><?= htmlspecialchars($category) ?></strong><?php endif; ?>
			<?php if ($search): ?> matching "<strong><?= htmlspecialchars($search) ?></strong>"<?php endif; ?>
		</div>

		<?php if (!empty($courses)): ?>
			<div class="explore-courses-grid">
				<?php foreach ($courses as $c): ?>
					<article class="explore-course-card">
						<div class="explore-course-thumb">
							<img src="./assets/adsity_assets/<?= htmlspecialchars($c['thumbnail']) ?>" alt="<?= htmlspecialchars($c['title']) ?>">
							<span class="explore-course-category"><?= htmlspecialchars($c['category']) ?></span>
						</div>

						<div class="explore-course-body">
							<h2 class="explore-course-title"><?= htmlspecialchars($c['title']) ?></h2>
							<p class="explore-course-desc"><?= htmlspecialchars($c['description']) ?></p>

							<div class="explore-course-meta">
								<span>📚 <?= $c['total_lessons'] ?> Practical Lessons</span>
								<span class="meta-tag-free">
									<img src="./assets/icons/check-circle.svg" width="14" height="14" alt="Check">
									Free Certificate
								</span>
							</div>

							<a href="signup.php" class="btn-enroll-course">
								<span>Enroll for Free</span>
								<img src="./assets/icons/arrow-right.svg" width="16" height="16" alt="Enroll" style="filter: brightness(0) invert(1);">
							</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php else: ?>
			<div class="empty-explore-box">
				<img src="./assets/icons/book-open.svg" width="48" height="48" alt="No courses" style="opacity: 0.35;">
				<h3 style="font-size: 1.25rem; font-weight: 700; color: #334155; margin-top: 14px; margin-bottom: 8px;">No courses found</h3>
				<p style="color: #94a3b8; margin-bottom: 20px;">Try adjusting your search query or selecting a different category.</p>
				<a href="courses.php" class="btn-enroll-course" style="display: inline-flex; width: fit-content;">View All Courses</a>
			</div>
		<?php endif; ?>
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
