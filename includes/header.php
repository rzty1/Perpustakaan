<?php
$pageTitle = $pageTitle ?? 'PerpusKita';
$activePage = $activePage ?? '';
$notice = take_flash();
$user = current_user();
$basePath = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/') ? '../' : '';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> · PerpusKita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $basePath ?>assets/style.css">
</head>
<body>
<div class="app-shell">
    <header class="topbar">
        <a class="brand" href="<?= $basePath . ($user ? ($user['role'] === 'anggota' ? 'dashboard.php' : 'dashboard_admin.php') : 'index.php') ?>">
            <span class="brand-mark">P</span><span>Perpus<span class="brand-light">Kita</span></span>
        </a>
        <?php if ($user): ?>
            <nav class="nav-links">
                <?php if ($user['role'] === 'anggota'): ?>
                    <a class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" href="<?= $basePath ?>dashboard.php">Dashboard</a>
                    <a class="<?= $activePage === 'buku' ? 'active' : '' ?>" href="<?= $basePath ?>buku.php">Daftar Buku</a>
                    <a class="<?= $activePage === 'pinjaman' ? 'active' : '' ?>" href="<?= $basePath ?>peminjaman_saya.php">Peminjaman Saya</a>
                <?php else: ?>
                    <a class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" href="<?= $basePath ?>dashboard_admin.php">Dashboard</a>
                    <a class="<?= $activePage === 'buku' ? 'active' : '' ?>" href="<?= $basePath ?>admin/buku.php">Buku</a>
                    <a class="<?= $activePage === 'anggota' ? 'active' : '' ?>" href="<?= $basePath ?>admin/anggota.php">Anggota</a>
                    <a class="<?= $activePage === 'peminjaman' ? 'active' : '' ?>" href="<?= $basePath ?>admin/peminjaman.php">Peminjaman</a>
                <?php endif; ?>
            </nav>
            <div class="topbar-user">
                <div class="avatar"><?= e(strtoupper(substr($user['nama'], 0, 1))) ?></div>
                <div class="user-meta"><strong><?= e($user['nama']) ?></strong><span><?= e(ucfirst($user['role'])) ?></span></div>
                <a class="logout-link" href="<?= $basePath ?>logout.php" title="Keluar">↗</a>
            </div>
        <?php else: ?>
            <div class="topbar-actions"><a class="muted-link" href="<?= $basePath ?>login_anggota.php">Login Anggota</a><a class="button button-small" href="<?= $basePath ?>login_petugas.php">Login Admin / Petugas</a></div>
        <?php endif; ?>
    </header>
    <main class="main-content">
        <?php if ($notice): ?>
            <div class="alert alert-<?= e($notice['type']) ?>"><?= e($notice['message']) ?></div>
        <?php endif; ?>
