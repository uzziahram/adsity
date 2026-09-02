<?php

function getConnection(): PDO
{
    $host     = '127.0.0.1';
    $port     = '3307';
    $dbname   = 'adsity';
    $username = 'root';
    $password = '';
    $socket   = '/opt/lampp/var/mysql/mysql.sock';

    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        $dsn = "mysql:unix_socket={$socket};dbname={$dbname};charset=utf8mb4";
        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
}
