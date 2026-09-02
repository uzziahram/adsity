<?php

require_once __DIR__ . '/../database/config.php';

$code = $_GET['code'] ?? null;
$certificate = null;

if ($code) {
    try {
        $pdo = getConnection();
        $sql = "SELECT 
                    cert.id,
                    cert.certificate_code,
                    cert.issued_at,
                    u.full_name AS student_name,
                    c.title AS course_title,
                    c.category
                FROM certificates cert
                JOIN users u ON cert.user_id = u.id
                JOIN courses c ON cert.course_id = c.id
                WHERE cert.certificate_code = :code
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':code', $code);
        $stmt->execute();
        $certificate = $stmt->fetch();
    } catch (PDOException $e) {
        $certificate = null;
    }
}
