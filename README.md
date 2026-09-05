# Adsity - Technical & Functional Documentation

**Version:** 1.0.0  
**Environment:** Linux / LAMPP (XAMPP for Linux)  
**Database:** MariaDB 10.4 (Port 3307)  
**Web Server:** Apache (Port 81)  
**URL Base:** `http://localhost:81/projects/adsity/`

---

## 1. Project Overview

**Adsity** is a web-based educational platform providing free, ad-supported technology courses and verified industry certificates. The system operates on a model where students watch short advertisement breaks throughout lessons in exchange for zero-cost education and credentialing.

### Core User Roles:
- **Student (`role_id = 3`):** Explores courses, tracks enrolled and in-progress learning, finishes courses, and views/prints verified completion certificates.
- **Instructor / Teacher (`role_id = 2`):** Applies/registers to teach, creates curriculum, and shares ad revenue.
- **Administrator (`role_id = 1`):** Superuser with a dedicated monitoring dashboard to oversee all registered students and teachers, inspect system stats, and delete accounts.

---

## 2. Technology Stack

| Layer | Technology | Details |
| :--- | :--- | :--- |
| **Backend** | PHP 8.x | Native procedural PHP with PDO database abstraction |
| **Database** | MariaDB 10.4.32 | Relational schema with Foreign Keys and Cascading Constraints |
| **Session & Auth** | PHP Sessions (`$_SESSION`) | Server-side authentication with role-based access control (RBAC) |
| **Frontend** | HTML5 / CSS3 | Custom CSS (`style.css`), Google Font (*Raleway*, *Cinzel*), Flexbox/Grid |
| **Assets** | Standalone SVG Vectors | Stored in `assets/icons/` and loaded via `<img>` tags |

---

## 3. Directory & File Structure

```
adsity/
├── admin/
│   ├── admindashboard.css       # Dedicated stylesheet for Admin Dashboard
│   ├── dashboard.php            # Admin Dashboard view (KPIs, teacher & student monitoring tables)
│   ├── dashboard_function.php   # Admin data handler, queries & session gate (admin only)
│   └── delete_user.php          # Action handler to delete student or teacher records
│
├── assets/
│   ├── Adsity-Homepage-Mockup_Genella.pdf # Original design specification
│   ├── adsity_assets/           # Course thumbnails, branding, and logo images
│   └── icons/                   # Standalone SVG icon vector library
│       ├── alert-circle.svg
│       ├── arrow-left.svg
│       ├── arrow-right.svg
│       ├── award.svg
│       ├── book-open.svg
│       ├── box.svg
│       ├── check-circle.svg
│       ├── clock.svg
│       ├── cloud.svg
│       ├── code.svg
│       ├── download.svg
│       ├── eye.svg
│       ├── globe.svg
│       ├── graduation-cap.svg
│       ├── layout.svg
│       ├── link.svg
│       ├── play.svg
│       ├── shield-check.svg
│       ├── shield.svg
│       ├── trash.svg
│       ├── user-check.svg
│       └── users.svg
│
├── database/
│   ├── config.php               # PDO database connection function getConnection()
│   └── schema.sql               # Complete SQL schema & table definitions
│
├── instructor/
│   ├── create_course.css        # Dedicated stylesheet for Multi-Video Course Studio
│   ├── create_course.php        # Form view to publish new courses
│   ├── create_course_function.php # Action handler to insert new courses
│   ├── dashboard.php            # Instructor Studio dashboard view (Metrics, Courses table)
│   ├── dashboard_function.php   # Instructor data query handler & session gate (instructor only)
│   ├── delete_course.php        # Action handler to delete an instructor's course
│   └── instructordashboard.css  # Dedicated stylesheet for Instructor Dashboard
│
├── course_details.css           # Dedicated stylesheet for Course Overview & Enrollment page
├── course_details.php           # Course Overview page (Thumbnail, Lesson Syllabus, Final Output, Enroll CTA)
├── courses.css                  # Dedicated stylesheet for Course Catalog & Category filters
├── courses.php                  # Course Catalog with search keyword & category filters
├── courses_function.php         # Courses query handler with dynamic SQL filters & enrollment state
├── index.php                    # Adsity landing page / homepage
├── login.php                    # User authentication login view (supports redirect_course)
├── login_function.php           # Login validation, credential verification & role routing
├── logout.php                   # Session destruction & logout redirection handler
├── README.md                    # Detailed project technical documentation & architecture guide
├── signup.php                   # Student registration view (supports redirect_course)
├── signup_function.php          # Student registration handler & auto-login
├── style.css                    # Global application stylesheet & responsive design system
├── teach.php                    # Instructor application & registration view
├── teach_function.php           # Instructor registration handler & session assignment
├── validation.php               # Centralized input validation functions
│
├── student/
│   ├── certificate.css          # Dedicated stylesheet for Verified Certificate & Print view
│   ├── certificate.php          # Verified Certificate view (Print / Save as PDF)
│   ├── certificate_function.php # Certificate lookup and verification query handler
│   ├── dashboard.php            # Student Dashboard view (In-Progress, Completed, Certificates)
│   ├── dashboard_function.php   # Student data query handler & session gate (student only)
│   ├── enroll_function.php      # Student course enrollment backend processor
│   ├── studentdashboard.css     # Dedicated stylesheet for Student Dashboard
│   ├── submit_exam.css          # Dedicated stylesheet for Final Exam / Project submission
│   ├── submit_exam.php          # Final project / exam submission form (locked until course completion)
│   └── submit_exam_function.php # Project upload & certificate issuance handler (with completion gate)
│
└── uploads/
    ├── instructors/             # Instructor storage partitioned by instructor ID & course ID
    │   └── {instructor_id}/
    │       └── {course_id}/
    │           ├── thumbnail/   # Course cover thumbnail (thumbnail_{timestamp}.ext)
    │           └── lesson_{n}_{timestamp}.mp4 # Uploaded lesson MP4 video files
    └── submissions/             # Uploaded student project exam submissions
```

