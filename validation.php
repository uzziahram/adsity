<?php

require_once __DIR__ . '/helpers.php';

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
 * You can comment out any individual if statement below if you do not want to enforce that specific check.
 */
function validatePassword(string $password, int $minLength = 8): ?string
{
    // 1. Minimum length check (comment out if not needed)
    if (strlen($password) < $minLength) {
        return "Password must be at least $minLength characters long.";
    }

    // 2. Uppercase letter check (comment out if not needed)
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter (A-Z).";
    }

    // 3. Lowercase letter check (comment out if not needed)
    if (!preg_match('/[a-z]/', $password)) {
        return "Password must contain at least one lowercase letter (a-z).";
    }

    // 4. Number check (comment out if not needed)
    if (!preg_match('/[0-9]/', $password)) {
        return "Password must contain at least one number (0-9).";
    }

    // 5. Special character check (comment out if not needed)
    if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
        return "Password must contain at least one special character (e.g. !@#$%^&*).";
    }

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

/**
 * Session Security & CSRF Protection Utilities
 */
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

