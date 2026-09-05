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
├── student/
│   ├── certificate.css          # Dedicated stylesheet for Verified Certificate & Print view
│   ├── certificate.php          # Verified Certificate view (Print / Save as PDF)
│   ├── certificate_function.php # Certificate lookup and verification query handler
│   ├── dashboard.php            # Student Dashboard view (In-Progress, Completed, Certificates)
│   ├── dashboard_function.php   # Student data query handler & session gate (student only)
│   ├── studentdashboard.css     # Dedicated stylesheet for Student Dashboard
│   ├── submit_exam.css          # Dedicated stylesheet for Final Exam / Project submission
│   ├── submit_exam.php          # Final project / exam submission form (GitHub repo / File / Live URL)
│   └── submit_exam_function.php # Project upload & certificate issuance handler
│
├── uploads/
│   ├── instructors/             # Instructor storage folders partitioned by instructor ID
│   │   └── {instructor_id}/
│   │       └── courses/
│   │           └── {course_id}/ # Uploaded lesson MP4 video files
│   └── submissions/             # Uploaded student project exam submissions
│
├── courses.css                  # Dedicated stylesheet for Course Catalog & Category filters
├── courses.php                  # Course Catalog with search keyword & category filters
├── courses_function.php         # Courses query handler with dynamic SQL filters
├── index.php                    # Adsity landing page / homepage
├── login.php                    # User authentication login view
├── login_function.php           # Login validation, credential verification & session initialization
├── logout.php                   # Session destruction & logout redirection handler
├── README.md                    # Detailed project technical documentation & architecture guide
├── signup.php                   # Student registration view
├── signup_function.php          # Student registration handler & auto-login
├── style.css                    # Global application stylesheet & responsive design system
├── teach.php                    # Instructor application & registration view
├── teach_function.php           # Instructor registration handler & session assignment
└── validation.php               # Centralized input validation functions
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
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Course ID |
| `title` | `VARCHAR(150)` | `NOT NULL` | Course Title |
| `description`| `TEXT` | `NULL` | Detailed course overview |
| `category` | `VARCHAR(100)` | `NULL` | Topic category |
| `thumbnail`| `VARCHAR(255)` | `NULL` | Image asset path |
| `total_lessons` | `INT` | `DEFAULT 10` | Total lessons count |
| `created_at`| `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Course creation timestamp |

#### 4. `enrollments` Table
| Column | Type | Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `INT` | `AUTO_INCREMENT, PRIMARY KEY` | Enrollment ID |
| `user_id` | `INT` | `NOT NULL, FK` | References `users(id)` |
| `course_id` | `INT` | `NOT NULL, FK` | References `courses(id)` |
| `progress_percent` | `INT` | `DEFAULT 0` | Completion percentage (0 - 100) |
| `status` | `ENUM` | `'in_progress', 'completed'` | Current study state |
| `enrolled_at` | `TIMESTAMP` | `DEFAULT CURRENT_TIMESTAMP` | Enrollment date |
| `completed_at` | `TIMESTAMP` | `NULL` | Course completion date |

#### 5. `certificates` Table
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

### C. Instructor Studio & Multi-Video Course Publishing
1. Instructor navigates to [`instructor/dashboard.php`](file:///home/ugenella/coding/xampp-projects/adsity/instructor/dashboard.php) and clicks **"Create New Course"** to open [`instructor/create_course.php`](file:///home/ugenella/coding/xampp-projects/adsity/instructor/create_course.php).
2. The instructor enters course details, selects the assessment type (`github_repo`, `file_upload`, or `live_url`), and builds the video curriculum.
3. **Automatic Video Duration Detection**:
   - The instructor is **not** asked to manually choose or type a duration.
   - When a video file (`.mp4`, `.webm`, `.ogg`) is selected, the browser automatically inspects the video file metadata via the HTML5 Video Metadata API and displays a live badge (e.g. `⏱️ Auto-Detected Duration: 08:45`).
   - On submission, [`instructor/create_course_function.php`](file:///home/ugenella/coding/xampp-projects/adsity/instructor/create_course_function.php) executes `ffprobe` to verify the exact duration from the uploaded file and stores it into the `lessons` table.
4. Uploaded videos are organized locally under `uploads/instructors/{instructor_id}/courses/{course_id}/`.

### D. Instructor Application
1. Instructor submits application at [`teach.php`](file:///home/ugenella/coding/xampp-projects/adsity/teach.php).
2. [`teach_function.php`](file:///home/ugenella/coding/xampp-projects/adsity/teach_function.php) validates inputs, registers user with `role_id = 2` (`instructor`), sets session data, and provides success feedback.

### D. Admin User Monitoring & Management
1. Administrator logs in using `admin@adsity.org` / `admin123`.
2. [`admin/dashboard.php`](file:///home/ugenella/coding/xampp-projects/adsity/admin/dashboard.php) verifies session `role_name === 'admin'` and displays:
   - Total user metrics (Students, Teachers, All Users).
   - **Teachers Table:** Lists all instructors with registration dates and a delete action.
   - **Students Table:** Lists all students with registration dates and a delete action.
3. Deletion requests are processed via [`admin/delete_user.php`](file:///home/ugenella/coding/xampp-projects/adsity/admin/delete_user.php), with built-in safeguards protecting administrator accounts from deletion.

### E. Course Catalog & Exploration
1. Users click **"Explore"** on any page or search from the top navigation bar.
2. [`courses.php`](file:///home/ugenella/coding/xampp-projects/adsity/courses.php) renders the courses catalog with:
   - Text search query parsing (`?search=...`).
   - Category filtering pills (`?category=...`).
   - Course metadata (Lessons, ad-supported free badge, and Enrollment CTA).

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
| `play.svg` | Continue learning video module button |
| `shield-check.svg` | 100% Free ad-supported verification badge |
| `shield.svg` | Cybersecurity skill badge |
| `trash.svg` | Delete user action buttons in Admin panel |
| `user-check.svg` | Student KPI indicators in Admin dashboard |
| `users.svg` | Total users KPI card in Admin dashboard |
