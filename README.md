# Adsity - Technical & Functional Documentation

**Version:** 1.0.0  
**Environment:** Linux / LAMPP (XAMPP for Linux)  
**Database:** MariaDB 10.4 (Port 3307)  
**Web Server:** Apache (Port 81)  
**URL Base:** `http://localhost:81/projects/adsity/`

---

## 1. Project Overview

**Adsity** is a web-based educational platform providing free, ad-supported technology courses and verified industry certificates. The system operates on a sustainable model where students watch short 15-second sponsor advertisement breaks before lessons in exchange for zero-cost education and credentialing, while instructors earn a direct revenue share ($0.05 per completed ad session).

### Core User Roles & Responsibilities:
- **Student (`role_id = 3`):** Explores course catalogs, enrolls in courses, participates in the interactive video classroom, completes lessons sequentially, submits final project deliverables, and views/prints accredited completion certificates.
- **Instructor / Teacher (`role_id = 2`):** Registers via dedicated onboarding, builds multi-video courses with custom assessment rubrics, monitors student rosters and completion rates, grades submitted final projects, earns ad revenue, and requests withdrawals via PayPal, GCash, or Bank Transfer.
- **Administrator (`role_id = 1`):** System superuser who oversees all registered students and teachers, reviews and processes instructor cash-out requests with transaction receipts, disburses earnings or refunds rejected requests, and manages user accounts.

---

## 2. Technology Stack

| Layer | Technology | Details |
| :--- | :--- | :--- |
| **Backend** | PHP 8.x | Native procedural PHP with PDO database abstraction and transactions |
| **Database** | MariaDB 10.4.32 | Relational schema with foreign keys, indexes, and cascading constraints |
| **Session & Auth** | PHP Sessions (`$_SESSION`) | Secure session handling, role-based access control (RBAC), and credential memory purging |
| **Media Processing** | FFmpeg (`ffprobe`) | Server-side CLI execution to detect precise video durations from uploaded MP4s |
| **Frontend** | HTML5 / CSS3 / Vanilla JS | Custom modular stylesheets (`style.css`, view-specific CSS), Raleway & Cinzel fonts, HTML5 Video API |
| **UI Components** | Custom JavaScript | Global glassmorphic logout confirmation modal (`logout_modal.js`), ad player state machine |
| **Assets** | Standalone SVG Vectors | 30 bespoke icons stored in `assets/icons/` and rendered via standard `<img>` and inline SVG |

---

## 3. Directory & File Structure

