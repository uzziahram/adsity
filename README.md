# Adsity - Technical & Functional Documentation

**Version:** 1.2.0  
**Environment:** Linux / LAMPP (XAMPP for Linux)  
**Database:** MariaDB 10.4 (Port 3307)  
**Web Server:** Apache (Port 81)  
**URL Base:** `http://localhost:81/projects/adsity/`

---

## 1. Project Overview

**Adsity** is a web-based educational platform providing free, ad-supported technology courses and verified industry certificates. The system operates on a sustainable monetization model where students watch short 15-second sponsor advertisement breaks before lessons in exchange for zero-cost education and accredited credentialing, while instructors earn a direct, configurable revenue share from every completed sponsor ad session.

### Core User Roles & Responsibilities:
- **Student (`role_id = 3`):** Explores approved courses in the public catalog, enrolls in courses, participates in the interactive video classroom, views pre-roll sponsor advertisements with active partner information, completes lessons sequentially, submits final project deliverables, and views/prints accredited completion certificates.
- **Instructor / Teacher (`role_id = 2`):** Registers via dedicated onboarding, builds multi-video courses with custom assessment rubrics, monitors student rosters and completion telemetry, grades submitted final projects, tracks course publication status (`Live`, `In Review`, `Revisions Requested`), earns ad revenue based on platform revenue-share percentages, and requests withdrawals via PayPal, GCash, or Bank Transfer.
- **Administrator (`role_id = 1`):** System superuser who oversees platform health, moderation, monetization, and compliance:
  1. **Course Moderation Queue:** Reviews newly submitted instructor courses, approves them to the public catalog, or rejects them with revision guidance.
  2. **Sponsor Ad Engine:** Manages sponsor ad campaigns, uploads video files via native operating system file manager or custom URLs, and configures platform monetization economics (CPM rates, instructor revenue-share %, and ad cooldown intervals).
  3. **Certificate Anti-Fraud Registry:** Audits issued certificates, revokes invalid or fraudulent credentials with documented justification, renders public invalidation watermarks and red audit banners, and restores revoked certificates if appealed.
  4. **Instructor Payout Review:** Inspects cash-out requests, disburses funds with transaction trace receipts, or rejects invalid requests with automated balance refunds.
  5. **User Administration & RBAC:** Inspects user profiles, promotes students to instructors (with automated wallet provisioning) or demotes instructors, and safely deletes inactive accounts.
  6. **Data Export Engine:** Generates instant CSV audit reports for users, courses, payouts, certificates, ad logs, and sponsor campaigns.

---

## 2. Dedicated End-to-End Website Process

The following 10-phase operational lifecycle outlines the complete workflow of the Adsity platform across all user roles, from onboarding and curriculum ingestion to video learning, ad monetization, project evaluation, credential verification, and financial disbursements.

### Phase 1: Onboarding & Account Provisioning
1. **Student Registration (`signup.php`):** Learners register with their name, email, and a secure 5-tier password. Upon registration, their account is provisioned with `role_id = 3` (`student`), authenticated into `$_SESSION`, and directed to the student dashboard or their selected course.
2. **Instructor Onboarding (`teach.php`):** Educators apply with professional credentials, background, and teaching category. An instructor account is provisioned (`role_id = 2`) along with an initialized wallet record in `instructor_wallets` ($0.00 balance).
3. **Administrator Access (`login.php`):** Platform superusers authenticate using root credentials (`role_id = 1`) and access the central Admin Control Center (`admin/dashboard.php`).

### Phase 2: Course Authoring & Curriculum Upload
1. **Multi-Step Wizard (`instructor/create_course.php`):** Instructors author courses via a guided 4-step wizard:
   - **Step 1 (Overview):** Specify title, category, syllabus description, and upload a cover thumbnail to `uploads/instructors/{id}/{course_id}/thumbnail/`.
   - **Step 2 (Curriculum):** Upload sequential MP4 lesson modules. Durations are automatically detected client-side via HTML5 Video API and validated server-side using CLI `ffprobe`.
   - **Step 3 (Assessment Rubric):** Define the required final project format (GitHub repository, live website URL, or project archive file upload) and provide grading rubrics.
   - **Step 4 (Pre-Flight Preview):** Review catalog card mockups, syllabus layout, and submit the curriculum for review.
2. **Moderation Queue Ingestion:** Newly submitted courses are saved with `status = 'pending_review'` and remain hidden from the public course catalog until administrative approval.

### Phase 3: Administrative Course Moderation & Catalog Publication
1. **Moderation Queue Review (`admin/moderate_course.php`):** Administrators review pending courses in the "Course Moderation" tab of the Admin Control Center.
2. **Approval Path:** If curriculum and video quality meet platform standards, the administrator approves the course (`status = 'published'`). The course instantly appears in the public catalog (`courses.php`) and topic category filters.
3. **Rejection / Revision Path:** If revisions are necessary (e.g. missing audio, incomplete syllabus, unclear rubric), the administrator rejects the course with mandatory feedback notes (`status = 'rejected'`). The instructor receives an immediate alert and feedback note in Instructor Studio to make required adjustments.

