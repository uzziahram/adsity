# Adsity - Technical & Functional Documentation

**Version:** 1.2.0  
**Environment:** Linux / LAMPP (XAMPP for Linux)  
**Database:** MariaDB 10.4 (Port 3307)  
**Web Server:** Apache (Port 81)  
**URL Base:** `http://localhost:81/projects/adsity/`

---

## 1. Project Overview

**Adsity** is a web-based educational platform providing **free, ad-supported technology courses and verified completion certificates**. 

The system operates on a sustainable monetization model:
* **Zero-Cost Education:** Students watch a short 15-second sponsor advertisement break before each lesson video in exchange for free access to course content and accredited credentials.
* **Shared Revenue:** Instructors earn a configurable revenue share from every completed sponsor ad view on their courses.
* **Treasury Sustainability:** The platform retains a percentage of ad earnings to sustain infrastructure and operational costs.

### Core User Roles

* **Student (`role_id = 3`):** Explores approved courses, enrolls for free, watches lessons with pre-roll sponsor ads, completes sequential modules, submits capstone project deliverables, and views or prints verified completion certificates.
* **Instructor (`role_id = 2`):** Registers an instructor profile, creates courses via a 4-step wizard, uploads sequential lesson videos, establishes assessment rubrics, grades student deliverables (approving or requesting revisions), tracks ad revenue in an automated wallet, and requests cash-out withdrawals via PayPal, GCash, or Bank Transfer.
* **Administrator (`role_id = 1`):** Platform superuser who oversees quality moderation, system economics, and compliance:
  1. **Course Moderation Queue:** Reviews, approves, or rejects submitted instructor courses with detailed feedback.
  2. **Ad Engine & Monetization:** Manages sponsor ad campaigns, monitors the simulated Google AdSense for Video stream, and configures global monetization rates (CPM, revenue share %, ad cooldowns).
  3. **Certificate Anti-Fraud Registry:** Audits issued certificates, revokes fraudulent credentials with public audit banners and watermarks, and restores appealed certificates.
  4. **Instructor Payout Review:** Inspects withdrawal requests, disburses funds with transaction trace receipts, or rejects invalid requests with automated balance refunds.
  5. **Platform Treasury Withdrawals:** Withdraws accumulated platform ad margins directly to commercial bank accounts with automated ledger tracking.
  6. **User Administration & RBAC:** Promotes/demotes user roles (with root admin protection) and manages active accounts.
  7. **Data Export Engine:** Generates instant UTF-8 CSV audit reports for users, courses, payouts, certificates, and ad impressions.

---

## 2. Dedicated End-to-End Website Process

The 10-phase operational lifecycle outlines the complete workflow of Adsity across all roles:

