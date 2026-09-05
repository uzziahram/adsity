<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Authentication Guard: Must be logged in as a student
if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'student') {
    $courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
    header('Location: ../login.php?redirect_course=' . $courseId . '&status=error&message=' . urlencode('Please log in as a student to access the classroom.'));
    exit;
}

require_once __DIR__ . '/../database/config.php';

$studentId = (int)$_SESSION['user_id'];
$courseId  = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if ($courseId <= 0) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Invalid course specified.'));
    exit;
}

$course = null;
$lessons = [];
$completedLessonIds = [];
$activeLesson = null;
$allLessonsCompleted = false;

try {
    $pdo = getConnection();

    // 2. Verify Enrollment
    $stmtEnr = $pdo->prepare("SELECT id, progress_percent, status FROM enrollments WHERE user_id = :uid AND course_id = :cid LIMIT 1");
    $stmtEnr->execute([':uid' => $studentId, ':cid' => $courseId]);
    $enrollment = $stmtEnr->fetch();

    if (!$enrollment) {
        header('Location: ../course_details.php?id=' . $courseId . '&status=error&message=' . urlencode('You must be enrolled in this course to access the classroom.'));
        exit;
    }

    // 3. Fetch Course Details
    $stmtCourse = $pdo->prepare("SELECT id, title, description, category, thumbnail, total_lessons, assessment_type, assessment_instructions FROM courses WHERE id = :cid LIMIT 1");
    $stmtCourse->execute([':cid' => $courseId]);
    $course = $stmtCourse->fetch();

    if (!$course) {
        header('Location: dashboard.php?status=error&message=' . urlencode('Course not found.'));
        exit;
    }

    // 4. Fetch All Lessons for this course
    $stmtLessons = $pdo->prepare("SELECT id, lesson_number, title, video_path, duration FROM lessons WHERE course_id = :cid ORDER BY lesson_number ASC");
    $stmtLessons->execute([':cid' => $courseId]);
    $lessons = $stmtLessons->fetchAll();

    // 5. Fetch Completed Lesson IDs for this student
    $stmtComp = $pdo->prepare("SELECT lesson_id FROM lesson_completions WHERE user_id = :uid AND course_id = :cid");
    $stmtComp->execute([':uid' => $studentId, ':cid' => $courseId]);
    $completedLessonIds = $stmtComp->fetchAll(PDO::FETCH_COLUMN);
    $completedLessonIds = array_map('intval', $completedLessonIds);

    $totalLessonsCount = count($lessons);
    $completedCount = count($completedLessonIds);
    $currentProgressPercent = $totalLessonsCount > 0 ? min(100, (int)round(($completedCount / $totalLessonsCount) * 100)) : 0;
    $allLessonsCompleted = ($currentProgressPercent >= 100 || ($completedCount >= $totalLessonsCount && $totalLessonsCount > 0));

    // 6. Determine Active Lesson (Resume where student left off at 00:00)
    $requestedLessonId = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;

    if ($requestedLessonId > 0) {
        foreach ($lessons as $l) {
            if ((int)$l['id'] === $requestedLessonId) {
                $activeLesson = $l;
                break;
            }
        }
    }

    // If no specific lesson requested, find first uncompleted lesson
    if (!$activeLesson) {
        foreach ($lessons as $l) {
            if (!in_array((int)$l['id'], $completedLessonIds)) {
                $activeLesson = $l;
                break;
            }
        }
    }

    // If all completed or none found, fall back to lesson 1
    if (!$activeLesson && !empty($lessons)) {
        $activeLesson = $lessons[0];
    }

} catch (PDOException $e) {
    header('Location: dashboard.php?status=error&message=' . urlencode('Database error: ' . $e->getMessage()));
    exit;
}

// Prepare video URL: Ensure clean path
$rawVideoPath = $activeLesson['video_path'] ?? 'assets/ad/sample_ad.mp4';
$lessonVideoSrc = str_starts_with($rawVideoPath, 'http') || str_starts_with($rawVideoPath, '/')
    ? $rawVideoPath
    : '../' . $rawVideoPath;

