
<?php

require_once __DIR__ . '/config/app.php';

require_member();

$userId = (int) current_user()['id'];

// Menghitung total koleksi buku
$totalBooks = (int) $pdo->query(
    'SELECT COUNT(*) FROM buku'
)->fetchColumn();

// Menghitung jumlah peminjaman aktif milik anggota
$myActive = $pdo->prepare("
    SELECT COUNT(*)
    FROM pinjam_buku
    WHERE id_anggota = ?
    AND status = 'dipinjam'
");

$myActive->execute([$userId]);
$activeCount = (int) $myActive->fetchColumn();

// Mengambil 5 riwayat peminjaman terbaru
$myHistory = $pdo->prepare("
    SELECT
        p.*,
        b.judul_buku
    FROM pinjam_buku p
    JOIN buku b ON b.id_buku = p.id_buku
    WHERE p.id_anggota = ?
    ORDER BY p.id DESC
    LIMIT 5
");

$myHistory->execute([$userId]);
$history = $myHistory->fetchAll();

$pageTitle = 'Dashboard Anggota';
$activePage = 'dashboard';

require __DIR__ . '/includes/header.php';

?>

<div class="page-heading">

    <div>
        <div class="eyebrow">
            Ruang baca pribadi
        </div>

        <h1>
            Halo, <?= e(current_user()['nama']) ?>.
        </h1>

        <p>
            Senang melihat Anda kembali. Temukan bacaan berikutnya
            atau cek buku yang sedang dipinjam.
        </p>
    </div>

    <a class="button" href="buku.php">
        Jelajahi Buku ↗
    </a>

</div>

<div class="grid stats-grid">

    <div class="stat-card">
        <div class="stat-label">
            Total koleksi buku
        </div>

        <div class="stat-value">
            <?= $totalBooks ?>
        </div>

        <div class="stat-note">
            Judul terdaftar di perpustakaan
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">
            Sedang Anda pinjam
        </div>

        <div class="stat-value">
            <?= $activeCount ?>
        </div>

        <div class="stat-note">
            Buku yang belum dikembalikan
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">
            Batas peminjaman
        </div>

        <div class="stat-value">
            7 <span class="small muted">hari</span>
        </div>

        <div class="stat-note">
            Dihitung sejak tanggal pinjam
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">
            Akses akun
        </div>

        <div class="stat-value" style="font-size:23px">
            Aktif
        </div>

        <div class="stat-note">
            Status keanggotaan Anda
        </div>
    </div>

</div>

<section class="panel">

    <div class="panel-head">

        <div>
            <h2>Aktivitas peminjaman</h2>

            <p>
                Riwayat peminjaman buku terbaru.
            </p>
        </div>

        <a class="muted-link" href="peminjaman_saya.php">
            Lihat semua →
        </a>

    </div>

    <?php if (!$history): ?>

        <div class="empty-state">
            Belum ada peminjaman. Mulai dengan menjelajahi daftar buku.
        </div>

    <?php else: ?>

        <div class="table-wrap">

            <table>

                <thead>
                    <tr>
                        <th>Judul buku</th>
                        <th>Tanggal pinjam</th>
                        <th>Batas kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($history as $row): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?= e($row['judul_buku']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= e($row['tanggal_pinajaman']) ?>
                            </td>

                            <td>
                                <?= e($row['tanggal_kembali']) ?>
                            </td>

                            <td>
                                <span class="badge <?= $row['status'] === 'dipinjam' ? 'amber' : 'gray' ?>">
                                    <?= e(ucfirst($row['status'])) ?>
                                </span>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
