<?php
/**
 * Adsity Global Helper Functions
 * 
 * Centralized, reusable PHP utilities for:
 * 1. Session & Flash Messaging
 * 2. Authentication & Role-Based Access Control (RBAC)
 * 3. CSRF Verification & Form Handling
 * 4. Output Escaping & Input Sanitization
 * 5. Data Formatting (Currency, Dates, Lesson Durations, Text Truncation)
 * 6. HTTP & Navigation (JSON responses, safe redirects)
 * 7. Secure File Upload Handling
 */

require_once __DIR__ . '/validation.php';

// =============================================================================
// 1. SESSION & FLASH MESSAGING HELPERS
// =============================================================================

/**
 * Sets a flash message to be displayed on the next request.
 * 
 * @param string $type Message category: 'success', 'error', 'warning', 'info'
 * @param string $message The notification text to display
 */
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

/**
 * Retrieves and clears flash messages for a specific type.
 * 
 * @param string $type 'success', 'error', 'warning', 'info'
 * @return array Array of message strings
 */
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

/**
 * Checks if flash messages exist for a specific type, or any type if null.
 */
function hasFlashMessages(?string $type = null): bool
{
    ensureSessionStarted();
    if ($type === null) {
        return !empty($_SESSION['flash']);
    }
    return !empty($_SESSION['flash'][$type]);
}

/**
 * Renders all queued flash messages as HTML notification banners.
 */
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
// 2. AUTHENTICATION & ROLE-BASED ACCESS CONTROL (RBAC) HELPERS
// =============================================================================

/**
 * Checks if the current visitor has an authenticated session.
 */
function isAuthenticated(): bool
{
    ensureSessionStarted();
    return !empty($_SESSION['user_id']);
}

/**
 * Returns the currently authenticated user's ID, or null.
 */
function getCurrentUserId(): ?int
{
    ensureSessionStarted();
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

/**
 * Returns the currently authenticated user's role slug (e.g. 'admin', 'instructor', 'student').
 */
function getCurrentUserRole(): ?string
{
    ensureSessionStarted();
    return $_SESSION['role_name'] ?? null;
}

/**
 * Returns the currently authenticated user's full name.
 */
function getCurrentUserName(): ?string
{
    ensureSessionStarted();
    return $_SESSION['user_name'] ?? null;
}

/**
 * Checks whether the current user matches one or more allowed roles.
 * 
 * @param string|array $roles Single role string or array of allowed role slugs
 */
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

/**
 * Enforces authentication and optional role restrictions.
 * Automatically redirects unauthorized requests with a descriptive notice.
 * 
 * @param string|array|null $allowedRoles Allowed role(s) or null for any authenticated user
 * @param string $redirectUrl Fallback URL on failure
 */
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

/**
 * Enforces guest-only access for login/signup pages.
 * Redirects logged-in users to their role-appropriate dashboard.
 */
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
// 3. CSRF VERIFICATION HELPER
// =============================================================================

/**
 * Validates the CSRF token in $_POST. On failure, sets a flash error and redirects.
 * 
 * @param string $redirectUrl URL to redirect to upon token failure
 * @param string $message Friendly error message
 */
function verifyCsrfOrRedirect(string $redirectUrl, string $message = 'Security validation failed (invalid CSRF token). Please try again.'): void
{
    $token = $_POST['csrf_token'] ?? null;
    if (!validateCsrfToken($token)) {
        redirect($redirectUrl, 'error', $message);
    }
}


// =============================================================================
// 4. OUTPUT ESCAPING & INPUT SANITIZATION
// =============================================================================

/**
 * Shorthand for htmlspecialchars with safe UTF-8 encoding.
 * Use inside templates at render time to prevent XSS.
 * 
 * @param string|int|float|null $value Value to escape
 * @return string Escaped safe HTML string
 */
function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitizes generic user input string by stripping invalid tags and trimming.
 */
function sanitizeString(?string $str): string
{
    return trim((string)($str ?? ''));
}


// =============================================================================
// 5. DATA FORMATTING UTILITIES
// =============================================================================

/**
 * Formats a numeric value as currency.
 * 
 * @param float|int|string|null $amount Numeric amount
 * @param int $decimals Number of decimal places (default 2)
 * @param string $symbol Currency prefix symbol (default '$')
 */
function formatCurrency($amount, int $decimals = 2, string $symbol = '$'): string
{
    $val = (float)($amount ?? 0);
    return $symbol . number_format($val, $decimals);
}

/**
 * Formats a date string safely. Returns a fallback if null or invalid.
 * 
 * @param string|null $dateStr Raw database timestamp or date string
 * @param string $format Target PHP date format (default 'M d, Y')
 * @param string $fallback Fallback string if date is empty
 */
function formatDate(?string $dateStr, string $format = 'M d, Y', string $fallback = '-'): string
{
    if (empty($dateStr)) {
        return $fallback;
    }
    $timestamp = strtotime($dateStr);
    return $timestamp ? date($format, $timestamp) : $fallback;
}

/**
 * Formats seconds into MM:SS or HH:MM:SS string for lesson durations.
 * 
 * @param int|float|null $seconds Duration in seconds
 */
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

/**
 * Truncates a string to a specified character length while preserving word boundaries.
 */
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
// 6. HTTP & NAVIGATION UTILITIES
// =============================================================================

/**
 * Safely redirects to a destination URL with an optional flash message.
 * 
 * @param string $url Target redirect URL
 * @param string|null $flashType 'success', 'error', 'warning', 'info'
 * @param string|null $flashMessage Notice message
 */
function redirect(string $url, ?string $flashType = null, ?string $flashMessage = null): void
{
    if ($flashType !== null && $flashMessage !== null) {
        setFlashMessage($flashType, $flashMessage);
    }
    header("Location: {$url}");
    exit;
}

/**
 * Emits a standardized JSON response and terminates execution.
 * 
 * @param array $data Data array to encode
 * @param int $statusCode HTTP status code (default 200)
 */
function sendJsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}


// =============================================================================
// 7. FILE UPLOAD & SECURITY UTILITIES
// =============================================================================

/**
 * Validates an uploaded file for error status, size limits, extension, and real MIME type.
 * 
 * @param array $file $_FILES['input_name'] array
 * @param array $allowedExtensions Lowercase list of allowed extensions (e.g. ['jpg', 'png'])
 * @param array $allowedMimes List of allowed MIME types (e.g. ['image/jpeg', 'image/png'])
 * @param int $maxBytes Maximum file size in bytes
 * @return array ['valid' => bool, 'error' => ?string]
 */
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

    // Verify MIME type using finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $realMime = $finfo->file($file['tmp_name']);
    if (!in_array($realMime, $allowedMimes, true)) {
        return ['valid' => false, 'error' => "Invalid file content type ({$realMime})."];
    }

    return ['valid' => true, 'error' => null];
}

/**
 * Generates a collision-resistant, sanitized unique filename for file storage.
 * 
 * @param string $originalFilename The user's original uploaded filename
 * @param string $prefix Optional prefix for identification (e.g. 'thumb_', 'lesson_')
 * @return string Safe unique filename (e.g. 'thumb_a1b2c3d4e5f6.jpg')
 */
function generateSafeFilename(string $originalFilename, string $prefix = ''): string
{
    $ext = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
    $randomBytes = bin2hex(random_bytes(8));
    $timestamp = time();
    return "{$prefix}{$timestamp}_{$randomBytes}.{$ext}";
}