---

## 4. Database Schema & Architecture

### Entity Relationship Diagram

```mermaid
erDiagram
    ROLES ||--o{ USERS : "has"
    USERS ||--o{ ENROLLMENTS : "enrolls in"
    COURSES ||--o{ ENROLLMENTS : "contains"
    USERS ||--o{ CERTIFICATES : "earns"
    COURSES ||--o{ CERTIFICATES : "issued for"

    ROLES {
        int id PK
        varchar name UK
        varchar description
    }

    USERS {
        int id PK
        varchar full_name
        varchar email UK
        varchar password
        int role_id FK
        timestamp created_at
    }

    COURSES {
        int id PK
        varchar title
        text description
        varchar category
        varchar thumbnail
        int total_lessons
        timestamp created_at
    }

    ENROLLMENTS {
        int id PK
        int user_id FK
        int course_id FK
        int progress_percent
        enum status
        timestamp enrolled_at
        timestamp completed_at
    }

    CERTIFICATES {
        int id PK
        int user_id FK
        int course_id FK
        varchar certificate_code UK
        timestamp issued_at
    }
```

### Table Definitions:

#### 1. `roles` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Role Identifier (`1 = admin`, `2 = instructor`, `3 = student`) |
| `name` | `VARCHAR(50)` | `NOT NULL, UNIQUE` | Unique role name (`admin`, `instructor`, `student`) |
| `description`| `VARCHAR(255)` | `NULL` | Role access description |