// Ad video asset
$adVideoSrc = '../assets/ad/sample_ad.mp4';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= htmlspecialchars($activeLesson['title'] ?? 'Lesson') ?> - <?= htmlspecialchars($course['title']) ?> | Adsity Classroom</title>
	<link rel="stylesheet" href="../style.css">
	<link rel="stylesheet" href="learn.css">
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body class="classroom-body">

	<!-- Classroom Top Header -->
	<header class="classroom-navbar">
		<div class="nav-left">
			<a href="dashboard.php" class="btn-nav-back" title="Return to Student Dashboard">
				<img src="../assets/icons/arrow-left.svg" width="16" height="16" alt="Back">
				<span>Dashboard</span>
			</a>
			<div class="nav-divider"></div>
			<div class="nav-course-info">
				<span class="nav-course-badge"><?= htmlspecialchars($course['category'] ?? 'Course') ?></span>
				<h1 class="nav-course-title" title="<?= htmlspecialchars($course['title']) ?>">
					<?= htmlspecialchars($course['title']) ?>
				</h1>
			</div>
		</div>

		<div class="nav-right" id="navRightContainer">
			<div class="nav-progress-badge">
				<div class="progress-ring-text">
					<span id="navProgressPercent"><?= $currentProgressPercent ?>%</span> Complete
				</div>
				<div class="nav-progress-bar-bg">
					<div id="navProgressBar" class="nav-progress-bar-fill" style="width: <?= $currentProgressPercent ?>%;"></div>
				</div>
			</div>

			<a href="submit_exam.php?course_id=<?= $courseId ?>" class="btn-top-submit-exam" id="topNavSubmitBtn" style="<?= $allLessonsCompleted ? '' : 'display: none;' ?>">
				<img src="../assets/icons/award.svg" width="16" height="16" alt="Award">
				<span>Submit Final Project</span>
			</a>
		</div>
	</header>

	<!-- Main Classroom Layout (Left: Video Player / Ad, Right: Syllabus Sidebar) -->
	<div class="classroom-layout">

		<!-- ============================================================
		     LEFT / MAIN WORKSPACE: VIDEO STAGE
		     ============================================================ -->
		<main class="classroom-main">

			<!-- Video Player Stage Container -->
			<div class="video-stage-container">

				<!-- 1. SPONSOR AD CONTAINER (Plays First) -->
				<div id="adPlayerWrapper" class="ad-player-wrapper">
					<video 
						id="adVideoPlayer" 
						class="video-element" 
						src="<?= htmlspecialchars($adVideoSrc) ?>" 
						playsinline
						preload="auto"
					></video>

					<!-- Ad Overlay Controls & Wellness Reminder -->
					<div class="ad-overlay-layer">
						<div class="ad-overlay-top">
							<span class="ad-sponsor-pill">
								<span class="ad-pulse-dot"></span>
								Ad Session
							</span>
							<div class="ad-countdown-pill" id="adCountdownBadge">
								Lesson starts in <strong id="adTimerText">15s</strong>
							</div>
						</div>

						<!-- Wellness Break Notification -->
						<div class="ad-wellness-box">
							<div class="ad-wellness-icon">💧</div>
							<div class="ad-wellness-content">
								<div class="ad-wellness-title">Take a Quick Sip of Water &amp; Stretch!</div>
								<div class="ad-wellness-sub">
									Our sponsor keeps this verified course 100% free. Your lesson will start automatically.
								</div>
							</div>
						</div>
					</div>

					<!-- Big Play Trigger for Browsers Blocking Autoplay -->
					<div id="adPlayOverlayBtn" class="play-trigger-overlay" style="display: none;">
						<button type="button" class="btn-big-play" onclick="startAdPlayback()">
							<img src="../assets/icons/play.svg" width="28" height="28" alt="Play" style="filter: brightness(0) invert(1);">
							<span>Start Ad Session</span>
						</button>
					</div>
				</div>

				<!-- 2. LESSON VIDEO CONTAINER (Plays after Ad) -->
				<div id="lessonPlayerWrapper" class="lesson-player-wrapper" style="display: none;">
					<video 
						id="lessonVideoPlayer" 
						class="video-element" 
						src="<?= htmlspecialchars($lessonVideoSrc) ?>" 
						controls 
						playsinline
						preload="metadata"
					></video>

					<!-- Lesson Transition Overlay (shown when lesson completes) -->
					<div id="lessonCompleteOverlay" class="lesson-complete-overlay" style="display: none;">
						<div class="lesson-complete-card">
							<div class="complete-checkmark-icon">✓</div>
							<h3 class="complete-card-title">Lesson Completed!</h3>
							<p class="complete-card-subtitle" id="completeCardMessage">
								Next lesson starting after a brief ad session...
							</p>
							<div class="complete-card-actions">
								<button type="button" class="btn-complete-advance" id="btnAdvanceNow">
									<span>Continue to Next Lesson</span>
									<img src="../assets/icons/arrow-right.svg" width="16" height="16" alt="Next" style="filter: brightness(0) invert(1);">
								</button>
							</div>
						</div>
					</div>
				</div>

			</div>

			<!-- Lesson Information & Action Bar Below Player -->
			<div class="lesson-info-bar">
				<div class="lesson-info-left">
					<div class="lesson-number-tag">
						Lesson <?= str_pad($activeLesson['lesson_number'] ?? 1, 2, '0', STR_PAD_LEFT) ?> of <?= count($lessons) ?>
					</div>
					<h2 class="lesson-current-title" id="currentLessonTitleDisplay">
						<?= htmlspecialchars($activeLesson['title'] ?? 'Lesson Title') ?>
					</h2>
				</div>

				<div class="lesson-info-right">
					<div class="lesson-status-pill" id="lessonStatusBadge">
						<?php if (in_array((int)$activeLesson['id'], $completedLessonIds)): ?>
							<span class="status-badge status-badge--completed">✓ Completed</span>
						<?php else: ?>
							<span class="status-badge status-badge--playing">▶ In Progress</span>
						<?php endif; ?>
					</div>

					<?php if (in_array((int)$activeLesson['id'], $completedLessonIds)): ?>
						<button type="button" class="btn-manual-complete btn-manual-completed" id="btnManualComplete" disabled style="opacity: 0.65; cursor: default;">
							<img src="../assets/icons/check-circle.svg" width="16" height="16" alt="Check">
							<span>Completed</span>
						</button>
					<?php else: ?>
						<button type="button" class="btn-manual-complete" id="btnManualComplete" onclick="markActiveLessonComplete()">
							<img src="../assets/icons/check-circle.svg" width="16" height="16" alt="Check">
							<span>Mark as Complete</span>
						</button>
					<?php endif; ?>
				</div>
			</div>

			<!-- Quick Advice Card -->
			<div class="classroom-tip-card">
				<div class="tip-icon">💡</div>
				<div class="tip-content">
					<strong>Active Learning Mode:</strong> Take notes as you watch. Lessons automatically mark completed when you finish watching, saving your progress securely to your account.
				</div>
			</div>

		</main>

		<!-- ============================================================
		     RIGHT SIDEBAR: SYLLABUS, PROGRESS & FINAL EXAM MILESTONE
		     ============================================================ -->
		<aside class="classroom-sidebar">

			<!-- Sidebar Header & Progress -->
			<div class="sidebar-header-box">
				<div class="sidebar-title-row">
					<h3 class="sidebar-heading">Course Syllabus</h3>
					<span class="sidebar-lesson-count" id="sidebarProgressCount">
						<?= $completedCount ?> / <?= $totalLessonsCount ?> Lessons
					</span>
				</div>

				<div class="sidebar-progress-container">
					<div class="sidebar-progress-bar-bg">
						<div id="sidebarProgressBar" class="sidebar-progress-bar-fill" style="width: <?= $currentProgressPercent ?>%;"></div>
					</div>
					<div class="sidebar-progress-text">
						<span id="sidebarProgressText"><?= $currentProgressPercent ?>% Complete</span>
						<span><?= $allLessonsCompleted ? '🎉 Ready for Final Output' : 'Watch all to unlock Exam' ?></span>
					</div>
				</div>
			</div>

			<!-- Lessons Interactive List -->
			<div class="sidebar-lessons-scroll">
				<div class="sidebar-lessons-list">
					<?php foreach ($lessons as $idx => $l): 
						$isCurrent = ((int)$l['id'] === (int)$activeLesson['id']);
						$isDone    = in_array((int)$l['id'], $completedLessonIds);
					?>
						<a 
							href="learn.php?course_id=<?= $courseId ?>&lesson_id=<?= $l['id'] ?>" 
							class="sidebar-lesson-item <?= $isCurrent ? 'active' : '' ?> <?= $isDone ? 'completed' : '' ?>"
							id="sidebarLesson_<?= $l['id'] ?>"
						>
							<div class="lesson-item-status-icon">
								<?php if ($isDone): ?>
									<span class="icon-check">✓</span>
								<?php elseif ($isCurrent): ?>
									<span class="icon-play">▶</span>
								<?php else: ?>
									<span class="icon-num"><?= str_pad($l['lesson_number'], 2, '0', STR_PAD_LEFT) ?></span>
								<?php endif; ?>
							</div>

							<div class="lesson-item-details">
								<div class="lesson-item-title"><?= htmlspecialchars($l['title']) ?></div>
								<div class="lesson-item-meta">
									<span>⏱️ <?= htmlspecialchars($l['duration'] ?: '10:00') ?></span>
									<?php if ($isDone): ?>
										<span class="meta-done-tag">Finished</span>
									<?php elseif ($isCurrent): ?>
										<span class="meta-current-tag">Playing Now</span>
									<?php endif; ?>
								</div>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Final Project Deliverable Box at bottom of sidebar -->
			<div class="sidebar-final-milestone">
				<div class="milestone-card <?= $allLessonsCompleted ? 'milestone-unlocked' : 'milestone-locked' ?>" id="milestoneCard">
					<div class="milestone-header">
						<div class="milestone-icon-circle">
							<?php if ($allLessonsCompleted): ?>
								<img src="../assets/icons/award.svg" width="22" height="22" alt="Unlocked">
							<?php else: ?>
								<img src="../assets/icons/lock.svg" width="20" height="20" alt="Locked" style="filter: brightness(0) saturate(100%) invert(42%) sepia(13%) saturate(1072%) hue-rotate(182deg) brightness(94%) contrast(87%);">
							<?php endif; ?>
						</div>
						<div>
							<h4 class="milestone-title">Final Project Assessment</h4>
							<div class="milestone-sub">
								<?= $allLessonsCompleted ? 'Ready for Submission' : 'Locked until all lessons complete' ?>
							</div>
						</div>
					</div>

					<p class="milestone-desc">
						<?= $allLessonsCompleted 
							? 'All video lessons are complete! Submit your practical deliverable to earn your official certificate.' 
							: 'Complete all video lessons in the course to unlock your project deliverable form.' 
						?>
					</p>

					<div class="milestone-action-wrap" id="milestoneActionWrap">
						<?php if ($allLessonsCompleted): ?>
							<a href="submit_exam.php?course_id=<?= $courseId ?>" class="btn-milestone-submit">
								<span>Submit Final Project</span>
								<img src="../assets/icons/arrow-right.svg" width="16" height="16" alt="Submit" style="filter: brightness(0) invert(1);">
							</a>
						<?php else: ?>
							<div class="milestone-locked-badge">
								<img src="../assets/icons/lock.svg" width="14" height="14" alt="Locked" style="filter: brightness(0) saturate(100%) invert(42%) sepia(13%) saturate(1072%) hue-rotate(182deg) brightness(94%) contrast(87%);">
								<span>Complete remaining lessons to unlock</span>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

		</aside>

	</div>

	<!-- Pass PHP State to Classroom JavaScript Controller -->
	<script>
		const COURSE_ID = <?= (int)$courseId ?>;
		const CURRENT_LESSON_ID = <?= (int)$activeLesson['id'] ?>;
		const CURRENT_LESSON_NUM = <?= (int)$activeLesson['lesson_number'] ?>;
		const TOTAL_LESSONS = <?= (int)$totalLessonsCount ?>;
		let isCompleted = <?= in_array((int)$activeLesson['id'], $completedLessonIds) ? 'true' : 'false' ?>;

		// Lessons list metadata
		const ALL_LESSONS = <?= json_encode(array_map(function($l) use ($completedLessonIds) {
			return [
				'id'            => (int)$l['id'],
				'lesson_number' => (int)$l['lesson_number'],
				'title'         => $l['title'],
				'video_path'    => $l['video_path'],
				'duration'      => $l['duration'],
				'is_completed'  => in_array((int)$l['id'], $completedLessonIds)
			];
		}, $lessons)) ?>;

		// DOM Elements
		const adPlayerWrapper     = document.getElementById('adPlayerWrapper');
		const adVideo             = document.getElementById('adVideoPlayer');
		const adTimerText         = document.getElementById('adTimerText');
		const adPlayOverlayBtn    = document.getElementById('adPlayOverlayBtn');

		const lessonPlayerWrapper = document.getElementById('lessonPlayerWrapper');
		const lessonVideo         = document.getElementById('lessonVideoPlayer');
		const lessonOverlay       = document.getElementById('lessonCompleteOverlay');
		const btnAdvanceNow       = document.getElementById('btnAdvanceNow');

		// 1. Start Ad Playback on Page Load
		function startAdPlayback() {
			adPlayOverlayBtn.style.display = 'none';
			adVideo.play().catch(() => {
				// Browser autoplay policy prevented playback; reveal manual click button
				adPlayOverlayBtn.style.display = 'flex';
			});
		}

		// Update countdown timer while ad plays
		function updateAdCountdown() {
			if (adVideo.duration) {
				const remaining = Math.max(0, Math.ceil(adVideo.duration - adVideo.currentTime));
				adTimerText.textContent = `${remaining}s`;
			}
		}
		adVideo.addEventListener('timeupdate', updateAdCountdown);
		adVideo.addEventListener('loadedmetadata', updateAdCountdown);

		// Enforce unskippable sponsor break
		adVideo.addEventListener('contextmenu', (e) => e.preventDefault());
		adVideo.addEventListener('keydown', (e) => {
			if (['ArrowLeft', 'ArrowRight', 'Home', 'End', 'PageUp', 'PageDown'].includes(e.key)) {
				e.preventDefault();
			}
		});

		// 2. When Sponsor Ad Ends: Seamlessly switch to Lesson Video at 00:00
		adVideo.addEventListener('ended', () => {
			transitionFromAdToLesson();
		});

		function transitionFromAdToLesson() {
			adPlayerWrapper.style.display = 'none';
			lessonPlayerWrapper.style.display = 'block';

			// Start from the beginning (00:00) per requirement
			lessonVideo.currentTime = 0;
			lessonVideo.play().catch(() => {
				// Native controls will allow user to click play if autoplay was blocked
			});
		}

		// 3. When Lesson Video Finishes: Mark complete & advance
		lessonVideo.addEventListener('ended', () => {
			markActiveLessonComplete(true);
		});

		// 4. Mark Lesson Completed Function (Calls complete_lesson.php API)
		let isSubmittingCompletion = false;

		async function markActiveLessonComplete(autoAdvance = false) {
			if (isSubmittingCompletion) return;
			isSubmittingCompletion = true;

			try {
				const response = await fetch('complete_lesson.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({
						course_id: COURSE_ID,
						lesson_id: CURRENT_LESSON_ID
					})
				});

				const result = await response.json();

				if (result.success) {
					// Update UI State
					updateClassroomProgressUI(result);

					if (autoAdvance) {
						handleNextLessonTransition(result);
					}
				}
			} catch (err) {
				console.error('Failed to update lesson completion:', err);
			} finally {
				isSubmittingCompletion = false;
			}
		}

		function updateClassroomProgressUI(data) {
			// Update Top Navbar Progress
			document.getElementById('navProgressPercent').textContent = `${data.progress_percent}%`;
			document.getElementById('navProgressBar').style.width = `${data.progress_percent}%`;

			// Update Sidebar Progress
			document.getElementById('sidebarProgressBar').style.width = `${data.progress_percent}%`;
			document.getElementById('sidebarProgressText').textContent = `${data.progress_percent}% Complete`;
			document.getElementById('sidebarProgressCount').textContent = `${data.completed_lessons} / ${data.total_lessons} Lessons`;

			// Update Lesson item in sidebar
			const sidebarItem = document.getElementById(`sidebarLesson_${CURRENT_LESSON_ID}`);
			if (sidebarItem) {
				sidebarItem.classList.add('completed');
				const iconContainer = sidebarItem.querySelector('.lesson-item-status-icon');
				if (iconContainer) iconContainer.innerHTML = '<span class="icon-check">✓</span>';
			}

			// Update lesson status badge & manual complete button
			const statusBadge = document.getElementById('lessonStatusBadge');
			if (statusBadge) {
				statusBadge.innerHTML = '<span class="status-badge status-badge--completed">✓ Completed</span>';
			}

			const btnManual = document.getElementById('btnManualComplete');
			if (btnManual) {
				btnManual.innerHTML = '<img src="../assets/icons/check-circle.svg" width="16" height="16" alt="Check"><span>Completed</span>';
				btnManual.disabled = true;
				btnManual.style.opacity = '0.65';
				btnManual.style.cursor = 'default';
			}

			// If all lessons are completed, unlock the final project assessment
			if (data.all_completed) {
				unlockFinalProjectMilestone();
			}
		}

		function unlockFinalProjectMilestone() {
			const milestoneCard = document.getElementById('milestoneCard');
			const actionWrap = document.getElementById('milestoneActionWrap');

			if (milestoneCard && actionWrap) {
				milestoneCard.classList.remove('milestone-locked');
				milestoneCard.classList.add('milestone-unlocked');

				actionWrap.innerHTML = `
					<a href="submit_exam.php?course_id=${COURSE_ID}" class="btn-milestone-submit">
						<span>Submit Final Project</span>
						<img src="../assets/icons/arrow-right.svg" width="16" height="16" alt="Submit" style="filter: brightness(0) invert(1);">
					</a>
				`;
			}

			// Also reveal top navbar "Submit Final Project" button
			const topNavBtn = document.getElementById('topNavSubmitBtn');
			if (topNavBtn) {
				topNavBtn.style.display = 'inline-flex';
			}
		}

		function handleNextLessonTransition(data) {
			if (data.next_lesson) {
				// Show transition modal
				lessonOverlay.style.display = 'flex';
				document.getElementById('completeCardMessage').textContent = 
					`Lesson ${CURRENT_LESSON_NUM} completed! Next: "${data.next_lesson.title}". Advancing in 3 seconds...`;

				btnAdvanceNow.onclick = () => {
					window.location.href = `learn.php?course_id=${COURSE_ID}&lesson_id=${data.next_lesson.id}`;
				};

				setTimeout(() => {
					window.location.href = `learn.php?course_id=${COURSE_ID}&lesson_id=${data.next_lesson.id}`;
				}, 3500);
			} else {
				// Course 100% Finished!
				lessonOverlay.style.display = 'flex';
				document.getElementById('completeCardMessage').textContent = 
					`🎉 Congratulations! You have finished all lessons in this course!`;
				btnAdvanceNow.innerHTML = `
					<span>Go to Final Project Assessment</span>
					<img src="../assets/icons/arrow-right.svg" width="16" height="16" alt="Go" style="filter: brightness(0) invert(1);">
				`;
				btnAdvanceNow.onclick = () => {
					window.location.href = `submit_exam.php?course_id=${COURSE_ID}`;
				};
			}
		}

		// Start Ad playback on page ready
		window.addEventListener('DOMContentLoaded', () => {
			startAdPlayback();
		});
	</script>

</body>

</html>