### Phase 4: Sponsor Ad Campaign Ingestion & Monetization Setup
1. **Campaign Creation (`admin/manage_ads.php`):** Administrators launch campaigns via the "Sponsor Ad Engine" tab:
   - **Native File Manager Upload:** The administrator clicks the dropzone to open their operating system's native file chooser (or drags-and-drops a video file). Supported formats include MP4, WebM, and MOV up to 100MB. Files are saved securely in `uploads/ads/` with sanitized, unique names (`ad_{timestamp}_{uniqid}.{ext}`).
   - **Custom/Fallback Paths:** Admins can toggle custom paths to use bundled sample ads (`assets/ad/sample_ad.mp4`) or external video URLs.
   - **Campaign Settings:** Admin configures sponsor brand name, marketing headline, destination click URL, custom CPM rate, and initial serving status (`active` or `paused`).
2. **Platform Monetization Economics (`platform_settings`):** Administrators configure global financial parameters:
   - Default Ad CPM Rate ($ per completed 15-second impression)
   - Instructor Revenue Share Split % (e.g. 70% to instructor / 30% to platform)
   - Anti-Spam Ad Cooldown Interval (minutes required between billable views per student per lesson)

### Phase 5: Student Discovery, Enrollment & Interactive Learning
1. **Catalog Exploration (`courses.php`):** Students browse published courses with search and topic filters. Cards dynamically show "View Course & Enroll" or "In Progress (View)" based on enrollment status.
2. **Enrollment (`course_details.php` & `student/enroll_function.php`):** Clicking "Enroll in Course" creates an enrollment record (`progress_percent = 0`, `status = 'in_progress'`) and routes the student directly into the video classroom.
3. **Interactive Classroom (`student/learn.php`):**
   - **Mandatory 15-Second Pre-Roll:** Before any lesson video begins, an active sponsor video plays with a real-time countdown timer.
   - **Branded Sponsor Overlay:** Displays "Sponsored by {Brand}" with a clickable "Learn More &rarr;" link to the sponsor's landing page.
   - **Curriculum Navigation:** Students navigate unlocked lessons sequentially while monitoring their course completion percentage.

### Phase 6: Impression Verification & Automated Revenue Allocation
1. **Ad Telemetry & Verification (`student/record_ad_activity.php`):** When the 15-second ad completes, an automated AJAX call dispatches impression data:
   - **Anti-Spam Verification:** Validates that the student has not recorded an impression for the same lesson within the configured cooldown window (e.g. 5 minutes).
   - **Self-Preview Guard:** Prevents instructors from generating ad revenue while previewing their own courses.
   - **Atomic Revenue Credit:** Calculates earnings based on configured CPM and revenue share %, logs the audit record to `ad_activity_logs`, increments `sponsor_ads.total_impressions`, and deposits earnings into `instructor_wallets` (`total_earned` and `available_balance`) within an ACID database transaction.
2. **Lesson Completion Engine (`student/complete_lesson.php`):** When the lesson video concludes, completion is logged to `lesson_completions`, course progress percentage updates, and the learner progresses to the next module.

### Phase 7: Final Assessment & Project Evaluation
1. **Assessment Gate (`student/submit_exam.php`):** Deliverable submission unlocks exclusively when course progress reaches 100%.
2. **Deliverable Submission (`student/submit_exam_function.php`):** The student submits their deliverable (GitHub repo URL, live website link, or uploaded project file archive saved into `assets/submissions/`) with project notes. Submission status is set to `pending`.
3. **Instructor Evaluation (`instructor/review_submission_function.php`):**
   - **Approval:** Instructor evaluates the deliverable, enters commentary, and marks it `approved`. Enrollment status is set to `completed`, and a unique credential code (`ADS-{YEAR}-{HEX}`) is generated in `certificates` with `status = 'valid'`.
   - **Revision Request:** If criteria are unmet, instructor marks `revision_needed` with actionable feedback. The student is notified and can revise and resubmit their work.

### Phase 8: Credential Issuance & Anti-Fraud Verification
1. **Certificate Presentation (`student/certificate.php`):** Students view and print accredited completion certificates displaying their legal name, course title, completion date, and unique verification ID.
2. **Anti-Fraud Registry & Revocation (`admin/revoke_certificate.php`):**
   - Administrators audit credentials via the "Certificates & Logs" tab in the Admin Control Center.
   - If academic dishonesty or deliverable plagiarism is detected, the administrator revokes the credential with documented audit reasoning.
   - When viewed publicly, a revoked certificate displays a prominent red audit warning banner, invalidation reason, and a diagonal "REVOKED" watermark.
   - Administrators can restore revoked certificates if an appeal is resolved.
3. **Intelligent Return Routing:** The certificate return button detects the viewer's active session role (Admin, Instructor, Student) to avoid role collisions and prevent inadvertent administrative logouts.

