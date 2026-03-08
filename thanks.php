<?php
require __DIR__ . '/includes/bootstrap.php';

$appName = $config['app']['name'] ?? 'Lamaran Kerja';
$id = (int)($_SESSION['last_application_id'] ?? 0);

// Bersihkan agar refresh tidak mengulang info lama
unset($_SESSION['last_application_id'], $_SESSION['last_mail_sent']);
?>
<!doctype html>
<html lang="id">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Terima kasih - <?= e($appName) ?></title>
	<meta name="description" content="Konfirmasi lamaran berhasil dikirim." />
	<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-blue-50 to-white text-gray-900">
	<main class="max-w-xl mx-auto px-4 py-16">
		<div class="bg-white border rounded-2xl p-7">
			<div class="inline-flex items-center gap-2 rounded-full border bg-white px-3 py-1 text-xs text-gray-600">
				<span class="h-2 w-2 rounded-full bg-green-600"></span>
				<span>Lamaran berhasil dikirim</span>
			</div>

			<h1 class="mt-4 text-2xl font-bold tracking-tight">Terima kasih telah melamar</h1>
			<p class="mt-2 text-sm text-gray-700">
				Lamaran Anda sudah kami terima<?= $id ?  : '' ?>.
				Silakan tunggu informasi selanjutnya dari tim kami melalui email/telepon.
			</p>

			<div class="mt-5 rounded-xl border bg-gray-50 p-4 text-sm text-gray-700">
				Jika Anda perlu memperbarui data, silakan hubungi tim HR.
			</div>

			<div class="mt-6 flex flex-wrap gap-3">
				<a class="inline-flex items-center justify-center rounded-md bg-blue-600 px-5 py-3 text-sm font-medium text-white hover:bg-blue-700" href="<?= e(url_for($config, '/')) ?>">
					Kembali ke Homepage
				</a>
			</div>
		</div>
	</main>
</body>
</html>
