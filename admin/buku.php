
<?php

require_once __DIR__ . '/../config/app.php';

require_staff();

$error = '';

// Proses tambah, edit, dan hapus buku
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $action = $_POST['action'] ?? '';

    try {

        // Tambah atau edit buku
        if ($action === 'save') {

            $id = (int) ($_POST['id_buku'] ?? 0);
            $judul = trim($_POST['judul_buku'] ?? '');
            $pengarang = trim($_POST['pengarang_buku'] ?? '');

            if ($judul === '' || $pengarang === '') {
                throw new RuntimeException(
                    'Judul dan nama pengarang wajib diisi.'
                );
            }

            if ($id > 0) {

                $stmt = $pdo->prepare(
                    'UPDATE buku SET judul_buku = ?, pengarang_buku = ? WHERE id_buku = ?'
                );

                $stmt->execute([$judul, $pengarang, $id]);

                flash('success', 'Data buku berhasil diperbarui.');

            } else {

                $stmt = $pdo->prepare(
                    'INSERT INTO buku (judul_buku, pengarang_buku) VALUES (?, ?)'
                );

                $stmt->execute([$judul, $pengarang]);

                flash('success', 'Buku baru berhasil ditambahkan.');
            }

            redirect('buku.php');

        // Hapus buku
        } elseif ($action === 'delete') {

            $stmt = $pdo->prepare(
                'DELETE FROM buku WHERE id_buku = ?'
            );

            $stmt->execute([
                (int) ($_POST['id_buku'] ?? 0)
            ]);

            flash('success', 'Buku berhasil dihapus.');

            redirect('buku.php');
        }

    } catch (PDOException $e) {

        $error = 'Buku tidak dapat dihapus karena sudah tercatat pada riwayat peminjaman.';

    } catch (RuntimeException $e) {

        $error = $e->getMessage();
    }
}


// Ambil data buku untuk diedit
$edit = null;

if (isset($_GET['edit'])) {

    $stmt = $pdo->prepare(
        'SELECT * FROM buku WHERE id_buku=?'
    );

    $stmt->execute([
        (int) $_GET['edit']
    ]);

    $edit = $stmt->fetch();
}


// Pencarian buku
$q = trim($_GET['q'] ?? '');

$sql = "
    SELECT b.*,
        CASE
            WHEN EXISTS (
                SELECT 1
                FROM pinjam_buku p
                WHERE p.id_buku = b.id_buku
                AND p.status = 'dipinjam'
            )
            THEN 'Dipinjam'
            ELSE 'Tersedia'
        END AS status_buku
    FROM buku b
";

$params = [];

if ($q !== '') {

    $sql .= ' WHERE b.judul_buku LIKE ? OR b.pengarang_buku LIKE ?';

    $params = [
        '%' . $q . '%',
        '%' . $q . '%'
    ];
}

$sql .= ' ORDER BY b.id_buku DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$books = $stmt->fetchAll();


// Pengaturan halaman
$pageTitle = 'Kelola Buku';
$activePage = 'buku';

require __DIR__ . '/../includes/header.php';

?>


<!-- Judul Halaman -->
<div class="page-heading">

    <div>

        <div class="eyebrow">
            Manajemen katalog
        </div>

        <h1>Kelola buku.</h1>

        <p>
            Tambah, ubah, dan hapus data buku.
            Ketersediaan dihitung dari peminjaman yang masih aktif.
        </p>

    </div>

</div>


<!-- Pesan Error -->
<?php if ($error): ?>

    <div class="alert alert-error">
        <?= e($error) ?>
    </div>

<?php endif; ?>


