
<?php

require_once __DIR__ . '/config/app.php';

require_login();

// Pencarian buku
$q = trim($_GET['q'] ?? '');

$sql = "
    SELECT
        b.*,
        CASE
            WHEN EXISTS (
                SELECT 1
                FROM pinjam_buku p
                WHERE p.id_buku = b.id_buku
                AND p.status = 'dipinjam'
            )
            THEN 0
            ELSE 1
        END AS tersedia
    FROM buku b
";

$params = [];

if ($q !== '') {
    $sql .= "
        WHERE b.judul_buku LIKE ?
        OR b.pengarang_buku LIKE ?
    ";

    $params = [
        '%' . $q . '%',
        '%' . $q . '%'
    ];
}

$sql .= " ORDER BY b.judul_buku ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

$pageTitle = 'Daftar Buku';
$activePage = 'buku';

require __DIR__ . '/includes/header.php';

?>

<div class="page-heading">
    <div>
        <div class="eyebrow">Katalog perpustakaan</div>

        <h1>Daftar buku.</h1>

        <p>
            Temukan buku berdasarkan judul atau nama penulis.
            Status tersedia diperbarui otomatis dari data peminjaman.
        </p>
    </div>
</div>

<div class="toolbar">

    <form class="search-bar" method="get">

        <input
            name="q"
            value="<?= e($q) ?>"
            placeholder="Cari judul buku atau nama penulis..."
        >

        <button class="button" type="submit">
            Cari
        </button>

        <?php if ($q !== ''): ?>
            <a class="button button-ghost" href="buku.php">
                Reset
            </a>
        <?php endif; ?>

    </form>

    <span class="muted small">
        <?= count($books) ?> buku ditemukan
    </span>

</div>

<?php if (!$books): ?>

    <div class="empty-state">
        Buku tidak ditemukan. Coba kata kunci yang berbeda.
    </div>

<?php else: ?>

    <div class="grid book-grid">

        <?php foreach ($books as $book): ?>

            <article class="book-card">

                <div class="book-cover">
                    B
                </div>

                <h3>
                    <?= e($book['judul_buku']) ?>
                </h3>

                <div class="book-author">
                    Oleh <?= e($book['pengarang_buku']) ?>
                </div>

                <div class="book-card-foot">

                    <span class="badge <?= $book['tersedia'] ? '' : 'gray' ?>">
                        <?= $book['tersedia'] ? 'Tersedia' : 'Sedang dipinjam' ?>
                    </span>

                    <?php if (current_user()['role'] === 'anggota'): ?>

                        <form
                            class="inline-form"
                            method="post"
                            action="pinjam.php"
                        >

                            <?= csrf_field() ?>

                            <input
                                type="hidden"
                                name="id_buku"
                                value="<?= (int)$book['id_buku'] ?>"
                            >

                            <button
                                class="button button-small"
                                type="submit"
                                <?= !$book['tersedia'] ? 'disabled' : '' ?>
                            >
                                Pinjam
                            </button>

                        </form>

                    <?php else: ?>

                        <a class="muted-link" href="admin/buku.php">
                            Kelola →
                        </a>

                    <?php endif; ?>

                </div>

            </article>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
