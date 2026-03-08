<?php
require __DIR__ . '/../includes/bootstrap.php';

admin_require_login($config);

$applicants = [];
$totalApplicants = null;
$perPage = 20;
$page = (int)($_GET['p'] ?? 1);
if ($page < 1) {
  $page = 1;
}
$totalPages = 1;
$dbError = null;
try {
  $pdo = db($config);
  $totalApplicants = (int)$pdo->query('SELECT COUNT(*) FROM applicants')->fetchColumn();

  $totalPages = max(1, (int)ceil($totalApplicants / $perPage));
  if ($page > $totalPages) {
    $page = $totalPages;
  }

  $offset = ($page - 1) * $perPage;
  $stmt = $pdo->prepare('SELECT id, full_name, email, phone, position_applied, created_at FROM applicants ORDER BY id DESC LIMIT :limit OFFSET :offset');
  $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->execute();
  $applicants = $stmt->fetchAll();
} catch (Throwable $e) {
  $dbError = $e->getMessage();
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Pelamar</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
  <header class="bg-white border-b">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="font-semibold">Dashboard Pelamar</div>
      <div class="flex items-center gap-3">
        <a class="text-sm text-gray-600 hover:text-gray-900" href="<?= e(url_for($config, '/admin/logout.php')) ?>">Logout</a>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-8">
    <?php if ($dbError): ?>
      <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
        <div class="font-semibold">Database belum siap / koneksi bermasalah</div>
        <div class="mt-2">Pastikan Anda sudah import <b>database.sql</b> dan konfigurasi DB di <b>config.php</b> benar.</div>
        <div class="mt-2 text-xs text-amber-700">Detail: <?= e($dbError) ?></div>
      </div>
    <?php endif; ?>

    <div class="bg-white border rounded-xl overflow-hidden">
      <div class="p-5 border-b">
        <h1 class="text-lg font-semibold">Data Pelamar</h1>
        <p class="mt-1 text-sm text-gray-600"></p>
        <?php if (!$dbError && is_int($totalApplicants)): ?>
          <p class="mt-1 text-xs text-gray-500">Total: <?= e((string)$totalApplicants) ?></p>
        <?php endif; ?>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="text-left font-medium px-4 py-3">Tanggal</th>
              <th class="text-left font-medium px-4 py-3">Nama</th>
              <th class="text-left font-medium px-4 py-3">Posisi</th>
              <th class="text-left font-medium px-4 py-3">Email</th>
              <th class="text-left font-medium px-4 py-3">HP</th>
              <th class="text-left font-medium px-4 py-3">Detail</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <?php if (!$applicants): ?>
              <tr>
                <td class="px-4 py-4 text-gray-600" colspan="6">Belum ada data pelamar.</td>
              </tr>
            <?php endif; ?>

            <?php foreach ($applicants as $a): ?>
              <tr>
                <td class="px-4 py-3 text-gray-700 whitespace-nowrap"><?= e($a['created_at']) ?></td>
                <td class="px-4 py-3 font-medium text-gray-900"><?= e($a['full_name']) ?></td>
                <td class="px-4 py-3 text-gray-700"><?= e($a['position_applied']) ?></td>
                <td class="px-4 py-3 text-gray-700"><?= e($a['email']) ?></td>
                <td class="px-4 py-3 text-gray-700"><?= e($a['phone']) ?></td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <a class="text-blue-600 hover:text-blue-800" href="<?= e(url_for($config, '/admin/view.php?id=' . (int)$a['id'])) ?>">Detail</a>

                    <form method="post" action="<?= e(url_for($config, '/admin/delete.php')) ?>" onsubmit="return confirm('Hapus data pelamar ini?');">
                      <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>" />
                      <input type="hidden" name="id" value="<?= (int)$a['id'] ?>" />
                      <input type="hidden" name="p" value="<?= e((string)$page) ?>" />
                      <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if (!$dbError && is_int($totalApplicants) && $totalApplicants > 0): ?>
        <div class="border-t bg-white">
          <div class="p-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="text-sm text-gray-600">Halaman <?= e((string)$page) ?> dari <?= e((string)$totalPages) ?></div>
            <div class="flex flex-wrap gap-2">
              <?php if ($page > 1): ?>
                <a class="rounded-md border bg-white px-3 py-2 text-sm text-gray-800 hover:bg-gray-50" href="<?= e(url_for($config, '/admin/index.php?p=' . ($page - 1))) ?>">Sebelumnya</a>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i === $page): ?>
                  <span class="rounded-md border border-blue-600 bg-blue-600 px-3 py-2 text-sm font-medium text-white"><?= e((string)$i) ?></span>
                <?php else: ?>
                  <a class="rounded-md border bg-white px-3 py-2 text-sm text-gray-800 hover:bg-gray-50" href="<?= e(url_for($config, '/admin/index.php?p=' . $i)) ?>"><?= e((string)$i) ?></a>
                <?php endif; ?>
              <?php endfor; ?>

              <?php if ($page < $totalPages): ?>
                <a class="rounded-md border bg-white px-3 py-2 text-sm text-gray-800 hover:bg-gray-50" href="<?= e(url_for($config, '/admin/index.php?p=' . ($page + 1))) ?>">Berikutnya</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
