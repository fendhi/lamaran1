<?php
require __DIR__ . '/../includes/bootstrap.php';

admin_require_login($config);

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect(url_for($config, '/admin/index.php'));
}


$a = null;
$dbError = null;
try {
  $pdo = db($config);
  $stmt = $pdo->prepare('SELECT * FROM applicants WHERE id = :id');
  $stmt->execute([':id' => $id]);
  $a = $stmt->fetch();
} catch (Throwable $e) {
  $dbError = $e->getMessage();
}

if ($dbError) {
  http_response_code(500);
  echo 'Database error: ' . htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8');
  exit;
}

if (!$a) {
    redirect(url_for($config, '/admin/index.php'));
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Detail Pelamar</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
  <header class="bg-white border-b">
    <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between">
      <a class="font-semibold" href="<?= e(url_for($config, '/admin/index.php')) ?>">← Dashboard</a>
      <a class="text-sm text-gray-600 hover:text-gray-900" href="<?= e(url_for($config, '/admin/logout.php')) ?>">Logout</a>
    </div>
  </header>

  <main class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white border rounded-xl p-6">
      <div class="flex items-start justify-between gap-4">
        <div>
          <h1 class="text-xl font-bold"><?= e($a['full_name']) ?></h1>
          <p class="mt-1 text-sm text-gray-600"><?= e($a['position_applied']) ?> • <?= e($a['created_at']) ?></p>
        </div>
        <div class="flex items-center gap-2">
          <a class="rounded-md border px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-50" target="_blank" rel="noopener" href="<?= e(url_for($config, '/admin/download.php?id=' . (int)$a['id'] . '&inline=1')) ?>">Buka CV</a>
          <a class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700" href="<?= e(url_for($config, '/admin/download.php?id=' . (int)$a['id'])) ?>">Download CV</a>
        </div>
      </div>

      <div class="mt-6 grid gap-4 md:grid-cols-2 text-sm">
        <div class="rounded-lg border bg-gray-50 p-4">
          <div class="text-gray-600">Email</div>
          <div class="font-medium text-gray-900 mt-1"><?= e($a['email']) ?></div>
        </div>
        <div class="rounded-lg border bg-gray-50 p-4">
          <div class="text-gray-600">No. HP</div>
          <div class="font-medium text-gray-900 mt-1"><?= e($a['phone']) ?></div>
        </div>
        <div class="rounded-lg border bg-gray-50 p-4 md:col-span-2">
          <div class="text-gray-600">Alamat</div>
          <div class="font-medium text-gray-900 mt-1"><?= e($a['address'] ?? '-') ?></div>
        </div>
      </div>

      <div class="mt-6">
        <div class="text-sm font-semibold">Cover Letter</div>
        <div class="mt-2 whitespace-pre-wrap rounded-lg border bg-white p-4 text-sm text-gray-800"><?= e($a['cover_letter'] ?? '-') ?></div>
      </div>

      <div class="mt-6">
        <div class="text-sm font-semibold">CV</div>
        <div class="mt-2 text-sm text-gray-700">
          <div class="rounded-lg border bg-gray-50 p-4">
            <div class="font-medium text-gray-900"><?= e($a['cv_original_name'] ?? '-') ?></div>
            <div class="mt-1 text-xs text-gray-600"><?= e($a['cv_mime'] ?? '-') ?> • <?= e((string)($a['cv_size'] ?? '0')) ?> bytes</div>
          </div>
        </div>

        <?php
          $mime = (string)($a['cv_mime'] ?? '');
          $isPdf = stripos($mime, 'application/pdf') === 0;
        ?>

        <?php if ($isPdf): ?>
          <div class="mt-4">
            <iframe
              class="w-full h-[70vh] rounded-lg border bg-white"
              src="<?= e(url_for($config, '/admin/download.php?id=' . (int)$a['id'] . '&inline=1')) ?>"
              title="Preview CV"
            ></iframe>
          </div>
        <?php else: ?>
          <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            Preview hanya tersedia untuk file PDF. Silakan klik <b>Download CV</b> untuk membuka file ini.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</body>
</html>