### Phase 9: Instructor Cash-Out & Administrative Disbursement
1. **Payout Request (`instructor/request_payout_function.php`):** Instructors with an `available_balance >= $5.00` submit cash-out requests via PayPal, GCash, or Bank Transfer. A database transaction locks the balance and sets `payout_requests.status = 'pending'`.
2. **Administrative Disbursement (`admin/process_payout_function.php`):**
   - **Disbursement:** The administrator reviews destination details, disburses funds through the chosen financial channel, enters a trace reference code (e.g. bank trace, PayPal transaction ID, GCash ref), and marks the request `completed`. Total withdrawn funds are updated.
   - **Rejection with Refund:** If payout details are invalid, the administrator rejects the request with mandatory explanatory notes. Locked funds are automatically refunded back to the instructor's `available_balance`.

### Phase 10: Platform Governance, RBAC & Audit Export
1. **User Role Management (`admin/change_role_function.php`):** Administrators can inspect any student or teacher profile, promote students to instructors (automatically provisioning wallet accounts), or demote instructors. Self-demotion by the root admin is strictly prevented.
2. **Platform Data Export (`admin/export_data.php`):** Administrators can download real-time CSV reports (Users, Courses, Payouts, Certificates, Ad Logs, Sponsor Campaigns) with UTF-8 BOM encoding for direct opening in Microsoft Excel and spreadsheet software.
3. **Safe Session Termination (`assets/js/logout_modal.js`):** Global glassmorphic logout confirmation modal prevents accidental session terminations across all user roles.

---

## 3. Technology Stack

| Layer | Technology | Details |
| :--- | :--- | :--- |
| **Backend** | PHP 8.x | Native procedural PHP with PDO database abstraction, prepared statements, and transactional rollbacks |
| **Database** | MariaDB 10.4.32 | Relational schema with 13 tables, foreign keys, cascading constraints, and unique indices |
| **Session & Auth** | PHP Sessions (`$_SESSION`) | Role-based access control (RBAC), session gating, and credential memory purging |
| **Media Processing** | FFmpeg (`ffprobe`) | Server-side CLI execution to detect video durations from uploaded lesson MP4s |
| **File Upload Handling** | Native PHP Uploads | Secure file uploads for sponsor videos (MP4, WebM, MOV up to 100MB), thumbnails, and student project deliverables |
| **Frontend** | HTML5 / CSS3 / Vanilla JS | Modular stylesheets (`style.css`, `admindashboard.css`), Raleway & Cinzel typography, HTML5 Video API |
| **UI Components** | Custom JavaScript | Native File Manager dropzone picker, glassmorphic modal system, ad player state machine |
| **Reporting & Export** | Native PHP Stream | UTF-8 BOM-encoded CSV export streams for spreadsheet and Excel compatibility |
| **Assets** | Standalone SVG Vectors | 30 bespoke icons stored in `assets/icons/` and rendered via standard `<img>` and inline SVG |

---

## 4. Directory & File Structure

