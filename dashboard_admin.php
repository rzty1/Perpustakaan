
<?php

require_once __DIR__ . '/config/app.php';

require_staff();

// Menghitung data statistik
$totalBooks = (int) $pdo->query(
    'SELECT COUNT(*) FROM buku'
)->fetchColumn();

$totalMembers = (int) $pdo->query(
    "SELECT COUNT(*) FROM anggota WHERE status = 'aktif'"
)->fetchColumn();

$activeLoans = active_loan_count($pdo);

$totalStaff = (int) $pdo->query(
    "SELECT COUNT(*) FROM petugas WHERE status = 'aktif'"
)->fetchColumn();

// Mengambil 7 data peminjaman aktif terbaru
$latest = $pdo->query("
    SELECT
        p.*,
        b.judul_buku,
        a.nama AS nama_anggota
    FROM pinjam_buku p
    JOIN buku b ON b.id_buku = p.id_buku
    JOIN anggota a ON a.id_anggota = p.id_anggota
    WHERE p.status = 'dipinjam'
    ORDER BY p.tanggal_kembali ASC
    LIMIT 7
")->fetchAll();

$pageTitle = 'Dashboard Pengelola';
$activePage = 'dashboard';

require __DIR__ . '/includes/header.php';

?>

<div class="page-heading">

    <div>
        <div class="eyebrow">
            Panel pengelola · <?= e(ucfirst(current_user()['role'])) ?>
        </div>

        <h1>Dashboard.</h1>

        <p>
            Ringkasan koleksi, anggota, dan peminjaman perpustakaan.
        </p>
    </div>

    <a class="button" href="admin/buku.php">
        Kelola Buku ↗
    </a>

</div>

<div class="grid stats-grid">

    <div class="stat-card">
        <div class="stat-label">Total buku</div>
        <div class="stat-value"><?= $totalBooks ?></div>
        <div class="stat-note">Judul di dalam katalog</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Anggota aktif</div>
        <div class="stat-value"><?= $totalMembers ?></div>
        <div class="stat-note">Akun anggota aktif</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Sedang dipinjam</div>
        <div class="stat-value"><?= $activeLoans ?></div>
        <div class="stat-note">Belum dikembalikan</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Pengelola aktif</div>
        <div class="stat-value"><?= $totalStaff ?></div>
        <div class="stat-note">Admin dan petugas</div>
    </div>

</div>

<section class="panel">

    <div class="panel-head">

        <div>
            <h2>Peminjaman aktif</h2>
            <p>
                Urutan berdasarkan tanggal pengembalian terdekat.
            </p>
        </div>

        <a class="muted-link" href="admin/peminjaman.php">
            Lihat semua →
        </a>

    </div>

    <?php if (!$latest): ?>

        <div class="empty-state">
            Belum ada peminjaman aktif.
        </div>

    <?php else: ?>

        <div class="table-wrap">

            <table>

                <thead>
                    <tr>
                        <th>Anggota</th>
                        <th>Judul buku</th>
                        <th>Tanggal pinjam</th>
                        <th>Batas kembali</th>
                        <th>Status waktu</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($latest as $loan): ?>

                        <?php
                        $late = $loan['tanggal_kembali'] < date('Y-m-d');
                        ?>

                        <tr>

                            <td>
                                <?= e($loan['nama_anggota']) ?>
                            </td>

                            <td>
                                <strong>
                                    <?= e($loan['judul_buku']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= e($loan['tanggal_pinajaman']) ?>
                            </td>

                            <td>
                                <?= e($loan['tanggal_kembali']) ?>
                            </td>

                            <td>
                                <span class="badge <?= $late ? 'red' : 'amber' ?>">
                                    <?= $late ? 'Terlambat' : 'Dalam batas' ?>
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
