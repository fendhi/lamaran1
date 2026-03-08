<?php
require __DIR__ . '/../includes/bootstrap.php';

admin_require_login($config);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(url_for($config, '/admin/index.php'));
}

if (!csrf_verify($_POST['_csrf'] ?? null)) {
    redirect(url_for($config, '/admin/index.php'));
}

$id = (int)($_POST['id'] ?? 0);
$p = (int)($_POST['p'] ?? 1);
if ($p < 1) {
    $p = 1;
}

if ($id < 1) {
    redirect(url_for($config, '/admin/index.php?p=' . $p));
}

try {
    $pdo = db($config);
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('SELECT cv_stored_name FROM applicants WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    if (!$row) {
        $pdo->rollBack();
        redirect(url_for($config, '/admin/index.php?p=' . $p));
    }

    $del = $pdo->prepare('DELETE FROM applicants WHERE id = :id');
    $del->execute([':id' => $id]);

    $pdo->commit();

    $stored = (string)($row['cv_stored_name'] ?? '');
    $stored = basename($stored);
    $cvDir = (string)($config['upload']['cv_dir'] ?? '');
    if ($stored !== '' && $cvDir !== '') {
        $path = rtrim($cvDir, '/') . '/' . $stored;
        if (is_file($path)) {
            @unlink($path);
        }
    }
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
}

redirect(url_for($config, '/admin/index.php?p=' . $p));
