<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protect Instructor Portal: Must be logged in as instructor
if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'instructor') {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in with an instructor account.'));
    exit;
}

require_once __DIR__ . '/../database/config.php';

$instructorId = (int)$_SESSION['user_id'];
$redirectTarget = 'dashboard.php?status=error&message=';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: ' . $redirectTarget . urlencode('Invalid request method.') . '#revenue');
    exit;
}

$amount       = isset($_POST['amount']) ? round((float)$_POST['amount'], 2) : 0.00;
$payoutMethod = trim($_POST['payout_method'] ?? '');
$notes        = trim($_POST['instructor_notes'] ?? '');

// 1. Validate Minimum Withdrawal Amount ($5.00)
if ($amount < 5.00) {
    header('Location: ' . $redirectTarget . urlencode('Minimum withdrawal amount is $5.00.') . '#revenue');
    exit;
}

// 2. Validate Payout Method & Details
if (!in_array($payoutMethod, ['paypal', 'gcash', 'bank_transfer'], true)) {
    header('Location: ' . $redirectTarget . urlencode('Please select a valid payout method.') . '#revenue');
    exit;
}

$payoutDetails = [];

if ($payoutMethod === 'paypal') {
    $paypalEmail = trim($_POST['paypal_email'] ?? '');
    if (empty($paypalEmail) || !filter_var($paypalEmail, FILTER_VALIDATE_EMAIL)) {
        header('Location: ' . $redirectTarget . urlencode('Please provide a valid PayPal email address.') . '#revenue');
        exit;
    }
    $payoutDetails = [
        'email' => $paypalEmail
    ];

} elseif ($payoutMethod === 'gcash') {
    $gcashName   = trim($_POST['gcash_name'] ?? '');
    $gcashNumber = trim($_POST['gcash_number'] ?? '');

    if (empty($gcashName)) {
        header('Location: ' . $redirectTarget . urlencode('Please provide the GCash account holder name.') . '#revenue');
        exit;
    }

    // Clean whitespace/dashes
    $cleanNumber = preg_replace('/[^0-9+]/', '', $gcashNumber);
    if (!preg_match('/^(09|\+639)\d{9}$/', $cleanNumber)) {
        header('Location: ' . $redirectTarget . urlencode('Please provide a valid 11-digit GCash mobile number (e.g., 09171234567).') . '#revenue');
        exit;
    }

    $payoutDetails = [
        'account_name'  => $gcashName,
        'mobile_number' => $cleanNumber
    ];

} elseif ($payoutMethod === 'bank_transfer') {
    $bankName      = trim($_POST['bank_name'] ?? '');
    $accountName   = trim($_POST['bank_account_name'] ?? '');
    $accountNumber = trim($_POST['bank_account_number'] ?? '');

    if (empty($bankName) || empty($accountName) || empty($accountNumber)) {
        header('Location: ' . $redirectTarget . urlencode('Please fill in all bank transfer fields (Bank Name, Account Name, Account Number).') . '#revenue');
        exit;
    }

    $payoutDetails = [
        'bank_name'      => $bankName,
        'account_name'   => $accountName,
        'account_number' => $accountNumber
    ];
}

$detailsJson = json_encode($payoutDetails);

try {
    $pdo = getConnection();
    $pdo->beginTransaction();

    // 3. Check wallet balance with row-level lock
    $stmtWallet = $pdo->prepare("SELECT available_balance FROM instructor_wallets WHERE instructor_id = :iid FOR UPDATE");
    $stmtWallet->execute([':iid' => $instructorId]);
    $wallet = $stmtWallet->fetch();

    $availableBalance = $wallet ? (float)$wallet['available_balance'] : 0.00;

    if ($amount > $availableBalance) {
        $pdo->rollBack();
        header('Location: ' . $redirectTarget . urlencode("Requested amount ($" . number_format($amount, 2) . ") exceeds your available balance ($" . number_format($availableBalance, 2) . ").") . '#revenue');
        exit;
    }

    // 4. Deduct requested amount from available_balance (lock into pending withdrawal)
    $stmtDeduct = $pdo->prepare("UPDATE instructor_wallets 
                                 SET available_balance = available_balance - :amount 
                                 WHERE instructor_id = :iid");
    $stmtDeduct->execute([
        ':amount' => $amount,
        ':iid'    => $instructorId
    ]);

    // 5. Insert new payout request record
    $stmtRequest = $pdo->prepare("INSERT INTO payout_requests 
                                  (instructor_id, amount, payout_method, payout_details, instructor_notes, status, created_at)
                                  VALUES (:iid, :amount, :method, :details, :notes, 'pending', CURRENT_TIMESTAMP)");
    $stmtRequest->execute([
        ':iid'     => $instructorId,
        ':amount'  => $amount,
        ':method'  => $payoutMethod,
        ':details' => $detailsJson,
        ':notes'   => !empty($notes) ? htmlspecialchars($notes) : null
    ]);

    $pdo->commit();

    header('Location: dashboard.php?status=success&message=' . urlencode("Payout request of $" . number_format($amount, 2) . " submitted successfully! Your cash-out is now pending admin approval.") . '#revenue');
    exit;

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: ' . $redirectTarget . urlencode('Database error: ' . $e->getMessage()) . '#revenue');
    exit;
}