```
adsity/
├── admin/
│   ├── admindashboard.css           # Comprehensive stylesheet for Admin Control Center, sidebar, and modals
│   ├── change_role_function.php     # RBAC action handler to alter user roles (Student <-> Instructor)
│   ├── dashboard.php                # Admin Control Center (Overview, Courses, Payouts, Ads, Users, Analytics)
│   ├── dashboard_function.php       # Data aggregation, financial analytics, tab routing, and session gate
│   ├── delete_course.php            # Administrative action handler to delete platform courses
│   ├── delete_user.php              # Action handler to safely remove non-admin users
│   ├── export_data.php              # Action handler generating UTF-8 CSV exports for platform reporting
│   ├── manage_ads.php               # Sponsor ads handler (file manager video upload, CPM, rev-share settings)
│   ├── moderate_course.php          # Course moderation queue handler (approvals & rejections with notes)
│   ├── process_payout_function.php  # Payout review handler (approvals, disbursements, refunds & rejections)
│   └── revoke_certificate.php       # Anti-fraud certificate registry handler (revocation & restoration)
│
├── assets/
│   ├── ad/
│   │   └── sample_ad.mp4            # Default fallback 15-second sponsor advertisement video asset
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
│   └── schema.sql                   # Complete SQL schema & table definitions (13 relational tables)
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
│   └── review_submission_function.php # Project grading handler (approvals, revisions & cert issuance)
│
├── student/
│   ├── certificate.css              # Stylesheet for Verified Certificate view and print layout
│   ├── certificate.php              # Verified Certificate view (Print / Save as PDF / Anti-Fraud Watermark)
│   ├── certificate_function.php     # Certificate lookup, verification handler, and role-aware return routing
│   ├── complete_lesson.php          # AJAX endpoint to record lesson completions and recalculate progress %
│   ├── dashboard.php                # Student Dashboard view (In-Progress, Completed, Certificates)
│   ├── dashboard_function.php       # Student data query handler and session gate
│   ├── enroll_function.php          # Student course enrollment processor
│   ├── learn.css                    # Dedicated stylesheet for Interactive Video Classroom & Ad Stage
│   ├── learn.php                    # Video Classroom interface with dynamic sponsor ad breaks & partner overlays
│   ├── record_ad_activity.php       # AJAX endpoint to log ad views, enforce cooldowns, and credit instructor
│   ├── studentdashboard.css         # Stylesheet for Student Dashboard and course progress cards
│   ├── submit_exam.css              # Stylesheet for Final Exam / Project submission view
│   ├── submit_exam.php              # Final project deliverable submission form (locked until 100% progress)
│   └── submit_exam_function.php     # Deliverable upload handler (saves submission in pending status)
│
├── uploads/
│   ├── ads/                         # Upload directory for sponsor campaign videos (ad_{timestamp}_{uniqid}.{ext})
│   └── instructors/                 # Isolated instructor storage partitioned by instructor ID & course ID
│       └── {instructor_id}/
│           └── {course_id}/
│               ├── thumbnail/       # Course cover thumbnail (thumbnail_{timestamp}.ext)
│               └── lesson_{n}_{timestamp}.mp4 # Uploaded lesson MP4 video files
│
├── course_details.css               # Stylesheet for Course Overview & Syllabus page
├── course_details.php               # Course Overview page (Thumbnail, Syllabus, Final Output, Sticky CTA)
├── courses.css                      # Stylesheet for Course Catalog & Category filters
├── courses.php                      # Course Catalog with search, category filtering (published courses only)
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

## 5. Database Schema & Architecture

### Relational Architecture & Entity Matrix

| Entity | Primary Key | Foreign Keys | Cardinality / Relationships | Responsibility |
| :--- | :--- | :--- | :--- | :--- |
| **`roles`** | `id` | None | `1 : N` with `users` | Defines system permissions (`1 = admin`, `2 = instructor`, `3 = student`). |
| **`users`** | `id` | `role_id -> roles(id)` | `1 : N` with `courses`, `enrollments`, `certificates` | Stores credentials, profile data, and assigned security role. |
| **`courses`** | `id` | `instructor_id -> users(id)`, `reviewed_by -> users(id)` | `1 : N` with `lessons`, `enrollments`, `course_submissions` | Holds course catalog meta, deliverable specifications, and moderation state (`draft`, `pending_review`, `published`, `rejected`). |
| **`lessons`** | `id` | `course_id -> courses(id)` | `1 : N` with `lesson_completions`, `ad_activity_logs` | Represents sequential curriculum video modules and detected durations. |
| **`enrollments`** | `id` | `user_id -> users(id)`, `course_id -> courses(id)` | Many-to-Many bridge (`users` &lt;-&gt; `courses`) | Tracks student course enrollment, progress percent (0-100), and status. |
| **`lesson_completions`** | `id` | `user_id -> users(id)`, `course_id -> courses(id)`, `lesson_id -> lessons(id)` | Many-to-Many bridge (`users` &lt;-&gt; `lessons`) | Granular record of individual completed lessons per student. |
| **`course_submissions`** | `id` | `user_id -> users(id)`, `course_id -> courses(id)` | `N : 1` with `users`, `N : 1` with `courses` | Tracks student project deliverables (URLs/files), grading status, and instructor feedback. |
| **`certificates`** | `id` | `user_id -> users(id)`, `course_id -> courses(id)`, `revoked_by -> users(id)` | `1 : 1` unique pair (`user_id`, `course_id`) | Accredited credential records with verification codes and anti-fraud status (`valid`, `revoked`). |
| **`instructor_wallets`** | `instructor_id` | `instructor_id -> users(id)` | `1 : 1` with `users` (instructors) | Aggregates lifetime earnings, available balance, and disbursed totals. |
| **`payout_requests`** | `id` | `instructor_id -> users(id)`, `processed_by -> users(id)` | `N : 1` with `users` (instructors), `N : 1` with `users` (admin) | Manages cash-out requests, transfer channels, receipt references, and approval state. |
| **`sponsor_ads`** | `id` | None | `1 : N` with `ad_activity_logs` | Manages sponsor video campaigns, uploaded files, click URLs, CPM rates, and active serving status. |
| **`platform_settings`** | `setting_key` | None | Key-value configuration repository | Controls platform-wide monetization rules (default CPM, revenue-share %, ad intervals). |
| **`ad_activity_logs`** | `id` | `course_id -> courses(id)`, `lesson_id -> lessons(id)`, `student_id -> users(id)`, `instructor_id -> users(id)`, `ad_id -> sponsor_ads(id)` | Audit trail connecting students, instructors, lessons, and sponsor ads | Immutable log of completed sponsor ad views used to calculate and credit instructor balances. |

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
| `status` | `ENUM` | `'draft', 'pending_review', 'published', 'rejected'` | Moderation status (Default: `'published'`) |
| `rejection_reason` | `TEXT` | `NULL` | Feedback and revision requirements provided by administrator |
| `reviewed_at` | `TIMESTAMP` | `NULL` | Timestamp of administrative review decision |
| `reviewed_by` | `INT` | `NULL, FK` | References `users(id)` ON DELETE SET NULL ON UPDATE CASCADE |
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
| `issued_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Timestamp of project approval |
| `status` | `ENUM` | `'valid', 'revoked'` | Anti-fraud status (`valid` or `revoked`) |
| `revocation_reason` | `TEXT` | `NULL` | Documented reason for administrative credential revocation |
| `revoked_at` | `TIMESTAMP` | `NULL` | Timestamp of revocation action |
| `revoked_by` | `INT` | `NULL, FK` | References `users(id)` ON DELETE SET NULL |
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

