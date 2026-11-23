
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

$error = '';

// Proses login anggota
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("
        SELECT
            id_anggota,
            nama,
            username,
            password_hash
        FROM anggota
        WHERE username = ?
        AND status = 'aktif'
        LIMIT 1
    ");

    $stmt->execute([$username]);
    $member = $stmt->fetch();

    if ($member && password_verify($password, $member['password_hash'])) {

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int) $member['id_anggota'],
            'nama' => $member['nama'],
            'role' => 'anggota'
        ];

        redirect('dashboard.php');
    }

    $error = 'Username atau password salah, atau akun tidak aktif.';
}

$pageTitle = 'Login Anggota';

require __DIR__ . '/includes/header.php';

?>

<div class="auth-wrap">

    <section class="auth-card">

        <div class="eyebrow">
            Area anggota
        </div>

        <h1>Selamat datang.</h1>

        <p>
            Login untuk melihat katalog dan mengelola peminjaman buku.
        </p>

        <?php if ($error): ?>

            <div class="alert alert-error">
                <?= e($error) ?>
            </div>

        <?php endif; ?>

        <form method="post">

            <?= csrf_field() ?>

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    id="username"
                    name="username"
                    required
                    autocomplete="username"
                    placeholder="Masukkan username"
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan password"
                >

            </div>

            <button
                class="button"
                type="submit"
                style="width:100%"
            >
                Login Anggota ↗
            </button>

        </form>

        <div class="auth-links">

            <a href="register.php">
                Belum punya akun? Registrasi
            </a>

            <a href="reset_password.php?type=anggota">
                Lupa password?
            </a>

        </div>

        <p class="hint">
            Admin/petugas memiliki halaman login terpisah.
        </p>

        <a
            class="muted-link"
            style="padding-left:0"
            href="login_petugas.php"
        >
            Login Admin / Petugas →
        </a>

    </section>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
