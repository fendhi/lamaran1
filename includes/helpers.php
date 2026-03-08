<?php
// includes/helpers.php

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function app_detect_base_path(): string
{
    // Detect project folder path relative to web root (e.g. /lamaran)
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? null;
    if (!is_string($docRoot) || $docRoot === '') {
        return '';
    }

    $docRootReal = realpath($docRoot);
    $projectRootReal = realpath(__DIR__ . '/..');

    if (!$docRootReal || !$projectRootReal) {
        return '';
    }

    $docRootReal = rtrim(str_replace('\\', '/', $docRootReal), '/');
    $projectRootReal = rtrim(str_replace('\\', '/', $projectRootReal), '/');

    if (strncmp($projectRootReal, $docRootReal, strlen($docRootReal)) !== 0) {
        return '';
    }

    $rel = substr($projectRootReal, strlen($docRootReal));
    $rel = '/' . ltrim($rel, '/');
    return rtrim($rel, '/');
}

function app_detect_base_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = app_detect_base_path();

    return $scheme . '://' . $host . $basePath;
}

function url_for(array $config, string $path): string
{
    $base = (string)($config['app']['base_url'] ?? '');
    $base = trim($base);
    if ($base === '') {
        $base = app_detect_base_url();
    }
    $base = rtrim($base, '/');
    $path = '/' . ltrim($path, '/');
    return $base . $path;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['_csrf'];
}

function csrf_verify(?string $token): bool
{
    return isset($_SESSION['_csrf']) && is_string($token) && hash_equals($_SESSION['_csrf'], $token);
}

function flash_set(string $key, string $message): void
{
    $_SESSION['_flash'][$key] = $message;
}

function flash_get(string $key): ?string
{
    if (!isset($_SESSION['_flash'][$key])) {
        return null;
    }
    $msg = $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);
    return is_string($msg) ? $msg : null;
}

function normalize_phone(?string $phone): string
{
    $phone = trim((string)$phone);
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    return $phone ?? '';
}

function upload_cv(array $config, array $file): array
{
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Upload CV tidak valid.');
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $map = [
            UPLOAD_ERR_INI_SIZE => 'Ukuran file terlalu besar (ini).',
            UPLOAD_ERR_FORM_SIZE => 'Ukuran file terlalu besar.',
            UPLOAD_ERR_PARTIAL => 'Upload tidak selesai.',
            UPLOAD_ERR_NO_FILE => 'CV wajib di-upload.',
        ];
        throw new RuntimeException($map[$file['error']] ?? 'Gagal upload CV.');
    }

    if (($file['size'] ?? 0) > ($config['upload']['max_bytes'] ?? 0)) {
        throw new RuntimeException('Ukuran CV maksimal 5MB.');
    }

    $originalName = (string)($file['name'] ?? 'cv');
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    $allowedExt = $config['upload']['allowed_ext'] ?? [];
    if (!in_array($ext, $allowedExt, true)) {
        throw new RuntimeException('Format CV harus: ' . implode(', ', $allowedExt));
    }

    $tmp = (string)($file['tmp_name'] ?? '');
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp) ?: 'application/octet-stream';

    $allowedMime = $config['upload']['allowed_mime'] ?? [];
    if (!in_array($mime, $allowedMime, true)) {
        throw new RuntimeException('Tipe file CV tidak didukung.');
    }

    $dir = (string)($config['upload']['cv_dir'] ?? '');
    if ($dir === '' || !is_dir($dir)) {
        throw new RuntimeException('Folder upload tidak ditemukan.');
    }

    $stored = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = rtrim($dir, '/') . '/' . $stored;

    if (!move_uploaded_file($tmp, $dest)) {
        throw new RuntimeException('Gagal menyimpan file CV.');
    }

    return [
        'original_name' => $originalName,
        'stored_name' => $stored,
        'mime' => $mime,
        'size' => (int)$file['size'],
        'absolute_path' => $dest,
    ];
}
