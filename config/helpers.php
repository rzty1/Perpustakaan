<?php
function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_staff(): bool
{
    return is_logged_in() && in_array(current_user()['role'], ['admin', 'petugas'], true);
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Silakan login terlebih dahulu.');
        $insideAdminFolder = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/');
        redirect($insideAdminFolder ? '../index.php' : 'index.php');
    }
}

function require_member(): void
{
    require_login();
    if (current_user()['role'] !== 'anggota') {
        redirect('dashboard_admin.php');
    }
}

function require_staff(): void
{
    require_login();
    if (!is_staff()) {
        $insideAdminFolder = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/');
        redirect($insideAdminFolder ? '../dashboard.php' : 'dashboard.php');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function take_flash(): ?array
{
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $message;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    if (!is_string($submitted) || !hash_equals(csrf_token(), $submitted)) {
        http_response_code(419);
        exit('Permintaan tidak valid. Silakan kembali dan muat ulang halaman.');
    }
}

function staff_id(): ?int
{
    return is_staff() ? (int)current_user()['id'] : null;
}

function active_loan_count(PDO $pdo): int
{
    return (int)$pdo->query("SELECT COUNT(*) FROM pinjam_buku WHERE status = 'dipinjam'")->fetchColumn();
}