```
adsity/
├── admin/
│   ├── admindashboard.css           # Stylesheet for Admin Dashboard and Payout Modal
│   ├── dashboard.php                # Admin Dashboard view (KPIs, teacher/student tables, payout reviews)
│   ├── dashboard_function.php       # Data handler, KPI aggregation, and admin session gate
│   ├── delete_user.php              # Action handler to delete student or teacher records
│   └── process_payout_function.php  # Payout review handler (approvals, disbursements, refunds & rejections)
│
├── assets/
│   ├── ad/
│   │   └── sample_ad.mp4            # Default 15-second sponsor advertisement video asset
│   ├── adsity_assets/               # Course thumbnails, branding, and logo graphics
│   ├── icons/                       # Standalone SVG icon vector library (30 icons)
│   ├── js/
│   │   └── logout_modal.js          # Global interactive logout confirmation modal
│   ├── submissions/                 # Storage directory for student-uploaded project deliverables
│   ├── Adsity-Homepage-Mockup_Genella.pdf # Original design specification
│   ├── instructor_sign_up.jpg       # Hero branding graphic for instructor registration
│   └── student_sign_up.jpg          # Hero branding graphic for student registration
│
├── database/
│   ├── config.php                   # PDO database connection factory with failover fallback
│   └── schema.sql                   # Complete SQL schema & table definitions (11 tables)
│
├── instructor/
│   ├── course_overview.php          # Dedicated per-course analytics, student roster, and syllabus stats
│   ├── create_course.css            # Stylesheet for Multi-Step Course Creation Wizard
│   ├── create_course.php            # 4-step wizard interface to publish new courses
│   ├── create_course_function.php   # Backend course publishing handler, directory maker & ffprobe detector
│   ├── dashboard.php                # Instructor Studio dashboard (Overview, Courses, Submissions, Revenue)
│   ├── dashboard_function.php       # Instructor data aggregation, wallet metrics, and session gate
│   ├── delete_course.php            # Action handler to delete an instructor's own course
│   ├── instructordashboard.css      # Stylesheet for Instructor Studio and management tables
│   ├── request_payout_function.php  # Payout request validator (PayPal, GCash, Bank) with row-locking
│   └── review_submission_function.php # Project grading handler (approvals, revisions & cert issuance/revocation)
│
├── student/
│   ├── certificate.css              # Stylesheet for Verified Certificate view and print layout
│   ├── certificate.php              # Verified Certificate view (Print / Save as PDF)
│   ├── certificate_function.php     # Certificate lookup and verification query handler
│   ├── complete_lesson.php          # AJAX endpoint to record lesson completions and recalculate progress %
│   ├── dashboard.php                # Student Dashboard view (In-Progress, Completed, Certificates)
│   ├── dashboard_function.php       # Student data query handler and session gate
│   ├── enroll_function.php          # Student course enrollment processor (routes to course details)
│   ├── learn.css                    # Dedicated stylesheet for Interactive Video Classroom & Ad Stage
│   ├── learn.php                    # Video Classroom interface with mandatory 15s sponsor ad breaks
│   ├── record_ad_activity.php       # AJAX endpoint to log ad views, enforce cooldowns, and credit instructor
│   ├── studentdashboard.css         # Stylesheet for Student Dashboard and course progress cards
│   ├── submit_exam.css              # Stylesheet for Final Exam / Project submission view
│   ├── submit_exam.php              # Final project deliverable submission form (locked until 100% progress)
│   └── submit_exam_function.php     # Deliverable upload handler (saves submission in pending status)
│
├── uploads/
│   └── instructors/                 # Isolated instructor storage partitioned by instructor ID & course ID
│       └── {instructor_id}/
│           └── {course_id}/
│               ├── thumbnail/       # Course cover thumbnail (thumbnail_{timestamp}.ext)
│               └── lesson_{n}_{timestamp}.mp4 # Uploaded lesson MP4 video files
│
├── course_details.css               # Stylesheet for Course Overview & Syllabus page
├── course_details.php               # Course Overview page (Thumbnail, Syllabus, Final Output, Sticky CTA)
├── courses.css                      # Stylesheet for Course Catalog & Category filters
├── courses.php                      # Course Catalog with dynamic search and category filtering
├── courses_function.php             # Courses query handler with dynamic SQL filters and enrollment checks
├── index.php                        # Adsity landing page / platform homepage
├── login.php                        # User authentication login view (supports redirect_course)
├── login_function.php               # Login validation, credential verification & role routing
├── logout.php                       # Session destruction & logout redirection handler
├── README.md                        # Technical & functional documentation
├── signup.php                       # Student registration view (supports redirect_course)
├── signup_function.php              # Student registration handler & auto-login
├── style.css                        # Global design system, typography, colors, and responsive layout
├── teach.php                        # Instructor application & registration view
├── teach_function.php               # Instructor registration handler & session initialization
└── validation.php                   # Centralized input validation functions (email, password criteria, terms)
```

---

## 4. Database Schema & Architecture

### Relational Architecture & Entity Matrix

