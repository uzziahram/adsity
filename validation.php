<?php
/**
 * Adsity Validation & Platform Utility Library
 * 
 * Centralized, reusable functions for:
 * 1. Form Input Validation Rules (Signup, Login, Teacher Registration)
 * 2. Session Security & CSRF Token Protection
 * 3. Cross-Request Flash Messaging System
 * 4. Authentication & Role-Based Access Control (RBAC)
 * 5. Context-Aware Output Escaping & Sanitization
 * 6. Data Formatting Utilities (Currency, Dates, Lesson Durations, Text)
 * 7. HTTP & API Navigation (Safe redirects, JSON responses)
 * 8. Secure File Upload Handling
 */

// =============================================================================
// 1. FORM INPUT VALIDATION RULES
// =============================================================================

function validateRequired(string $value, string $label): ?string
{
    return trim($value) === '' ? "$label is required." : null;
}

function validateEmailFormat(string $value): ?string
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : "Enter a valid email address.";
}

function validateMinLength(string $value, string $label, int $min): ?string
{
    return strlen($value) < $min ? "$label must be at least $min characters long." : null;
}

function validatePasswordMatch(string $password, string $confirmPassword): ?string
{
    return $password !== $confirmPassword ? "Passwords do not match." : null;
}

/**
 * Validates password criteria with independent if checks.
 */
function validatePassword(string $password, int $minLength = 8): ?string
{
    if (strlen($password) < $minLength) {
        return "Password must be at least $minLength characters long.";
    }

    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter (A-Z).";
    }

    if (!preg_match('/[a-z]/', $password)) {
        return "Password must contain at least one lowercase letter (a-z).";
    }

    if (!preg_match('/[0-9]/', $password)) {
        return "Password must contain at least one number (0-9).";
    }

    if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
        return "Password must contain at least one special character (e.g. !@#$%^&*).";
    }

    return null;
}

function validateSignupInput(array $post): array
{
    $fullName        = trim($post['full_name'] ?? '');
    $email           = trim($post['email'] ?? '');
    $password        = $post['password'] ?? '';
    $confirmPassword = $post['confirm_password'] ?? '';

    $errors = array_filter([
        validateRequired($fullName, 'Full Name'),
        validateRequired($email, 'Email Address'),
        validateRequired($password, 'Password'),
        $email !== '' ? validateEmailFormat($email) : null,
        $password !== '' ? validatePassword($password, 8) : null,
        $password !== '' ? validatePasswordMatch($password, $confirmPassword) : null,
    ]);
    $errors = array_values($errors);

    return [
        'errors' => $errors,
        'data'   => [
            'full_name' => $fullName,
            'email'     => $email,
        ],
    ];
}

function validateLoginInput(array $post): array
{
    $email    = trim($post['email'] ?? '');
    $password = $post['password'] ?? '';

    $errors = array_filter([
        validateRequired($email, 'Email Address'),
        validateRequired($password, 'Password'),
        $email !== '' ? validateEmailFormat($email) : null,
    ]);
    $errors = array_values($errors);

    return [
        'errors' => $errors,
        'data'   => [
            'email' => $email,
        ],
    ];
}

function validateTeacherSignupInput(array $post): array
{
    $result = validateSignupInput($post);
    $result['data']['role_id'] = 2;
    return $result;
}


// =============================================================================
// 2. SESSION SECURITY & CSRF TOKEN PROTECTION
// =============================================================================

function ensureSessionStarted(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        if (!headers_sent()) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_samesite', 'Lax');
        }
        session_start();
    }
}