#### 11. `sponsor_ads` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Sponsor ad campaign ID |
| `sponsor_name` | `VARCHAR(150)` | `NOT NULL` | Sponsor brand or enterprise partner name |
| `campaign_title`| `VARCHAR(200)` | `NOT NULL` | Marketing campaign headline / callout |
| `video_url` | `VARCHAR(255)` | `NOT NULL` | Stored video path (`uploads/ads/...` or external URL) |
| `click_url` | `VARCHAR(255)` | `NULL` | Destination website or landing page URL |
| `cpm_rate` | `DECIMAL(10,4)`| `NOT NULL, DEFAULT 0.0500` | Revenue generated per completed view ($) |
| `status` | `ENUM` | `'active', 'paused'` | Ad serving status (Default: `'active'`) |
| `total_impressions` | `INT` | `NOT NULL, DEFAULT 0` | Cumulative completed video ad impressions |
| `created_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Campaign creation timestamp |

#### 12. `platform_settings` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `setting_key` | `VARCHAR(100)` | `PRIMARY KEY` | Unique configuration key (e.g. `default_ad_cpm`, `instructor_rev_share_percent`) |
| `setting_value`| `VARCHAR(255)` | `NOT NULL` | Assigned configuration value |
| `description` | `VARCHAR(255)` | `NULL` | Human-readable setting explanation |
| `updated_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` | Last updated timestamp |

#### 13. `ad_activity_logs` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Ad impression log ID |
| `course_id` | `INT` | `NOT NULL, FK` | References `courses(id)` ON DELETE CASCADE |
| `lesson_id` | `INT` | `NOT NULL, FK` | References `lessons(id)` ON DELETE CASCADE |
| `student_id` | `INT` | `NOT NULL, FK` | References `users(id)` ON DELETE CASCADE |
| `instructor_id`| `INT` | `NOT NULL, FK` | References `users(id)` ON DELETE CASCADE |
| `ad_id` | `INT` | `NULL, FK` | References `sponsor_ads(id)` ON DELETE SET NULL |
| `amount_earned`| `DECIMAL(10,4)`| `NOT NULL, DEFAULT 0.0500`| Revenue share credited to instructor wallet |
| `ad_duration_seconds` | `INT` | `NOT NULL, DEFAULT 15` | Required watch time in seconds |
| `created_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Timestamp of completed ad impression |

---

## 6. Core Platform Workflows

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

### C. 4-Step Course Creation Wizard & Moderation Submission

Instructors publish structured courses using a guided 4-step wizard in `instructor/create_course.php`:

1. **Step 1: Course Info & Cover:** Title, category, description, and thumbnail upload (JPG, PNG, WEBP).
2. **Step 2: Sequential Curriculum Builder:**
   - Adds lessons sequentially with module titles and MP4 video selectors.
   - Client-side HTML5 Video API detects durations and calculates estimated ad breaks.
   - Server-side `instructor/create_course_function.php` runs `ffprobe` to verify video duration.
3. **Step 3: Deliverable & Rubric:** Selects format (`github_repo`, `file_upload`, `live_url`) and inputs grading rubrics.
4. **Step 4: Preview & Submission to Moderation Queue:**
   - Course is inserted with `status = 'pending_review'`.
   - The course does **not** appear in the public catalog until an administrator approves it.
   - In `instructor/dashboard.php`, the course displays a yellow **"Pending Admin Review"** badge.

---

### D. Filesystem Storage Hierarchy & File Naming Syntax

When an instructor publishes a course or an admin uploads an ad video, isolated directories prevent naming collisions:

```
uploads/
├── ads/
│   └── ad_{timestamp}_{uniqid}.{ext}
└── instructors/
    └── {instructor_id}/
        └── {course_id}/
            ├── thumbnail/
            │   └── thumbnail_{timestamp}.{ext}
            ├── lesson_1_{timestamp}.mp4
            ├── lesson_2_{timestamp}.mp4
            └── lesson_3_{timestamp}.mp4
```

#### Naming Syntax Rationale:
- **`ad_{timestamp}_{uniqid}.{ext}` & `lesson_{n}_{timestamp}.mp4`**:
  - **Collision Prevention:** Eliminates accidental file overwriting upon re-uploads.
  - **Cache Invalidation:** Prevents browsers from serving stale cached video files.
  - **Filesystem Sanitization:** Strips unsafe characters, spaces, and path traversal vectors.

---

### E. Course Catalog & Exploration (`courses.php`)

1. Accessible from navigation or search triggers.
2. `courses_function.php` enforces quality control by querying **only** courses with `status = 'published'`. Courses in `pending_review`, `draft`, or `rejected` states are hidden from public listing.
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
   - Inserts record into `enrollments` (`progress_percent = 0`, `status = 'in_progress'`) with `ON DUPLICATE KEY UPDATE`.
   - Redirects to `course_details.php?id={course_id}&status=success&message=Successfully+enrolled!` so the learner can immediately begin learning.