| Entity | Primary Key | Foreign Keys | Cardinality / Relationships | Responsibility |
| :--- | :--- | :--- | :--- | :--- |
| **`roles`** | `id` | None | `1 : N` with `users` | Defines user permissions (`1 = admin`, `2 = instructor`, `3 = student`). |
| **`users`** | `id` | `role_id -> roles(id)` | `1 : N` with `courses`, `enrollments`, `certificates` | Stores credentials, profile data, and assigned security role. |
| **`courses`** | `id` | `instructor_id -> users(id)` | `1 : N` with `lessons`, `enrollments`, `course_submissions` | Holds course catalog meta, categories, deliverable type, and instructions. |
| **`lessons`** | `id` | `course_id -> courses(id)` | `1 : N` with `lesson_completions`, `ad_activity_logs` | Represents sequential curriculum video modules and detected durations. |
| **`enrollments`** | `id` | `user_id -> users(id)`, `course_id -> courses(id)` | Many-to-Many bridge (`users` &lt;-&gt; `courses`) | Tracks student course enrollment, progress percent (0-100), and status. |
| **`lesson_completions`** | `id` | `user_id -> users(id)`, `course_id -> courses(id)`, `lesson_id -> lessons(id)` | Many-to-Many bridge (`users` &lt;-&gt; `lessons`) | Granular record of individual completed lessons per student. |
| **`course_submissions`** | `id` | `user_id -> users(id)`, `course_id -> courses(id)` | `N : 1` with `users`, `N : 1` with `courses` | Tracks student project deliverables (URLs/files), grading status, and instructor feedback. |
| **`certificates`** | `id` | `user_id -> users(id)`, `course_id -> courses(id)` | `1 : 1` unique pair (`user_id`, `course_id`) | Accredited credential records with unique public verification codes. |
| **`instructor_wallets`** | `instructor_id` | `instructor_id -> users(id)` | `1 : 1` with `users` (instructors) | Aggregates lifetime earnings, available withdrawal balance, and disbursed totals. |
| **`payout_requests`** | `id` | `instructor_id -> users(id)`, `processed_by -> users(id)` | `N : 1` with `users` (instructors), `N : 1` with `users` (admin) | Manages cash-out requests, transfer methods, receipt references, and approval state. |
| **`ad_activity_logs`** | `id` | `course_id -> courses(id)`, `lesson_id -> lessons(id)`, `student_id -> users(id)`, `instructor_id -> users(id)` | Audit trail connecting students, instructors, and lessons | Immutable log of completed sponsor ad views used to credit instructor balances. |

---

### Detailed Table Specifications

#### 1. `roles` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Role ID (`1 = admin`, `2 = instructor`, `3 = student`) |
| `name` | `VARCHAR(50)` | `NOT NULL, UNIQUE` | Unique role identifier name (`admin`, `instructor`, `student`) |
| `description`| `VARCHAR(255)` | `NULL` | Human-readable role description |

#### 2. `users` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Unique User ID |
| `full_name` | `VARCHAR(100)` | `NOT NULL` | User full legal name |
| `email` | `VARCHAR(150)` | `NOT NULL, UNIQUE` | User login email address |
| `password` | `VARCHAR(255)` | `NOT NULL` | Bcrypt hashed password (`PASSWORD_DEFAULT`) |
| `role_id` | `INT` | `NOT NULL, DEFAULT 3, FK` | References `roles(id)` ON DELETE RESTRICT ON UPDATE CASCADE |
| `created_at`| `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Account registration timestamp |

#### 3. `courses` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Unique Course ID |
| `title` | `VARCHAR(150)` | `NOT NULL` | Course Title |
| `description`| `TEXT` | `NULL` | Detailed course overview and syllabus narrative |
| `category` | `VARCHAR(100)` | `NULL` | Topic category (e.g. Web Development, Cloud Computing) |
| `thumbnail`| `VARCHAR(255)` | `NULL` | Path to cover thumbnail image |
| `total_lessons` | `INT` | `DEFAULT 10` | Total lessons count published in curriculum |
| `instructor_id` | `INT` | `NULL, FK` | References `users(id)` ON DELETE SET NULL ON UPDATE CASCADE |
| `assessment_type` | `ENUM` | `'github_repo', 'file_upload', 'live_url'` | Required format for final deliverable |
| `assessment_instructions` | `TEXT` | `NULL` | Guidelines and rubric for final project grading |
| `created_at`| `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Course publication timestamp |

#### 4. `lessons` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Unique Lesson ID |
| `course_id` | `INT` | `NOT NULL, FK` | References `courses(id)` ON DELETE CASCADE |
| `lesson_number` | `INT` | `NOT NULL` | Sequential position within course curriculum (`1`, `2`, `3`...) |
| `title` | `VARCHAR(150)` | `NOT NULL` | Module / Lesson Title |
| `video_path` | `VARCHAR(255)` | `NOT NULL` | Relative path to uploaded MP4 video file |
| `duration` | `VARCHAR(20)` | `DEFAULT '10:00'` | Detected video duration (e.g. `12:45`) |
| `created_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Lesson record creation timestamp |

#### 5. `enrollments` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Enrollment ID |
| `user_id` | `INT` | `NOT NULL, FK` | References `users(id)` ON DELETE CASCADE |
| `course_id` | `INT` | `NOT NULL, FK` | References `courses(id)` ON DELETE CASCADE |
| `progress_percent` | `INT` | `DEFAULT 0` | Completion percentage (0 - 100) |
| `status` | `ENUM` | `'in_progress', 'completed'` | Current study state |
| `enrolled_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Initial enrollment timestamp |
| `completed_at` | `TIMESTAMP` | `NULL` | Date when final project was approved |
| *Index* | `UNIQUE` | `uq_user_course (user_id, course_id)` | Prevents duplicate student enrollments |

