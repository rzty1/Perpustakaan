
<?php

require_once __DIR__ . '/../config/app.php';

require_staff();

$error = '';

// Proses update data anggota
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $id = (int) ($_POST['id_anggota'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $status = $_POST['status'] ?? 'aktif';

    if ($nama === '') {
        $error = 'Nama anggota wajib diisi.';

    } elseif (!in_array($status, ['aktif', 'nonaktif'], true)) {
        $error = 'Status anggota tidak valid.';

    } else {

        $stmt = $pdo->prepare(
            'UPDATE anggota SET nama=?, status=? WHERE id_anggota=?'
        );

        $stmt->execute([$nama, $status, $id]);

        flash('success', 'Data anggota berhasil diperbarui.');

        redirect('anggota.php');
    }
}

// Ambil data anggota untuk diedit
$edit = null;

if (isset($_GET['edit'])) {

    $stmt = $pdo->prepare(
        'SELECT * FROM anggota WHERE id_anggota=?'
    );

    $stmt->execute([(int) $_GET['edit']]);

    $edit = $stmt->fetch();
}

// Pencarian anggota
$q = trim($_GET['q'] ?? '');

$sql = 'SELECT * FROM anggota';
$params = [];

if ($q !== '') {

    $sql .= ' WHERE nama LIKE ? OR username LIKE ? OR email LIKE ?';

    $params = [
        '%' . $q . '%',
        '%' . $q . '%',
        '%' . $q . '%'
    ];
}

$sql .= ' ORDER BY id_anggota DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$members = $stmt->fetchAll();

// Pengaturan halaman
$pageTitle = 'Kelola Anggota';
$activePage = 'anggota';

require __DIR__ . '/../includes/header.php';

?>

<!-- Judul Halaman -->
<div class="page-heading">

    <div>
        <div class="eyebrow">Manajemen anggota</div>

        <h1>Data anggota.</h1>

        <p>
            Lihat anggota yang terdaftar dan perbarui
            nama atau status keanggotaannya.
        </p>
    </div>

</div>

<!-- Pesan Error -->
<?php if ($error): ?>

    <div class="alert alert-error">
        <?= e($error) ?>
    </div>

<?php endif; ?>


<!-- Form Edit Anggota -->
<?php if ($edit): ?>

    <section class="panel modalish" style="margin-bottom: 20px">

        <div class="panel-head">

            <div>
                <h2>
                    Edit anggota #<?= (int) $edit['id_anggota'] ?>
                </h2>

                <p>
                    Username dan email ditampilkan sebagai identitas akun.
                </p>
            </div>

        </div>

        <form method="post">

            <?= csrf_field() ?>

            <input
                type="hidden"
                name="id_anggota"
                value="<?= (int) $edit['id_anggota'] ?>"
            >

            <div class="form-grid">

                <div class="form-group">

                    <label>Nama lengkap</label>

                    <input
                        name="nama"
                        value="<?= e($edit['nama']) ?>"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Status</label>

                    <select name="status">

                        <option
                            value="aktif"
                            <?= $edit['status'] === 'aktif' ? 'selected' : '' ?>
                        >
                            Aktif
                        </option>

                        <option
                            value="nonaktif"
                            <?= $edit['status'] === 'nonaktif' ? 'selected' : '' ?>
                        >
                            Nonaktif
                        </option>

                    </select>

                </div>

            </div>

            <div class="form-actions">

                <button class="button">
                    Simpan Perubahan
                </button>

                <a
                    class="button button-ghost"
                    href="anggota.php"
                >
                    Batal
                </a>

            </div>

        </form>

    </section>

<?php endif; ?>


<!-- Daftar Anggota -->
<section class="panel">

    <div class="panel-head">

        <div>

            <h2>Daftar anggota</h2>

            <p>
                <?= count($members) ?> akun terdaftar
            </p>

        </div>

        <!-- Form Pencarian -->
        <form class="search-bar" method="get">

            <input
                name="q"
                value="<?= e($q) ?>"
                placeholder="Cari nama, username, email"
            >

            <button class="button button-small">
                Cari
            </button>

        </form>

    </div>


    <!-- Kondisi Data Anggota -->
    <?php if (!$members): ?>

        <div class="empty-state">
            Belum ada anggota terdaftar.
        </div>

    <?php else: ?>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($members as $m): ?>

                        <tr>

                            <td>
                                #<?= (int) $m['id_anggota'] ?>
                            </td>

                            <td>
                                <strong>
                                    <?= e($m['nama']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= e($m['username']) ?>
                            </td>

                            <td>
                                <?= e($m['email']) ?>
                            </td>

                            <td>

                                <span class="badge <?= $m['status'] === 'aktif' ? '' : 'gray' ?>">

                                    <?= e(ucfirst($m['status'])) ?>

                                </span>

                            </td>

                            <td>

                                <a
                                    class="muted-link"
                                    href="anggota.php?edit=<?= (int) $m['id_anggota'] ?>"
                                >
                                    Edit
                                </a>

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
