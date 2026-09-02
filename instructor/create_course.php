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
	<title>Create Multi-Video Course - Adsity Studio</title>
	<link rel="stylesheet" href="../style.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<style>
		.builder-wrapper {
			max-width: 820px;
			margin: 30px auto 60px auto;
			padding: 0 20px;
		}

		.builder-card {
			background-color: #ffffff;
			border: 1px solid #e2e8f0;
			border-radius: 20px;
			padding: 40px;
			box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
		}

		.builder-section-title {
			font-size: 1.15rem;
			font-weight: 800;
			color: #0f172a;
			margin-bottom: 16px;
			padding-bottom: 8px;
			border-bottom: 2px solid #f1f5f9;
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.lesson-item {
			background-color: #f8fafc;
			border: 1px solid #e2e8f0;
			border-radius: 14px;
			padding: 20px;
			margin-bottom: 18px;
			position: relative;
			transition: border-color 0.2s;
		}

		.lesson-item:hover {
			border-color: #cbd5e1;
		}

		.lesson-item-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 14px;
		}

		.lesson-badge {
			font-size: 0.82rem;
			font-weight: 800;
			color: #0284c7;
			background-color: #e0f2fe;
			padding: 4px 12px;
			border-radius: 9999px;
			text-transform: uppercase;
			letter-spacing: 0.05em;
		}

		.btn-remove-lesson {
			background: none;
			border: none;
			color: #ef4444;
			font-size: 0.82rem;
			font-weight: 700;
			cursor: pointer;
			display: inline-flex;
			align-items: center;
			gap: 4px;
			padding: 4px 8px;
			border-radius: 6px;
			transition: background-color 0.2s;
		}

		.btn-remove-lesson:hover {
			background-color: #fee2e2;
		}

		.lesson-grid {
			display: grid;
			grid-template-columns: 2fr 1fr;
			gap: 14px;
		}

		.ad-break-notice {
			background-color: #ecfdf5;
			border: 1px solid #a7f3d0;
			border-radius: 12px;
			padding: 16px 20px;
			margin: 24px 0;
			display: flex;
			align-items: center;
			gap: 14px;
		}

		.btn-add-lesson {
			background-color: #f1f5f9;
			color: #0f172a;
			border: 2px dashed #cbd5e1;
			width: 100%;
			padding: 14px;
			border-radius: 12px;
			font-weight: 700;
			font-size: 0.92rem;
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 8px;
			transition: background-color 0.2s, border-color 0.2s;
			margin-bottom: 28px;
		}

		.btn-add-lesson:hover {
			background-color: #e2e8f0;
			border-color: #94a3b8;
		}
	</style>
</head>