#### 6. `lesson_completions` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Completion record ID |
| `user_id` | `INT` | `NOT NULL, FK` | References `users(id)` ON DELETE CASCADE |
| `course_id` | `INT` | `NOT NULL, FK` | References `courses(id)` ON DELETE CASCADE |
| `lesson_id` | `INT` | `NOT NULL, FK` | References `lessons(id)` ON DELETE CASCADE |
| `completed_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Timestamp when video playback finished |
| *Index* | `UNIQUE` | `uq_user_lesson (user_id, lesson_id)` | Ensures a lesson is completed once per student |

#### 7. `course_submissions` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Submission record ID |
| `user_id` | `INT` | `NOT NULL, FK` | References `users(id)` ON DELETE CASCADE |
| `course_id` | `INT` | `NOT NULL, FK` | References `courses(id)` ON DELETE CASCADE |
| `submission_type` | `VARCHAR(50)` | `NOT NULL` | Deliverable format (`github_repo`, `file_upload`, `live_url`) |
| `submission_value`| `VARCHAR(255)` | `NOT NULL` | Repository URL, live website link, or saved file name |
| `notes` | `TEXT` | `NULL` | Student commentary and project overview |
| `instructor_feedback` | `TEXT` | `NULL` | Review comments and revision guidance from instructor |
| `status` | `ENUM` | `'pending', 'approved', 'revision_needed', 'rejected'` | Manual grading status |
| `reviewed_at` | `TIMESTAMP` | `NULL` | Date when instructor evaluated deliverable |
| `submitted_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Submission timestamp |

#### 8. `certificates` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Certificate ID |
| `user_id` | `INT` | `NOT NULL, FK` | References `users(id)` ON DELETE CASCADE |
| `course_id` | `INT` | `NOT NULL, FK` | References `courses(id)` ON DELETE CASCADE |
| `certificate_code` | `VARCHAR(50)` | `NOT NULL, UNIQUE` | Verification code (e.g. `ADS-2026-ABCD1234`) |
| `issued_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Timestamp of instructor project approval |
| *Index* | `UNIQUE` | `uq_user_cert_course (user_id, course_id)` | One certificate per student per course |

#### 9. `instructor_wallets` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `instructor_id` | `INT` | `PRIMARY KEY, FK` | References `users(id)` ON DELETE CASCADE |
| `total_earned` | `DECIMAL(10,2)`| `NOT NULL, DEFAULT 0.00` | Lifetime cumulative ad revenue earned |
| `available_balance` | `DECIMAL(10,2)`| `NOT NULL, DEFAULT 0.00` | Current funds available for withdrawal |
| `total_withdrawn` | `DECIMAL(10,2)`| `NOT NULL, DEFAULT 0.00` | Total funds successfully disbursed by admin |
| `updated_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` | Last balance modification timestamp |

#### 10. `payout_requests` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Cash-out request ID |
| `instructor_id` | `INT` | `NOT NULL, FK` | References `users(id)` ON DELETE CASCADE |
| `amount` | `DECIMAL(10,2)`| `NOT NULL` | Requested withdrawal amount (minimum $5.00) |
| `payout_method` | `ENUM` | `'paypal', 'gcash', 'bank_transfer'` | Selected transfer channel |
| `payout_details`| `TEXT` | `NOT NULL` | JSON encoded destination account details |
| `instructor_notes`| `TEXT` | `NULL` | Optional comments submitted by instructor |
| `admin_notes` | `TEXT` | `NULL` | Admin disbursement notes or rejection reasoning |
| `transaction_reference` | `VARCHAR(100)` | `NULL` | Bank trace, GCash reference, or PayPal transaction ID |
| `status` | `ENUM` | `'pending', 'completed', 'rejected'` | Processing status |
| `processed_by` | `INT` | `NULL, FK` | References `users(id)` ON DELETE SET NULL |
| `created_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Request timestamp |
| `processed_at` | `TIMESTAMP` | `NULL` | Timestamp when reviewed by administrator |