### Phase 1: Onboarding & Account Provisioning
* **Student Registration ([signup.php](file:///home/ugenella/coding/xampp-projects/adsity/signup.php)):** Learners register with their name, email, and a secure 5-tier password. Upon creation, their account is assigned `role_id = 3` and logged in immediately.
* **Instructor Onboarding ([teach.php](file:///home/ugenella/coding/xampp-projects/adsity/teach.php)):** Educators register with professional credentials and category expertise. An account is created (`role_id = 2`) with an initialized wallet balance of `$0.00`.
* **Administrator Access ([login.php](file:///home/ugenella/coding/xampp-projects/adsity/login.php)):** Authenticates superuser accounts (`role_id = 1`) and routes them directly to the Admin Control Center ([admin/dashboard.php](file:///home/ugenella/coding/xampp-projects/adsity/admin/dashboard.php)).

### Phase 2: Course Authoring & Curriculum Upload
* **4-Step Wizard ([instructor/create_course.php](file:///home/ugenella/coding/xampp-projects/adsity/instructor/create_course.php)):**
  1. **Overview:** Enter title, category, and syllabus description, and upload a course thumbnail.
  2. **Curriculum:** Upload sequential MP4 video lessons. Durations are detected on the client via the HTML5 Video API and validated on the server using `ffprobe`.
  3. **Assessment Rubric:** Choose the final project format (GitHub repository, live website URL, or file archive) and specify grading instructions.
  4. **Preview & Submission:** Review catalog card mockups and submit the curriculum for review.
* Newly created courses are marked `pending_review` and hidden from the public catalog until approved.

### Phase 3: Administrative Course Moderation & Publication
* **Moderation Queue ([admin/moderate_course.php](file:///home/ugenella/coding/xampp-projects/adsity/admin/moderate_course.php)):** Administrators review pending courses in the Admin Control Center.
* **Approval:** If curriculum standards are met, the course status changes to `published` and it immediately appears in the public catalog ([courses.php](file:///home/ugenella/coding/xampp-projects/adsity/courses.php)).
* **Rejection / Revision Request:** If changes are required, the admin rejects the submission with mandatory feedback. The instructor is notified in the Instructor Studio to make adjustments.

### Phase 4: Sponsor Ad Integration & Monetization Setup
* **Ad Network Integration:** Video advertisements are delivered via the simulated Google AdSense for Video sandbox ([mock_adsense/](file:///home/ugenella/coding/xampp-projects/adsity/mock_adsense)), serving authentic partner commercials programmatically in JSON and VAST 3.0 XML formats.
* **Platform Monetization Economics:** Administrators configure global parameters:
  * **Default Ad CPM Rate:** Revenue generated per completed 15-second impression (e.g., `$0.0500`).
  * **Revenue Share Split:** Default split is **65% to the instructor** and **35% to the platform treasury**.
  * **Anti-Spam Ad Cooldown:** Configured time window required between billable ad views per student per lesson.

### Phase 5: Student Discovery, Enrollment & Learning
* **Course Catalog ([courses.php](file:///home/ugenella/coding/xampp-projects/adsity/courses.php)):** Students browse published courses with dynamic cards showing enrollment status ("View Course & Enroll" or "In Progress").
* **Enrollment ([course_details.php](file:///home/ugenella/coding/xampp-projects/adsity/course_details.php)):** Clicking "Enroll in Course" registers the student (`progress_percent = 0`, `status = 'in_progress'`) and enters the classroom.
* **Interactive Classroom ([student/learn.php](file:///home/ugenella/coding/xampp-projects/adsity/student/learn.php)):**
  * Plays a mandatory 15-second sponsor video with a real-time countdown timer before each lesson starts.
  * Displays a branded sponsor overlay with partner information and landing page links.
  * Lets students navigate unlocked lessons sequentially while tracking progress.

### Phase 6: Impression Verification & Automated Revenue Allocation
* **Ad Verification & Logging ([student/record_ad_activity.php](file:///home/ugenella/coding/xampp-projects/adsity/student/record_ad_activity.php)):**
  * When the 15-second ad finishes, an automated request validates the impression against the anti-spam cooldown window.
  * Prevents instructors from generating ad revenue while previewing their own courses.
  * Automatically calculates and splits the revenue within a database transaction: 65% credits the instructor's wallet balance and 35% deposits into the platform treasury.
* **Lesson Progress ([student/complete_lesson.php](file:///home/ugenella/coding/xampp-projects/adsity/student/complete_lesson.php)):** Completing a lesson updates student progress and unlocks the next module.

### Phase 7: Final Assessment & Project Evaluation
* **Assessment Gate ([student/submit_exam.php](file:///home/ugenella/coding/xampp-projects/adsity/student/submit_exam.php)):** Deliverable submission unlocks exclusively when course progress reaches 100%.
* **Deliverable Submission:** The student submits their GitHub repository URL, live application link, or uploaded project archive with notes.
* **Instructor Evaluation:**
  * **Approval:** The instructor approves the project, marking the enrollment as completed. A unique certificate verification code (`ADS-{YEAR}-{HEX}`) is generated.
  * **Revision Request:** If criteria are unmet, the instructor marks the submission for revision with actionable feedback so the student can resubmit.

### Phase 8: Credential Issuance & Anti-Fraud Verification
* **Certificate Display ([student/certificate.php](file:///home/ugenella/coding/xampp-projects/adsity/student/certificate.php)):** Students view and print accredited certificates displaying their legal name, course title, completion date, and verification ID.
* **Anti-Fraud Auditing & Revocation:**
  * Administrators can review and revoke any certificate suspected of academic dishonesty or deliverable plagiarism.
  * A revoked certificate publicly displays a red audit warning banner, invalidation reason, and a diagonal "REVOKED" watermark.
  * Administrators can restore a revoked certificate if an appeal is resolved.
* **Intelligent Routing:** The certificate return button detects whether the viewer is an Admin, Instructor, or Student to avoid session collisions.

### Phase 9: Payouts & Platform Treasury Cash-Out
* **Instructor Cash-Out:** Instructors with an available balance of at least `$5.00` can request withdrawals via PayPal, GCash, or Bank Transfer. The requested balance is locked pending review.
* **Administrative Payout Processing:**
  * **Disbursement:** The admin inspects the destination account, disburses the funds, enters a transaction reference ID, and marks the request completed.
  * **Rejection & Refund:** If payment details are invalid, the admin rejects the request with notes, and the locked funds automatically refund to the instructor's available balance.
* **Platform Treasury Bank Cash-Out ([admin/admin_withdraw.php](file:///home/ugenella/coding/xampp-projects/adsity/admin/admin_withdraw.php)):**
  * Administrators can withdraw accumulated platform funds directly to a commercial bank account with no minimum threshold.
  * Every withdrawal executes an atomic ledger transaction, generates a unique banking trace reference (`BNK-YYYY-XXXX`), and logs the transfer.

### Phase 10: Platform Governance, RBAC & Reporting
* **User Management:** Administrators can promote students to instructors (automatically provisioning a wallet) or demote instructors, while protecting root administrator accounts from accidental lockouts.
* **Data Export Engine ([admin/export_data.php](file:///home/ugenella/coding/xampp-projects/adsity/admin/export_data.php)):** Administrators can download instant CSV audit reports with UTF-8 BOM encoding for Excel (Users, Courses, Instructor Payouts, Admin Bank Ledger, Certificates, Ad Logs, and Sponsor Campaigns).
* **Safe Logout:** A global confirmation modal intercepts logout triggers across all user roles to prevent accidental session loss.

---

## 3. Technology Stack

| Layer | Technology | Details |
| :--- | :--- | :--- |
| **Backend** | PHP 8.x | Native procedural PHP with PDO database abstraction, prepared statements, and transactional rollbacks |
| **Database** | MariaDB 10.4 | Relational database with foreign key constraints, cascading updates/deletes, and unique indices |
| **Session & Auth** | PHP Sessions (`$_SESSION`) | Role-based access control (RBAC), session gating, and immediate memory purging of plaintext passwords |
| **Media Processing** | FFmpeg (`ffprobe`) | Server-side CLI execution to detect video durations from uploaded lesson files |
| **File Handling** | Native PHP Uploads | Secure uploads for video lessons, thumbnails, and student project deliverables |
| **Frontend** | HTML5 / CSS3 / Vanilla JS | Responsive CSS, Raleway & Cinzel typography, and HTML5 Video API |
| **UI Components** | Custom JavaScript | Native file dropzone pickers, ad player state machines, and glassmorphic confirmation modals |
| **Reporting & Export** | Native PHP Stream | UTF-8 BOM-encoded CSV export streams for spreadsheet and Excel compatibility |
| **Assets** | Standalone SVG Vectors | 30 bespoke icons stored in `assets/icons/` |

---

## 4. Input Validation Architecture

All input validation rules are centralized in [`validation.php`](file:///home/ugenella/coding/xampp-projects/adsity/validation.php):

* **Required Field Check (`validateRequired`):** Ensures trimmed user input is non-empty.
* **Email Format Check (`validateEmailFormat`):** Enforces RFC-compliant email structure using PHP's `FILTER_VALIDATE_EMAIL`.
* **Minimum Length Check (`validateMinLength`):** Validates minimum character requirements for text fields.
* **5-Tier Password Security Policy (`validatePassword`):**
  1. Minimum 8 characters in length.
  2. At least one uppercase letter (`[A-Z]`).
  3. At least one lowercase letter (`[a-z]`).
  4. At least one numeric digit (`[0-9]`).
  5. At least one special character (`[!@#$%^&*()\-_=+{};:,<.>]`).
* **Password Match (`validatePasswordMatch`):** Verifies that the confirmation password matches the entered password.
* **Terms of Service Consent (`validateTerms`):** Verifies that the user agreed to platform terms.
* **Aggregated Validators:** Handles full form validation for student registration (`validateSignupInput`), instructor registration (`validateTeacherSignupInput`), and authentication (`validateLoginInput`).

---

## 5. Setup & Local Testing Guide

### Prerequisites
* XAMPP / LAMPP installed on Linux.
* Apache web server running on Port `81`.
* MariaDB database service running on Port `3307` (or socket `/opt/lampp/var/mysql/mysql.sock`).
* FFmpeg (`ffprobe`) installed in system path for video duration detection.
* Recommended PHP configuration (`/opt/lampp/etc/php.ini`):
  * `upload_max_filesize = 100M` (or higher)
  * `post_max_size = 100M` (or higher)

### Directory Permissions
Ensure write permissions are configured for media upload directories:
```bash
chmod -R 775 uploads/ads/
chmod -R 775 uploads/instructors/
chmod -R 775 assets/submissions/
```

### Database Initialization
Import the database schema and default seeds:
```bash
/opt/lampp/bin/mysql -u root -S /opt/lampp/var/mysql/mysql.sock < database/schema.sql
```

### Default Administrator Credentials

| Role | Email | Password | Dashboard URL |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@adsity.org` | `admin123` | `http://localhost:81/projects/adsity/admin/dashboard.php` |

> [!NOTE]
> Instructors can be registered at [teach.php](file:///home/ugenella/coding/xampp-projects/adsity/teach.php) and Students at [signup.php](file:///home/ugenella/coding/xampp-projects/adsity/signup.php). All passwords are encrypted with `PASSWORD_DEFAULT` (Bcrypt).

---

## 6. Vector Icons Reference

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
| `external-link.svg` | External platform links, sponsor click-through links |
| `eye.svg` | View credential and details action buttons, user inspection |
| `file-text.svg` | File upload deliverable indicator |
| `github.svg` | GitHub repository deliverable badge |
| `globe.svg` | Language and localization selector |
| `graduation-cap.svg` | Instructor badges and teacher portal identifiers |
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
