<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Perpustakaan UST' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?? '../' ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark" id="mainNav">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $baseUrl ?? '../' ?>index.php">
            <i class="fas fa-book-open me-2"></i>
            <span>Perpustakaan <strong>UST</strong></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage ?? '') == 'dashboard' ? 'active' : '' ?>" 
                       href="<?= $baseUrl ?? '../' ?>index.php">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage ?? '') == 'buku' ? 'active' : '' ?>" 
                       href="<?= $baseUrl ?? '../' ?>buku/index.php">
                        <i class="fas fa-book me-1"></i> Buku
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage ?? '') == 'anggota' ? 'active' : '' ?>" 
                       href="<?= $baseUrl ?? '../' ?>anggota/index.php">
                        <i class="fas fa-users me-1"></i> Anggota
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage ?? '') == 'peminjaman' ? 'active' : '' ?>" 
                       href="<?= $baseUrl ?? '../' ?>peminjaman/index.php">
                        <i class="fas fa-exchange-alt me-1"></i> Peminjaman
                    </a>
                </li>
                <li class="nav-item ms-2">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#"
                           data-bs-toggle="dropdown" style="background:rgba(255,255,255,0.1);border-radius:8px;padding:.45rem .9rem !important">
                            <i class="fas fa-user-circle"></i>
                            <span><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="border-radius:10px;border:1px solid #dde8dd;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:180px">
                            <li><span class="dropdown-item-text text-muted" style="font-size:.8rem;padding:.5rem 1rem">
                                Masuk sebagai <strong><?= htmlspecialchars($_SESSION['admin_user'] ?? 'admin') ?></strong>
                            </span></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= $baseUrl ?? '../' ?>logout.php"
                                   onclick="return confirm('Yakin ingin keluar?')">
                                    <i class="fas fa-sign-out-alt me-2"></i>Keluar
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- BREADCRUMB + CONTENT AREA -->
<div class="page-wrapper">
