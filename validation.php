<?php
/**
 * Adsity Unified Validation, CSRF, & Auth Library
 */

// =============================================================================
// 1. FORM INPUT VALIDATION (Signup, Login, Teacher)
// =============================================================================

function validateRequired(string $value, string $label): ?string
{
    return trim($value) === '' ? "{$label} is required." : null;
}

function validateEmailFormat(string $value): ?string
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : "Enter a valid email address.";
}

function validateMinLength(string $value, string $label, int $min): ?string
{
    return strlen($value) < $min ? "{$label} must be at least {$min} characters long." : null;
}

function validatePasswordMatch(string $password, string $confirmPassword): ?string
{
    return $password !== $confirmPassword ? "Passwords do not match." : null;
}

function validatePassword(string $password, int $minLength = 8): ?string
{
    if (strlen($password) < $minLength) return "Password must be at least {$minLength} characters long.";
    if (!preg_match('/[A-Z]/', $password)) return "Password must contain at least one uppercase letter (A-Z).";
    if (!preg_match('/[a-z]/', $password)) return "Password must contain at least one lowercase letter (a-z).";
    if (!preg_match('/[0-9]/', $password)) return "Password must contain at least one number (0-9).";
    if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) return "Password must contain at least one special character.";
    return null;
}

function validateTerms(bool $termsAccepted): ?string
{
    return !$termsAccepted ? "You must agree to the Terms of Service to sign up." : null;
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

    return [
        'errors' => array_values($errors),
        'data'   => ['full_name' => $fullName, 'email' => $email],
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

    return [
        'errors' => array_values($errors),
        'data'   => ['email' => $email],
    ];
}

function validateTeacherSignupInput(array $post): array
{
    $result = validateSignupInput($post);
    $result['data']['role_id'] = 2;
    return $result;
}

// =============================================================================
// 2. SESSION SECURITY & CSRF PROTECTION
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
    return !empty($_SESSION['csrf_token']) && !empty($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function verifyCsrfOrRedirect(string $redirectUrl, string $message = 'Security validation failed (invalid CSRF token). Please try again.'): void
{
    if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
        redirect($redirectUrl, 'error', $message);
    }
}

// =============================================================================
// 3. AUTHENTICATION & ROLE-BASED ACCESS CONTROL (RBAC)
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
    if (!$currentRole) return false;
    return is_array($roles) ? in_array($currentRole, $roles, true) : $currentRole === $roles;
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
        $target = ($role === 'admin') ? 'admin/dashboard.php' : (($role === 'instructor') ? 'instructor/dashboard.php' : (($role === 'student') ? 'student/dashboard.php' : $defaultRedirect));
        redirect($target);
    }
}

// =============================================================================
// 4. HTTP NAVIGATION, JSON RESPONSES & FLASH MESSAGING
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

function setFlashMessage(string $type, string $message): void
{
    ensureSessionStarted();
    $_SESSION['flash'][$type][] = $message;
}

function getFlashMessages(string $type): array
{
    ensureSessionStarted();
    $messages = $_SESSION['flash'][$type] ?? [];
    unset($_SESSION['flash'][$type]);
    return $messages;
}

function hasFlashMessages(?string $type = null): bool
{
    ensureSessionStarted();
    return $type === null ? !empty($_SESSION['flash']) : !empty($_SESSION['flash'][$type]);
}