#### 2. `users` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Unique User ID |
| `full_name` | `VARCHAR(100)` | `NOT NULL` | User full name |
| `email` | `VARCHAR(150)` | `NOT NULL, UNIQUE` | User login email |
| `password` | `VARCHAR(255)` | `NOT NULL` | Hashed password (`PASSWORD_DEFAULT` / bcrypt) |
| `role_id` | `INT` | `NOT NULL, DEFAULT 3, FK` | References `roles(id)` |
| `created_at`| `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Account registration timestamp |

#### 3. `courses` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Course ID (starts at `1000`) |
| `title` | `VARCHAR(150)` | `NOT NULL` | Course Title |
| `description`| `TEXT` | `NULL` | Detailed course overview |
| `category` | `VARCHAR(100)` | `NULL` | Topic category |
| `thumbnail`| `VARCHAR(255)` | `NULL` | Image asset path (e.g. `uploads/instructors/{id}/{course_id}/thumbnail/...`) |
| `total_lessons` | `INT` | `DEFAULT 10` | Total lessons count |
| `instructor_id` | `INT` | `NULL, FK` | References `users(id)` |
| `assessment_type` | `ENUM` | `'github_repo', 'file_upload', 'live_url'` | Deliverable submission format |
| `assessment_instructions` | `TEXT` | `NULL` | Instructor guidelines and rubric for final deliverable |
| `created_at`| `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Course creation timestamp |

#### 4. `lessons` Table (Multi-Video Curriculum)
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Unique Lesson ID |
| `course_id` | `INT` | `NOT NULL, FK` | References `courses(id)` ON DELETE CASCADE |
| `lesson_number` | `INT` | `NOT NULL` | Sequential position (`1`, `2`, `3`...) |
| `title` | `VARCHAR(150)` | `NOT NULL` | Lesson Title |
| `video_path` | `VARCHAR(255)` | `NOT NULL` | Relative path to uploaded MP4 file |
| `duration` | `VARCHAR(20)` | `DEFAULT '10:00'` | Lesson duration (detected automatically) |
| `created_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Lesson creation timestamp |

#### 5. `enrollments` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Enrollment ID |
| `user_id` | `INT` | `NOT NULL, FK` | References `users(id)` |
| `course_id` | `INT` | `NOT NULL, FK` | References `courses(id)` |
| `progress_percent` | `INT` | `DEFAULT 0` | Completion percentage (0 - 100) |
| `status` | `ENUM` | `'in_progress', 'completed'` | Current study state |
| `enrolled_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Enrollment date |
| `completed_at` | `TIMESTAMP` | `NULL` | Course completion date |

#### 6. `certificates` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Certificate ID |
| `user_id` | `INT` | `NOT NULL, FK` | References `users(id)` |
| `course_id` | `INT` | `NOT NULL, FK` | References `courses(id)` |
| `certificate_code` | `VARCHAR(50)` | `NOT NULL, UNIQUE` | Public verification code |
| `issued_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Certificate issuance timestamp |

---

## 5. Key Workflows & User Flows

### A. Authentication & Session Routing Flow

```mermaid
flowchart TD
    Start[User Visits login.php] --> Submit[POST Credentials to login_function.php]
    Submit --> Validate{Validation Passed?}
    Validate -->|No| ErrRedirect[Redirect to login.php?status=error]
    Validate -->|Yes| QueryUser[Query users joined with roles]
    QueryUser --> PassCheck{password_verify matches?}
    PassCheck -->|No| ErrRedirect
    PassCheck -->|Yes| SetSession[Set $_SESSION user_id, full_name, email, role_name]
    SetSession --> RoleBranch{Check role_name}
    RoleBranch -->|admin| AdminRedirect[admin/dashboard.php]
    RoleBranch -->|student| StudentRedirect[student/dashboard.php]
    RoleBranch -->|instructor| IndexRedirect[instructor/dashboard.php]
