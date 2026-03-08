<?php
// config.php
// Sesuaikan nilai di bawah ini dengan environment XAMPP Anda.

return [
    'app' => [
        'name' => 'PT Angksa Pura 1',
        'timezone' => 'Asia/Jakarta',
        // Ganti dengan URL folder proyek Anda jika berbeda.
        // Contoh: http://localhost/lamaran
        'base_url' => 'http://localhost/lamaran1',
    ],

    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'lamaran',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],

    // Email tujuan (HR / Recruiter)
    'mail' => [
        'to' => 'fendimaulam9@gmail.com',
        // Dari (From) untuk fungsi mail() (butuh konfigurasi sendmail di server)
        'from_email' => 'no-reply@localhost',
        'from_name' => 'Website Lamaran',
        // Jika true, email pelamar akan di-CC
        'cc_applicant' => false,
    ],

    // Login admin sederhana untuk dashboard
    // Username: admin
    // Password default: admin123 (silakan ganti)
    // Untuk mengganti, generate hash baru via: php -r "echo password_hash('PASSWORD_BARU', PASSWORD_DEFAULT), PHP_EOL;"
    'admin' => [
        'username' => 'admin',
        'password_hash' => '$2y$12$D.oGDc5oK8MngjBff1O87uUkK6Oy7Q7/q4qTxT24jWz2N8BSRgl0.',
        'session_name' => 'lamaran_admin',
    ],

    'upload' => [
        // Max 5MB
        'max_bytes' => 5 * 1024 * 1024,
        'allowed_ext' => ['pdf', 'doc', 'docx'],
        'allowed_mime' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        // Path relatif dari project root
        'cv_dir' => __DIR__ . '/uploads/cv',
    ],
];
