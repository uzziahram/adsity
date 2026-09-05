<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database/config.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userId     = $_SESSION['user_id'] ?? null;
$userName   = $_SESSION['full_name'] ?? '';
$userRole   = $_SESSION['role_name'] ?? '';

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($courseId <= 0) {
    header('Location: courses.php?status=error&message=' . urlencode('Invalid course ID.'));
    exit;
}

$course = null;
$instructor = null;
$lessons = [];
$enrollment = null;
$isEnrolled = false;

try {
    $pdo = getConnection();

    // 1. Fetch Course details with Instructor info
    $sqlCourse = "SELECT c.*, u.full_name AS instructor_name, u.email AS instructor_email 
                  FROM courses c 
                  LEFT JOIN users u ON c.instructor_id = u.id 
                  WHERE c.id = :id 
                  LIMIT 1";
    $stmtCourse = $pdo->prepare($sqlCourse);
    $stmtCourse->bindValue(':id', $courseId, PDO::PARAM_INT);
    $stmtCourse->execute();
    $course = $stmtCourse->fetch();

    if (!$course) {
        header('Location: courses.php?status=error&message=' . urlencode('The requested course could not be found.'));
        exit;
    }

    // 2. Fetch Lessons for this course
    $sqlLessons = "SELECT * FROM lessons WHERE course_id = :id ORDER BY lesson_number ASC";
    $stmtLessons = $pdo->prepare($sqlLessons);
    $stmtLessons->bindValue(':id', $courseId, PDO::PARAM_INT);
    $stmtLessons->execute();
    $lessons = $stmtLessons->fetchAll();

    // 3. Check Enrollment Status for Current User
    if ($isLoggedIn && $userRole === 'student') {
        $sqlEnr = "SELECT id, progress_percent, status, enrolled_at FROM enrollments WHERE user_id = :uid AND course_id = :cid LIMIT 1";
        $stmtEnr = $pdo->prepare($sqlEnr);
        $stmtEnr->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmtEnr->bindValue(':cid', $courseId, PDO::PARAM_INT);
        $stmtEnr->execute();
        $enrollment = $stmtEnr->fetch();
        $isEnrolled = ($enrollment !== false);
    }

} catch (PDOException $e) {
    header('Location: courses.php?status=error&message=' . urlencode('Database error: ' . $e->getMessage()));
    exit;
}

// Compute total duration
$totalMinutes = 0;
foreach ($lessons as $l) {
    $dur = $l['duration'] ?? '10:00';
    $parts = explode(':', $dur);
    if (count($parts) === 2) {
        $totalMinutes += (int)$parts[0] + round(((int)$parts[1]) / 60);
    }
}
$displayDuration = $totalMinutes > 0 ? "{$totalMinutes} mins" : ($course['total_lessons'] * 10 . " mins est.");

// Resolve thumbnail
$thumbSrc = !empty($course['thumbnail']) && str_starts_with($course['thumbnail'], 'uploads/')
    ? './' . $course['thumbnail']
    : './assets/adsity_assets/' . ($course['thumbnail'] ?: 'Web_Development_Basics.png');