```

### B. Student Registration & Dashboard
1. Visitor submits form at [`signup.php`](file:///home/ugenella/coding/xampp-projects/adsity/signup.php).
2. [`signup_function.php`](file:///home/ugenella/coding/xampp-projects/adsity/signup_function.php) validates inputs, securely hashes the password via `password_hash(..., PASSWORD_DEFAULT)`, inserts record into `users` with `role_id = 3`, sets session variables, and automatically logs the student in.
3. Student lands on [`student/dashboard.php`](file:///home/ugenella/coding/xampp-projects/adsity/student/dashboard.php) which displays:
   - **Metrics:** Active courses, completed courses, and earned certificates counts.
   - **In Progress Cards:** Progress bar, percentage, lessons finished, and "Continue Learning" button.
   - **Completed Cards:** Completion date and "View Certificate" button.
   - **My Certificates:** Verification codes and direct links to [`student/certificate.php`](file:///home/ugenella/coding/xampp-projects/adsity/student/certificate.php).

### C. 4-Step Course Creation Wizard (`instructor/create_course.php`)

Adsity features a multi-step course creation wizard designed to streamline the publishing process:

```
Step 1: Course Info & Cover Thumbnail ──▶ Step 2: Sequential Curriculum ──▶ Step 3: Final Deliverable ──▶ Step 4: Student Preview & Publish
```

1. **Step 1: Course Overview & Thumbnail Upload:**
   - Instructor provides the course title, topic category, and description.
   - A dedicated drag-and-drop dropzone accepts `.png`, `.jpg`, `.jpeg`, or `.webp` files.
   - Displays a live image preview badge with exact file size before proceeding.

2. **Step 2: Sequential Curriculum Builder:**
   - Starts cleanly with **Lesson 01** (title and video file selector).
   - An **"+ Add Next Lesson"** button dynamically increments and appends Lesson 02, Lesson 03, etc., with individual remove triggers.
   - **HTML5 Video Metadata Detection:** When a video file is picked, client-side JavaScript reads video headers directly via `URL.createObjectURL` to determine the exact duration (e.g. `12:45`) and automatically estimates the sponsor ad breaks.
   - On submission, `ffprobe` extracts the exact duration from the uploaded file on the server.

3. **Step 3: Final Exam Deliverables & Assessment:**
   - Instructor selects the deliverable submission format:
     - 🐙 **GitHub Repository (`github_repo`):** For code repositories and software projects.
     - 📦 **Project File Upload (`file_upload`):** For ZIP, PDF, or raw design files.
     - 🌐 **Live Application URL (`live_url`):** For hosted web apps, portfolios, or demos.
   - Instructor provides detailed submission instructions and grading rubrics.

4. **Step 4: Live Student Perspective Preview & Pre-Flight:**
   - Renders a live mock catalog card.
   - Outlines the complete lesson syllabus with interstitial 15-second sponsor ad breaks.
   - Displays a pre-flight publication readiness checklist before the instructor clicks **"Publish Course"**.

---

### D. Filesystem Storage Hierarchy & File Naming Syntax

When an instructor publishes a course, [`instructor/create_course_function.php`](file:///home/ugenella/coding/xampp-projects/adsity/instructor/create_course_function.php) creates an isolated directory structure partitioned by instructor ID and course ID:

```
uploads/instructors/{instructor_id}/{course_id}/
├── thumbnail/
│   └── thumbnail_{timestamp}.{ext}
├── lesson_1_{timestamp}.mp4
├── lesson_2_{timestamp}.mp4
└── lesson_3_{timestamp}.mp4
```

#### Why `lesson_1_{timestamp}.mp4`?
The filename syntax `lesson_{lessonNumber}_{time()}.{ext}` combines:
* **`lesson_{n}`**: The sequential lesson index.
* **`{timestamp}`**: The Unix timestamp generated by PHP's `time()` (e.g. `1788587790`).
  1. **Collision Defense:** Prevents identical filenames from overwriting each other if an instructor re-uploads.
  2. **Cache Busting:** Ensures students' browsers always load the newest video rather than playing an old cached version from memory.
  3. **Filesystem Sanitization:** Prevents spaces, parentheses, or directory traversal characters from causing server errors.

#### PHP Upload Size Configuration Trap (`php.ini`):
* **`upload_max_filesize` (e.g. `250M`):** The maximum allowed size for **any single video file**.
* **`post_max_size` (e.g. `500M`):** The combined total size of **all uploaded files + form data submitted together**.
  > [!WARNING]
  > When `post_max_size` is exceeded, PHP silently clears `$_POST` and `$_FILES`. Ensure `post_max_size` is always significantly larger than `upload_max_filesize * total_lessons`.

---

### E. Course Catalog & Exploration (`courses.php`)
1. Users click **"Explore"** on any page or search from the top navigation bar.
2. [`courses.php`](file:///home/ugenella/coding/xampp-projects/adsity/courses.php) renders the catalog with live search (`?search=...`) and category filtering.
3. **Smart Enrollment Detection:**
   * If a logged-in student is already enrolled in a course, the card displays a green **"In Progress (View)"** button.
   * If not yet enrolled, it displays **"View Course & Enroll"**.
4. Clicking any course card opens the dedicated **Course Overview** page ([`course_details.php`](file:///home/ugenella/coding/xampp-projects/adsity/course_details.php)).

---

### F. Course Overview & Student Enrollment Flow

```mermaid
sequenceDiagram
    autonumber
    actor Student as Student
    participant Details as course_details.php
    participant Handler as student/enroll_function.php
    participant DB as MariaDB (enrollments)
    participant Dash as student/dashboard.php

    Student->>Details: Views course overview (Thumbnail, Syllabus, Final Output)
    Student->>Details: Clicks "Enroll in Course"
    Details->>Handler: POST /student/enroll_function.php (course_id)
    Handler->>DB: INSERT into enrollments (user_id, course_id, progress=0, status='in_progress')
    DB-->>Handler: Enrollment confirmed
    Handler-->>Dash: Redirect with ?status=success&message=Successfully+enrolled!
    Dash-->>Student: Displays enrolled course in "In Progress" with "Learn" button
