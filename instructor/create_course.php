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
			<a href="../logout.php" class="instructor-btn-logout">
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

				<a href="../logout.php" class="btn-sidebar-logout">
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

			<!-- Multi-Step Wizard Progress Stepper -->
			<div class="wizard-stepper-card" style="max-width: 1040px; margin: 0 auto 24px auto;">
				<div class="wizard-stepper">
					<button type="button" class="step-indicator active" id="stepIndicator1" onclick="handleStepIndicatorClick(1)">
						<span class="step-bubble" id="stepBubble1">1</span>
						<div class="step-label">
							<span class="step-number-title">Step 1</span>
							<span class="step-name">Basics &amp; Thumbnail</span>
						</div>
					</button>
					<div class="step-connector" id="stepConnector1"></div>

					<button type="button" class="step-indicator" id="stepIndicator2" onclick="handleStepIndicatorClick(2)">
						<span class="step-bubble" id="stepBubble2">2</span>
						<div class="step-label">
							<span class="step-number-title">Step 2</span>
							<span class="step-name">Curriculum &amp; Lessons</span>
						</div>
					</button>
					<div class="step-connector" id="stepConnector2"></div>

					<button type="button" class="step-indicator" id="stepIndicator3" onclick="handleStepIndicatorClick(3)">
						<span class="step-bubble" id="stepBubble3">3</span>
						<div class="step-label">
							<span class="step-number-title">Step 3</span>
							<span class="step-name">Final Assessment</span>
						</div>
					</button>
					<div class="step-connector" id="stepConnector3"></div>

					<button type="button" class="step-indicator" id="stepIndicator4" onclick="handleStepIndicatorClick(4)">
						<span class="step-bubble" id="stepBubble4">4</span>
						<div class="step-label">
							<span class="step-number-title">Step 4</span>
							<span class="step-name">Student Preview</span>
						</div>
					</button>
				</div>
			</div>

			<!-- Multi-Step Workstation Form -->
			<form action="create_course_function.php" method="POST" enctype="multipart/form-data" id="courseForm" style="max-width: 1040px; margin: 0 auto;" novalidate>
				<input type="hidden" name="create_course" value="1">

				<!-- ==========================================
				     STEP 1: COURSE BASICS & THUMBNAIL
				     ========================================== -->
				<div class="wizard-step-panel active" id="stepPanel1" data-step="1">
					<div class="studio-section-card">
						<div class="studio-section-header">
							<div>
								<h2 class="studio-section-title">
									<span class="section-number-badge">1</span>
									<span>Course Details &amp; Cover Thumbnail</span>
								</h2>
								<p class="studio-section-desc">Upload a course cover thumbnail and specify core metadata seen by learners.</p>
							</div>
							<span class="section-tag-pill">Step 1 of 4</span>
						</div>

						<div class="step-alert" id="step1Alert" style="display: none;">
							<img src="../assets/icons/alert-circle.svg" width="18" height="18" alt="Alert">
							<span id="step1AlertText">Please complete all required fields on this step.</span>
						</div>

						<!-- 1. Course Cover Thumbnail Upload -->
						<div class="form-group">
							<label class="form-label">
								<span>Course Cover Thumbnail</span>
								<span class="form-required">* Required (Recommended 16:9)</span>
							</label>

							<div class="thumb-upload-dropzone" id="thumbDropzone">
								<input 
									type="file" 
									id="course_thumbnail" 
									name="course_thumbnail" 
									accept="image/png,image/jpeg,image/jpg,image/webp" 
									class="thumb-file-input" 
									onchange="handleThumbnailUpload(this)"
								>

								<label for="course_thumbnail" class="thumb-upload-label" id="thumbUploadLabel">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="thumb-upload-icon">
										<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
										<polyline points="17 8 12 3 7 8"></polyline>
										<line x1="12" y1="3" x2="12" y2="15"></line>
									</svg>
									<div class="thumb-upload-text">
										<span class="thumb-upload-title">Click or drag &amp; drop to upload Course Thumbnail *</span>
										<span class="thumb-upload-hint">PNG, JPG, or WEBP (Saved into course thumbnail folder)</span>
									</div>
								</label>

								<!-- Thumbnail Selected Preview -->
								<div id="thumbPreviewBox" class="thumb-preview-box" style="display: none;">
									<div class="thumb-preview-img-wrap">
										<img src="../assets/adsity_assets/Web_Development_Basics.png" alt="Thumbnail Preview" id="thumbPreviewImg">
									</div>
									<div class="thumb-preview-info">
										<div class="thumb-preview-filename" id="thumbPreviewName">thumbnail.png</div>
										<div class="thumb-preview-size" id="thumbPreviewSize">Ready for upload</div>
									</div>
									<button type="button" class="btn-remove-thumb" onclick="clearCustomThumbnail()">Change Image</button>
								</div>
							</div>
							<div class="form-hint">A clear, professional thumbnail significantly increases student engagement and clicks in the course catalog.</div>
						</div>

						<!-- 2. Course Title -->
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
									oninput="clearError('title')"
								>
							</div>
							<div class="form-hint">Make it engaging and specific so students quickly understand what they will master.</div>
						</div>

						<!-- 3. Course Category -->
						<div class="form-group">
							<label for="category" class="form-label">
								<span>Primary Category</span>
								<span class="form-required">* Required</span>
							</label>
							<div class="form-input-wrapper">
								<select id="category" name="category" class="form-input" onchange="clearError('category')">
									<option value="Development">Development</option>
									<option value="Security">Security</option>
									<option value="Networking">Networking</option>
									<option value="Cloud &amp; DevOps">Cloud &amp; DevOps</option>
									<option value="Data &amp; Backend">Data &amp; Backend</option>
									<option value="Design">Design / UI/UX</option>
								</select>
							</div>
						</div>

						<!-- 4. Course Description -->
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
									rows="5" 
									placeholder="Describe the skills taught, curriculum highlights, prerequisites, and student takeaways..." 
									style="resize: vertical;" 
									oninput="clearError('description')"
								></textarea>
							</div>
							<div class="form-hint">A comprehensive description helps students assess if the curriculum aligns with their learning goals.</div>
						</div>

						<!-- Wizard Navigation Actions -->
						<div class="wizard-actions">
							<div style="font-size: 0.84rem; color: var(--text-muted); font-weight: 600;">
								<span>Step 1: Fill details and proceed to upload lessons</span>
							</div>
							<div class="wizard-actions-right">
								<button type="button" class="btn-wizard-nav btn-wizard-next" onclick="if (validateStep1()) goToStep(2);">
									<span>Next: Upload Lessons</span>
									<img src="../assets/icons/arrow-right.svg" width="16" height="16" alt="Next" style="filter: brightness(0) invert(1);">
								</button>
							</div>
						</div>
					</div>
				</div>

				<!-- ==========================================
				     STEP 2: LESSONS CURRICULUM BUILDER
				     ========================================== -->
				<div class="wizard-step-panel" id="stepPanel2" data-step="2">
					<div class="studio-section-card">
						<div class="studio-section-header">
							<div>
								<h2 class="studio-section-title">
									<span class="section-number-badge">2</span>
									<span>Video Lessons Curriculum</span>
								</h2>
								<p class="studio-section-desc">Upload sequential lesson videos. Adsity sequences 15-second sponsor ads between lessons.</p>
							</div>
							<span class="section-tag-pill" id="lessonCounterBadge">1 Lesson Added</span>
						</div>

						<div class="step-alert" id="step2Alert" style="display: none;">
							<img src="../assets/icons/alert-circle.svg" width="18" height="18" alt="Alert">
							<span id="step2AlertText">Please add at least one lesson with title and video.</span>
						</div>

						<!-- Monetization Callout Card -->
						<div class="inspector-monetization-box" style="margin-bottom: 22px;">
							<div class="monetization-box-title">
								<img src="../assets/icons/check-circle.svg" width="16" height="16" alt="Monetization" style="filter: brightness(0) saturate(100%) invert(48%) sepia(85%) saturate(415%) hue-rotate(94deg) brightness(97%) contrast(92%);">
								<span>Automated Monetization Engine Active</span>
							</div>
							<p class="monetization-box-desc">
								Adsity automatically sequences 15-second sponsor video ads between lessons to sponsor free education and monetize your content.
							</p>
							<div class="monetization-box-metrics">
								<span id="adBreakSummary">0 Interstitial Ad Breaks</span>
								<span>⚡ 70% Partner Revenue Split</span>
							</div>
						</div>

						<!-- Dynamic Lessons Container -->
						<div id="lessonsContainer">
							<!-- Lesson 1 -->
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
											placeholder="e.g. 01. Introduction to Web Development &amp; Setup" 
											oninput="clearLessonError(this)"
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

						<!-- Add Lesson Button -->
						<button type="button" class="btn-add-lesson" id="btnAddLesson">
							<img src="../assets/icons/plus.svg" width="16" height="16" alt="Add">
							<span>+ Add Another Lesson Video (Lesson 2, Lesson 3...)</span>
						</button>

						<!-- Wizard Navigation Actions -->
						<div class="wizard-actions">
							<button type="button" class="btn-wizard-nav btn-wizard-prev" onclick="goToStep(1)">
								<img src="../assets/icons/arrow-left.svg" width="16" height="16" alt="Back">
								<span>Back to Course Basics</span>
							</button>
							<div class="wizard-actions-right">
								<button type="button" class="btn-wizard-nav btn-wizard-next" onclick="if (validateStep2()) goToStep(3);">
									<span>Next: Final Assessment</span>
									<img src="../assets/icons/arrow-right.svg" width="16" height="16" alt="Next" style="filter: brightness(0) invert(1);">
								</button>
							</div>
						</div>
					</div>
				</div>

				<!-- ==========================================
				     STEP 3: FINAL ASSESSMENT & DELIVERABLES
				     ========================================== -->
				<div class="wizard-step-panel" id="stepPanel3" data-step="3">
					<div class="studio-section-card">
						<div class="studio-section-header">
							<div>
								<h2 class="studio-section-title">
									<span class="section-number-badge">3</span>
									<span>Final Exam Deliverables &amp; Rubrics</span>
								</h2>
								<p class="studio-section-desc">Requirements and grading rubric learners must submit to earn their Adsity verified certificate.</p>
							</div>
							<span class="section-tag-pill">Certification</span>
						</div>

						<div class="step-alert" id="step3Alert" style="display: none;">
							<img src="../assets/icons/alert-circle.svg" width="18" height="18" alt="Alert">
							<span id="step3AlertText">Please fill out the final exam instructions and rubrics.</span>
						</div>

						<!-- 1. Submission Format -->
						<div class="form-group">
							<label for="assessment_type" class="form-label">
								<span>Submission Format</span>
								<span class="form-required">* Required</span>
							</label>
							<div class="form-input-wrapper">
								<select id="assessment_type" name="assessment_type" class="form-input">
									<option value="github_repo">🐙 GitHub Repository Link (Students submit source code repository)</option>
									<option value="file_upload">📁 Project File Upload (Students submit ZIP, PDF, or Design file)</option>
									<option value="live_url">🌐 Live Project URL (Students submit a deployed demo website)</option>
								</select>
							</div>
							<div class="form-hint">Select the deliverable type students must submit upon completing all lesson videos.</div>
						</div>

						<!-- 2. Assessment Instructions & Rubric -->
						<div class="form-group">
							<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
								<label for="assessment_instructions" class="form-label" style="margin-bottom: 0;">
									<span>Assessment Instructions &amp; Grading Rubric</span>
									<span class="form-required">* Required</span>
								</label>
								<button type="button" class="btn-template-insert" onclick="insertRubricTemplate()">
									✨ Insert Capstone Rubric Template
								</button>
							</div>
							<div class="form-input-wrapper">
								<textarea 
									id="assessment_instructions" 
									name="assessment_instructions" 
									class="form-input" 
									rows="6" 
									placeholder="Provide capstone project requirements, submission guidelines, and grading criteria (e.g. Code Quality: 30%, Feature Completeness: 40%, Documentation: 30%)..." 
									style="resize: vertical;" 
									oninput="clearError('assessment_instructions')"
								></textarea>
							</div>
							<div class="form-hint">Instructors review and grade student capstone submissions directly from the "Student Submissions" studio dashboard.</div>
						</div>

						<!-- Wizard Navigation Actions -->
						<div class="wizard-actions">
							<button type="button" class="btn-wizard-nav btn-wizard-prev" onclick="goToStep(2)">
								<img src="../assets/icons/arrow-left.svg" width="16" height="16" alt="Back">
								<span>Back to Curriculum</span>
							</button>
							<div class="wizard-actions-right">
								<button type="button" class="btn-wizard-nav btn-wizard-next" onclick="if (validateStep3()) goToStep(4);">
									<span>Next: Student Preview</span>
									<img src="../assets/icons/arrow-right.svg" width="16" height="16" alt="Next" style="filter: brightness(0) invert(1);">
								</button>
							</div>
						</div>
					</div>
				</div>

				<!-- ==========================================
				     STEP 4: STUDENT PREVIEW & PUBLISH
				     ========================================== -->
				<div class="wizard-step-panel" id="stepPanel4" data-step="4">
					<div class="studio-section-card">
						<div class="studio-section-header">
							<div>
								<h2 class="studio-section-title">
									<span class="section-number-badge">4</span>
									<span>Student Experience Preview &amp; Verification</span>
								</h2>
								<p class="studio-section-desc">Review your course through a student's eyes before publishing to the Adsity catalog.</p>
							</div>
							<span class="section-tag-pill" style="background-color: var(--primary-green-subtle); color: var(--primary-green-hover);">Ready to Publish</span>
						</div>

						<div class="student-preview-wrapper">

							<!-- Student Perspective Notice Banner -->
							<div class="preview-banner">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
									<circle cx="12" cy="12" r="3"></circle>
								</svg>
								<span><strong>Student Perspective Preview:</strong> This preview reflects how enrolled students experience your course curriculum, video sequence, and final assessment.</span>
							</div>

							<!-- 2-Column Student Showcase -->
							<div class="preview-grid-2col">

								<!-- Left: Real Student Course Card (Catalog View) -->
								<div>
									<div style="font-size: 0.82rem; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.04em;">Catalog Course Card</div>
									<div class="live-course-mockup">
										<div class="mockup-thumbnail-wrap">
											<img src="../assets/adsity_assets/Web_Development_Basics.png" alt="Course Cover Preview" class="mockup-thumbnail-img" id="previewImg">
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
													<span>1 Lesson</span>
												</span>
												<span class="mockup-ad-badge">100% Free with Ads</span>
											</div>
										</div>
									</div>
								</div>

								<!-- Right: Course Overview & Monetization -->
								<div class="preview-meta-card">
									<div>
										<span class="section-tag-pill" id="previewDetailCategory" style="margin-bottom: 8px; display: inline-block;">Development</span>
										<h3 class="preview-meta-title" id="previewDetailTitle">Course Title</h3>
									</div>

									<div>
										<div style="font-size: 0.8rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Course Description</div>
										<p class="preview-meta-desc" id="previewDetailDesc">Course description will appear here...</p>
									</div>

									<div class="inspector-monetization-box" style="margin-top: auto;">
										<div class="monetization-box-title">
											<img src="../assets/icons/shield-check.svg" width="16" height="16" alt="Partner">
											<span>Adsity Partner Revenue Split</span>
										</div>
										<div class="monetization-box-metrics">
											<span id="previewAdBreaksCount">0 Interstitial Ad Breaks</span>
											<span>⚡ 70% Partner Share</span>
										</div>
									</div>
								</div>

							</div>

							<!-- Syllabus Breakdown Preview -->
							<div class="preview-syllabus-section">
								<h3 class="preview-syllabus-title">
									<span>Curriculum Video Lessons</span>
									<span id="previewSyllabusLessonsCount" style="font-size: 0.82rem; color: var(--primary-blue); font-weight: 700;">1 Lesson Total</span>
								</h3>
								<div class="preview-syllabus-list" id="previewSyllabusList">
									<!-- Dynamically populated by renderStudentPreview() -->
								</div>
							</div>

							<!-- Final Exam Deliverable Preview Box -->
							<div class="preview-exam-box">
								<div class="preview-exam-header">
									<h4 class="preview-exam-title">
										<img src="../assets/icons/award.svg" width="18" height="18" alt="Exam">
										<span>Capstone Final Exam Requirement</span>
									</h4>
									<span class="preview-format-pill" id="previewDeliverableBadge">🐙 GitHub Repository Link</span>
								</div>
								<div class="preview-exam-body" id="previewAssessmentInstructions">
									Assessment instructions and grading rubric will appear here...
								</div>
								<div style="font-size: 0.82rem; color: #7e22ce; font-weight: 700; display: flex; align-items: center; gap: 6px;">
									<span>🎓</span>
									<span>Verified Adsity Completion Certificate issued upon passing instructor review.</span>
								</div>
							</div>

							<!-- Pre-Flight Readiness Summary -->
							<div class="preview-check-summary">
								<div style="font-size: 0.84rem; font-weight: 800; color: var(--text-dark);">Pre-Flight Publication Checklist</div>
								<div class="preview-check-grid">
									<div class="preview-check-item">
										<span>✓</span>
										<span>Course thumbnail uploaded</span>
									</div>
									<div class="preview-check-item">
										<span>✓</span>
										<span>Metadata &amp; description complete</span>
									</div>
									<div class="preview-check-item">
										<span>✓</span>
										<span id="checkLessonsFinalText">1 video lesson attached</span>
									</div>
									<div class="preview-check-item">
										<span>✓</span>
										<span>Capstone deliverables set</span>
									</div>
									<div class="preview-check-item">
										<span>✓</span>
										<span>70% partner revenue active</span>
									</div>
								</div>
							</div>

						</div>

						<!-- Master Publish Actions -->
						<div class="wizard-actions">
							<button type="button" class="btn-wizard-nav btn-wizard-prev" onclick="goToStep(3)">
								<img src="../assets/icons/arrow-left.svg" width="16" height="16" alt="Back">
								<span>Back to Assessment</span>
							</button>
							<div class="wizard-actions-right">
								<button type="submit" name="create_course" class="btn-wizard-publish" id="btnPublishFinal">
									<span>Publish Course to Platform</span>
									<img src="../assets/icons/arrow-right.svg" width="18" height="18" alt="Publish" style="filter: brightness(0) invert(1);">
								</button>
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

	<!-- Multi-Step Studio Script -->
	<script>
		let currentStep = 1;
		let thumbnailObjectURL = null;

		const container = document.getElementById('lessonsContainer');
		const btnAdd = document.getElementById('btnAddLesson');
		const adSummary = document.getElementById('adBreakSummary');
		const lessonCounterBadge = document.getElementById('lessonCounterBadge');

		// Step Navigation
		function goToStep(step) {
			if (step < 1 || step > 4) return;

			currentStep = step;

			// Update step panels visibility
			for (let i = 1; i <= 4; i++) {
				const panel = document.getElementById(`stepPanel${i}`);
				const indicator = document.getElementById(`stepIndicator${i}`);
				const bubble = document.getElementById(`stepBubble${i}`);

				if (panel) {
					if (i === step) {
						panel.classList.add('active');
					} else {
						panel.classList.remove('active');
					}
				}

				if (indicator) {
					indicator.classList.remove('active', 'completed');
					if (i === step) {
						indicator.classList.add('active');
						if (bubble) bubble.textContent = i;
					} else if (i < step) {
						indicator.classList.add('completed');
						if (bubble) bubble.innerHTML = '✓';
					} else {
						if (bubble) bubble.textContent = i;
					}
				}
			}

			// Update connectors between steps (1 to 3)
			for (let i = 1; i <= 3; i++) {
				const connector = document.getElementById(`stepConnector${i}`);
				if (connector) {
					if (i < step) {
						connector.classList.add('completed');
					} else {
						connector.classList.remove('completed');
					}
				}
			}

			// Scroll smoothly to top of workstation
			window.scrollTo({ top: 80, behavior: 'smooth' });

			// When entering step 4, generate full student preview
			if (step === 4) {
				renderStudentPreview();
			}
		}

		function handleStepIndicatorClick(targetStep) {
			if (targetStep <= currentStep) {
				goToStep(targetStep);
			} else {
				// Validate sequentially up to target step
				for (let s = 1; s < targetStep; s++) {
					if (s === 1 && !validateStep1()) { goToStep(1); return; }
					if (s === 2 && !validateStep2()) { goToStep(2); return; }
					if (s === 3 && !validateStep3()) { goToStep(3); return; }
				}
				goToStep(targetStep);
			}
		}

		// Validation on Step 1
		function validateStep1() {
			const title = document.getElementById('title');
			const desc = document.getElementById('description');
			const thumbInput = document.getElementById('course_thumbnail');
			const alertBox = document.getElementById('step1Alert');
			const alertText = document.getElementById('step1AlertText');

			const hasThumb = thumbInput && thumbInput.files && thumbInput.files.length > 0;
			const hasTitle = title && title.value.trim() !== '';
			const hasDesc = desc && desc.value.trim() !== '';

			if (!hasThumb || !hasTitle || !hasDesc) {
				let missing = [];
				let firstInvalid = null;
				if (!hasThumb) {
					missing.push('course thumbnail');
					const label = document.getElementById('thumbUploadLabel');
					label?.classList.add('form-input-error');
					if (!firstInvalid) firstInvalid = label;
				}
				if (!hasTitle) {
					missing.push('course title');
					title?.classList.add('form-input-error');
					if (!firstInvalid) firstInvalid = title;
				}
				if (!hasDesc) {
					missing.push('course description');
					desc?.classList.add('form-input-error');
					if (!firstInvalid) firstInvalid = desc;
				}

				if (alertBox && alertText) {
					alertText.textContent = `Please provide the required: ${missing.join(', ')}.`;
					alertBox.style.display = 'flex';
					alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
				} else if (firstInvalid) {
					firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
				}
				if (firstInvalid && typeof firstInvalid.focus === 'function') {
					firstInvalid.focus();
				}
				return false;
			}

			if (alertBox) alertBox.style.display = 'none';
			return true;
		}

		// Validation on Step 2
		function validateStep2() {
			const items = container.querySelectorAll('.lesson-item');
			const alertBox = document.getElementById('step2Alert');
			const alertText = document.getElementById('step2AlertText');

			if (items.length === 0) {
				if (alertBox && alertText) {
					alertText.textContent = 'Please add at least one lesson to your course curriculum.';
					alertBox.style.display = 'flex';
					alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
				}
				return false;
			}

			let invalidItem = null;
			let invalidEl = null;
			let invalidLessonNum = 1;
			let invalidReason = '';

			items.forEach((item, idx) => {
				if (invalidItem) return;
				const titleInput = item.querySelector('input[name="lesson_titles[]"]');
				const videoInput = item.querySelector('input[name="lesson_videos[]"]');

				if (!titleInput || titleInput.value.trim() === '') {
					invalidItem = item;
					invalidEl = titleInput;
					invalidLessonNum = idx + 1;
					invalidReason = 'title';
					titleInput?.classList.add('form-input-error');
				} else if (!videoInput || !videoInput.files || videoInput.files.length === 0) {
					invalidItem = item;
					const uploadBox = item.querySelector('.lesson-upload-box');
					invalidEl = uploadBox || videoInput;
					invalidLessonNum = idx + 1;
					invalidReason = 'video file';
					uploadBox?.classList.add('form-input-error');
				}
			});

			if (invalidItem) {
				if (alertBox && alertText) {
					alertText.textContent = `Lesson ${invalidLessonNum} is missing its ${invalidReason}. Please upload a video and enter a title.`;
					alertBox.style.display = 'flex';
					alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
				}
				if (invalidEl && typeof invalidEl.focus === 'function') {
					invalidEl.focus();
				}
				return false;
			}

			if (alertBox) alertBox.style.display = 'none';
			return true;
		}

		// Validation on Step 3
		function validateStep3() {
			const instructions = document.getElementById('assessment_instructions');
			const alertBox = document.getElementById('step3Alert');
			const alertText = document.getElementById('step3AlertText');

			if (!instructions || instructions.value.trim() === '') {
				instructions?.classList.add('form-input-error');
				if (alertBox && alertText) {
					alertText.textContent = 'Please provide assessment instructions and grading criteria for the final exam.';
					alertBox.style.display = 'flex';
					alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
				}
				instructions?.focus();
				return false;
			}

			if (alertBox) alertBox.style.display = 'none';
			return true;
		}

		// Validate all steps before publishing
		function validateAll() {
			if (!validateStep1()) {
				goToStep(1);
				return false;
			}
			if (!validateStep2()) {
				goToStep(2);
				return false;
			}
			if (!validateStep3()) {
				goToStep(3);
				return false;
			}
			return true;
		}

		function clearError(fieldId) {
			const el = document.getElementById(fieldId);
			if (el) el.classList.remove('form-input-error');
			const alertBox = document.getElementById(`step${currentStep}Alert`);
			if (alertBox) alertBox.style.display = 'none';
		}

		function clearLessonError(input) {
			input.classList.remove('form-input-error');
			const alertBox = document.getElementById('step2Alert');
			if (alertBox) alertBox.style.display = 'none';
		}

		// Thumbnail Handlers
		function handleThumbnailUpload(input) {
			if (input.files && input.files[0]) {
				const file = input.files[0];
				thumbnailObjectURL = URL.createObjectURL(file);

				// Step 1 preview
				const previewImg = document.getElementById('thumbPreviewImg');
				const previewBox = document.getElementById('thumbPreviewBox');
				const previewName = document.getElementById('thumbPreviewName');
				const previewSize = document.getElementById('thumbPreviewSize');
				const uploadLabel = document.getElementById('thumbUploadLabel');

				if (previewImg) previewImg.src = thumbnailObjectURL;
				if (previewName) previewName.textContent = file.name;
				if (previewSize) {
					const sizeKb = (file.size / 1024).toFixed(1);
					const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
					previewSize.textContent = file.size > 1024 * 1024 ? `${sizeMb} MB` : `${sizeKb} KB`;
				}

				if (previewBox) previewBox.style.display = 'flex';
				if (uploadLabel) {
					uploadLabel.style.display = 'none';
					uploadLabel.classList.remove('form-input-error');
				}

				const alertBox = document.getElementById('step1Alert');
				if (alertBox) alertBox.style.display = 'none';
			}
		}

		function clearCustomThumbnail() {
			const input = document.getElementById('course_thumbnail');
			const previewBox = document.getElementById('thumbPreviewBox');
			const uploadLabel = document.getElementById('thumbUploadLabel');
			const previewImg = document.getElementById('thumbPreviewImg');

			if (input) input.value = '';
			if (thumbnailObjectURL) {
				URL.revokeObjectURL(thumbnailObjectURL);
				thumbnailObjectURL = null;
			}
			if (previewBox) previewBox.style.display = 'none';
			if (uploadLabel) uploadLabel.style.display = 'flex';
			if (previewImg) previewImg.src = '../assets/adsity_assets/Web_Development_Basics.png';
		}

		// Lesson Management
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
			if (lessonCounterBadge) {
				lessonCounterBadge.textContent = `${total} ${total === 1 ? 'Lesson' : 'Lessons'} Added`;
			}
			if (btnAdd) {
				const nextNum = total + 1;
				const span = btnAdd.querySelector('span');
				if (span) {
					span.textContent = `+ Add Another Lesson Video (Lesson ${nextNum}, Lesson ${nextNum + 1}...)`;
				}
			}
		}

		// Add Lesson Video Row
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
							placeholder="e.g. ${formattedCount}. Next Topic &amp; Hands-on Code" 
							oninput="clearLessonError(this)"
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

		// Automatic Video Duration Detection via HTML5 Video Metadata API
		function handleVideoDurationDetection(fileInput) {
			const file = fileInput.files[0];
			if (!file) return;

			const parent = fileInput.closest('.lesson-item');
			parent.querySelector('.lesson-upload-box')?.classList.remove('form-input-error');
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
		}

		// Insert Template into Assessment Instructions
		function insertRubricTemplate() {
			const textarea = document.getElementById('assessment_instructions');
			if (!textarea) return;

			const template = `## Capstone Final Exam Project Requirements
Complete and submit your final capstone project to demonstrate mastery of the curriculum.

### Deliverable Checklist:
1. Complete working codebase adhering to best practices and modular architecture.
2. Clean documentation in README.md outlining project setup, dependencies, and environment configuration.
3. Demonstration of error handling, responsive design, and core course concepts.

### Grading Rubric (100 Points Total):
- **Core Functionality & Feature Completeness:** 40 Points
- **Code Architecture, Cleanliness & Modularity:** 30 Points
- **Documentation, Setup Instructions & Testing:** 20 Points
- **User Interface & Experience Polish:** 10 Points
*Passing Grade: 70% or higher required for verified certificate.*`;

			textarea.value = template;
			textarea.classList.remove('form-input-error');
			const alertBox = document.getElementById('step3Alert');
			if (alertBox) alertBox.style.display = 'none';
		}

		// Step 4: Render Full Student Experience Preview
		function renderStudentPreview() {
			const titleVal = document.getElementById('title')?.value.trim() || 'Untitled Course';
			const categorySelect = document.getElementById('category');
			const categoryVal = categorySelect ? categorySelect.options[categorySelect.selectedIndex].text : 'Development';
			const descVal = document.getElementById('description')?.value.trim() || 'No description provided.';
			const assessTypeSelect = document.getElementById('assessment_type');
			const assessTypeText = assessTypeSelect ? assessTypeSelect.options[assessTypeSelect.selectedIndex].text : 'GitHub Repository';
			const assessInstructionsVal = document.getElementById('assessment_instructions')?.value.trim() || 'No instructions provided.';

			// 1. Catalog Card Mockup
			const previewImg = document.getElementById('previewImg');
			if (previewImg) {
				previewImg.src = thumbnailObjectURL || '../assets/adsity_assets/Web_Development_Basics.png';
			}

			const previewTitle = document.getElementById('previewTitle');
			if (previewTitle) previewTitle.textContent = titleVal;

			const previewCategory = document.getElementById('previewCategory');
			if (previewCategory) previewCategory.textContent = categoryVal;

			// 2. Detail Meta
			const previewDetailTitle = document.getElementById('previewDetailTitle');
			if (previewDetailTitle) previewDetailTitle.textContent = titleVal;

			const previewDetailCategory = document.getElementById('previewDetailCategory');
			if (previewDetailCategory) previewDetailCategory.textContent = categoryVal;

			const previewDetailDesc = document.getElementById('previewDetailDesc');
			if (previewDetailDesc) previewDetailDesc.textContent = descVal;

			// 3. Lessons Outline
			const items = container.querySelectorAll('.lesson-item');
			const totalLessons = items.length;
			const adBreaks = Math.max(0, totalLessons - 1);

			const previewLessonsCount = document.getElementById('previewLessonsCount');
			if (previewLessonsCount) {
				previewLessonsCount.innerHTML = `<img src="../assets/icons/video.svg" width="13" height="13" alt="Video"> <span>${totalLessons} ${totalLessons === 1 ? 'Lesson' : 'Lessons'}</span>`;
			}

			const previewSyllabusCount = document.getElementById('previewSyllabusLessonsCount');
			if (previewSyllabusCount) {
				previewSyllabusCount.textContent = `${totalLessons} ${totalLessons === 1 ? 'Lesson Total' : 'Lessons Total'}`;
			}

			const previewAdBreaksCount = document.getElementById('previewAdBreaksCount');
			if (previewAdBreaksCount) {
				previewAdBreaksCount.textContent = `${adBreaks} Interstitial Ad ${adBreaks === 1 ? 'Break' : 'Breaks'}`;
			}

			const checkLessonsFinalText = document.getElementById('checkLessonsFinalText');
			if (checkLessonsFinalText) {
				checkLessonsFinalText.textContent = `${totalLessons} ${totalLessons === 1 ? 'video lesson' : 'video lessons'} attached`;
			}

			const syllabusList = document.getElementById('previewSyllabusList');
			if (syllabusList) {
				syllabusList.innerHTML = '';
				items.forEach((item, index) => {
					const num = index + 1;
					const formattedNum = String(num).padStart(2, '0');
					const titleInput = item.querySelector('input[name="lesson_titles[]"]');
					const lessonTitle = titleInput?.value.trim() || `Lesson ${formattedNum}`;
					const durationText = item.querySelector('.duration-text')?.textContent || '10:00';
					const videoInput = item.querySelector('input[name="lesson_videos[]"]');
					const videoName = videoInput?.files?.[0]?.name || 'Video File Ready';

					const card = document.createElement('div');
					card.className = 'preview-lesson-card';
					card.innerHTML = `
						<div class="preview-lesson-left">
							<span class="preview-lesson-num">${formattedNum}</span>
							<div>
								<div class="preview-lesson-name">${escapeHtml(lessonTitle)}</div>
								<div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 2px;">📁 ${escapeHtml(videoName)}</div>
							</div>
						</div>
						<div class="preview-lesson-duration">⏱️ ${escapeHtml(durationText)}</div>
					`;
					syllabusList.appendChild(card);

					// If not the last lesson, insert interstitial sponsor ad break
					if (index < totalLessons - 1) {
						const adDiv = document.createElement('div');
						adDiv.className = 'preview-ad-break-divider';
						adDiv.innerHTML = `
							<span>⚡</span>
							<span>Automated 15-Second Sponsor Ad Break (70% Partner Monetization)</span>
						`;
						syllabusList.appendChild(adDiv);
					}
				});
			}

			// 4. Assessment Deliverable Preview
			const deliverableBadge = document.getElementById('previewDeliverableBadge');
			if (deliverableBadge) {
				deliverableBadge.textContent = assessTypeText;
			}

			const assessmentInstructionsEl = document.getElementById('previewAssessmentInstructions');
			if (assessmentInstructionsEl) {
				assessmentInstructionsEl.textContent = assessInstructionsVal;
			}
		}

		function escapeHtml(text) {
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}

		// Prevent double submission on publishing without stripping submit button payload
		document.getElementById('courseForm')?.addEventListener('submit', function(e) {
			// Final comprehensive validity check
			if (!validateAll()) {
				e.preventDefault();
				return false;
			}

			const btn = document.getElementById('btnPublishFinal');
			if (btn) {
				btn.style.pointerEvents = 'none';
				btn.style.opacity = '0.8';
				btn.innerHTML = `
					<span>Publishing Course &amp; Uploading Media...</span>
					<div class="status-dot-pulse" style="background-color: #ffffff; box-shadow: 0 0 8px #ffffff;"></div>
				`;
			}
		});

		// Drag & Drop on Thumbnail Dropzone
		const dropzone = document.getElementById('thumbDropzone');
		if (dropzone) {
			['dragenter', 'dragover'].forEach(eventName => {
				dropzone.addEventListener(eventName, (e) => {
					e.preventDefault();
					e.stopPropagation();
					dropzone.classList.add('dragover');
				}, false);
			});

			['dragleave', 'drop'].forEach(eventName => {
				dropzone.addEventListener(eventName, (e) => {
					e.preventDefault();
					e.stopPropagation();
					dropzone.classList.remove('dragover');
				}, false);
			});

			dropzone.addEventListener('drop', (e) => {
				const dt = e.dataTransfer;
				const files = dt.files;
				if (files && files.length > 0) {
					const input = document.getElementById('course_thumbnail');
					input.files = files;
					handleThumbnailUpload(input);
				}
			}, false);
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
		});
	</script>
	<script src="../assets/js/logout_modal.js"></script>
</body>
</html>