---

### G. Interactive Classroom & Dynamic Ad Monetization Engine

The core learning and monetization loop is implemented across `student/learn.php`, `student/complete_lesson.php`, and `student/record_ad_activity.php`:

1. **Dynamic Pre-Roll Sponsor Selection:**
   - Classroom queries `sponsor_ads` for an `active` campaign (ordered randomly or by weight).
   - If an ad is found, renders the video source from `uploads/ads/...` (or custom URL) and displays an overlay badge: *"Sponsored by {sponsor_name}"* with an optional *"Learn More &rarr;"* link to `click_url`.
   - If no active campaign exists, falls back cleanly to `assets/ad/sample_ad.mp4`.
2. **Mandatory 15-Second Viewing:**
   - Video controls are locked during the sponsor session with a visual countdown timer.
   - Fallback play overlay ensures reliable playback on mobile devices blocking autoplay.
3. **Ad Monetization & Instructor Credit (`student/record_ad_activity.php`):**
   - Dispatches AJAX request upon completion of the ad session.
   - **Configurable Economics:** Reads `platform_settings` for `default_ad_cpm` (e.g. $0.0500), `instructor_rev_share_percent` (e.g. 70%), and `ad_interval_minutes` (e.g. 5 minutes).
   - If the specific campaign defines a custom `cpm_rate`, that rate takes precedence.
   - **Anti-Spam Cooldown:** Checks `ad_activity_logs` to ensure the student has not logged an impression for the same lesson within the configured cooldown interval.
   - **Self-Preview Guard:** Prevents instructors from generating revenue while previewing their own courses.
   - **Atomic Wallet Increment:** Within a database transaction, inserts an audit log into `ad_activity_logs`, increments `sponsor_ads.total_impressions`, and increments `instructor_wallets` (`total_earned` and `available_balance`) by the instructor's revenue share portion.
4. **Lesson Playback & Sequential Completion (`student/complete_lesson.php`):**
   - Ad player dismounts and the actual lesson video starts.
   - On video completion, records completion in `lesson_completions` and recalculates course progress.
   - When 100% progress is reached, enables final project deliverable submission.

---

### H. Student Final Project Submission Flow

1. **Completion Gate (`student/submit_exam.php` & `student/submit_exam_function.php`):**
   - Access is restricted until `progress_percent >= 100` or `status = 'completed'`.
   - Incomplete progress renders a lock notice displaying remaining percentage and resume link.
2. **Deliverable Submission:**
   - Depending on course `assessment_type`, accepts a GitHub URL, live application URL, or file archive upload (ZIP, RAR, PDF, TAR, GZ, JPG, PNG; saved into `assets/submissions/sub_{studentId}_{courseId}_{timestamp}.{ext}`).
   - Sets submission record in `course_submissions` with `status = 'pending'`.
   - Notifies student that deliverable has been submitted for manual instructor grading.

---

### I. Instructor Review, Grading & Certificate Issuance

Located in `instructor/review_submission_function.php`:

1. **Submission Review Queue:** Instructors inspect student submissions from the "Student Submissions" tab on `instructor/dashboard.php`.
2. **Action: Approve Project:**
   - Sets `course_submissions.status = 'approved'` with optional instructor commentary.
   - Updates student enrollment: `status = 'completed'`, `progress_percent = 100`, `completed_at = CURRENT_TIMESTAMP`.
   - Generates unique certificate code: `ADS-{YEAR}-{HEX}` (e.g. `ADS-2026-B81A4C9F`).
   - Inserts record into `certificates` table with `status = 'valid'`.
3. **Action: Request Revision:**
   - Sets `course_submissions.status = 'revision_needed'` with mandatory feedback.
   - Sets `enrollments.status = 'in_progress'` and `completed_at = NULL`.
   - Revokes any previously issued certificate via `DELETE FROM certificates`.
   - Student resubmits their revised project from `student/submit_exam.php`.

---

### J. Course Moderation & Quality Assurance Queue

Located in `admin/moderate_course.php` and `admin/dashboard.php` ("Course Moderation" tab):

1. **Moderation Queue Navigation:**
   - Dedicated filter sub-tabs: **Pending Review**, **Published (Live)**, and **Needs Revisions / Rejected**.
   - Prominent badge on the admin sidebar highlights outstanding unreviewed courses.
2. **Course Inspection:**
   - Admin inspects title, instructor, category, total lessons, deliverable rubric, and syllabus.
3. **Action: Approve Course:**
   - Updates `courses.status = 'published'`, `reviewed_at = CURRENT_TIMESTAMP`, `reviewed_by = adminId`.
   - Course immediately becomes live and searchable in `courses.php`.
4. **Action: Reject / Request Revisions:**
   - Opens `#adminRejectCourseModal`.
   - Admin provides required corrective feedback (e.g. missing lesson audio, insufficient syllabus, or vague project rubric).
   - Sets `courses.status = 'rejected'` and logs `rejection_reason`.
   - In Instructor Studio, instructor sees red **"Revisions Requested"** badge with the admin's exact feedback note.