<body class="instructor-dashboard-body">

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

	<main class="builder-wrapper">
		<div class="builder-card">
			<a href="dashboard.php" class="back-home-link">
				<img src="../assets/icons/arrow-left.svg" width="16" height="16" alt="Back">
				Back to Dashboard
			</a>

			<div class="auth-card__header" style="margin-top: 14px;">
				<span class="auth-badge" style="background-color: #e0f2fe; color: #0284c7;">
					<img src="../assets/icons/video.svg" width="14" height="14" alt="Video">
					Multi-Video Course Studio
				</span>
				<h1 class="auth-title">Publish Multi-Video Course</h1>
				<p class="auth-subtitle">
					Upload sequential lesson videos. Adsity automatically places 15-second sponsor ads between each video to fund free education and generate instructor revenue.
				</p>
			</div>

			<?php if ($status === 'error'): ?>
				<div class="alert alert--error" style="margin-bottom: 24px;">
					<img src="../assets/icons/alert-circle.svg" width="20" height="20" alt="Error">
					<span><?= htmlspecialchars($message ?? 'An error occurred.') ?></span>
				</div>
			<?php endif; ?>

			<form action="create_course_function.php" method="POST" enctype="multipart/form-data" class="auth-form" id="courseForm">

				<!-- 1. COURSE BASICS -->
				<div class="builder-section-title">
					<img src="../assets/icons/book-open.svg" width="18" height="18" alt="Book">
					1. Course Details &amp; Metadata
				</div>

				<div class="form-group">
					<label for="title" class="form-label">Course Title</label>
					<div class="form-input-wrapper">
						<input 
							type="text" 
							id="title" 
							name="title" 
							class="form-input" 
							placeholder="e.g. Modern Full-Stack Next.js &amp; MariaDB Masterclass" 
							required
						>
					</div>
				</div>

				<div class="lesson-grid">
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
						<label for="thumbnail" class="form-label">Thumbnail Artwork</label>
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
				</div>

				<div class="form-group">
					<label for="description" class="form-label">Course Description &amp; Overview</label>
					<div class="form-input-wrapper">
						<textarea 
							id="description" 
							name="description" 
							class="form-input" 
							rows="3" 
							placeholder="Describe what students will learn, curriculum summary, prerequisites..." 
							style="resize: vertical;" 
							required
						></textarea>
					</div>
				</div>

				<!-- 2. FINAL EXAM DELIVERABLE -->
				<div class="builder-section-title" style="margin-top: 24px;">
					<img src="../assets/icons/award.svg" width="18" height="18" alt="Award">
					2. Final Exam / Certificate Deliverable
				</div>

				<div class="form-group">
					<label for="assessment_type" class="form-label">Assessment Submission Type</label>
					<div class="form-input-wrapper">
						<select id="assessment_type" name="assessment_type" class="form-input" style="background-color: #ffffff;" required>
							<option value="github_repo">🐙 GitHub Repository Link (Students submit source code repository)</option>
							<option value="file_upload">📁 Project File Upload (Students submit ZIP, PDF, or Design file)</option>
							<option value="live_url">🌐 Live Project / Demo URL (Students submit a deployed website link)</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="assessment_instructions" class="form-label">Assessment Instructions / Requirements</label>
					<div class="form-input-wrapper">
						<textarea 
							id="assessment_instructions" 
							name="assessment_instructions" 
							class="form-input" 
							rows="2" 
							placeholder="e.g. Build and push your final project to GitHub with documentation in the README." 
							style="resize: vertical;" 
							required
						></textarea>
					</div>
				</div>

				<!-- 3. MULTI-VIDEO LESSON BUILDER -->
				<div class="builder-section-title" style="margin-top: 24px;">
					<img src="../assets/icons/video.svg" width="18" height="18" alt="Video">
					3. Video Lessons Curriculum Builder
				</div>

				<div id="lessonsContainer">
					<!-- Initial Lesson 1 -->
					<div class="lesson-item" data-lesson="1">
						<div class="lesson-item-header">
							<span class="lesson-badge">Lesson 1</span>
						</div>

						<div class="form-group" style="margin-bottom: 12px;">
							<label class="form-label">Lesson Title</label>
							<div class="form-input-wrapper">
								<input 
									type="text" 
									name="lesson_titles[]" 
									class="form-input" 
									placeholder="e.g. 01. Introduction &amp; Architecture Overview" 
									required
								>
							</div>
						</div>

						<div class="lesson-grid">
							<div class="form-group" style="margin-bottom: 0;">
								<label class="form-label">Video File (MP4, WebM, OGG)</label>
								<div class="form-input-wrapper">
									<input 
										type="file" 
										name="lesson_videos[]" 
										class="form-input" 
										accept="video/mp4,video/webm,video/ogg" 
										style="padding: 8px;"
									>
								</div>
							</div>

							<div class="form-group" style="margin-bottom: 0;">
								<label class="form-label">Duration (MM:SS)</label>
								<div class="form-input-wrapper">
									<input 
										type="text" 
										name="lesson_durations[]" 
										class="form-input" 
										placeholder="08:30" 
										value="08:30"
									>
								</div>
							</div>
						</div>
					</div>

					<!-- Initial Lesson 2 -->
					<div class="lesson-item" data-lesson="2">
						<div class="lesson-item-header">
							<span class="lesson-badge">Lesson 2</span>
							<button type="button" class="btn-remove-lesson" onclick="removeLesson(this)">
								<img src="../assets/icons/trash.svg" width="14" height="14" alt="Delete">
								Remove
							</button>
						</div>

						<div class="form-group" style="margin-bottom: 12px;">
							<label class="form-label">Lesson Title</label>
							<div class="form-input-wrapper">
								<input 
									type="text" 
									name="lesson_titles[]" 
									class="form-input" 
									placeholder="e.g. 02. Setting Up Database Schemas &amp; Models" 
									required
								>
							</div>
						</div>

						<div class="lesson-grid">
							<div class="form-group" style="margin-bottom: 0;">
								<label class="form-label">Video File (MP4, WebM, OGG)</label>
								<div class="form-input-wrapper">
									<input 
										type="file" 
										name="lesson_videos[]" 
										class="form-input" 
										accept="video/mp4,video/webm,video/ogg" 
										style="padding: 8px;"
									>
								</div>
							</div>

							<div class="form-group" style="margin-bottom: 0;">
								<label class="form-label">Duration (MM:SS)</label>
								<div class="form-input-wrapper">
									<input 
										type="text" 
										name="lesson_durations[]" 
										class="form-input" 
										placeholder="12:45" 
										value="12:45"
									>
								</div>
							</div>
						</div>
					</div>
				</div>

				<button type="button" class="btn-add-lesson" id="btnAddLesson">
					<img src="../assets/icons/plus.svg" width="16" height="16" alt="Plus">
					+ Add Another Lesson Video
				</button>

				<!-- Live Ad Monetization Notice -->
				<div class="ad-break-notice">
					<img src="../assets/icons/check-circle.svg" width="24" height="24" alt="Ad Monetization" style="filter: brightness(0) saturate(100%) invert(48%) sepia(85%) saturate(415%) hue-rotate(94deg) brightness(97%) contrast(92%);">
					<div>
						<strong style="color: #065f46; font-size: 0.92rem; display: block;">Automated Ad-Break Monetization Engine</strong>
						<span id="adBreakSummary" style="color: #047857; font-size: 0.85rem;">
							Curriculum: <strong>2 lessons</strong> • Automated Sponsor Ad Breaks: <strong>1 interstitial break</strong>.
						</span>
					</div>
				</div>

				<button type="submit" name="create_course" class="btn-auth-submit" style="background-color: #0284c7; padding: 14px;">
					Publish Full Multi-Video Course
					<img src="../assets/icons/arrow-right.svg" width="18" height="18" alt="Submit" style="filter: brightness(0) invert(1);">
				</button>
			</form>
		</div>
	</main>

	<!-- Dynamic Lesson Script -->
	<script>
		const container = document.getElementById('lessonsContainer');
		const btnAdd = document.getElementById('btnAddLesson');
		const adSummary = document.getElementById('adBreakSummary');

		function updateLessonNumbers() {
			const items = container.querySelectorAll('.lesson-item');
			items.forEach((item, index) => {
				const num = index + 1;
				item.dataset.lesson = num;
				const badge = item.querySelector('.lesson-badge');
				if (badge) {
					badge.textContent = 'Lesson ' + num;
				}
			});

			const total = items.length;
			const adBreaks = Math.max(0, total - 1);
			adSummary.innerHTML = `Curriculum: <strong>${total} lessons</strong> • Automated Sponsor Ad Breaks: <strong>${adBreaks} interstitial ${adBreaks === 1 ? 'break' : 'breaks'}</strong>.`;
		}

		btnAdd.addEventListener('click', () => {
			const count = container.querySelectorAll('.lesson-item').length + 1;
			const lessonDiv = document.createElement('div');
			lessonDiv.className = 'lesson-item';
			lessonDiv.dataset.lesson = count;
			lessonDiv.innerHTML = `
				<div class="lesson-item-header">
					<span class="lesson-badge">Lesson ${count}</span>
					<button type="button" class="btn-remove-lesson" onclick="removeLesson(this)">
						<img src="../assets/icons/trash.svg" width="14" height="14" alt="Delete">
						Remove
					</button>
				</div>
				<div class="form-group" style="margin-bottom: 12px;">
					<label class="form-label">Lesson Title</label>
					<div class="form-input-wrapper">
						<input 
							type="text" 
							name="lesson_titles[]" 
							class="form-input" 
							placeholder="e.g. 0${count}. Lesson Module Title" 
							required
						>
					</div>
				</div>
				<div class="lesson-grid">
					<div class="form-group" style="margin-bottom: 0;">
						<label class="form-label">Video File (MP4, WebM, OGG)</label>
						<div class="form-input-wrapper">
							<input 
								type="file" 
								name="lesson_videos[]" 
								class="form-input" 
								accept="video/mp4,video/webm,video/ogg" 
								style="padding: 8px;"
							>
						</div>
					</div>
					<div class="form-group" style="margin-bottom: 0;">
						<label class="form-label">Duration (MM:SS)</label>
						<div class="form-input-wrapper">
							<input 
								type="text" 
								name="lesson_durations[]" 
								class="form-input" 
								placeholder="10:00" 
								value="10:00"
							>
						</div>
					</div>
				</div>
			`;
			container.appendChild(lessonDiv);
			updateLessonNumbers();
		});

		function removeLesson(btn) {
			const items = container.querySelectorAll('.lesson-item');
			if (items.length <= 1) {
				alert('A course must have at least one lesson.');
				return;
			}
			btn.closest('.lesson-item').remove();
			updateLessonNumbers();
		}
	</script>

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