```

1. **Course Overview ([`course_details.php`](file:///home/ugenella/coding/xampp-projects/adsity/course_details.php)):**
   - **Thumbnail & Metadata:** Displays instructor thumbnail, course title, category, description, and instructor profile.
   - **Sequential Syllabus:** Renders each lesson row with badge numbers (`01`, `02`...), titles, exact durations, and sponsor break tags.
   - **Final Output Deliverable:** Displays the required submission format (GitHub Repo, File Upload, or Live URL) and assessment instructions.
   - **Enrollment Action (Sticky Card):**
     - Unenrolled student: Prominent **"Enroll in Course"** button.
     - Enrolled student: Shows **"You are enrolled! (X% completed)"** and a link to their dashboard.
     - Guest: **"Log In to Enroll"** (carries `redirect_course` to route the student right back upon authentication).

2. **Enrollment Backend Processor ([`student/enroll_function.php`](file:///home/ugenella/coding/xampp-projects/adsity/student/enroll_function.php)):**
   - Enforces student authentication.
   - Inserts record into `enrollments` (`user_id`, `course_id`, `progress_percent = 0`, `status = 'in_progress'`) with `ON DUPLICATE KEY UPDATE`.
   - Redirects to [`student/dashboard.php`](file:///home/ugenella/coding/xampp-projects/adsity/student/dashboard.php) with a welcoming confirmation banner.

---

### G. Course Completion Gate for Final Project Submission

To preserve academic integrity and credential value, **students cannot submit a final project until they have finished all lessons in the course**:

1. **Backend Protection ([`student/submit_exam_function.php`](file:///home/ugenella/coding/xampp-projects/adsity/student/submit_exam_function.php)):**
   - Checks that the student is enrolled.
   - Validates that `progress_percent >= 100` or `status = 'completed'`.
   - Direct POST attempts with incomplete progress are rejected immediately with an error redirect.

2. **Locked Deliverable View ([`student/submit_exam.php`](file:///home/ugenella/coding/xampp-projects/adsity/student/submit_exam.php)):**
   - If `progress_percent < 100`, the submission form is completely hidden.
   - Renders a **"Final Project Submission is Locked"** notice with a 🔒 lock badge, current progress %, and a **"Resume Course Lessons"** button.
   - Only unlocks the upload form once all lessons have been completed.

3. **Dashboard Button States ([`student/dashboard.php`](file:///home/ugenella/coding/xampp-projects/adsity/student/dashboard.php)):**
   - While a course is in progress (`progress < 100%`), only the clean **"Learn"** button is displayed.
   - Once progress reaches **100%**, the **"Submit Project"** button automatically appears alongside Learn.

---

## 6. Login Authentication System

Adsity features a multi-tiered, secure login authentication system built directly into procedural PHP with PDO database abstraction. It enforces strong cryptographic verification, role-based access control (RBAC), and defense-in-depth memory hygiene.

### Architecture & Key Components

The authentication subsystem is partitioned across four primary modules:

| Component | File | Responsibility |
| :--- | :--- | :--- |
| **View / Interface** | [`login.php`](file:///home/ugenella/coding/xampp-projects/adsity/login.php) | User login interface with split-screen branding, feedback alerts, and credential form. |
| **Authentication Controller** | [`login_function.php`](file:///home/ugenella/coding/xampp-projects/adsity/login_function.php) | Request validation, PDO query execution, password verification, memory cleanup, session initialization, and role routing. |
| **Input Validation** | [`validation.php`](file:///home/ugenella/coding/xampp-projects/adsity/validation.php) | Server-side format enforcement (email format via `filter_var`, required field checks). |
| **Database Connection** | [`database/config.php`](file:///home/ugenella/coding/xampp-projects/adsity/database/config.php) | PDO singleton-style connection factory configuring strict error reporting (`ERRMODE_EXCEPTION`) and prepared statement defaults. |

---

### Step-by-Step Authentication Lifecycle

```mermaid
sequenceDiagram
    autonumber
    actor User as User Browser
    participant LoginView as login.php
    participant AuthCtrl as login_function.php
    participant Val as validation.php
    participant DB as MariaDB (users + roles)
    participant Session as PHP $_SESSION

    User->>LoginView: Submits Email & Password
    LoginView->>AuthCtrl: POST /login_function.php (name="login")
    
    Note over AuthCtrl: Step 1: Guard check (isset($_POST['login']))
    AuthCtrl->>Val: validateLoginInput($_POST)
    Val-->>AuthCtrl: Return sanitized data or validation errors
    
    alt Validation Failed
        AuthCtrl-->>LoginView: Redirect ?status=error&message=...
    else Validation Succeeded
        AuthCtrl->>DB: Prepared SELECT with JOIN roles WHERE email = :email
        DB-->>AuthCtrl: Return user row (id, password, role_name, etc.)
        
        Note over AuthCtrl: Step 4: password_verify(inputPassword, user.password)
        Note over AuthCtrl: Step 5: unset($user['password'], $inputPassword)
        
        alt Invalid Credentials (User missing OR password mismatch)
            AuthCtrl-->>LoginView: Redirect ?status=error&message=Invalid+email+or+password.
        else Credentials Valid
            AuthCtrl->>Session: Store user_id, full_name, email, role_id, role_name
            Note over AuthCtrl: Step 8: Evaluate role_name for redirection
            alt role == 'admin'
                AuthCtrl-->>User: HTTP 302 Redirect -> admin/dashboard.php
            else role == 'instructor'
                AuthCtrl-->>User: HTTP 302 Redirect -> instructor/dashboard.php
            else role == 'student'
                AuthCtrl-->>User: HTTP 302 Redirect -> student/dashboard.php
            else Default
                AuthCtrl-->>User: HTTP 302 Redirect -> index.php
            end
        end
    end
