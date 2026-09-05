-- Adsity Database Schema & Seed Data

CREATE DATABASE IF NOT EXISTS adsity;
USE adsity;

-- 1. Roles Table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL
);

-- Seed Predefined Roles
INSERT INTO roles (id, name, description) VALUES
(1, 'admin', 'System Administrator with full management access'),
(2, 'instructor', 'Teacher or Instructor who creates and manages courses'),
(3, 'student', 'Learner who enrolls in courses and earns certificates')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description);

-- 2. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL DEFAULT 3,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_roles FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Seed Default Admin Account (password: admin123 hashed via PASSWORD_DEFAULT)
INSERT INTO users (full_name, email, password, role_id)
SELECT 'Administrator', 'admin@adsity.org', '$2y$10$BLSCQZeQ8xPK0zNeEbwytOoUJV.jxYZxBlLKnbeXltwhg3MOhVRUG', 1
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@adsity.org');

-- 3. Courses Table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    thumbnail VARCHAR(255) NULL,
    total_lessons INT DEFAULT 10,
    instructor_id INT NULL,
    assessment_type ENUM('github_repo', 'file_upload', 'live_url') DEFAULT 'github_repo',
    assessment_instructions TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_courses_instructors FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- 4. Enrollments Table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    progress_percent INT DEFAULT 0,
    status ENUM('in_progress', 'completed') DEFAULT 'in_progress',
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    CONSTRAINT fk_enrollments_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_enrollments_courses FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY uq_user_course (user_id, course_id)
);

-- 5. Lessons Table (Multi-Video Curriculum)
CREATE TABLE IF NOT EXISTS lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    lesson_number INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    video_path VARCHAR(255) NOT NULL,
    duration VARCHAR(20) DEFAULT '10:00',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_lessons_courses FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- 6. Course Submissions Table
CREATE TABLE IF NOT EXISTS course_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    submission_type VARCHAR(50) NOT NULL,
    submission_value VARCHAR(255) NOT NULL,
    notes TEXT NULL,
    status ENUM('approved', 'pending', 'rejected') DEFAULT 'approved',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_submissions_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_submissions_courses FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- 6. Certificates Table
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    certificate_code VARCHAR(50) UNIQUE NOT NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cert_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_cert_courses FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY uq_user_cert_course (user_id, course_id)
);
