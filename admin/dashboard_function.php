<?php

session_start();

// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';

// Protect Admin Panel: Must be logged in as admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role_name'] ?? '') !== 'admin') {
    header('Location: ../login.php?status=error&message=' . urlencode('Please log in with an administrator account.'));
    exit;
}

require_once __DIR__ . '/../database/config.php';

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

try {
    $pdo = getConnection();

    // Fetch all students (role_id = 3)
    $stmtStudents = $pdo->prepare("SELECT u.id, u.full_name, u.email, u.created_at, r.name AS role_name 
                                  FROM users u 
                                  JOIN roles r ON u.role_id = r.id 
                                  WHERE u.role_id = 3 
                                  ORDER BY u.id DESC");
    $stmtStudents->execute();
    $students = $stmtStudents->fetchAll();

    // Fetch all instructors (role_id = 2)
    $stmtTeachers = $pdo->prepare("SELECT u.id, u.full_name, u.email, u.created_at, r.name AS role_name 
                                  FROM users u 
                                  JOIN roles r ON u.role_id = r.id 
                                  WHERE u.role_id = 2 
                                  ORDER BY u.id DESC");
    $stmtTeachers->execute();
    $instructors = $stmtTeachers->fetchAll();

    $totalStudents    = count($students);
    $totalInstructors = count($instructors);
    $totalUsers       = $totalStudents + $totalInstructors;

    // Fetch all Payout / Withdrawal Requests
    $sqlPayouts = "SELECT 
                      p.id,
                      p.instructor_id,
                      p.amount,
                      p.payout_method,
                      p.payout_details,
                      p.instructor_notes,
                      p.admin_notes,
                      p.transaction_reference,
                      p.status,
                      p.created_at,
                      p.processed_at,
                      u.full_name AS instructor_name,
                      u.email AS instructor_email,
                      COALESCE(w.available_balance, 0.00) AS instructor_balance,
                      admin_u.full_name AS processed_by_name
                   FROM payout_requests p
                   JOIN users u ON p.instructor_id = u.id
                   LEFT JOIN instructor_wallets w ON w.instructor_id = p.instructor_id
                   LEFT JOIN users admin_u ON p.processed_by = admin_u.id
                   ORDER BY 
                       CASE WHEN p.status = 'pending' THEN 1 ELSE 2 END,
                       p.created_at DESC";
    $stmtPayouts = $pdo->prepare($sqlPayouts);
    $stmtPayouts->execute();
    $payoutRequests = $stmtPayouts->fetchAll();

    $pendingPayoutCount  = 0;
    $pendingPayoutAmount = 0.00;
    $totalDisbursed      = 0.00;

    foreach ($payoutRequests as $pr) {
        if ($pr['status'] === 'pending') {
            $pendingPayoutCount++;
            $pendingPayoutAmount += (float)$pr['amount'];
        } elseif ($pr['status'] === 'completed') {
            $totalDisbursed += (float)$pr['amount'];
        }
    }

} catch (PDOException $e) {
    $students            = [];
    $instructors         = [];
    $payoutRequests      = [];
    $totalStudents       = 0;
    $totalInstructors    = 0;
    $totalUsers          = 0;
    $pendingPayoutCount  = 0;
    $pendingPayoutAmount = 0.00;
    $totalDisbursed      = 0.00;
    $status              = 'error';
    $message             = $e->getMessage();
}