```

#### Detailed Flow Breakdown:

1. **Request Origin Guard (`isset($_POST['login'])`)**:
   - The controller checks if the incoming request is a valid POST submission triggered by the form's submit button.
   - Direct GET requests or bot crawling attempts to [`login_function.php`](file:///home/ugenella/coding/xampp-projects/adsity/login_function.php) are immediately rejected with a redirect back to [`login.php`](file:///home/ugenella/coding/xampp-projects/adsity/login.php).

2. **Input Sanitization & Server-Side Validation (`validateLoginInput`)**:
   - Both `email` and `password` fields are validated as required.
   - The email is trimmed and verified against RFC standards using `filter_var($value, FILTER_VALIDATE_EMAIL)`.
   - If validation fails, error messages are encoded in query parameters and rendered in error alerts on the form.

3. **Parameterized Database Lookup (SQL Injection Defense)**:
   - The user lookup executes a single, parameterized query joining `users` with the `roles` table:
     ```php
     $sql = "SELECT u.id, u.full_name, u.email, u.password, u.role_id, r.name AS role_name 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.email = :email 
             LIMIT 1";
     $stmt = $pdo->prepare($sql);
     $stmt->bindValue(':email', $result['data']['email']);
     $stmt->execute();
     ```
   - Using PDO prepared statements with `:email` binding ensures user input is never interpolated directly into the SQL string, neutralizing SQL injection vectors.

4. **Cryptographic Password Verification**:
   - Passwords are authenticated using PHP's native `password_verify($inputPassword, $user['password'])`.
   - Hashes are created during registration using `password_hash($password, PASSWORD_DEFAULT)` (Bcrypt).
   - `password_verify` automatically extracts the cost and salt embedded within the hash string and performs constant-time cryptographic comparison, mitigating timing attacks.

5. **In-Memory Credential Purging (Credential Hygiene)**:
   - Immediately following `password_verify()`, the raw input password and the database hash are erased from the PHP runtime memory:
     ```php
     if ($user) {
         unset($user['password']);
     }
     unset($inputPassword, $_POST['password']);
     ```
   - This prevents sensitive plain-text passwords or hashes from persisting in memory or leaking in the event of an unhandled exception or debug dump.

6. **Anti-User Enumeration Generic Error Messaging**:
   - Whether the email does not exist in the database or the password does not match, the exact same error is returned:
     ```
     "Invalid email or password."
     ```
   - This prevents malicious actors from distinguishing between registered and non-registered email addresses through trial-and-error.

7. **Session State Initialization**:
   - Upon successful credential verification, a secure server-side session is established and populated with authentication claims:
     ```php
     $_SESSION['user_id']   = $user['id'];
     $_SESSION['full_name'] = $user['full_name'];
     $_SESSION['email']     = $user['email'];
     $_SESSION['role_id']   = $user['role_id'];
     $_SESSION['role_name'] = $user['role_name'];
     ```

8. **Role-Based Access Control (RBAC) Redirection**:
   - The user is redirected to their dedicated portal based on `$_SESSION['role_name']`:
     - **`admin`** &rarr; [`admin/dashboard.php`](file:///home/ugenella/coding/xampp-projects/adsity/admin/dashboard.php)
     - **`instructor`** &rarr; [`instructor/dashboard.php`](file:///home/ugenella/coding/xampp-projects/adsity/instructor/dashboard.php)
     - **`student`** &rarr; [`student/dashboard.php`](file:///home/ugenella/coding/xampp-projects/adsity/student/dashboard.php)
     - **Default fallback** &rarr; [`index.php`](file:///home/ugenella/coding/xampp-projects/adsity/index.php)

---

### Security Safeguards Matrix

| Threat Vector | Mitigation Strategy | Implementation |
| :--- | :--- | :--- |
| **SQL Injection (SQLi)** | Prepared Statements & Parameter Binding | PDO `prepare()` and `bindValue(':email', ...)` |
| **Credential Cracking** | One-way Cryptographic Hashing | `password_hash()` and `password_verify()` with `PASSWORD_DEFAULT` |
| **Memory Dump Leakage** | Explicit Credential Unsetting | `unset($user['password'], $inputPassword, $_POST['password'])` |
| **User Account Enumeration** | Unified Error Messages | `"Invalid email or password."` for all authentication failures |
| **Privilege Escalation** | Role-Based Access Control (RBAC) | Strict session validation (`role_name`) guarding each dashboard route |
| **Cross-Site Scripting (XSS)** | Output Encoding | HTML escaping via `htmlspecialchars()` on all dynamic alert notices |

---

## 7. Input Validation Architecture

Located in [`validation.php`](file:///home/ugenella/coding/xampp-projects/adsity/validation.php):

- `validateRequired(string $value, string $label): ?string` &mdash; Ensures fields are non-empty.
- `validateEmailFormat(string $value): ?string` &mdash; Validates standard email structure with `FILTER_VALIDATE_EMAIL`.
- `validateMinLength(string $value, string $label, int $min): ?string` &mdash; Enforces minimum string length.
- `validatePasswordMatch(string $password, string $confirmPassword): ?string` &mdash; Ensures confirmation passwords match.
- `validateTerms(bool $termsAccepted): ?string` &mdash; Ensures Terms of Service agreement checkbox is checked.
- `validateSignupInput(array $post): array` &mdash; Aggregates student signup validation errors and sanitized data.
- `validateLoginInput(array $post): array` &mdash; Aggregates login input validation.
- `validateTeacherSignupInput(array $post): array` &mdash; Aggregates instructor registration validation (delegates to `validateSignupInput` with `role_id = 2`).

---

## 8. Setup & Local Testing Guide

### Prerequisites
- XAMPP / LAMPP installed on Linux.
- Apache running on Port `81`.
- MariaDB running on Port `3307` (or socket `/opt/lampp/var/mysql/mysql.sock`).

### Database Initialization
Import the database schema and default seeds:
```bash
/opt/lampp/bin/mysql -u root -S /opt/lampp/var/mysql/mysql.sock < database/schema.sql
```

### Pre-configured Administrator Account

| Role | Email | Password | Target Dashboard |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@adsity.org` | `admin123` | `http://localhost:81/projects/adsity/admin/dashboard.php` |