function getCsrfToken(): string
{
    ensureSessionStarted();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken(?string $token): bool
{
    ensureSessionStarted();
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function verifyCsrfOrRedirect(string $redirectUrl, string $message = 'Security validation failed (invalid CSRF token). Please try again.'): void
{
    $token = $_POST['csrf_token'] ?? null;
    if (!validateCsrfToken($token)) {
        redirect($redirectUrl, 'error', $message);
    }
}


// =============================================================================
// 3. CROSS-REQUEST FLASH MESSAGING SYSTEM
// =============================================================================

function setFlashMessage(string $type, string $message): void
{
    ensureSessionStarted();
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    if (!isset($_SESSION['flash'][$type])) {
        $_SESSION['flash'][$type] = [];
    }
    $_SESSION['flash'][$type][] = $message;
}

function getFlashMessages(string $type): array
{
    ensureSessionStarted();
    if (isset($_SESSION['flash'][$type])) {
        $messages = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $messages;
    }
    return [];
}

function hasFlashMessages(?string $type = null): bool
{
    ensureSessionStarted();
    if ($type === null) {
        return !empty($_SESSION['flash']);
    }
    return !empty($_SESSION['flash'][$type]);
}

function renderFlashMessages(): string
{
    ensureSessionStarted();
    if (empty($_SESSION['flash'])) {
        return '';
    }

    $html = '';
    $categories = [
        'success' => ['bg' => '#ecfdf5', 'border' => '#a7f3d0', 'color' => '#065f46', 'icon' => '✓'],
        'error'   => ['bg' => '#fef2f2', 'border' => '#fecaca', 'color' => '#991b1b', 'icon' => '✕'],
        'warning' => ['bg' => '#fffbeb', 'border' => '#fde68a', 'color' => '#92400e', 'icon' => '⚠'],
        'info'    => ['bg' => '#eff6ff', 'border' => '#bfdbfe', 'color' => '#1e40af', 'icon' => 'ℹ'],
    ];

    foreach ($_SESSION['flash'] as $type => $messages) {
        $cfg = $categories[$type] ?? $categories['info'];
        foreach ($messages as $msg) {
            $safeMsg = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
            $html .= "<div class=\"adsity-flash-alert adsity-flash-alert--{$type}\" style=\"background-color: {$cfg['bg']}; border: 1px solid {$cfg['border']}; color: {$cfg['color']}; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; font-size: 0.9rem; font-weight: 500;\">";
            $html .= "<strong style=\"font-size: 1rem;\">{$cfg['icon']}</strong>";
            $html .= "<span>{$safeMsg}</span>";
            $html .= "</div>";
        }
    }

    $_SESSION['flash'] = [];
    return $html;
}


// =============================================================================
// 4. AUTHENTICATION & ROLE-BASED ACCESS CONTROL (RBAC) HELPERS
// =============================================================================

function isAuthenticated(): bool
{
    ensureSessionStarted();
    return !empty($_SESSION['user_id']);
}

function getCurrentUserId(): ?int
{
    ensureSessionStarted();
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function getCurrentUserRole(): ?string
{
    ensureSessionStarted();
    return $_SESSION['role_name'] ?? null;
}

function getCurrentUserName(): ?string
{
    ensureSessionStarted();
    return $_SESSION['user_name'] ?? null;
}

function hasRole($roles): bool
{
    $currentRole = getCurrentUserRole();
    if (!$currentRole) {
        return false;
    }

    if (is_array($roles)) {
        return in_array($currentRole, $roles, true);
    }
    return $currentRole === $roles;
}

function requireAuth($allowedRoles = null, string $redirectUrl = '../login.php'): void
{
    ensureSessionStarted();

    if (!isAuthenticated()) {
        redirect($redirectUrl, 'error', 'Please log in to continue.');
    }

    if ($allowedRoles !== null && !hasRole($allowedRoles)) {
        redirect($redirectUrl, 'error', 'Access denied. You do not have permission to view this resource.');
    }
}

function requireGuest(string $defaultRedirect = 'index.php'): void
{
    ensureSessionStarted();

    if (isAuthenticated()) {
        $role = getCurrentUserRole();
        if ($role === 'admin') {
            redirect('admin/dashboard.php');
        } elseif ($role === 'instructor') {
            redirect('instructor/dashboard.php');
        } elseif ($role === 'student') {
            redirect('student/dashboard.php');
        } else {
            redirect($defaultRedirect);
        }
    }
}


// =============================================================================
// 5. OUTPUT ESCAPING & INPUT SANITIZATION
// =============================================================================

function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function sanitizeString(?string $str): string
{
    return trim((string)($str ?? ''));
}


// =============================================================================
// 6. DATA FORMATTING UTILITIES
// =============================================================================

function formatCurrency($amount, int $decimals = 2, string $symbol = '$'): string
{
    $val = (float)($amount ?? 0);
    return $symbol . number_format($val, $decimals);
}

function formatDate(?string $dateStr, string $format = 'M d, Y', string $fallback = '-'): string
{
    if (empty($dateStr)) {
        return $fallback;
    }
    $timestamp = strtotime($dateStr);
    return $timestamp ? date($format, $timestamp) : $fallback;
}

function formatDuration($seconds): string
{
    $sec = (int)($seconds ?? 0);
    if ($sec <= 0) {
        return '0:00';
    }
    $hours = floor($sec / 3600);
    $minutes = floor(($sec % 3600) / 60);
    $remainingSeconds = $sec % 60;

    if ($hours > 0) {
        return sprintf('%d:%02d:%02d', $hours, $minutes, $remainingSeconds);
    }
    return sprintf('%d:%02d', $minutes, $remainingSeconds);
}

function truncateText(string $text, int $limit = 100, string $ellipsis = '...'): string
{
    $clean = trim(strip_tags($text));
    if (mb_strlen($clean) <= $limit) {
        return $clean;
    }
    $truncated = mb_substr($clean, 0, $limit);
    $lastSpace = mb_strrpos($truncated, ' ');
    if ($lastSpace !== false) {
        $truncated = mb_substr($truncated, 0, $lastSpace);
    }
    return $truncated . $ellipsis;
}


// =============================================================================
// 7. HTTP & API UTILITIES
// =============================================================================

function redirect(string $url, ?string $flashType = null, ?string $flashMessage = null): void
{
    if ($flashType !== null && $flashMessage !== null) {
        setFlashMessage($flashType, $flashMessage);
    }
    header("Location: {$url}");
    exit;
}

function sendJsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}


// =============================================================================
// 8. FILE UPLOAD & SECURITY UTILITIES
// =============================================================================

function validateUploadedFile(array $file, array $allowedExtensions, array $allowedMimes, int $maxBytes = 52428800): array
{
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['valid' => false, 'error' => 'Invalid file upload parameters.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE specified in the form.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder for file uploads.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write uploaded file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        ];
        $msg = $uploadErrors[$file['error']] ?? 'An unknown file upload error occurred.';
        return ['valid' => false, 'error' => $msg];
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        return ['valid' => false, 'error' => 'File was not uploaded via a valid HTTP POST request.'];
    }

    if ($file['size'] > $maxBytes) {
        $maxMb = round($maxBytes / (1024 * 1024), 1);
        return ['valid' => false, 'error' => "File size exceeds the allowable limit of {$maxMb}MB."];
    }

    $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions, true)) {
        $allowedStr = implode(', ', $allowedExtensions);
        return ['valid' => false, 'error' => "Invalid file extension (.{$ext}). Allowed: {$allowedStr}."];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $realMime = $finfo->file($file['tmp_name']);
    if (!in_array($realMime, $allowedMimes, true)) {
        return ['valid' => false, 'error' => "Invalid file content type ({$realMime})."];
    }

    return ['valid' => true, 'error' => null];
}

function generateSafeFilename(string $originalFilename, string $prefix = ''): string
{
    $ext = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
    $randomBytes = bin2hex(random_bytes(8));
    $timestamp = time();
    return "{$prefix}{$timestamp}_{$randomBytes}.{$ext}";
}