<!-- Form Tambah dan Edit Buku -->
<div class="grid two-col">

    <section class="panel">

        <div class="panel-head">

            <div>

                <h2>
                    <?= $edit ? 'Edit buku' : 'Tambah buku' ?>
                </h2>

                <p>
                    Data sesuai class diagram buku.
                </p>

            </div>

        </div>


        <form method="post">

            <?= csrf_field() ?>

            <input
                type="hidden"
                name="action"
                value="save"
            >

            <input
                type="hidden"
                name="id_buku"
                value="<?= (int) ($edit['id_buku'] ?? 0) ?>"
            >


            <!-- Input Judul Buku -->
            <div class="form-group">

                <label>Judul buku</label>

                <input
                    name="judul_buku"
                    required
                    value="<?= e($edit['judul_buku'] ?? '') ?>"
                    placeholder="Masukkan judul buku"
                >

            </div>


            <!-- Input Pengarang -->
            <div class="form-group">

                <label>Pengarang buku</label>

                <input
                    name="pengarang_buku"
                    required
                    value="<?= e($edit['pengarang_buku'] ?? '') ?>"
                    placeholder="Masukkan nama pengarang"
                >

            </div>


            <!-- Tombol Aksi -->
            <div class="form-actions">

                <button class="button" type="submit">

                    <?= $edit ? 'Simpan Perubahan' : 'Tambah Buku' ?>

                </button>

                <?php if ($edit): ?>

                    <a
                        class="button button-ghost"
                        href="buku.php"
                    >
                        Batal
                    </a>

                <?php endif; ?>

            </div>

        </form>

    </section>


    <!-- Catatan Pengelolaan -->
    <section class="panel">

        <div class="panel-head">

            <div>

                <h2>Catatan pengelolaan</h2>

                <p>
                    Informasi status katalog.
                </p>

            </div>

        </div>

        <p class="muted">

            Buku dengan peminjaman aktif akan otomatis
            berstatus <strong>Tidak tersedia</strong>.

            Setelah petugas menandai buku telah dikembalikan,
            buku akan tersedia kembali.

        </p>

        <div class="hint">

            Penghapusan buku yang pernah memiliki transaksi
            peminjaman dibatasi agar riwayat tetap terjaga.

        </div>

    </section>

</div>


<!-- Daftar Koleksi Buku -->
<section class="panel section-spacer">

    <div class="panel-head">

        <div>

            <h2>Daftar koleksi</h2>

            <p>
                <?= count($books) ?> data buku
            </p>

        </div>


        <!-- Form Pencarian -->
        <form class="search-bar" method="get">

            <input
                name="q"
                value="<?= e($q) ?>"
                placeholder="Cari judul atau pengarang"
            >

            <button class="button button-small">
                Cari
            </button>

        </form>

    </div>


    <!-- Kondisi Data Buku -->
    <?php if (!$books): ?>

        <div class="empty-state">
            Belum ada data buku.
        </div>

    <?php else: ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Judul buku</th>
                        <th>Pengarang</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($books as $book): ?>

                        <tr>

                            <td>
                                #<?= (int) $book['id_buku'] ?>
                            </td>

                            <td>

                                <strong>
                                    <?= e($book['judul_buku']) ?>
                                </strong>

                            </td>

                            <td>
                                <?= e($book['pengarang_buku']) ?>
                            </td>

                            <td>

                                <span class="badge <?= $book['status_buku'] === 'Tersedia' ? '' : 'gray' ?>">

                                    <?= e($book['status_buku']) ?>

                                </span>

                            </td>

                            <td>

                                <!-- Tombol Edit -->
                                <a
                                    class="muted-link"
                                    href="buku.php?edit=<?= (int) $book['id_buku'] ?>"
                                >
                                    Edit
                                </a>


                                <!-- Tombol Hapus -->
                                <form
                                    class="inline-form"
                                    method="post"
                                    onsubmit="return confirm('Hapus buku ini?')"
                                >

                                    <?= csrf_field() ?>

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="delete"
                                    >

                                    <input
                                        type="hidden"
                                        name="id_buku"
                                        value="<?= (int) $book['id_buku'] ?>"
                                    >

                                    <button
                                        class="muted-link danger-text"
                                        style="background: none; border: 0; cursor: pointer"
                                        type="submit"
                                    >
                                        Hapus
                                    </button>

                                </form>

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
