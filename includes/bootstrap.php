<?php
// includes/bootstrap.php

$config = require __DIR__ . '/../config.php';

date_default_timezone_set($config['app']['timezone'] ?? 'UTC');

if (session_status() !== PHP_SESSION_ACTIVE) {
    // Satu sesi untuk seluruh aplikasi (front + admin)
    if (!empty($config['admin']['session_name'])) {
        session_name((string)$config['admin']['session_name']);
    }
    // Gunakan cookie sesi yang lebih aman (best-effort)
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/auth.php';
