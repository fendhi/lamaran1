<?php
require __DIR__ . '/includes/bootstrap.php';

$appName = $config['app']['name'] ?? 'Lamaran Kerja';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= e($appName) ?> - Career</title>
  <meta name="description" content="Portal karier: kirim lamaran dan upload CV Anda." />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-900">
  <header class="sticky top-0 z-10 bg-white/90 backdrop-blur border-b">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <a class="font-semibold tracking-tight" href="<?= e(url_for($config, '/')) ?>"><?= e($appName) ?></a>
      <div class="flex items-center gap-3">
        <a class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700" href="<?= e(url_for($config, '/apply.php')) ?>">Daftar</a>
      </div>
    </div>
  </header>

  <main>
    <section>
      <div class="max-w-6xl mx-auto px-4">
        <img
          src="<?= e(url_for($config, '/aset/A2.jpeg')) ?>"
          alt="Hero"
          class="block w-full h-auto"
          loading="eager"
        />
      </div>
    </section>

    <section>
      <div class="max-w-6xl mx-auto px-4 py-10 md:py-12">
        <div class="grid gap-10 md:grid-cols-2 md:items-center">
          <div>
            <div class="inline-flex items-center gap-2 rounded-full border bg-white px-3 py-1 text-xs text-gray-600">
              <span class="h-2 w-2 rounded-full bg-blue-600"></span>
              <span>Career Portal</span>
            </div>
            <p class="mt-4 text-gray-600 leading-relaxed">
              Kirim lamaran Anda melalui form singkat dan upload CV.
            </p>

            <div class="mt-7 flex flex-wrap gap-3">
              <a class="inline-flex items-center justify-center rounded-md bg-blue-600 px-5 py-3 text-sm font-medium text-white hover:bg-blue-700" href="<?= e(url_for($config, '/apply.php')) ?>">Daftar Sekarang</a>
              <a class="inline-flex items-center justify-center rounded-md border bg-white px-5 py-3 text-sm font-medium text-gray-900 hover:bg-gray-50" href="#posisi">Lihat Posisi</a>
            </div>
          </div>

          <div class="bg-white border rounded-2xl p-6 md:p-7">
            <div class="flex items-start justify-between gap-4">
              <div>
                <div class="text-sm font-semibold text-gray-900">Persyaratan</div>
                <p class="mt-1 text-sm text-gray-600">Siapkan ini sebelum mengirim.</p>
              </div>
              <a class="text-sm font-medium text-blue-700 hover:text-blue-900" href="<?= e(url_for($config, '/apply.php')) ?>">Buka Form</a>
            </div>

            <ul class="mt-5 space-y-3 text-sm text-gray-700">
              <li class="flex gap-3">
                <span class="mt-0.5 h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                <span>Data diri pelamar</span>
              </li>
              <li class="flex gap-3">
                <span class="mt-0.5 h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                <span>Pilih posisi yang dilamar</span>
              </li>
              <li class="flex gap-3">
                <span class="mt-0.5 h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                <span>Upload CV (PDF/DOC/DOCX, maks 5MB)</span>
              </li>
            </ul>

            <div class="mt-6 rounded-xl bg-gray-50 border p-4 text-sm text-gray-700">
              Tips: Pastikan email & nomor HP aktif agar mudah dihubungi.
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="posisi" class="max-w-6xl mx-auto px-4 py-12">
      <div class="grid gap-6 md:grid-cols-3 md:items-start">
        <div class="md:col-span-1">
          <h2 class="text-xl font-bold tracking-tight">Posisi yang Dibutuhkan</h2>
          <p class="mt-2 text-sm text-gray-600">Silakan pilih posisi yang sesuai saat mengisi form.</p>
        </div>
        <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-3 gap-3">
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Pramugari / Pramugara</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Aviation Security Officer</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Fly Security</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Fire Fighting Officer</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Apron Movement</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Controller / Avia Bridge</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Air Traffic Maintenance</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Engineer</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Flight Operations Officer</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Perawat</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Marshaller</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Check In Counter</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Customer Service</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Pemadam Kebakaran</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Driver</div>
          <div class="rounded-xl border bg-white px-4 py-3 hover:bg-gray-50">Mekanik</div>
        </div>
      </div>
    </section>

    <section class="bg-gray-50 border-y">
      <div class="max-w-6xl mx-auto px-4 py-12">
        <div class="grid gap-8 md:grid-cols-2 md:items-start">
          <div>
            <div class="rounded-2xl border bg-white p-6">
              <h2 class="text-xl font-bold tracking-tight">Kualifikasi Umum</h2>
              <ul class="mt-5 grid gap-2 text-sm text-gray-700">
              <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>Pria / Wanita, usia 18–45 tahun</span></li>
              <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>Pendidikan minimal SMA sederajat / D3 / D4 / S1 / S2</span></li>
              <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>Sehat jasmani dan rohani</span></li>
              <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>Bisa bekerja dengan tim / kelompok</span></li>
              <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>Bersedia ditempatkan di seluruh wilayah kerja PT Angkasa Pura 1</span></li>
              </ul>
            </div>
          </div>

          <div>
            <div class="rounded-2xl border bg-white p-6">
              <h2 class="text-xl font-bold tracking-tight">Fasilitas</h2>
              <ul class="mt-5 grid gap-2 text-sm text-gray-700">
              <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>Gaji Pokok UMR Rp 7.900.000 s.d. Rp 16.000.000</span></li>
              <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>Uang Makan</span></li>
              <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>Mess / Transport / Jemputan</span></li>
              <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>Seragam</span></li>
              <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>BPJS</span></li>
              <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>Premi Kehadiran</span></li>
              <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></span><span>THR</span></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="border-t bg-white">
    <div class="max-w-6xl mx-auto px-4 py-8 text-sm text-gray-600 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
      <div>© <?= date('Y') ?> <?= e($appName) ?></div>
     
    </div>
  </footer>
</body>
</html>
