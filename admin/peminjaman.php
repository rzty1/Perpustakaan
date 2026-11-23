
<?php

require_once __DIR__ . '/../config/app.php';

require_staff();


// Proses pengembalian buku
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $id = (int) ($_POST['id'] ?? 0);

    $stmt = $pdo->prepare("
        UPDATE pinjam_buku
        SET
            status = 'dikembalikan',
            tanggal_dikembalikan = CURDATE(),
            id_petugas = ?
        WHERE id = ?
        AND status = 'dipinjam'
    ");

    $stmt->execute([
        staff_id(),
        $id
    ]);

    flash(
        $stmt->rowCount() ? 'success' : 'error',
        $stmt->rowCount()
            ? 'Buku ditandai telah dikembalikan.'
            : 'Data peminjaman tidak ditemukan atau sudah selesai.'
    );

    redirect('peminjaman.php');
}


// Pencarian data peminjaman
$q = trim($_GET['q'] ?? '');

$sql = "
    SELECT
        p.*,
        b.judul_buku,
        a.nama AS nama_anggota,
        s.nama AS nama_petugas

    FROM pinjam_buku p

    JOIN buku b
        ON b.id_buku = p.id_buku

    JOIN anggota a
        ON a.id_anggota = p.id_anggota

    LEFT JOIN petugas s
        ON s.id_petugas = p.id_petugas
";

$params = [];

if ($q !== '') {

    $sql .= "
        WHERE a.nama LIKE ?
        OR b.judul_buku LIKE ?
    ";

    $params = [
        '%' . $q . '%',
        '%' . $q . '%'
    ];
}

$sql .= "
    ORDER BY
        CASE
            WHEN p.status = 'dipinjam' THEN 0
            ELSE 1
        END,
        p.tanggal_kembali ASC,
        p.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$loans = $stmt->fetchAll();


// Pengaturan halaman
$pageTitle = 'Kelola Peminjaman';
$activePage = 'peminjaman';

require __DIR__ . '/../includes/header.php';

?>


<!-- Judul Halaman -->
<div class="page-heading">

    <div>

        <div class="eyebrow">
            Manajemen transaksi
        </div>

        <h1>Data peminjaman.</h1>

        <p>
            Pantau buku yang dipinjam dan tanggal batas pengembaliannya.
            Tandai transaksi saat buku sudah diterima kembali.
        </p>

    </div>

</div>


<!-- Daftar Transaksi Peminjaman -->
<section class="panel">

    <div class="panel-head">

        <div>

            <h2>Semua transaksi</h2>

            <p>
                <?= count($loans) ?> transaksi ditemukan
            </p>

        </div>


        <!-- Form Pencarian -->
        <form class="search-bar" method="get">

            <input
                name="q"
                value="<?= e($q) ?>"
                placeholder="Cari nama anggota atau judul buku"
            >

            <button class="button button-small">
                Cari
            </button>

        </form>

    </div>


    <!-- Kondisi Data Peminjaman -->
    <?php if (!$loans): ?>

        <div class="empty-state">
            Belum ada data peminjaman.
        </div>

    <?php else: ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Tanggal pinjam</th>
                        <th>Batas kembali</th>
                        <th>Tanggal dikembalikan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($loans as $loan):

                        $late = $loan['status'] === 'dipinjam'
                            && $loan['tanggal_kembali'] < date('Y-m-d');

                    ?>

                        <tr>

                            <!-- Nama Anggota -->
                            <td>

                                <strong>
                                    <?= e($loan['nama_anggota']) ?>
                                </strong>

                            </td>


                            <!-- Judul Buku -->
                            <td>
                                <?= e($loan['judul_buku']) ?>
                            </td>


                            <!-- Tanggal Peminjaman -->
                            <td>
                                <?= e($loan['tanggal_pinajaman']) ?>
                            </td>


                            <!-- Batas Pengembalian -->
                            <td>
                                <?= e($loan['tanggal_kembali']) ?>
                            </td>


                            <!-- Tanggal Pengembalian -->
                            <td>
                                <?= e($loan['tanggal_dikembalikan'] ?: '—') ?>
                            </td>


                            <!-- Status Peminjaman -->
                            <td>

                                <?php if ($loan['status'] === 'dikembalikan'): ?>

                                    <span class="badge gray">
                                        Dikembalikan
                                    </span>

                                <?php elseif ($late): ?>

                                    <span class="badge red">
                                        Terlambat
                                    </span>

                                <?php else: ?>

                                    <span class="badge amber">
                                        Dipinjam
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- Aksi Pengembalian -->
                            <td>

                                <?php if ($loan['status'] === 'dipinjam'): ?>

                                    <form
                                        method="post"
                                        class="inline-form"
                                        onsubmit="return confirm('Tandai buku sudah dikembalikan?')"
                                    >

                                        <?= csrf_field() ?>

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $loan['id'] ?>"
                                        >

                                        <button
                                            class="button button-small"
                                            type="submit"
                                        >
                                            Terima Kembali
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span class="muted small">
                                        Selesai
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


<?php

require __DIR__ . '/../includes/footer.php';

?>
