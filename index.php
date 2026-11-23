
<?php

require_once __DIR__ . '/config/app.php';

// Mengarahkan pengguna yang sudah login
if (is_logged_in()) {
    redirect(
        current_user()['role'] === 'anggota'
            ? 'dashboard.php'
            : 'dashboard_admin.php'
    );
}

$pageTitle = 'Selamat Datang';

require __DIR__ . '/includes/header.php';

?>

<section class="hero">

    <div class="eyebrow">
        Perpustakaan digital sederhana
    </div>

    <h1>
        Temukan cerita.<br>
        <span style="color:var(--accent)">
            Buka wawasan baru.
        </span>
    </h1>

    <p>
        Kelola koleksi buku dan aktivitas peminjaman dalam satu ruang
        yang sederhana. Masuk sebagai anggota untuk mencari buku,
        atau sebagai admin/petugas untuk mengelola perpustakaan.
    </p>

    <div class="hero-actions">

        <a class="button" href="login_anggota.php">
            Masuk sebagai Anggota <span>↗</span>
        </a>

        <a class="button button-ghost" href="login_petugas.php">
            Masuk Admin / Petugas
        </a>

    </div>

</section>

<div class="grid feature-grid">

    <article class="feature-card">

        <div class="feature-icon">
            ⌕
        </div>

        <h3>Jelajahi koleksi</h3>

        <p>
            Cari buku berdasarkan judul atau nama penulis
            dan lihat ketersediaannya.
        </p>

    </article>

    <article class="feature-card">

        <div class="feature-icon">
            ▤
        </div>

        <h3>Peminjaman terarah</h3>

        <p>
            Ajukan peminjaman dan pantau tanggal pengembalian buku.
        </p>

    </article>

    <article class="feature-card">

        <div class="feature-icon">
            ◫
        </div>

        <h3>Pengelolaan mudah</h3>

        <p>
            Admin dan petugas dapat mengelola data buku,
            anggota, dan peminjaman.
        </p>

    </article>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
