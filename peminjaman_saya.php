<?php
require_once __DIR__ . '/config/app.php';
require_member();
$stmt = $pdo->prepare("SELECT p.*, b.judul_buku, b.pengarang_buku, CASE WHEN p.status = 'dipinjam' AND p.tanggal_kembali < CURDATE() THEN 1 ELSE 0 END AS terlambat FROM pinjam_buku p JOIN buku b ON b.id_buku = p.id_buku WHERE p.id_anggota = ? ORDER BY p.id DESC");
$stmt->execute([(int)current_user()['id']]); $loans = $stmt->fetchAll();
$pageTitle = 'Peminjaman Saya'; $activePage = 'pinjaman';
require __DIR__ . '/includes/header.php';
?>
<div class="page-heading"><div><div class="eyebrow">Aktivitas anggota</div><h1>Peminjaman saya.</h1><p>Pantau buku yang sedang dipinjam, batas waktu pengembalian, dan riwayat buku yang sudah dikembalikan.</p></div><a class="button" href="buku.php">Cari Buku ↗</a></div>
<section class="panel"><div class="panel-head"><div><h2>Daftar peminjaman</h2><p>Masa pinjam standar adalah 7 hari.</p></div></div>
<?php if (!$loans): ?><div class="empty-state">Anda belum pernah meminjam buku.</div><?php else: ?><div class="table-wrap"><table><thead><tr><th>Buku</th><th>Tanggal pinjam</th><th>Batas kembali</th><th>Dikembalikan pada</th><th>Status</th></tr></thead><tbody>
<?php foreach ($loans as $loan): ?><tr><td><strong><?= e($loan['judul_buku']) ?></strong><div class="muted small"><?= e($loan['pengarang_buku']) ?></div></td><td><?= e($loan['tanggal_pinajaman']) ?></td><td><?= e($loan['tanggal_kembali']) ?></td><td><?= e($loan['tanggal_dikembalikan'] ?: '—') ?></td><td><?php if ($loan['status'] === 'dikembalikan'): ?><span class="badge gray">Dikembalikan</span><?php elseif ($loan['terlambat']): ?><span class="badge red">Terlambat</span><?php else: ?><span class="badge amber">Dipinjam</span><?php endif; ?></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
<?php

require_once __DIR__ . '/config/app.php';

require_member();

// Mengambil data peminjaman milik anggota
$stmt = $pdo->prepare("
    SELECT
        p.*,
        b.judul_buku,
        b.pengarang_buku,
        CASE
            WHEN p.status = 'dipinjam'
            AND p.tanggal_kembali < CURDATE()
            THEN 1
            ELSE 0
        END AS terlambat
    FROM pinjam_buku p
    JOIN buku b ON b.id_buku = p.id_buku
    WHERE p.id_anggota = ?
    ORDER BY p.id DESC
");

$stmt->execute([(int) current_user()['id']]);
$loans = $stmt->fetchAll();

$pageTitle = 'Peminjaman Saya';
$activePage = 'pinjaman';

require __DIR__ . '/includes/header.php';

?>

<div class="page-heading">

    <div>
        <div class="eyebrow">
            Aktivitas anggota
        </div>

        <h1>Peminjaman saya.</h1>

        <p>
            Pantau buku yang sedang dipinjam, batas waktu pengembalian,
            dan riwayat buku yang sudah dikembalikan.
        </p>
    </div>

    <a class="button" href="buku.php">
        Cari Buku ↗
    </a>

</div>

<section class="panel">

    <div class="panel-head">

        <div>
            <h2>Daftar peminjaman</h2>

            <p>
                Masa pinjam standar adalah 7 hari.
            </p>
        </div>

    </div>

    <?php if (!$loans): ?>

        <div class="empty-state">
            Anda belum pernah meminjam buku.
        </div>

    <?php else: ?>

        <div class="table-wrap">

            <table>

                <thead>
                    <tr>
                        <th>Buku</th>
                        <th>Tanggal pinjam</th>
                        <th>Batas kembali</th>
                        <th>Dikembalikan pada</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($loans as $loan): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?= e($loan['judul_buku']) ?>
                                </strong>

                                <div class="muted small">
                                    <?= e($loan['pengarang_buku']) ?>
                                </div>
                            </td>

                            <td>
                                <?= e($loan['tanggal_pinajaman']) ?>
                            </td>

                            <td>
                                <?= e($loan['tanggal_kembali']) ?>
                            </td>

                            <td>
                                <?= e($loan['tanggal_dikembalikan'] ?: '—') ?>
                            </td>

                            <td>

                                <?php if ($loan['status'] === 'dikembalikan'): ?>

                                    <span class="badge gray">
                                        Dikembalikan
                                    </span>

                                <?php elseif ($loan['terlambat']): ?>

                                    <span class="badge red">
                                        Terlambat
                                    </span>

                                <?php else: ?>

                                    <span class="badge amber">
                                        Dipinjam
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

