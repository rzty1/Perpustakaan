<?php
require_once __DIR__ . '/config/app.php';
if (is_logged_in()) redirect(current_user()['role'] === 'anggota' ? 'dashboard.php' : 'dashboard_admin.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if ($nama === '' || $username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Lengkapi nama, username, dan email yang valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO anggota (nama, username, email, password_hash, status) VALUES (?, ?, ?, ?, 'aktif')");
            $stmt->execute([$nama, $username, $email, password_hash($password, PASSWORD_DEFAULT)]);
            flash('success', 'Registrasi berhasil. Silakan login sebagai anggota.');
            redirect('login_anggota.php');
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000' ? 'Username atau email sudah digunakan.' : 'Registrasi gagal. Periksa kembali data Anda.';
        }
    }
}
$pageTitle = 'Registrasi Anggota';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap"><section class="auth-card">
    <div class="eyebrow">Buat akun anggota</div><h1>Mulai membaca.</h1><p>Isi data berikut untuk membuat akun perpustakaan.</p>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post"><?= csrf_field() ?>
        <div class="form-group"><label>Nama lengkap</label><input name="nama" required value="<?= e($_POST['nama'] ?? '') ?>" placeholder="Nama Anda"></div>
        <div class="form-group"><label>Username</label><input name="username" required value="<?= e($_POST['username'] ?? '') ?>" placeholder="Buat username"></div>
        <div class="form-group"><label>Email</label><input name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="nama@email.com"></div>
        <div class="form-grid"><div class="form-group"><label>Password</label><input name="password" type="password" minlength="8" required placeholder="Min. 8 karakter"></div><div class="form-group"><label>Ulangi password</label><input name="confirm_password" type="password" required placeholder="Ulangi password"></div></div>
        <button class="button" type="submit" style="width:100%">Buat Akun Anggota</button>
    </form>
    <div class="auth-links"><a href="login_anggota.php">Sudah punya akun? Login</a><a href="index.php">Halaman utama</a></div>
</section></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