> [!NOTE]
> Instructors can be registered at [`teach.php`](file:///home/ugenella/coding/xampp-projects/adsity/teach.php) and Students can be registered at [`signup.php`](file:///home/ugenella/coding/xampp-projects/adsity/signup.php). All new passwords will be automatically hashed with `PASSWORD_DEFAULT`.

---

## 9. Summary of Vector Assets

All vector icons are organized as standalone SVGs inside [`assets/icons/`](file:///home/ugenella/coding/xampp-projects/adsity/assets/icons/):

| Icon File | Usage |
| :--- | :--- |
| `alert-circle.svg` | Error alert banners across forms and views |
| `arrow-left.svg` | Back to Home and return navigation links |
| `arrow-right.svg` | Submit form buttons and action triggers |
| `award.svg` | Verified certificates badges and credential cards |
| `book-open.svg` | Course catalog, lessons, and in-progress learning icons |
| `box.svg` | Docker container skill badge |
| `check-circle.svg` | Success alerts and completed course indicators |
| `clock.svg` | In-progress time/learning indicators |
| `cloud.svg` | Cloud computing & AWS skill badge |
| `code.svg` | Programming languages (Python, JS, React) skill badges |
| `download.svg` | Download and print certificate actions |
| `eye.svg` | View credential action buttons |
| `globe.svg` | Language and localization selector in navbar |
| `graduation-cap.svg`| Instructor badges and teacher portal identifiers |
| `layout.svg` | UI/UX design skill badge |
| `link.svg` | Blockchain skill badge |
| `lock.svg` | Locked final project deliverable indicator |
| `play.svg` | Continue learning video module button |
| `shield-check.svg` | 100% Free ad-supported verification badge |
| `shield.svg` | Cybersecurity skill badge |
| `trash.svg` | Delete user action buttons in Admin panel |
| `user-check.svg` | Student KPI indicators in Admin dashboard |
| `users.svg` | Total users KPI card in Admin dashboard |
