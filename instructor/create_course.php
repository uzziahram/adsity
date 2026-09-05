<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'instructor') {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in with an instructor account.'));
    exit;
}

require_once __DIR__ . '/../database/config.php';

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

$instructorId = $_SESSION['user_id'];
$instructorName = $_SESSION['full_name'] ?? 'Instructor';

try {
    $pdo = getConnection();
    $stmtUser = $pdo->prepare("SELECT full_name FROM users WHERE id = :id LIMIT 1");
    $stmtUser->bindValue(':id', $instructorId, PDO::PARAM_INT);
    $stmtUser->execute();
    $row = $stmtUser->fetch();
    if ($row && !empty($row['full_name'])) {
        $instructorName = $row['full_name'];
    }
} catch (Exception $e) {
    // fallback to session
}

$initials = strtoupper(substr($instructorName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Create Course - Adsity Instructor Studio</title>
	<link rel="stylesheet" href="../style.css">
	<link rel="stylesheet" href="instructordashboard.css">
	<link rel="stylesheet" href="create_course.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body class="instructor-dashboard-body">

	<!-- Top Navigation Header -->
	<header class="instructor-navbar">
		<div class="instructor-nav-left">
			<button type="button" class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle Navigation Menu">
				<span class="hamburger-line"></span>
				<span class="hamburger-line"></span>
				<span class="hamburger-line"></span>
			</button>

			<a href="../index.php" class="instructor-nav-logo" title="Adsity Home">
				<img src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo">
			</a>
			<span class="studio-badge-title">Course Creator Studio</span>
		</div>

		<div class="instructor-nav-right">
			<a href="dashboard.php" class="instructor-nav-link">
				<img src="../assets/icons/arrow-left.svg" width="14" height="14" alt="Back" style="vertical-align: middle; margin-right: 4px;">
				<span>Back to Dashboard</span>
			</a>
			<a href="../courses.php" class="instructor-nav-link">Explore Catalog</a>
			<div class="instructor-user-pill">
				<div class="instructor-user-avatar"><?= $initials ?></div>
				<span class="instructor-user-name"><?= htmlspecialchars($instructorName) ?></span>
			</div>
			<a href="../logout.php" class="instructor-btn-logout" onclick="return confirm('Are you sure you want to log out?');">
				<img src="../assets/icons/arrow-left.svg" width="14" height="14" alt="Logout">
				<span>Log Out</span>
			</a>
		</div>
	</header>

	<!-- Master Studio Layout: Left Sidebar + Fluid Main Workspace -->
	<div class="instructor-dashboard-layout">

		<!-- ==========================================
		     LEFT SIDEBAR NAVBAR
		     ========================================== -->
		<aside class="instructor-sidebar" id="instructorSidebar">
			<div class="sidebar-top-section">

				<!-- Instructor Profile Summary Card -->
				<div class="instructor-profile-card">
					<div class="instructor-profile-avatar"><?= $initials ?></div>
					<div class="instructor-profile-info">
						<div class="instructor-profile-name" title="<?= htmlspecialchars($instructorName) ?>">
							<?= htmlspecialchars($instructorName) ?>
						</div>
						<div class="instructor-profile-status">
							<span class="status-indicator-dot"></span>
							Ad Revenue Partner
						</div>
					</div>
				</div>

				<!-- Primary Action Highlighted -->
				<div class="sidebar-cta-wrapper">
					<a href="create_course.php" class="btn-sidebar-create" style="box-shadow: 0 0 0 3px var(--primary-green-border);">
						<img src="../assets/icons/plus.svg" width="16" height="16" alt="Add" style="filter: brightness(0) invert(1);">
						<span>Publish New Course</span>
					</a>
				</div>

				<!-- Studio Navigation Actions -->
				<nav class="sidebar-nav-group">
					<div class="sidebar-group-label">Studio Actions</div>

					<a href="dashboard.php#overview" class="sidebar-nav-item">
						<div class="sidebar-nav-item-left">
							<img src="../assets/icons/layout.svg" class="sidebar-nav-icon" alt="Overview">
							<span>Dashboard Overview</span>
						</div>
					</a>

					<a href="dashboard.php#courses" class="sidebar-nav-item">
						<div class="sidebar-nav-item-left">
							<img src="../assets/icons/book-open.svg" class="sidebar-nav-icon" alt="Courses">
							<span>My Courses</span>
						</div>
					</a>

					<a href="dashboard.php#submissions" class="sidebar-nav-item">
						<div class="sidebar-nav-item-left">
							<img src="../assets/icons/award.svg" class="sidebar-nav-icon" alt="Submissions">
							<span>Student Submissions</span>
						</div>
					</a>

					<a href="dashboard.php#revenue" class="sidebar-nav-item">
						<div class="sidebar-nav-item-left">
							<img src="../assets/icons/dollar-sign.svg" class="sidebar-nav-icon" alt="Revenue">
							<span>Ad Revenue &amp; Splits</span>
						</div>
						<span class="sidebar-badge-share">70%</span>
					</a>

					<div class="sidebar-group-label" style="margin-top: 22px;">Explore Platform</div>

					<a href="../courses.php" class="sidebar-nav-item">
						<div class="sidebar-nav-item-left">
							<img src="../assets/icons/globe.svg" class="sidebar-nav-icon" alt="Courses">
							<span>Course Catalog</span>
						</div>
					</a>

					<a href="../index.php" class="sidebar-nav-item">
						<div class="sidebar-nav-item-left">
							<img src="../assets/icons/external-link.svg" class="sidebar-nav-icon" alt="Home">
							<span>Platform Homepage</span>
						</div>
					</a>
				</nav>
			</div>

			<!-- Sidebar Footer -->
			<div class="sidebar-bottom-section">
				<div class="partner-perk-box">
					<div class="perk-title">
						<img src="../assets/icons/shield-check.svg" width="16" height="16" alt="Partner">
						<span>Partner Status</span>
					</div>
					<p class="perk-desc">70% creator share on all automated video ad breaks.</p>
				</div>

				<a href="../logout.php" class="btn-sidebar-logout" onclick="return confirm('Are you sure you want to log out?');">
					<img src="../assets/icons/arrow-left.svg" width="15" height="15" alt="Logout">
					<span>Log Out</span>
				</a>
			</div>
		</aside>

		<!-- Backdrop for mobile drawer -->
		<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

		<!-- ==========================================
		     MAIN FULL-SCREEN STUDIO WORKSPACE
		     ========================================== -->
		<main class="studio-main-wrapper">

			<!-- Cohesive Studio Header Card -->
			<div class="studio-header-card">
				<div class="studio-header-left">
					<div class="studio-breadcrumb">
						<a href="dashboard.php">Dashboard</a>
						<span class="studio-breadcrumb-separator">/</span>
						<span>Course Creator</span>
					</div>
					<h1 class="studio-page-title">
						<span>Publish Multi-Video Course</span>
					</h1>
					<p class="studio-page-subtitle">
						Upload sequential lessons. Adsity monetizes your curriculum with automated 15-second sponsor ads between videos.
					</p>
				</div>

				<div class="studio-header-badges">
					<div class="studio-pill-status">
						<span class="status-dot-pulse"></span>
						<span>Curriculum Studio</span>
					</div>
					<div class="studio-pill-partner">
						<span>⚡ 70% Partner Split</span>
					</div>
				</div>
			</div>

			<!-- Error Alert Notification -->
			<?php if ($status === 'error'): ?>
				<div class="alert alert--error" style="margin-bottom: 24px;">
					<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
					<span><?= htmlspecialchars($message ?? 'An error occurred.') ?></span>
				</div>
			<?php endif; ?>

			<!-- Full-Screen 2-Column Workstation Form -->
			<form action="create_course_function.php" method="POST" enctype="multipart/form-data" id="courseForm">
				
				<!-- Hidden thumbnail input controlled by visual picker -->
				<input type="hidden" id="thumbnail" name="thumbnail" value="Fullstack_Web_Development.jpg">

				<div class="course-builder-grid">

					<!-- ==========================================
					     LEFT COLUMN: FORM SECTIONS (~60%)
					     ========================================== -->
					<div class="builder-main-column">

						<!-- Section 1: Course Basics & Metadata -->
						<div class="studio-section-card">
							<div class="studio-section-header">
								<div>
									<h2 class="studio-section-title">
										<span class="section-number-badge">1</span>
										<span>Course Details &amp; Metadata</span>
									</h2>
									<p class="studio-section-desc">Define the core information learners see in the catalog.</p>
								</div>
								<span class="section-tag-pill">Step 1 of 3</span>
							</div>

							<div class="form-group">
								<label for="title" class="form-label">
									<span>Course Title</span>
									<span class="form-required">* Required</span>
								</label>
								<div class="form-input-wrapper">
									<input 
										type="text" 
										id="title" 
										name="title" 
										class="form-input" 
										placeholder="e.g. Modern Full-Stack Web Development Masterclass" 
										required
										oninput="syncLivePreview()"
									>
								</div>
								<div class="form-hint">Make it engaging and specific to help students quickly understand what they will learn.</div>
							</div>

							<div class="form-group">
								<label for="category" class="form-label">
									<span>Primary Category</span>
									<span class="form-required">* Required</span>
								</label>
								<div class="form-input-wrapper">
									<select id="category" name="category" class="form-input" required onchange="syncLivePreview()">
										<option value="Development">Development</option>
										<option value="Security">Security</option>
										<option value="Networking">Networking</option>
										<option value="Cloud &amp; DevOps">Cloud &amp; DevOps</option>
										<option value="Data &amp; Backend">Data &amp; Backend</option>
										<option value="Design">Design / UI/UX</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="description" class="form-label">
									<span>Course Description &amp; Overview</span>
									<span class="form-required">* Required</span>
								</label>
								<div class="form-input-wrapper">
									<textarea 
										id="description" 
										name="description" 
										class="form-input" 
										rows="4" 
										placeholder="Describe the skills taught, curriculum highlights, prerequisites, and student takeaways..." 
										style="resize: vertical;" 
										required
										oninput="updateChecklist()"
									></textarea>
								</div>
								<div class="form-hint">A comprehensive description significantly improves student enrollments.</div>
							</div>
						</div>

						<!-- Section 2: Video Lessons Curriculum Builder -->
						<div class="studio-section-card">
							<div class="studio-section-header">
								<div>
									<h2 class="studio-section-title">
										<span class="section-number-badge">2</span>
										<span>Video Lessons Curriculum</span>
									</h2>
									<p class="studio-section-desc">Sequential lesson videos for student progression and automatic ad breaks.</p>
								</div>
								<span class="section-tag-pill" id="lessonCounterBadge">2 Lessons Added</span>
							</div>

							<div id="lessonsContainer">
								<!-- Initial Lesson 1 -->
								<div class="lesson-item" data-lesson="1">
									<div class="lesson-item-header">
										<span class="lesson-badge">
											<img src="../assets/icons/video.svg" width="12" height="12" alt="Lesson">
											Lesson 01
										</span>
									</div>

									<div class="form-group" style="margin-bottom: 12px;">
										<label class="form-label">
											<span>Lesson Title</span>
											<span class="form-required">*</span>
										</label>
										<div class="form-input-wrapper">
											<input 
												type="text" 
												name="lesson_titles[]" 
												class="form-input" 
												placeholder="e.g. 01. Introduction to Next.js &amp; Project Setup" 
												required
												oninput="updateChecklist()"
											>
										</div>
									</div>

									<div class="form-group">
										<label class="form-label">
											<span>Lesson Video File</span>
											<span class="form-required">* (MP4, WebM, OGG)</span>
										</label>
										<div class="lesson-upload-box">
											<div class="upload-box-left">
												<img src="../assets/icons/video.svg" width="18" height="18" alt="Video File" style="opacity: 0.6;">
												<input 
													type="file" 
													name="lesson_videos[]" 
													class="lesson-video-input" 
													accept="video/mp4,video/webm,video/ogg" 
													onchange="handleVideoDurationDetection(this)"
													required
												>
											</div>
											<div class="duration-badge" style="display: none;">
												<span>⏱️ Duration:</span>
												<span class="duration-text">00:00</span>
											</div>
										</div>
										<input type="hidden" name="lesson_durations[]" class="lesson-duration-hidden" value="">
									</div>
								</div>

								<!-- Initial Lesson 2 -->
								<div class="lesson-item" data-lesson="2">
									<div class="lesson-item-header">
										<span class="lesson-badge">
											<img src="../assets/icons/video.svg" width="12" height="12" alt="Lesson">
											Lesson 02
										</span>
										<button type="button" class="btn-remove-lesson" onclick="removeLesson(this)">
											<img src="../assets/icons/trash.svg" width="13" height="13" alt="Delete">
											<span>Remove</span>
										</button>
									</div>

									<div class="form-group" style="margin-bottom: 12px;">
										<label class="form-label">
											<span>Lesson Title</span>
											<span class="form-required">*</span>
										</label>
										<div class="form-input-wrapper">
											<input 
												type="text" 
												name="lesson_titles[]" 
												class="form-input" 
												placeholder="e.g. 02. Database Architecture &amp; Relationships" 
												required
												oninput="updateChecklist()"
											>
										</div>
									</div>

									<div class="form-group">
										<label class="form-label">
											<span>Lesson Video File</span>
											<span class="form-required">* (MP4, WebM, OGG)</span>
										</label>
										<div class="lesson-upload-box">
											<div class="upload-box-left">
												<img src="../assets/icons/video.svg" width="18" height="18" alt="Video File" style="opacity: 0.6;">
												<input 
													type="file" 
													name="lesson_videos[]" 
													class="lesson-video-input" 
													accept="video/mp4,video/webm,video/ogg" 
													onchange="handleVideoDurationDetection(this)"
													required
												>
											</div>
											<div class="duration-badge" style="display: none;">
												<span>⏱️ Duration:</span>
												<span class="duration-text">00:00</span>
											</div>
										</div>
										<input type="hidden" name="lesson_durations[]" class="lesson-duration-hidden" value="">
									</div>
								</div>
							</div>

							<button type="button" class="btn-add-lesson" id="btnAddLesson">
								<img src="../assets/icons/plus.svg" width="16" height="16" alt="Add">
								<span>+ Add Another Lesson Video</span>
							</button>
						</div>

						<!-- Section 3: Final Exam & Deliverable -->
						<div class="studio-section-card">
							<div class="studio-section-header">
								<div>
									<h2 class="studio-section-title">
										<span class="section-number-badge">3</span>
										<span>Final Exam &amp; Deliverable</span>
									</h2>
									<p class="studio-section-desc">Requirements learners must submit to earn their Adsity verified certificate.</p>
								</div>
								<span class="section-tag-pill">Certification</span>
							</div>

							<div class="form-group">
								<label for="assessment_type" class="form-label">
									<span>Submission Format</span>
									<span class="form-required">* Required</span>
								</label>
								<div class="form-input-wrapper">
									<select id="assessment_type" name="assessment_type" class="form-input" required>
										<option value="github_repo">🐙 GitHub Repository Link (Students submit source code repository)</option>
										<option value="file_upload">📁 Project File Upload (Students submit ZIP, PDF, or Design file)</option>
										<option value="live_url">🌐 Live Project URL (Students submit a deployed demo website)</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="assessment_instructions" class="form-label">
									<span>Assessment Instructions &amp; Rubric</span>
									<span class="form-required">* Required</span>
								</label>
								<div class="form-input-wrapper">
									<textarea 
										id="assessment_instructions" 
										name="assessment_instructions" 
										class="form-input" 
										rows="3" 
										placeholder="e.g. Build and push your final capstone project to GitHub with full setup instructions in README.md." 
										style="resize: vertical;" 
										required
										oninput="updateChecklist()"
									></textarea>
								</div>
								<div class="form-hint">Instructors grade student submissions directly from the "Student Submissions" studio tab.</div>
							</div>
						</div>

					</div>

					<!-- ==========================================
					     RIGHT COLUMN: UNIFIED STUDIO INSPECTOR (~40%)
					     ========================================== -->
					<div class="builder-sidebar-column">

						<!-- Unified Inspector Card -->
						<div class="studio-inspector">
							
							<!-- Header -->
							<div class="inspector-header">
								<h3 class="inspector-title">
									<img src="../assets/icons/eye.svg" width="16" height="16" alt="Preview">
									<span>Student View Preview</span>
								</h3>
								<span class="inspector-live-tag">
									<span class="status-dot-pulse"></span>
									<span>Real-Time</span>
								</span>
							</div>

							<!-- Body -->
							<div class="inspector-body">

								<!-- 1. Live Student Course Card Mockup -->
								<div class="live-course-mockup">
									<div class="mockup-thumbnail-wrap">
										<img src="../assets/adsity_assets/Fullstack_Web_Development.jpg" alt="Course Cover Preview" class="mockup-thumbnail-img" id="previewImg">
										<span class="mockup-category-badge" id="previewCategory">Development</span>
									</div>
									<div class="mockup-content">
										<h4 class="mockup-course-title" id="previewTitle">Course Title Preview</h4>
										<div class="mockup-instructor-row">
											<span>👨‍🏫 <?= htmlspecialchars($instructorName) ?></span>
											<span style="color: var(--primary-green-hover); font-weight: 700;">• Verified Partner</span>
										</div>
										<div class="mockup-footer">
											<span class="mockup-lesson-count" id="previewLessonsCount">
												<img src="../assets/icons/video.svg" width="13" height="13" alt="Video">
												<span>2 Lessons</span>
											</span>
											<span class="mockup-ad-badge">100% Free with Ads</span>
										</div>
									</div>
								</div>

								<!-- 2. Cover Artwork Visual Selector -->
								<div class="cover-picker-section">
									<div class="cover-picker-label">
										<span>Cover Artwork Banner</span>
										<span style="font-size: 0.74rem; color: var(--text-muted); font-weight: 600;">Click to apply:</span>
									</div>
									<div class="cover-picker-grid" id="coverPickerGrid">
										<button type="button" class="cover-thumb-btn active" onclick="selectCover('Fullstack_Web_Development.jpg', this)" title="Fullstack Web Development">
											<img src="../assets/adsity_assets/Fullstack_Web_Development.jpg" alt="Fullstack">
										</button>
										<button type="button" class="cover-thumb-btn" onclick="selectCover('CyberSecurityFundamentals.png', this)" title="Cybersecurity Fundamentals">
											<img src="../assets/adsity_assets/CyberSecurityFundamentals.png" alt="Cybersecurity">
										</button>
										<button type="button" class="cover-thumb-btn" onclick="selectCover('Clound_Computing.png', this)" title="Cloud Computing">
											<img src="../assets/adsity_assets/Clound_Computing.png" alt="Cloud">
										</button>
										<button type="button" class="cover-thumb-btn" onclick="selectCover('sql_and_database_management.png', this)" title="SQL & Database">
											<img src="../assets/adsity_assets/sql_and_database_management.png" alt="SQL">
										</button>
										<button type="button" class="cover-thumb-btn" onclick="selectCover('Computer_Networking_Fundamentals.png', this)" title="Networking">
											<img src="../assets/adsity_assets/Computer_Networking_Fundamentals.png" alt="Networking">
										</button>
										<button type="button" class="cover-thumb-btn" onclick="selectCover('Web_Development_Basics.png', this)" title="Web Dev Basics">
											<img src="../assets/adsity_assets/Web_Development_Basics.png" alt="Web Dev">
										</button>
										<button type="button" class="cover-thumb-btn" onclick="selectCover('Logo_Design.jpg', this)" title="Logo & Brand Design">
											<img src="../assets/adsity_assets/Logo_Design.jpg" alt="Design">
										</button>
									</div>
								</div>

								<!-- 3. Automated Ad-Break Monetization Box -->
								<div class="inspector-monetization-box">
									<div class="monetization-box-title">
										<img src="../assets/icons/check-circle.svg" width="16" height="16" alt="Monetization" style="filter: brightness(0) saturate(100%) invert(48%) sepia(85%) saturate(415%) hue-rotate(94deg) brightness(97%) contrast(92%);">
										<span>Ad Monetization Engine Active</span>
									</div>
									<p class="monetization-box-desc">
										Adsity automatically sequences 15-second sponsor video ads between lessons to sponsor free education.
									</p>
									<div class="monetization-box-metrics">
										<span id="adBreakSummary">1 Interstitial Ad Break</span>
										<span>70% Partner Revenue</span>
									</div>
								</div>

								<!-- 4. Publishing Readiness Checklist & Action -->
								<div class="inspector-publish-wrap">
									<button type="submit" name="create_course" class="btn-publish-master" id="btnSubmitCourse">
										<span>Publish Course to Platform</span>
										<img src="../assets/icons/arrow-right.svg" width="16" height="16" alt="Publish" style="filter: brightness(0) invert(1);">
									</button>

									<div class="publish-checklist">
										<div class="checklist-item" id="checkTitle">
											<span>○</span>
											<span>Course title provided</span>
										</div>
										<div class="checklist-item" id="checkDesc">
											<span>○</span>
											<span>Overview description added</span>
										</div>
										<div class="checklist-item complete" id="checkLessons">
											<span>✓</span>
											<span id="checkLessonsText">2 Lessons attached</span>
										</div>
										<div class="checklist-item" id="checkExam">
											<span>○</span>
											<span>Final exam instructions set</span>
										</div>
									</div>
								</div>

							</div>
						</div>

					</div>

				</div>
			</form>

		</main>
	</div>

	<!-- Bottom Footer -->
	<footer class="footer">
		<div class="footer-bottom">
			<div class="footer-bottom-inner">
				<img src="../assets/adsity_assets/Adsity_Logo.png" alt="Adsity Logo" class="footer-bottom-logo-img">
				<p class="footer-copyright">&copy; 2026 Adsity Instructor Studio. All rights reserved.</p>
			</div>
		</div>
	</footer>

	<!-- Interactive Studio Script -->
	<script>
		const container = document.getElementById('lessonsContainer');
		const btnAdd = document.getElementById('btnAddLesson');
		const adSummary = document.getElementById('adBreakSummary');
		const previewLessonsCount = document.getElementById('previewLessonsCount');
		const lessonCounterBadge = document.getElementById('lessonCounterBadge');
		const checkLessonsText = document.getElementById('checkLessonsText');

		function updateLessonNumbers() {
			const items = container.querySelectorAll('.lesson-item');
			items.forEach((item, index) => {
				const num = index + 1;
				item.dataset.lesson = num;
				const formattedNum = String(num).padStart(2, '0');
				const badge = item.querySelector('.lesson-badge');
				if (badge) {
					badge.innerHTML = `<img src="../assets/icons/video.svg" width="12" height="12" alt="Lesson"> Lesson ${formattedNum}`;
				}
			});

			const total = items.length;
			const adBreaks = Math.max(0, total - 1);

			if (adSummary) {
				adSummary.textContent = `${adBreaks} Interstitial Ad ${adBreaks === 1 ? 'Break' : 'Breaks'}`;
			}
			if (previewLessonsCount) {
				previewLessonsCount.innerHTML = `<img src="../assets/icons/video.svg" width="13" height="13" alt="Video"> <span>${total} Lessons</span>`;
			}
			if (lessonCounterBadge) {
				lessonCounterBadge.textContent = `${total} Lessons Added`;
			}
			if (checkLessonsText) {
				checkLessonsText.textContent = `${total} Lessons attached`;
			}

			updateChecklist();
		}

		// Automatic Video Duration Detection via HTML5 Video Metadata API
		function handleVideoDurationDetection(fileInput) {
			const file = fileInput.files[0];
			if (!file) return;

			const parent = fileInput.closest('.lesson-item');
			const badge = parent.querySelector('.duration-badge');
			const text = parent.querySelector('.duration-text');
			const hidden = parent.querySelector('.lesson-duration-hidden');

			const video = document.createElement('video');
			video.preload = 'metadata';

			video.onloadedmetadata = function() {
				window.URL.revokeObjectURL(video.src);
				const totalSecs = Math.round(video.duration);
				const hrs = Math.floor(totalSecs / 3600);
				const mins = Math.floor((totalSecs % 3600) / 60);
				const secs = totalSecs % 60;

				let formatted = '';
				if (hrs > 0) {
					formatted = `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
				} else {
					formatted = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
				}

				if (hidden) hidden.value = formatted;
				if (text) text.textContent = formatted;
				if (badge) badge.style.display = 'inline-flex';
			};

			video.onerror = function() {
				if (hidden) hidden.value = '10:00';
			};

			video.src = URL.createObjectURL(file);
			updateChecklist();
		}

		btnAdd.addEventListener('click', () => {
			const count = container.querySelectorAll('.lesson-item').length + 1;
			const formattedCount = String(count).padStart(2, '0');
			const lessonDiv = document.createElement('div');
			lessonDiv.className = 'lesson-item';
			lessonDiv.dataset.lesson = count;
			lessonDiv.innerHTML = `
				<div class="lesson-item-header">
					<span class="lesson-badge">
						<img src="../assets/icons/video.svg" width="12" height="12" alt="Lesson">
						Lesson ${formattedCount}
					</span>
					<button type="button" class="btn-remove-lesson" onclick="removeLesson(this)">
						<img src="../assets/icons/trash.svg" width="13" height="13" alt="Delete">
						<span>Remove</span>
					</button>
				</div>
				<div class="form-group" style="margin-bottom: 12px;">
					<label class="form-label">
						<span>Lesson Title</span>
						<span class="form-required">*</span>
					</label>
					<div class="form-input-wrapper">
						<input 
							type="text" 
							name="lesson_titles[]" 
							class="form-input" 
							placeholder="e.g. ${formattedCount}. Module Topic &amp; Hands-on Code" 
							required
							oninput="updateChecklist()"
						>
					</div>
				</div>
				<div class="form-group">
					<label class="form-label">
						<span>Lesson Video File</span>
						<span class="form-required">* (MP4, WebM, OGG)</span>
					</label>
					<div class="lesson-upload-box">
						<div class="upload-box-left">
							<img src="../assets/icons/video.svg" width="18" height="18" alt="Video File" style="opacity: 0.6;">
							<input 
								type="file" 
								name="lesson_videos[]" 
								class="lesson-video-input" 
								accept="video/mp4,video/webm,video/ogg" 
								onchange="handleVideoDurationDetection(this)"
								required
							>
						</div>
						<div class="duration-badge" style="display: none;">
							<span>⏱️ Duration:</span>
							<span class="duration-text">00:00</span>
						</div>
					</div>
					<input type="hidden" name="lesson_durations[]" class="lesson-duration-hidden" value="">
				</div>
			`;
			container.appendChild(lessonDiv);
			updateLessonNumbers();
		});

		function removeLesson(btn) {
			const items = container.querySelectorAll('.lesson-item');
			if (items.length <= 1) {
				alert('A course must contain at least one lesson.');
				return;
			}
			btn.closest('.lesson-item').remove();
			updateLessonNumbers();
		}

		// Synchronize Live Preview Card
		function syncLivePreview() {
			const titleInput = document.getElementById('title');
			const categorySelect = document.getElementById('category');
			const previewTitle = document.getElementById('previewTitle');
			const previewCategory = document.getElementById('previewCategory');

			if (titleInput && previewTitle) {
				previewTitle.textContent = titleInput.value.trim() || 'Course Title Preview';
			}

			if (categorySelect && previewCategory) {
				previewCategory.textContent = categorySelect.options[categorySelect.selectedIndex].text;
			}

			updateChecklist();
		}

		// Cover Art Selector
		function selectCover(filename, btnElement) {
			const hiddenInput = document.getElementById('thumbnail');
			const previewImg = document.getElementById('previewImg');

			if (hiddenInput) {
				hiddenInput.value = filename;
			}
			if (previewImg) {
				previewImg.src = '../assets/adsity_assets/' + filename;
			}

			const allBtns = document.querySelectorAll('.cover-thumb-btn');
			allBtns.forEach(b => b.classList.remove('active'));
			if (btnElement) {
				btnElement.classList.add('active');
			}
		}

		// Dynamic Checklist
		function updateChecklist() {
			const titleVal = document.getElementById('title')?.value.trim();
			const descVal = document.getElementById('description')?.value.trim();
			const examVal = document.getElementById('assessment_instructions')?.value.trim();

			setCheckStatus('checkTitle', Boolean(titleVal), 'Course title provided');
			setCheckStatus('checkDesc', Boolean(descVal), 'Overview description added');
			setCheckStatus('checkExam', Boolean(examVal), 'Final exam instructions set');
		}

		function setCheckStatus(id, isComplete, label) {
			const el = document.getElementById(id);
			if (!el) return;
			if (isComplete) {
				el.className = 'checklist-item complete';
				el.innerHTML = `<span>✓</span><span>${label}</span>`;
			} else {
				el.className = 'checklist-item';
				el.innerHTML = `<span>○</span><span>${label}</span>`;
			}
		}

		// Mobile Sidebar Toggle
		function toggleSidebar() {
			var sidebar = document.getElementById('instructorSidebar');
			var backdrop = document.getElementById('sidebarBackdrop');
			if (sidebar && backdrop) {
				sidebar.classList.toggle('open');
				backdrop.classList.toggle('open');
			}
		}

		document.addEventListener('DOMContentLoaded', function() {
			var toggleBtn = document.getElementById('sidebarToggle');
			if (toggleBtn) {
				toggleBtn.addEventListener('click', toggleSidebar);
			}
			updateLessonNumbers();
			syncLivePreview();
		});
	</script>
</body>
</html>
