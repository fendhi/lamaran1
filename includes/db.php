<?php
// includes/db.php

function db(array $config): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $db = $config['db'];
    $charset = $db['charset'] ?? 'utf8mb4';

    $unixSocket = $db['unix_socket'] ?? null;
    if (!$unixSocket) {
        $candidates = [
            '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock',
            '/opt/lampp/var/mysql/mysql.sock',
        ];
        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                $unixSocket = $candidate;
                break;
            }
        }
    }

    if ($unixSocket) {
        $dsn = sprintf(
            'mysql:unix_socket=%s;dbname=%s;charset=%s',
            $unixSocket,
            $db['name'],
            $charset
        );
    } else {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $db['host'],
            (int)($db['port'] ?? 3306),
            $db['name'],
            $charset
        );
    }

    $pdo = new PDO(
        $dsn,
        $db['user'],
        $db['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $pdo;
}
