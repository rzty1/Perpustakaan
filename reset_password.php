
<?php

require_once __DIR__ . '/config/app.php';

// Menentukan jenis akun
$type = ($_GET['type'] ?? 'anggota') === 'petugas'
    ? 'petugas'
    : 'anggota';

$error = '';

// Proses reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Menentukan tabel dan kolom ID berdasarkan jenis akun
    $table = $type === 'anggota' ? 'anggota' : 'petugas';
    $idColumn = $type === 'anggota' ? 'id_anggota' : 'id_petugas';

    // Memeriksa akun berdasarkan username
    $stmt = $pdo->prepare("
        SELECT
            {$idColumn} AS id,
            password_hash
        FROM {$table}
        WHERE username = ?
        LIMIT 1
    ");

    $stmt->execute([$username]);
    $account = $stmt->fetch();

    if (!$account || !password_verify($oldPassword, $account['password_hash'])) {

        $error = 'Username atau password lama tidak sesuai.';

    } elseif (strlen($newPassword) < 8) {

        $error = 'Password baru minimal 8 karakter.';

    } elseif ($newPassword !== $confirm) {

        $error = 'Konfirmasi password baru tidak sama.';

    } else {

        // Memperbarui password akun
        $update = $pdo->prepare("
            UPDATE {$table}
            SET password_hash = ?
            WHERE {$idColumn} = ?
        ");

        $update->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            $account['id']
        ]);

        flash(
            'success',
            'Password berhasil diperbarui. Silakan login kembali.'
        );

        redirect(
            $type === 'anggota'
                ? 'login_anggota.php'
                : 'login_petugas.php'
        );
    }
}

$pageTitle = 'Reset Password';

require __DIR__ . '/includes/header.php';

?>

<div class="auth-wrap">

    <section class="auth-card">

        <div class="eyebrow">
            Keamanan akun
        </div>

        <h1>Ganti password.</h1>

        <p>
            Masukkan username dan password lama,
            lalu tentukan password baru.
        </p>

        <?php if ($error): ?>

            <div class="alert alert-error">
                <?= e($error) ?>
            </div>

        <?php endif; ?>

        <div class="form-group">

            <label>
                Jenis akun
            </label>

            <div class="form-actions">

                <a
                    class="button <?= $type === 'anggota' ? '' : 'button-ghost' ?>"
                    href="reset_password.php?type=anggota"
                >
                    Anggota
                </a>

                <a
                    class="button <?= $type === 'petugas' ? '' : 'button-ghost' ?>"
                    href="reset_password.php?type=petugas"
                >
                    Admin / Petugas
                </a>

            </div>

        </div>

        <form method="post">

            <?= csrf_field() ?>

            <div class="form-group">

                <label>
                    Username
                </label>

                <input
                    name="username"
                    required
                    placeholder="Username akun"
                >

            </div>

            <div class="form-group">

                <label>
                    Password lama
                </label>

                <input
                    type="password"
                    name="old_password"
                    required
                >

            </div>

            <div class="form-group">

                <label>
                    Password baru
                </label>

                <input
                    type="password"
                    name="new_password"
                    minlength="8"
                    required
                >

            </div>

            <div class="form-group">

                <label>
                    Konfirmasi password baru
                </label>

                <input
                    type="password"
                    name="confirm_password"
                    minlength="8"
                    required
                >

            </div>

            <button
                class="button"
                type="submit"
                style="width:100%"
            >
                Simpan Password Baru
            </button>

        </form>

        <div class="auth-links">

            <a href="<?= $type === 'anggota' ? 'login_anggota.php' : 'login_petugas.php' ?>">
                ← Kembali ke login
            </a>

        </div>

    </section>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
