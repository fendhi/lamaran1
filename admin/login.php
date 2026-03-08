<?php
require __DIR__ . '/../includes/bootstrap.php';

if (admin_is_logged_in()) {
    redirect(url_for($config, '/admin/index.php'));
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $error = 'Sesi tidak valid. Silakan refresh dan coba lagi.';
    } else {
        $okUser = hash_equals((string)$config['admin']['username'], $username);
        $okPass = password_verify($password, (string)$config['admin']['password_hash']);

        if ($okUser && $okPass) {
            $_SESSION['admin_logged_in'] = 1;
            redirect(url_for($config, '/admin/index.php'));
        }

        $error = 'Username atau password salah.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
  <main class="max-w-sm mx-auto px-4 py-16">
    <div class="bg-white border rounded-xl p-6">
      <h1 class="text-xl font-bold">Login Admin</h1>
      <p class="mt-1 text-sm text-gray-600">Masuk untuk melihat data pelamar.</p>

      <?php if ($error): ?>
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"><?= e($error) ?></div>
      <?php endif; ?>

      <form class="mt-5 space-y-4" method="post">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>" />

        <div>
          <label class="block text-sm font-medium">Username</label>
          <input name="username" class="mt-1 w-full rounded-md border px-3 py-2" placeholder="admin" autocomplete="username" />
        </div>

        <div>
          <label class="block text-sm font-medium">Password</label>
          <input type="password" name="password" class="mt-1 w-full rounded-md border px-3 py-2" placeholder="••••••••" autocomplete="current-password" />
        </div>

        <button class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700" type="submit">Masuk</button>
      </form>

      
    </div>
  </main>
</body>
</html>
