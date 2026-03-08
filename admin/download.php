<?php
require __DIR__ . '/../includes/bootstrap.php';

admin_require_login($config);

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo 'Bad Request';
    exit;
}

$row = null;
try {
    $pdo = db($config);
    $stmt = $pdo->prepare('SELECT cv_original_name, cv_stored_name, cv_mime FROM applicants WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Database error';
    exit;
}

if (!$row) {
    http_response_code(404);
    echo 'Not Found';
    exit;
}

$dir = (string)($config['upload']['cv_dir'] ?? '');
$stored = basename((string)$row['cv_stored_name']);
$path = rtrim($dir, '/') . '/' . $stored;

$realDir = realpath($dir);
$realPath = realpath($path);

if (!$realDir || !$realPath || strncmp($realPath, $realDir, strlen($realDir)) !== 0 || !is_file($realPath)) {
    http_response_code(404);
    echo 'File Not Found';
    exit;
}

$mime = (string)($row['cv_mime'] ?? 'application/octet-stream');
$downloadName = (string)($row['cv_original_name'] ?? 'cv');

$inline = !empty($_GET['inline']);

header('Content-Type: ' . $mime);
header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . str_replace('"', '', $downloadName) . '"');
header('Content-Length: ' . filesize($realPath));

readfile($realPath);
exit;
