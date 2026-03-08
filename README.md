# Lamaran (PHP Native + Tailwind + MySQL)

## Fitur
- Landing page: `/` (index.php)
- Form lamaran + upload CV: `/apply.php`
- Simpan data ke MySQL (XAMPP)
- Kirim email ke HR (menggunakan `mail()` + attachment CV)
- Dashboard admin: `/admin`

## Setup Database
1. Buka phpMyAdmin: `http://localhost/phpmyadmin`
2. Import file `database.sql`
3. Pastikan konfigurasi DB di `config.php` sesuai:
   - db name default: `lamaran_db`
   - user: `root`
   - pass: kosong (default XAMPP)

## Setup Email
Aplikasi memakai fungsi PHP `mail()`.
- Jika email belum terkirim, biasanya karena SMTP/sendmail di server belum dikonfigurasi.
- Data pelamar tetap tersimpan di database dan bisa diunduh dari dashboard.

Ubah email HR di `config.php`:
- `mail.to`

## Login Admin
- URL: `http://localhost/lamaran/admin/login.php`
- Username: `admin`
- Password: `admin123`

## Catatan Upload
- Folder upload: `uploads/cv`
- Maksimal ukuran CV: 5MB
- Format: PDF/DOC/DOCX

Jika upload gagal karena permission, pastikan folder `uploads/cv` writable oleh Apache.
# lamaran1