$assessmentTypeNames = [
    'github_repo' => 'GitHub Repository Submission',
    'file_upload' => 'Project Archive / File Upload',
    'live_url'    => 'Live Hosted Application URL',
];
$assessmentName = $assessmentTypeNames[$course['assessment_type']] ?? 'Project Deliverable';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= htmlspecialchars($course['title']) ?> - Course Overview | Adsity</title>
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="course_details.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body class="details-page-body">

	<!-- Top Navigation Header -->
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
				<input type="text" placeholder="Search for Courses" onkeydown="if(event.key==='Enter') window.location.href='courses.php?search='+encodeURIComponent(this.value);">
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
				<a href="login.php?redirect_course=<?= $course['id'] ?>" class="btn-login">Log In</a>
				<a href="signup.php?redirect_course=<?= $course['id'] ?>" class="btn-signup">Sign Up</a>
			<?php endif; ?>
			<div class="lang-globe" title="Change Language">
				<img src="./assets/icons/globe.svg" width="18" height="18" alt="Language" style="display: block; filter: brightness(0) invert(1);">
			</div>
		</div>
	</header>

	<!-- Main Breadcrumb & Container -->
	<main class="details-container">

		<!-- Breadcrumbs -->
		<nav class="details-breadcrumb">
			<a href="courses.php" class="breadcrumb-back">
				<img src="./assets/icons/arrow-left.svg" width="16" height="16" alt="Back">
				Back to Explore Courses
			</a>
			<span class="breadcrumb-separator">/</span>
			<span class="breadcrumb-category"><?= htmlspecialchars($course['category'] ?? 'General') ?></span>
			<span class="breadcrumb-separator">/</span>
			<span class="breadcrumb-current"><?= htmlspecialchars($course['title']) ?></span>
		</nav>

		<!-- 2-Column Course Layout -->
		<div class="details-grid">

			<!-- LEFT MAIN COLUMN: Overview, Syllabus, Deliverables -->
			<div class="details-main-column">

				<!-- Hero Information Card -->
				<section class="course-intro-card">
					<div class="course-badge-row">
						<span class="category-pill-badge"><?= htmlspecialchars($course['category'] ?? 'Technology') ?></span>
						<span class="ad-supported-pill">
							<img src="./assets/icons/shield-check.svg" width="14" height="14" alt="Shield">
							100% Free • Ad-Funded Education
						</span>
						<span class="cert-pill">
							<img src="./assets/icons/award.svg" width="14" height="14" alt="Award">
							Verified Certificate Included
						</span>
					</div>

					<h1 class="course-page-title"><?= htmlspecialchars($course['title']) ?></h1>

					<p class="course-page-description">
						<?= nl2br(htmlspecialchars($course['description'] ?? '')) ?>
					</p>

					<!-- Instructor & Meta Row -->
					<div class="instructor-meta-box">
						<div class="instructor-avatar">
							<?= strtoupper(substr($course['instructor_name'] ?? 'Instructor', 0, 1)) ?>
						</div>
						<div class="instructor-info">
							<div class="instructor-name">
								<?= htmlspecialchars($course['instructor_name'] ?? 'Adsity Instructor') ?>
								<span class="instructor-tag">Verified Instructor</span>
							</div>
							<div class="instructor-sub">
								Published on <?= date('F j, Y', strtotime($course['created_at'])) ?>
							</div>
						</div>
					</div>

					<!-- Key Metrics Grid -->
					<div class="course-highlights-grid">
						<div class="highlight-item">
							<div class="highlight-icon">📚</div>
							<div>
								<div class="highlight-val"><?= count($lessons) > 0 ? count($lessons) : (int)$course['total_lessons'] ?> Lessons</div>
								<div class="highlight-lbl">Full Curriculum</div>
							</div>
						</div>
						<div class="highlight-item">
							<div class="highlight-icon">⏱️</div>
							<div>
								<div class="highlight-val"><?= htmlspecialchars($displayDuration) ?></div>
								<div class="highlight-lbl">Estimated Duration</div>
							</div>
						</div>
						<div class="highlight-item">
							<div class="highlight-icon">🎯</div>
							<div>
								<div class="highlight-val">Final Output</div>
								<div class="highlight-lbl">Required Deliverable</div>
							</div>
						</div>
						<div class="highlight-item">
							<div class="highlight-icon">⚡</div>
							<div>
								<div class="highlight-val">Self-Paced</div>
								<div class="highlight-lbl">Instant Access</div>
							</div>
						</div>
					</div>
				</section>

				<!-- SYLLABUS / LIST OF LESSONS SECTION -->
				<section class="syllabus-section">
					<div class="section-title-wrap">
						<div class="section-title-left">
							<img src="./assets/icons/book-open.svg" width="22" height="22" alt="Syllabus">
							<h2 class="section-heading">Course Curriculum & Video Lessons</h2>
						</div>
						<span class="syllabus-count-pill">
							<?= count($lessons) > 0 ? count($lessons) : (int)$course['total_lessons'] ?> Practical Modules
						</span>
					</div>

					<p class="section-subtext">
						Learn sequentially through practical video lessons with integrated 15-second sponsor breaks that keep your learning 100% free.
					</p>

					<div class="lessons-timeline-list">
						<?php if (!empty($lessons)): ?>
							<?php foreach ($lessons as $idx => $lesson): ?>
								<div class="lesson-row-card">
									<div class="lesson-num-badge">
										<?= str_pad($lesson['lesson_number'] ?? ($idx + 1), 2, '0', STR_PAD_LEFT) ?>
									</div>
									<div class="lesson-details">
										<div class="lesson-title-text"><?= htmlspecialchars($lesson['title']) ?></div>
										<div class="lesson-meta-line">
											<span class="lesson-format-tag">
												<img src="./assets/icons/play.svg" width="12" height="12" alt="Play">
												Video Lesson
											</span>
											<span class="lesson-duration-tag">⏱️ <?= htmlspecialchars($lesson['duration'] ?: '10:00') ?></span>
											<span class="lesson-sponsor-tag">Includes 15s Sponsor Break</span>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php else: ?>
							<!-- Fallback for legacy seeded courses without individual lesson rows -->
							<?php for ($i = 1; $i <= (int)($course['total_lessons'] ?: 5); $i++): ?>
								<div class="lesson-row-card">
									<div class="lesson-num-badge"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></div>
									<div class="lesson-details">
										<div class="lesson-title-text">Module <?= $i ?>: Core Concepts & Practical Implementation</div>
										<div class="lesson-meta-line">
											<span class="lesson-format-tag">
												<img src="./assets/icons/play.svg" width="12" height="12" alt="Play">
												Video Lesson
											</span>
											<span class="lesson-duration-tag">⏱️ 10:00</span>
										</div>
									</div>
								</div>
							<?php endfor; ?>
						<?php endif; ?>
					</div>
				</section>

				<!-- FINAL OUTPUT DELIVERABLES / ASSESSMENT SECTION -->
				<section class="assessment-section">
					<div class="section-title-wrap">
						<div class="section-title-left">
							<img src="./assets/icons/award.svg" width="22" height="22" alt="Award">
							<h2 class="section-heading">Final Exam Deliverables & Assessment</h2>
						</div>
						<span class="assessment-format-badge">
							<?php if ($course['assessment_type'] === 'github_repo'): ?>
								🐙 <?= $assessmentName ?>
							<?php elseif ($course['assessment_type'] === 'file_upload'): ?>
								📦 <?= $assessmentName ?>
							<?php else: ?>
								🌐 <?= $assessmentName ?>
							<?php endif; ?>
						</span>
					</div>

					<p class="section-subtext">
						To unlock your accredited completion certificate, you must successfully build and submit the following practical deliverable:
					</p>

					<div class="assessment-deliverable-card">
						<div class="deliverable-header">
							<div class="deliverable-icon-box">
								<?php if ($course['assessment_type'] === 'github_repo'): ?>
									<img src="./assets/icons/github.svg" width="28" height="28" alt="GitHub">
								<?php elseif ($course['assessment_type'] === 'file_upload'): ?>
									<img src="./assets/icons/file-text.svg" width="28" height="28" alt="File">
								<?php else: ?>
									<img src="./assets/icons/globe.svg" width="28" height="28" alt="Web">
								<?php endif; ?>
							</div>
							<div>
								<h3 class="deliverable-title">Required Output: <?= htmlspecialchars($assessmentName) ?></h3>
								<div class="deliverable-status">Verified Project Assessment</div>
							</div>
						</div>

						<div class="deliverable-instructions-box">
							<div class="instructions-label">Instructor Instructions & Grading Rubrics:</div>
							<div class="instructions-body">
								<?php if (!empty($course['assessment_instructions'])): ?>
									<?= nl2br(htmlspecialchars($course['assessment_instructions'])) ?>
								<?php else: ?>
									Build a functional project applying all key principles covered throughout the curriculum. Ensure clean code architecture, proper documentation, and tested deliverables.
								<?php endif; ?>
							</div>
						</div>

						<div class="deliverable-cert-guarantee">
							<img src="./assets/icons/check-circle.svg" width="20" height="20" alt="Check">
							<div>
								<strong>Guaranteed Verified Credential:</strong> Submitting this deliverable immediately generates your unique verifiable certificate code (e.g. <code>ADS-2026-XXXXXXXX</code>).
							</div>
						</div>
					</div>
				</section>

			</div>

			<!-- RIGHT SIDEBAR COLUMN: Sticky Thumbnail & Enrollment CTA Card -->
			<aside class="details-sidebar-column">
				<div class="sticky-enroll-card">

					<!-- Thumbnail Box -->
					<div class="enroll-thumb-frame">
						<img 
							src="<?= htmlspecialchars($thumbSrc) ?>" 
							alt="<?= htmlspecialchars($course['title']) ?>" 
							class="enroll-thumb-img"
							onerror="this.src='./assets/adsity_assets/Web_Development_Basics.png'"
						>
						<span class="enroll-category-badge"><?= htmlspecialchars($course['category'] ?? 'Course') ?></span>
					</div>

					<!-- Action Buttons -->
					<div class="enroll-action-wrapper">
						<?php if ($isLoggedIn && $userRole === 'student'): ?>
							<?php if ($isEnrolled): ?>
								<div class="already-enrolled-banner">
									<img src="./assets/icons/check-circle.svg" width="18" height="18" alt="Enrolled">
									<div>
										<strong>You are enrolled!</strong>
										<div style="font-size: 0.78rem; opacity: 0.9;">Progress: <?= (int)$enrollment['progress_percent'] ?>% completed</div>
									</div>
								</div>

								<a href="student/dashboard.php" class="btn-enroll-action btn-enroll-action--resume">
									<span>Go to Student Dashboard</span>
									<img src="./assets/icons/arrow-right.svg" width="18" height="18" alt="Go" style="filter: brightness(0) invert(1);">
								</a>
							<?php else: ?>
								<form action="student/enroll_function.php" method="POST">
									<input type="hidden" name="course_id" value="<?= $course['id'] ?>">
									<button type="submit" class="btn-enroll-action btn-enroll-action--primary">
										<span>Enroll in Course</span>
										<img src="./assets/icons/arrow-right.svg" width="18" height="18" alt="Enroll" style="filter: brightness(0) invert(1);">
									</button>
								</form>
							<?php endif; ?>

						<?php elseif ($isLoggedIn && $userRole === 'instructor'): ?>
							<div class="role-notice-banner">
								<span>Logged in as Instructor</span>
							</div>
							<a href="instructor/dashboard.php" class="btn-enroll-action btn-enroll-action--secondary">
								<span>Instructor Studio</span>
							</a>

						<?php elseif ($isLoggedIn && $userRole === 'admin'): ?>
							<a href="admin/dashboard.php" class="btn-enroll-action btn-enroll-action--secondary">
								<span>Admin Portal</span>
							</a>

						<?php else: ?>
							<!-- Guest / Not Logged In -->
							<a href="login.php?redirect_course=<?= $course['id'] ?>" class="btn-enroll-action btn-enroll-action--primary">
								<span>Log In to Enroll</span>
								<img src="./assets/icons/arrow-right.svg" width="18" height="18" alt="Log In" style="filter: brightness(0) invert(1);">
							</a>
							<div class="guest-signup-prompt">
								Don't have an account? 
								<a href="signup.php?redirect_course=<?= $course['id'] ?>">Sign up</a>
							</div>
						<?php endif; ?>
					</div>

					<!-- Features Checklist -->
					<div class="card-features-list">
						<div class="feature-item">
							<img src="./assets/icons/check-circle.svg" width="16" height="16" alt="Check">
							<span>Full access to all <?= count($lessons) > 0 ? count($lessons) : (int)$course['total_lessons'] ?> video lessons</span>
						</div>
						<div class="feature-item">
							<img src="./assets/icons/check-circle.svg" width="16" height="16" alt="Check">
							<span>Verified certificate upon final output submission</span>
						</div>
						<div class="feature-item">
							<img src="./assets/icons/check-circle.svg" width="16" height="16" alt="Check">
							<span>Self-paced with unlimited on-demand replay</span>
						</div>
					</div>

					<div class="card-guarantee-note">
						🔒 Adsity Learning Pledge: High-quality education accessible to anyone, anywhere.
					</div>

				</div>
			</aside>

		</div>

	</main>

	<!-- Global Footer -->
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