5. **Action: Delete Course (`admin/delete_course.php`):**
   - Removes problematic course; database cascading constraints clean associated lessons, enrollments, and submissions.

---

### K. Sponsor Ad Engine & File Manager Video Upload

Located in `admin/manage_ads.php` and `admin/dashboard.php` ("Sponsor Ad Engine" tab):

1. **Interactive Native File Picker Dropzone:**
   - Clicking "+ New Sponsor Campaign" opens `#adminNewAdModal` with `enctype="multipart/form-data"`.
   - Clicking the dropzone triggers the user's native operating system file manager dialog (Windows Explorer, macOS Finder, Linux file chooser) filtered for MP4, WebM, and MOV videos up to 100MB.
   - Drag-and-drop support allows dragging video files directly from the desktop into the dropzone.
   - Real-time JavaScript feedback displays the chosen filename, calculated file size in MB, and highlights the dropzone in green.
   - Custom URL toggle allows specifying existing paths (e.g. `assets/ad/sample_ad.mp4`) or external URLs.
2. **Backend Video Processing & Storage:**
   - Validates file extensions, MIME types, and server file upload limits.
   - Saves file into `uploads/ads/` with collision-safe name `ad_{timestamp}_{uniqid}.{ext}`.
   - Inserts record into `sponsor_ads` with initial status, click URL, and custom CPM rate.
3. **Campaign Management:**
   - **Toggle Status:** Instantly switch campaigns between `active` (serving to students) and `paused`.
   - **Delete Campaign:** Removes ad record and automatically unlinks the local video file from `uploads/ads/` to prevent orphaned disk usage.
4. **Platform Economics Configuration:**
   - Admin can tune global monetization parameters saved to `platform_settings`:
     - Default Ad CPM Rate ($)
     - Instructor Revenue Share Split % (e.g. 70% Instructor / 30% Platform)
     - Ad Cooldown Interval (Minutes between valid impressions per student per lesson)

---

### L. Certificate Anti-Fraud Registry & Revocation

Located in `admin/revoke_certificate.php`, `admin/dashboard.php` ("Certificates & Logs" tab), and `student/certificate.php`:

1. **Registry Search & Audit:**
   - Searchable table of all issued platform credentials by Certificate ID, Student Name, or Course.
   - Filter by status (`Valid` or `Revoked`).
2. **Revocation Modal & Action:**
   - Admin selects "Revoke" on compromised credentials (e.g. plagiarized project deliverable, academic dishonesty).
   - Admin enters mandatory audit reason in `#adminRevokeCertModal`.
   - Sets `certificates.status = 'revoked'`, `revoked_at = CURRENT_TIMESTAMP`, `revoked_by = adminId`, and stores `revocation_reason`.
3. **Public Verification Display (`student/certificate.php`):**
   - **Valid Credential:** Renders original green verified seal, course completion details, and print trigger.
   - **Revoked Credential:** Renders an official red audit warning banner: *"OFFICIAL AUDIT NOTICE: THIS CERTIFICATE HAS BEEN REVOKED"*, prints the exact revocation date and reason, transforms the verified seal to a red *"REVOKED / INVALID CREDENTIAL"* badge, and overlays a prominent 45-degree angled **"REVOKED"** watermark across the certificate frame.
4. **Restoration Action:**
   - Admin can click "Restore" to reinstate an erroneously revoked certificate back to `valid` status.
5. **Intelligent Role-Aware Return Routing:**
   - `student/certificate_function.php` detects the active viewer's session role:
     - Admin &rarr; Returns to `admin/dashboard.php#analytics`
     - Instructor &rarr; Returns to `instructor/dashboard.php`
     - Student &rarr; Returns to `student/dashboard.php`
     - Guest &rarr; Returns to `index.php`
   - Prevents role authentication collisions and accidental logouts when administrators audit student certificates.

---

### M. Instructor Payout / Cash-Out System

Located in `instructor/request_payout_function.php`:

1. **Eligibility & Threshold:** Instructor must possess `available_balance >= $5.00`.
2. **Transfer Method Validation:**
   - **PayPal:** Enforces RFC email format validation.
   - **GCash:** Validates account name and Philippine 11-digit mobile format (`/^(09|\+639)\d{9}$/`).
   - **Bank Transfer:** Requires Bank Name, Account Name, and Account Number.
3. **Atomic Balance Lock:** Executes a database transaction with `FOR UPDATE` row-locking on `instructor_wallets`. Deducts the requested withdrawal amount from `available_balance` and inserts a new record into `payout_requests` with `status = 'pending'`.

---

### N. Administrator Cash-Out Review & Disbursement

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

### O. User Administration & RBAC Role Management

Located in `admin/change_role_function.php` and `admin/delete_user.php`:

1. **User Directory:** Dual-tab view partitioning Students and Teachers with live search by name or email.
2. **Inspect User Modal:** Deep-dive modal revealing registration date, enrollment counts, published courses, and wallet balance.
3. **Role Change Modal (`change_role_function.php`):**
   - Allows administrators to alter user roles:
     - Student promoted to Instructor: Automatically provisions an `instructor_wallets` record.
     - Instructor demoted to Student.
   - **Root Admin Protection:** The active administrator is prevented from demoting or locking their own administrative account.
