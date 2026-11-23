<?php
require_once __DIR__ . '/config/app.php';
require_member();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('buku.php');
verify_csrf();
$idBuku = filter_input(INPUT_POST, 'id_buku', FILTER_VALIDATE_INT);
if (!$idBuku) { flash('error', 'Buku tidak valid.'); redirect('buku.php'); }
try {
    $pdo->beginTransaction();
    // Kunci baris buku agar dua anggota tidak meminjam salinan yang sama secara bersamaan.
    $bookStmt = $pdo->prepare('SELECT id_buku FROM buku WHERE id_buku = ? FOR UPDATE');
    $bookStmt->execute([$idBuku]);
    if (!$bookStmt->fetch()) throw new RuntimeException('Buku tidak ditemukan.');
    $check = $pdo->prepare("SELECT COUNT(*) FROM pinjam_buku WHERE id_buku = ? AND status = 'dipinjam'");
    $check->execute([$idBuku]);
    if ((int)$check->fetchColumn() > 0) throw new RuntimeException('Buku sedang dipinjam anggota lain.');
    $insert = $pdo->prepare("INSERT INTO pinjam_buku (tanggal_pinajaman, id_buku, tanggal_kembali, id_anggota, id_petugas, status) VALUES (CURDATE(), ?, DATE_ADD(CURDATE(), INTERVAL 7 DAY), ?, NULL, 'dipinjam')");
    $insert->execute([$idBuku, (int)current_user()['id']]);
    $pdo->commit();
    flash('success', 'Peminjaman berhasil dicatat. Batas pengembalian adalah 7 hari sejak tanggal pinjam.');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash('error', $e instanceof RuntimeException ? $e->getMessage() : 'Peminjaman gagal diproses.');
}
redirect('peminjaman_saya.php');