#### 11. `ad_activity_logs` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Ad impression log ID |
| `course_id` | `INT` | `NOT NULL, FK` | References `courses(id)` ON DELETE CASCADE |
| `lesson_id` | `INT` | `NOT NULL, FK` | References `lessons(id)` ON DELETE CASCADE |
| `student_id` | `INT` | `NOT NULL, FK` | References `users(id)` ON DELETE CASCADE |
| `instructor_id`| `INT` | `NOT NULL, FK` | References `users(id)` ON DELETE CASCADE |
| `amount_earned`| `DECIMAL(10,4)`| `NOT NULL, DEFAULT 0.0500`| Revenue share credited to instructor ($0.05) |
| `ad_duration_seconds` | `INT` | `NOT NULL, DEFAULT 15` | Required watch time in seconds |
| `created_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Timestamp of completed ad impression |

---

## 5. Core Platform Workflows

### A. Authentication & Session Routing Flow

1. **Request Submission:** User submits email and password at `login.php`.
2. **Controller Guard:** `login_function.php` ensures the request originated via POST and executes `validateLoginInput()`.
3. **Database Query:** Prepared PDO statement queries `users` joined with `roles` where `email = :email`.
4. **Password Verification:** PHP's `password_verify()` checks the submitted plaintext password against the Bcrypt database hash.
5. **Memory Purge:** Raw input password and database hash strings are immediately unset from memory.
6. **Session Assignment:** User identity, email, `role_id`, and `role_name` are populated into `$_SESSION`.
7. **RBAC Redirection Matrix:**
   - If `role_name === 'admin'` &rarr; Redirects to `admin/dashboard.php`.
   - If `role_name === 'instructor'` &rarr; Redirects to `instructor/dashboard.php`.
   - If `role_name === 'student'`:
     - If `redirect_course` parameter is present &rarr; Redirects to `course_details.php?id={redirect_course}`.
     - Otherwise &rarr; Redirects to `student/dashboard.php`.
   - Default fallback &rarr; Redirects to `index.php`.

---

### B. Student Registration & Dashboard Experience

1. Visitor registers at `signup.php` (optionally retaining `redirect_course`).
2. `signup_function.php` enforces the 5-tier password policy via `validateSignupInput()`, hashes the password with `PASSWORD_DEFAULT`, and inserts the record into `users` with `role_id = 3`.
3. The student is automatically authenticated into the session and routed to `student/dashboard.php`.
4. The dashboard provides a 4-tab interface:
   - **Overview Tab:** Quick metrics (In Progress, Completed, Certificates), welcome banner, and recent course cards with "Course Details" and conditional "Submit Project" buttons.
   - **In Progress Tab:** Enrolled active courses, completion percentages, lesson counters, and quick links to course details.
   - **Completed Tab:** Courses where deliverables have been approved by instructors, showing completion dates and "View Official Certificate" links.
   - **My Certificates Tab:** Showcase of earned certificates with verification codes, issue dates, and direct links to `student/certificate.php`.

---

### C. 4-Step Course Creation Wizard (`instructor/create_course.php`)

Instructors publish structured courses using a guided 4-step wizard:

```
[Step 1: Course Info & Cover] ──▶ [Step 2: Sequential Curriculum] ──▶ [Step 3: Deliverable & Rubric] ──▶ [Step 4: Live Catalog Preview]
```

1. **Step 1: Course Overview & Thumbnail Upload:**
   - Instructor inputs course title, category, and description.
   - Dropzone accepts JPG, PNG, or WEBP covers with real-time browser preview badges.
2. **Step 2: Sequential Curriculum Builder:**
   - Starts with Lesson 01 (title and video selector).
   - "+ Add Next Lesson" dynamically appends subsequent lessons with individual remove buttons.
   - Client-side HTML5 Video API reads metadata (`URL.createObjectURL`) to detect exact durations and calculate estimated sponsor breaks.
   - On submission, `instructor/create_course_function.php` executes server-side `ffprobe` to verify video duration.
3. **Step 3: Final Deliverable & Assessment Format:**
   - Selects output deliverable format:
     - GitHub Repository (`github_repo`)
     - Project File Upload (`file_upload`)
     - Live Application URL (`live_url`)
   - Instructor specifies detailed submission instructions and evaluation rubrics.
4. **Step 4: Student Perspective Preview & Pre-Flight:**
   - Displays a live catalog mock card, full lesson timeline with 15s ad break tags, and pre-flight publication checks before publishing.

---

### D. Filesystem Storage Hierarchy & File Naming Syntax

When an instructor publishes a course, `instructor/create_course_function.php` creates an isolated directory structure partitioned by instructor ID and course ID:

```
uploads/instructors/{instructor_id}/{course_id}/
├── thumbnail/
│   └── thumbnail_{timestamp}.{ext}
├── lesson_1_{timestamp}.mp4
├── lesson_2_{timestamp}.mp4
└── lesson_3_{timestamp}.mp4
```

#### Naming Syntax Rationale:
- **`lesson_{n}_{timestamp}.mp4`**:
  - **Collision Prevention:** Eliminates accidental file overwriting upon re-uploads.
  - **Cache Invalidation:** Prevents browsers from loading outdated cached videos.
  - **Filesystem Sanitization:** Strips unsafe characters and spaces.

---

### E. Course Catalog & Exploration (`courses.php`)

1. Accessible from navigation or search triggers.
2. `courses_function.php` queries active courses with parameterized SQL filters (`?search=...` and `?category=...`).
3. For logged-in students, queries `enrollments` to determine dynamic card states:
   - Already enrolled: Shows green **"In Progress (View)"** button.
   - Not enrolled: Shows blue **"View Course & Enroll"** button.
4. Clicking any course card opens `course_details.php`.

---

### F. Course Overview & Student Enrollment Flow

1. **Course Overview (`course_details.php`):**
   - Displays full curriculum with lesson timeline, duration badges, and sponsor tags.
   - Details required final deliverable format and assessment guidelines.
   - Displays sticky enrollment action card:
     - Unenrolled student: Prominent **"Enroll in Course"** button.
     - Enrolled student: Displays **"You are enrolled! (X% completed)"** banner with direct **"Start Lesson"** button pointing to `student/learn.php`.
     - Guest: **"Log In to Enroll"** button preserving `redirect_course` parameter.
2. **Enrollment Backend Processor (`student/enroll_function.php`):**
   - Verifies student authentication.
   - Inserts record into `enrollments` (`user_id`, `course_id`, `progress_percent = 0`, `status = 'in_progress'`) with `ON DUPLICATE KEY UPDATE`.
   - Redirects to `course_details.php?id={course_id}&status=success&message=Successfully+enrolled!` so the learner can immediately click **"Start Lesson"**.

---

### G. Interactive Classroom & Ad Monetization Engine

The core learning and monetization loop is implemented across `student/learn.php`, `student/complete_lesson.php`, and `student/record_ad_activity.php`:

1. **Pre-Roll Sponsor Ad Session:**
   - Before a video lesson starts, the classroom automatically plays a mandatory 15-second sponsor video ad (`assets/ad/sample_ad.mp4`).
   - Displays a wellness notification banner: *"Take a Quick Sip of Water & Stretch! Our sponsor keeps this verified course 100% free."*
   - Includes big-play fallback overlay for mobile browsers blocking unmuted autoplay.
2. **Ad Monetization & Instructor Credit (`student/record_ad_activity.php`):**
   - Upon completion of the 15-second ad, an AJAX call is dispatched to `student/record_ad_activity.php`.
   - **Anti-Spam Cooldown:** Checks `ad_activity_logs` to ensure the student has not logged an ad impression for the same lesson within the last 5 minutes.
   - **Self-Preview Guard:** If the course instructor is viewing their own course, ad views are logged but no funds are credited.
   - **Wallet Credit:** Within a database transaction, inserts an audit record into `ad_activity_logs` ($0.0500) and increments `instructor_wallets` (`total_earned` and `available_balance` by $0.05).
3. **Lesson Playback & Syllabus Navigation:**
   - Once the ad session finishes, the ad container hides, and the actual lesson video player mounts and begins playback.
   - Students can view their overall progress percentage in the sticky top header and select any unlocked lesson from the curriculum sidebar.
4. **Lesson Completion Engine (`student/complete_lesson.php`):**
   - When a lesson video finishes playing, an AJAX request posts to `student/complete_lesson.php`.
   - Inserts or updates a record in `lesson_completions`.
   - Recalculates course progress: `(completed_lessons / total_lessons) * 100`.
   - Updates `enrollments.progress_percent`.
   - Locates the next uncompleted lesson in sequential order.
   - Displays a completion modal allowing instant progression to the next lesson (which begins with its own sponsor break).
   - When 100% progress is reached, displays the **"Submit Final Project"** trigger in the navigation bar.

---

### H. Student Final Project Submission Flow

1. **Completion Gate (`student/submit_exam.php` & `student/submit_exam_function.php`):**
   - Access is restricted until `progress_percent >= 100` or `status = 'completed'`.
   - If progress is incomplete, the form is replaced by a lock notice displaying remaining progress and a "Resume Course Lessons" link.
2. **Deliverable Submission:**
   - Depending on course `assessment_type`, accepts a GitHub URL, live application URL, or file archive upload (ZIP, RAR, PDF, TAR, GZ, JPG, PNG; saved into `assets/submissions/sub_{studentId}_{courseId}_{timestamp}.{ext}`).
   - Sets submission record in `course_submissions` with `status = 'pending'`.
   - Informs the student that their deliverable has been submitted for manual instructor review.

---

### I. Instructor Review, Grading & Certificate Issuance

Located in `instructor/review_submission_function.php`:

1. **Submission Review Queue:** Instructors inspect student submissions directly from the "Student Submissions" tab on `instructor/dashboard.php` or from `instructor/course_overview.php`.
2. **Action: Approve Project:**
   - Sets `course_submissions.status = 'approved'` with optional instructor commentary.
   - Updates student enrollment: `status = 'completed'`, `progress_percent = 100`, `completed_at = CURRENT_TIMESTAMP`.
   - Generates unique certificate code: `ADS-{YEAR}-{HEX}` (e.g. `ADS-2026-B81A4C9F`).
   - Inserts record into `certificates` table.
3. **Action: Request Revision:**
   - Sets `course_submissions.status = 'revision_needed'` with mandatory feedback.
   - Sets `enrollments.status = 'in_progress'` and `completed_at = NULL`.
   - Revokes any previously issued certificate via `DELETE FROM certificates`.
   - Student can resubmit their revised project from `student/submit_exam.php`.

---

### J. Instructor Course Overview & Analytics (`instructor/course_overview.php`)

Provides instructors with granular telemetry for any published course:
- **Metrics Summary:** Total enrolled students, certificates issued, cumulative course ad revenue, and total ad impressions.
- **Student Roster:** Complete table of all enrolled learners showing individual progress percentages, enrollment timestamps, completed lesson counts, submission statuses, and direct links to verify deliverables.
- **Curriculum Telemetry:** Breakdown of completion counts per individual lesson.

---

### K. Instructor Payout / Cash-Out System

Located in `instructor/request_payout_function.php`:

1. **Eligibility & Threshold:** Instructor must have an `available_balance >= $5.00`.
2. **Transfer Method Validation:**
   - **PayPal:** Enforces RFC email format validation.
   - **GCash:** Validates account name and Philippine 11-digit mobile format (`/^(09|\+639)\d{9}$/`).
   - **Bank Transfer:** Requires Bank Name, Account Name, and Account Number.
3. **Atomic Balance Lock:** Executes a database transaction with `FOR UPDATE` row-locking on `instructor_wallets`. Deducts the requested withdrawal amount from `available_balance` and inserts a new record into `payout_requests` with `status = 'pending'`.

---

### L. Administrator Cash-Out Review & Disbursement

Located in `admin/dashboard.php` and `admin/process_payout_function.php`:

1. **Admin Control Center:** Dashboard displays pending payout count, total pending payout value, and cumulative disbursed funds.
2. **Review Dialog:** Admin inspects request details, instructor identity, and destination account.
3. **Approval Flow:**
   - Sets `payout_requests.status = 'completed'`, records admin ID, timestamp, and optional transaction reference (bank trace, PayPal ID, GCash ref).
   - Increments `instructor_wallets.total_withdrawn` by the disbursed amount.
4. **Rejection & Auto-Refund Flow:**
   - Admin provides mandatory reason for rejection in `admin_notes`.
   - Sets `payout_requests.status = 'rejected'`.
   - Automatically refunds the locked amount back to `instructor_wallets.available_balance`.

---

### M. Global Logout Confirmation Modal (`assets/js/logout_modal.js`)

- Included globally across the platform.
- Intercepts all clicks on links pointing to `logout.php`.
- Renders an animated glassmorphic modal requesting user confirmation before session termination.
- Supports keyboard navigation (`Escape` to cancel) and backdrop click dismissal.

---

## 6. Input Validation Architecture

Located in [`validation.php`](file:///home/ugenella/coding/xampp-projects/adsity/validation.php):

- `validateRequired(string $value, string $label): ?string`: Checks that trimmed input is non-empty.
- `validateEmailFormat(string $value): ?string`: Validates structure via `FILTER_VALIDATE_EMAIL`.
- `validateMinLength(string $value, string $label, int $min): ?string`: Enforces minimum length constraint.
- `validatePassword(string $password, int $minLength = 8): ?string`: Multi-criteria password validator enforcing:
  1. Minimum 8 characters in length.
  2. At least one uppercase letter (`[A-Z]`).
  3. At least one lowercase letter (`[a-z]`).
  4. At least one numeric digit (`[0-9]`).
  5. At least one special character (`[!@#$%^&*()\-_=+{};:,<.>]`).
- `validatePasswordMatch(string $password, string $confirmPassword): ?string`: Verifies matching confirmation password.
- `validateTerms(bool $termsAccepted): ?string`: Verifies agreement to platform Terms of Service.
- `validateSignupInput(array $post): array`: Aggregates validation for student accounts (utilizes `validatePassword`).
- `validateTeacherSignupInput(array $post): array`: Aggregates validation for instructor accounts (`role_id = 2`).
- `validateLoginInput(array $post): array`: Validates email format and password presence for authentication.

---

## 7. Setup & Local Testing Guide

### Prerequisites
- XAMPP / LAMPP installed on Linux.
- Apache web server running on Port `81`.
- MariaDB database service running on Port `3307` (or socket `/opt/lampp/var/mysql/mysql.sock`).
- FFmpeg (`ffprobe`) installed in system path for video duration detection.

### Database Initialization
Import the complete 11-table schema and default seeds:
```bash
/opt/lampp/bin/mysql -u root -S /opt/lampp/var/mysql/mysql.sock < database/schema.sql
```

### Pre-configured Administrator Account

| Role | Email | Password | Target Dashboard |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@adsity.org` | `admin123` | `http://localhost:81/projects/adsity/admin/dashboard.php` |

> [!NOTE]
> Instructors can be registered at `teach.php` and Students at `signup.php`. All newly generated passwords are encrypted with `PASSWORD_DEFAULT` (Bcrypt).

---

## 8. Summary of Vector Assets

All 30 vector icons are stored inside `assets/icons/`:

| Icon File | Usage & Description |
| :--- | :--- |
| `alert-circle.svg` | Error and warning alert banners |
| `arrow-left.svg` | Return navigation, back to catalog/home buttons |
| `arrow-right.svg` | Submit triggers, forward progression buttons |
| `award.svg` | Verified credentials, certificates, deliverables |
| `book-open.svg` | Course catalog, lesson syllabus, learning indicators |
| `box.svg` | Docker container technology badge |
| `check-circle.svg` | Success alerts, completed course indicators |
| `clock.svg` | In-progress time, lesson duration indicators |
| `cloud.svg` | Cloud computing & AWS technology badge |
| `code.svg` | Programming languages (Python, JS, React) badge |
| `dollar-sign.svg` | Ad revenue, instructor wallet, cash-out metrics |
| `download.svg` | Download and print certificate actions |
| `external-link.svg`| External platform links and homepage navigation |
| `eye.svg` | View credential and details action buttons |
| `file-text.svg` | File upload deliverable indicator |
| `github.svg` | GitHub repository deliverable badge |
| `globe.svg` | Language and localization selector |
| `graduation-cap.svg`| Instructor badges and teacher portal identifiers |
| `layout.svg` | UI/UX design skill badge, dashboard overview icon |
| `link.svg` | Blockchain technology badge, live URL deliverable icon |
| `lock.svg` | Locked final project deliverable indicator |
| `log-out.svg` | Global logout confirmation dialog badge |
| `play.svg` | Video lesson player, resume learning button |
| `plus.svg` | Publish new course, add lesson syllabus builder |
| `shield-check.svg` | 100% free ad-supported verification badge |
| `shield.svg` | Cybersecurity technology badge |
| `trash.svg` | Delete user and delete course action buttons |
| `user-check.svg` | Student KPI indicators in Admin dashboard |
| `users.svg` | Total users KPI card in Admin dashboard |
| `video.svg` | Curriculum module and video indicator |
