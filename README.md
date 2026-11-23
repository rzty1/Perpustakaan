# PerpusKita — Aplikasi Perpustakaan PHP & MySQL

Aplikasi sederhana berbasis PHP native dan MySQL dengan tema gelap minimalis.

## Fitur

### Anggota
- Registrasi akun dan login anggota pada halaman terpisah.
- Melihat katalog buku dan status ketersediaan.
- Pencarian berdasarkan judul buku atau nama pengarang.
- Mengajukan peminjaman buku yang tersedia.
- Melihat peminjaman, batas pengembalian, dan status keterlambatan.
- Mengganti password dengan verifikasi password lama.

### Admin / Petugas
- Login admin/petugas pada halaman terpisah dari anggota.
- Dashboard ringkasan buku, anggota, dan peminjaman.
- Menambah, mengubah, dan menghapus data buku.
- Melihat dan mengubah data anggota serta status keanggotaannya.
- Melihat seluruh transaksi dan menandai buku yang sudah dikembalikan.
- Mengganti password dengan verifikasi password lama.

## Cara menjalankan di XAMPP

1. Salin folder `web-perpus` ke `C:\xampp\htdocs\`.
2. Buka XAMPP Control Panel, lalu aktifkan **Apache** dan **MySQL**.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih menu **Import**, lalu pilih file `database.sql` dari folder proyek ini. Klik **Import/Go**.
5. Pastikan pengaturan database pada `config/database.php` sesuai. Default XAMPP: host `localhost`, user `root`, password kosong, database `web_perpus`.
6. Buka `http://localhost/web-perpus/` di browser.

## Akun demo

| Jenis akun | Username | Password |
|---|---|---|
| Admin | `admin` | `admin123` |
| Petugas | `petugas` | `petugas123` |

Anggota dapat membuat akun melalui tombol **Registrasi** pada halaman login anggota.

**Penting:** ganti password demo setelah login pertama. Untuk contoh tugas sekolah, reset password menggunakan password lama sesuai alur pada activity diagram.

## Struktur folder

```text
web-perpus/
├── admin/
│   ├── anggota.php
│   ├── buku.php
│   └── peminjaman.php
├── assets/
│   └── style.css
├── config/
│   ├── app.php
│   ├── database.php
│   └── helpers.php
├── includes/
│   ├── footer.php
│   └── header.php
├── admin/                 # halaman pengelolaan admin/petugas
├── database.sql
├── index.php
├── login_anggota.php
├── login_petugas.php
├── register.php
├── reset_password.php
├── dashboard.php
├── dashboard_admin.php
├── buku.php
├── pinjam.php
├── peminjaman_saya.php
└── logout.php
```

## Catatan struktur database

Class diagram memuat tabel `buku`, `anggota`, `pinjam_buku`, dan `petugas`. Untuk mendukung langkah login, registrasi, status ketersediaan, serta reset password pada activity diagram, database menambahkan kolom `username`, `password_hash`, `email`, `status`, dan timestamp. Pada `pinjam_buku`, `tanggal_kembali` dipakai sebagai tanggal batas pengembalian (sesuai aktivitas melihat sisa waktu / kapan buku harus dikembalikan), sedangkan `tanggal_dikembalikan` adalah tanggal aktual saat petugas menerima buku.

Kolom `id_petugas` pada transaksi boleh `NULL` saat anggota mengajukan pinjaman langsung dari halaman anggota. Kolom ini akan terisi ketika admin/petugas memproses pengembalian.

## Pemeriksaan

Kode menggunakan PDO, prepared statements, `password_hash()` / `password_verify()`, session, dan token CSRF untuk form POST. Jalankan aplikasi melalui Apache (misalnya XAMPP), bukan dengan membuka file PHP langsung dari folder.
