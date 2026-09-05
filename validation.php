<?php

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
    $terms           = isset($post['terms']);

    $errors = array_filter([
        validateRequired($fullName, 'Full Name'),
        validateRequired($email, 'Email Address'),
        validateRequired($password, 'Password'),
        $email !== '' ? validateEmailFormat($email) : null,
        $password !== '' ? validateMinLength($password, 'Password', 6) : null,
        $password !== '' ? validatePasswordMatch($password, $confirmPassword) : null,
        validateTerms($terms),
    ]);
    $errors = array_values($errors);

    if (empty($errors)) {
        $fullName = htmlspecialchars($fullName);
    }

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