4. **Safe Deletion (`delete_user.php`):**
   - Deletes non-admin users with confirmation modal.
   - Database constraint blocks deletion of users with `role_id = 1`.

---

### P. Platform Data Export Engine

Located in `admin/export_data.php`:

1. Accessible from sidebar shortcut or modal in Admin Control Center.
2. Generates real-time, stream-downloadable CSV reports with UTF-8 BOM encoding for seamless Excel opening:
   - **Users Report (`type=users`):** User ID, Full Name, Email, Role, Enrolled Courses, Published Courses, Wallet Balance, Joined Date.
   - **Courses Report (`type=courses`):** Course ID, Title, Category, Instructor, Status, Total Lessons, Enrolled Students, Completed Students, Created Date.
   - **Payouts Report (`type=payouts`):** Payout ID, Instructor, Amount, Method, Status, Transaction Reference, Created Date, Processed Date.
   - **Certificates Report (`type=certificates`):** Certificate ID, Code, Student Name, Course Title, Status, Issued Date, Revoked Date, Revocation Reason.
   - **Ad Activity Logs (`type=ad_logs`):** Log ID, Course Title, Lesson Title, Student Name, Instructor Name, Ad Campaign, Amount Earned, Duration, Timestamp.
   - **Sponsor Ads (`type=sponsor_ads`):** Campaign ID, Sponsor Name, Campaign Title, Video URL, CPM Rate, Status, Total Impressions, Created Date.

---

### Q. Global Logout Confirmation Modal (`assets/js/logout_modal.js`)

- Included globally across all platform views.
- Intercepts all clicks on links pointing to `logout.php`.
- Renders an animated glassmorphic modal requesting user confirmation before session termination.
- Supports keyboard navigation (`Escape` to cancel) and backdrop click dismissal.

---

## 7. Input Validation Architecture

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

## 8. Setup & Local Testing Guide

### Prerequisites
- XAMPP / LAMPP installed on Linux.
- Apache web server running on Port `81`.
- MariaDB database service running on Port `3307` (or socket `/opt/lampp/var/mysql/mysql.sock`).
- FFmpeg (`ffprobe`) installed in system path for video duration detection.
- Recommended PHP configuration (`/opt/lampp/etc/php.ini`):
  - `upload_max_filesize = 100M` (or 250M)
  - `post_max_size = 100M` (or 2000M)

### Directory Permissions
Ensure write permissions are configured for media upload directories:
```bash
chmod -R 775 uploads/ads/
chmod -R 775 uploads/instructors/
chmod -R 775 assets/submissions/
```

### Database Initialization
Import the complete 13-table schema and default seeds:
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

## 9. Summary of Vector Assets

All 30 vector icons are stored inside `assets/icons/`:

| Icon File | Usage & Description |
| :--- | :--- |
| `alert-circle.svg` | Error and warning alert banners, revoked certificate audit notices |
| `arrow-left.svg` | Return navigation, back to catalog/home buttons |
| `arrow-right.svg` | Submit triggers, forward progression buttons |
| `award.svg` | Verified credentials, certificates, deliverable evaluations |
| `book-open.svg` | Course catalog, lesson syllabus, learning indicators, course moderation |
| `box.svg` | Docker container technology badge |
| `check-circle.svg` | Success alerts, completed course indicators, approved course badges |
| `clock.svg` | In-progress time, lesson duration indicators, pending review badges |
| `cloud.svg` | Cloud computing & AWS technology badge |
| `code.svg` | Programming languages (Python, JS, React) badge |
| `dollar-sign.svg` | Ad revenue, instructor wallet, cash-out metrics |
| `download.svg` | Download and print certificate actions, CSV data export |
| `external-link.svg`| External platform links, sponsor click-through links |
| `eye.svg` | View credential and details action buttons, user inspection |
| `file-text.svg` | File upload deliverable indicator |
| `github.svg` | GitHub repository deliverable badge |
| `globe.svg` | Language and localization selector |
| `graduation-cap.svg`| Instructor badges and teacher portal identifiers |
| `layout.svg` | UI/UX design skill badge, dashboard overview icon |
| `link.svg` | Blockchain technology badge, live URL deliverable icon |
| `lock.svg` | Locked final project deliverable indicator |
| `log-out.svg` | Global logout confirmation dialog badge |
| `play.svg` | Video lesson player, resume learning button |
| `plus.svg` | Publish new course, add lesson syllabus builder, new ad campaign |
| `shield-check.svg` | 100% free ad-supported verification badge |
| `shield.svg` | Cybersecurity technology badge |
| `trash.svg` | Delete user, delete course, and delete sponsor ad action buttons |
| `user-check.svg` | Student KPI indicators in Admin dashboard |
| `users.svg` | Total users KPI card and User Directory in Admin dashboard |
| `video.svg` | Curriculum module and video indicator, sponsor ad engine |

