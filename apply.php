<?php
require __DIR__ . '/includes/bootstrap.php';

$appName = $config['app']['name'] ?? 'Lamaran Kerja';

$errors = [];
$old = [
  'full_name' => '',
  'email' => '',
    'phone' => '',
    'position_applied' => '',
    'address' => '',
    'cover_letter' => '',
];

$positionOptions = [
  'Pramugari / Pramugara',
  'Aviation Security Officer',
  'Fly Security',
  'Fire Fighting Officer',
  'Apron Movement',
  'Controller / Avia Bridge',
  'Air Traffic Maintenance',
  'Engineer',
  'Flight Operations Officer',
  'Perawat',
  'Marshaller',
  'Check In Counter',
  'Customer Service',
  'Pemadam Kebakaran',
  'Driver',
  'Mekanik',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['full_name'] = trim((string)($_POST['full_name'] ?? ''));
    $old['email'] = trim((string)($_POST['email'] ?? ''));
    $old['phone'] = normalize_phone($_POST['phone'] ?? '');
    $old['position_applied'] = trim((string)($_POST['position_applied'] ?? ''));
    $old['address'] = trim((string)($_POST['address'] ?? ''));
    $old['cover_letter'] = trim((string)($_POST['cover_letter'] ?? ''));

    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $errors[] = 'Sesi tidak valid. Silakan refresh halaman dan coba lagi.';
    }

    if ($old['full_name'] === '') {
        $errors[] = 'Nama lengkap wajib diisi.';
    }

    if ($old['email'] === '' || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }

    // Normalisasi email agar konsisten (dan membantu cek duplikasi)
    if ($old['email'] !== '') {
      $old['email'] = strtolower($old['email']);
    }

    if ($old['phone'] === '') {
        $errors[] = 'Nomor HP wajib diisi.';
    }

    if ($old['position_applied'] === '') {
        $errors[] = 'Posisi yang dilamar wajib diisi.';
    }

    if ($old['position_applied'] !== '' && !in_array($old['position_applied'], $positionOptions, true)) {
      $errors[] = 'Posisi yang dipilih tidak valid.';
    }

    $pdo = null;
    if (!$errors) {
      try {
        $pdo = db($config);
      } catch (Throwable $e) {
        $errors[] = 'Koneksi database gagal. Pastikan MySQL XAMPP berjalan dan database sudah dibuat. Detail: ' . $e->getMessage();
      }
    }

    // Cek duplikasi email (tidak boleh daftar dua kali dengan email yang sama)
    if (!$errors && $pdo instanceof PDO) {
      try {
        $stmt = $pdo->prepare('SELECT 1 FROM applicants WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $old['email']]);
        if ($stmt->fetchColumn()) {
          $errors[] = 'Email sudah digunakan. Silakan gunakan email lain.';
        }
      } catch (Throwable $e) {
        $errors[] = 'Gagal memeriksa email. Silakan coba lagi. Detail: ' . $e->getMessage();
      }
    }

    // Cek duplikasi No. HP (tidak boleh daftar dua kali dengan nomor yang sama)
    if (!$errors && $pdo instanceof PDO) {
      try {
        $stmt = $pdo->prepare('SELECT 1 FROM applicants WHERE phone = :phone LIMIT 1');
        $stmt->execute([':phone' => $old['phone']]);
        if ($stmt->fetchColumn()) {
          $errors[] = 'No. HP sudah digunakan. Silakan gunakan nomor lain.';
        }
      } catch (Throwable $e) {
        $errors[] = 'Gagal memeriksa No. HP. Silakan coba lagi. Detail: ' . $e->getMessage();
      }
    }

    $cvInfo = null;
    if (!$errors) {
      try {
        $cvInfo = upload_cv($config, $_FILES['cv'] ?? []);
      } catch (Throwable $e) {
        $errors[] = $e->getMessage();
      }
    }

    if (!$errors && $cvInfo && $pdo instanceof PDO) {
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO applicants (full_name, email, phone, position_applied, address, cover_letter, cv_original_name, cv_stored_name, cv_mime, cv_size, mail_sent, ip_address)
                 VALUES (:full_name, :email, :phone, :position_applied, :address, :cover_letter, :cv_original_name, :cv_stored_name, :cv_mime, :cv_size, 0, :ip_address)'
            );

            $stmt->execute([
                ':full_name' => $old['full_name'],
                ':email' => $old['email'],
                ':phone' => $old['phone'],
                ':position_applied' => $old['position_applied'],
                ':address' => $old['address'] !== '' ? $old['address'] : null,
                ':cover_letter' => $old['cover_letter'] !== '' ? $old['cover_letter'] : null,
                ':cv_original_name' => $cvInfo['original_name'],
                ':cv_stored_name' => $cvInfo['stored_name'],
                ':cv_mime' => $cvInfo['mime'],
                ':cv_size' => $cvInfo['size'],
                ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);

            $id = (int)$pdo->lastInsertId();

            $mailOk = false;
            try {
                $mailOk = send_application_email($config, [
                    'full_name' => $old['full_name'],
                    'email' => $old['email'],
                    'phone' => $old['phone'],
                    'position_applied' => $old['position_applied'],
                    'address' => $old['address'],
                    'cover_letter' => $old['cover_letter'],
                ], $cvInfo);
            } catch (Throwable $e) {
                $mailOk = false;
            }

            if ($mailOk) {
                $pdo->prepare('UPDATE applicants SET mail_sent = 1 WHERE id = :id')->execute([':id' => $id]);
            }

            $pdo->commit();

            // Sukses
            $_SESSION['last_application_id'] = $id;
            $_SESSION['last_mail_sent'] = $mailOk ? 1 : 0;
            redirect(url_for($config, '/thanks.php'));
        } catch (Throwable $e) {
            $pdo->rollBack();
            // Hapus file CV jika DB gagal
            if (!empty($cvInfo['absolute_path']) && is_file($cvInfo['absolute_path'])) {
                @unlink($cvInfo['absolute_path']);
            }

            $message = $e->getMessage();
            $code = (string)$e->getCode();
            // Tangani duplikasi email/No. HP (mis. jika ada UNIQUE index)
            if (str_contains($message, 'Duplicate entry') || $code === '23000') {
              if (str_contains($message, 'uq_applicants_email')) {
                $errors[] = 'Email sudah digunakan. Silakan gunakan email lain.';
              } elseif (str_contains($message, 'uq_applicants_phone')) {
                $errors[] = 'No. HP sudah digunakan. Silakan gunakan nomor lain.';
              } else {
                $errors[] = 'Email atau No. HP sudah digunakan. Silakan gunakan yang lain.';
              }
            } else {
              $errors[] = 'Gagal menyimpan lamaran: ' . $message;
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Lamar - <?= e($appName) ?></title>
  <meta name="description" content="Form lamaran kerja dan upload CV." />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-blue-50 to-white text-gray-900">
  <header class="bg-white/90 backdrop-blur border-b">
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
      <a class="font-semibold tracking-tight" href="<?= e(url_for($config, '/')) ?>"><?= e($appName) ?></a>
      <a class="text-sm text-gray-600 hover:text-gray-900" href="<?= e(url_for($config, '/')) ?>">← Kembali</a>
    </div>
  </header>

  <main class="max-w-5xl mx-auto px-4 py-10">
    <div class="grid gap-6 lg:grid-cols-3 lg:items-start">
      <div class="lg:col-span-1">
        <h1 class="text-2xl font-bold tracking-tight">Apply</h1>
        <p class="mt-2 text-sm text-gray-600">Lengkapi data singkat berikut. CV wajib di-upload.</p>

        <div class="mt-6 rounded-2xl border bg-white p-5">
          <div class="text-sm font-semibold">Catatan</div>
          <ul class="mt-3 space-y-2 text-sm text-gray-700">
            <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>Format CV: PDF/DOC/DOCX</span></li>
            <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>Maks ukuran: 5MB</span></li>
            <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>Pastikan email & nomor HP aktif</span></li>
          </ul>
        </div>

       
      </div>

      <div class="lg:col-span-2">

    <?php if ($errors): ?>
      <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <div class="font-semibold">Periksa kembali:</div>
        <ul class="mt-2 list-disc pl-5 space-y-1">
          <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form class="mt-6 bg-white border rounded-2xl p-6 md:p-7 space-y-5" method="post" enctype="multipart/form-data">
      <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>" />

      <div>
        <label class="block text-sm font-medium">Nama Lengkap <span class="text-red-600">*</span></label>
        <input name="full_name" value="<?= e($old['full_name']) ?>" class="mt-1 w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="Nama sesuai KTP" autocomplete="name" />
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="block text-sm font-medium">Email <span class="text-red-600">*</span></label>
          <input type="email" name="email" value="<?= e($old['email']) ?>" class="mt-1 w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="nama@email.com" autocomplete="email" />
        </div>
        <div>
          <label class="block text-sm font-medium">NO HP AKTIF (BUKAN WHATSAPP) <span class="text-red-600">*</span></label>
          <input name="phone" value="<?= e($old['phone']) ?>" class="mt-1 w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="08xxxxxxxxxx" autocomplete="tel" />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium">Posisi yang Dilamar <span class="text-red-600">*</span></label>
        <select name="position_applied" class="mt-1 w-full rounded-md border bg-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200">
          <option value="">-- Pilih posisi --</option>
          <?php foreach ($positionOptions as $pos): ?>
            <option value="<?= e($pos) ?>" <?= $old['position_applied'] === $pos ? 'selected' : '' ?>><?= e($pos) ?></option>
          <?php endforeach; ?>
        </select>
        <p class="mt-1 text-xs text-gray-500">Pilih salah satu posisi yang tersedia.</p>
      </div>

      <div>
        <label class="block text-sm font-medium">Alamat</label>
        <input name="address" value="<?= e($old['address']) ?>" class="mt-1 w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="Kota, Provinsi" />
      </div>

      <div>
        <label class="block text-sm font-medium">Cover Letter</label>
        <textarea name="cover_letter" rows="5" class="mt-1 w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="Perkenalkan diri Anda singkat..."><?= e($old['cover_letter']) ?></textarea>
      </div>

      <div>
        <label class="block text-sm font-medium">Upload CV <span class="text-red-600">*</span></label>
        <input type="file" name="cv" accept=".pdf,.doc,.docx" class="mt-1 w-full rounded-md border bg-white px-3 py-2" />
        <p class="mt-1 text-xs text-gray-500">Format: PDF/DOC/DOCX. Maks 5MB.</p>
      </div>

      <div class="flex items-center justify-end gap-3">
        <a class="rounded-md border px-4 py-2 text-sm hover:bg-gray-50" href="<?= e(url_for($config, '/')) ?>">Batal</a>
        <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700" type="submit">Kirim Lamaran</button>
      </div>
    </form>
      </div>
    </div>
  </main>
</body>
</html>
