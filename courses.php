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
	<link rel="stylesheet" href="courses.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
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
			<a href="courses.php" class="explore-btn">Explore</a>
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
				<?php foreach ($courses as $c): 
					$isEnrolledInThis = in_array((int)$c['id'], array_map('intval', $enrolledCourseIds));
				?>
					<article class="explore-course-card">
						<a href="course_details.php?id=<?= $c['id'] ?>" class="explore-course-thumb" style="display: block; text-decoration: none;">
							<?php
							$thumbSrc = !empty($c['thumbnail']) && str_starts_with($c['thumbnail'], 'uploads/')
								? './' . $c['thumbnail']
								: './assets/adsity_assets/' . ($c['thumbnail'] ?: 'Web_Development_Basics.png');
							?>
							<img src="<?= htmlspecialchars($thumbSrc) ?>" alt="<?= htmlspecialchars($c['title']) ?>" onerror="this.src='./assets/adsity_assets/Web_Development_Basics.png'">
							<span class="explore-course-category"><?= htmlspecialchars($c['category']) ?></span>
						</a>

						<div class="explore-course-body">
							<h2 class="explore-course-title">
								<a href="course_details.php?id=<?= $c['id'] ?>" style="color: inherit; text-decoration: none;">
									<?= htmlspecialchars($c['title']) ?>
								</a>
							</h2>
							<p class="explore-course-desc"><?= htmlspecialchars($c['description']) ?></p>

							<div class="explore-course-meta">
								<span>📚 <?= $c['total_lessons'] ?> Practical Lessons</span>
								<span class="meta-tag-free">
									<img src="./assets/icons/check-circle.svg" width="14" height="14" alt="Check">
									Free Certificate
								</span>
							</div>

							<?php if ($isEnrolledInThis): ?>
								<a href="course_details.php?id=<?= $c['id'] ?>" class="btn-enroll-course" style="background: linear-gradient(135deg, #15803d 0%, #166534 100%);">
									<span>In Progress (View)</span>
									<img src="./assets/icons/arrow-right.svg" width="16" height="16" alt="View" style="filter: brightness(0) invert(1);">
								</a>
							<?php else: ?>
								<a href="course_details.php?id=<?= $c['id'] ?>" class="btn-enroll-course">
									<span>View Course & Enroll</span>
									<img src="./assets/icons/arrow-right.svg" width="16" height="16" alt="Enroll" style="filter: brightness(0) invert(1);">
								</a>
							<?php endif; ?>
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
