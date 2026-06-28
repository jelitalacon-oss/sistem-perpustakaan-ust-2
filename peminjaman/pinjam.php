<?php
// ============================================
// FILE: peminjaman/pinjam.php
< << anggota1-database
// Halaman: Catat Peminjaman
// ============================================

require_once '../includes/auth.php';
require_once '../config/db.php';

$pageTitle  = 'Catat Peminjaman — Perpustakaan UST';
$activePage = 'peminjaman';

$pesan = $_GET['pesan'] ?? '';
$tipe  = $_GET['tipe']  ?? 'success';

$anggotaQ = mysqli_query($conn, "SELECT id_anggota, nama FROM anggota WHERE status='aktif' ORDER BY nama ASC");
$bukuQ    = mysqli_query($conn, "SELECT id_buku, judul, stok FROM buku WHERE stok > 0 ORDER BY judul ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_anggota = (int)($_POST['id_anggota'] ?? 0);
    $id_buku    = (int)($_POST['id_buku'] ?? 0);
    $tgl_pinjam = $_POST['tgl_pinjam'] ?? '';
    $tgl_rencana = $_POST['tgl_kembali_rencana'] ?? '';

    if (!$id_anggota || !$id_buku || !$tgl_pinjam || !$tgl_rencana) {
        $msg = 'Semua data wajib diisi.';
        header("Location: pinjam.php?pesan=".urlencode($msg)."&tipe=error");
        exit;
    }

    // Ambil stok buku saat ini
    $stokRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok, judul FROM buku WHERE id_buku='$id_buku' LIMIT 1"));
    if (!$stokRow) {
        header("Location: pinjam.php?pesan=".urlencode('Buku tidak ditemukan.')."&tipe=error");
        exit;
    }
    if ((int)$stokRow['stok'] <= 0) {
        header("Location: pinjam.php?pesan=".urlencode('Stok buku tidak tersedia.')."&tipe=error");
        exit;
    }

    // Validasi tanggal
    if (strtotime($tgl_rencana) <= strtotime($tgl_pinjam)) {
        header("Location: pinjam.php?pesan=".urlencode('Tanggal kembali rencana harus setelah tanggal pinjam.')."&tipe=error");
        exit;
    }

    $id_petugas = (int)($_SESSION['admin_id'] ?? 0);

    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, "
            INSERT INTO peminjaman (id_anggota, id_buku, tgl_pinjam, tgl_kembali_rencana, status, id_petugas)
            VALUES (?, ?, ?, ?, 'dipinjam', ?)
        ");
        mysqli_stmt_bind_param($stmt, 'iissi', $id_anggota, $id_buku, $tgl_pinjam, $tgl_rencana, $id_petugas);
        mysqli_stmt_execute($stmt);

        // Kurangi stok buku
        $stmt2 = mysqli_prepare($conn, "UPDATE buku SET stok = stok - 1 WHERE id_buku = ? AND stok > 0");
        mysqli_stmt_bind_param($stmt2, 'i', $id_buku);
        mysqli_stmt_execute($stmt2);

        mysqli_commit($conn);

        header("Location: index.php?pesan=".urlencode('Peminjaman berhasil dicatat.')."&tipe=success");
        exit;
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        header("Location: pinjam.php?pesan=".urlencode('Gagal mencatat peminjaman.')."&tipe=error");
        exit;
    }
}

=======
// Dibuat oleh: Anggota 3 — Logika peminjaman
// ============================================
$pageTitle  = 'Catat Peminjaman — Perpustakaan UST';
$activePage = 'peminjaman';
require_once '../includes/auth.php';
require_once '../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_anggota = (int)$_POST['id_anggota'];
    $id_buku    = (int)$_POST['id_buku'];
    $tgl_pinjam = bersihkan($conn, $_POST['tgl_pinjam']);
    $tgl_kembali= bersihkan($conn, $_POST['tgl_kembali_rencana']);

    // Validasi server-side
    if (!$id_anggota || !$id_buku || !$tgl_pinjam || !$tgl_kembali) {
        $error = "Semua field wajib diisi.";
    } elseif ($tgl_kembali <= $tgl_pinjam) {
        $error = "Tanggal kembali harus setelah tanggal pinjam.";
    } else {
        // Cek stok buku
        $stokRes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok, judul FROM buku WHERE id_buku=$id_buku"));
        if (!$stokRes) {
            $error = "Buku tidak ditemukan.";
        } elseif ($stokRes['stok'] < 1) {
            $error = "Stok buku <strong>{$stokRes['judul']}</strong> habis, tidak bisa dipinjam.";
        } else {
            // Cek apakah anggota sudah meminjam buku yang sama
            $cekPinjam = mysqli_fetch_row(mysqli_query($conn,
                "SELECT COUNT(*) FROM peminjaman WHERE id_anggota=$id_anggota AND id_buku=$id_buku AND status='dipinjam'"));
            if ($cekPinjam[0] > 0) {
                $error = "Anggota ini sudah meminjam buku tersebut dan belum dikembalikan.";
            } else {
                // Simpan peminjaman
                $sql = "INSERT INTO peminjaman (id_anggota, id_buku, tgl_pinjam, tgl_kembali_rencana, status)
                        VALUES ($id_anggota, $id_buku, '$tgl_pinjam', '$tgl_kembali', 'dipinjam')";
                if (mysqli_query($conn, $sql)) {
                    // Kurangi stok buku
                    mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id_buku=$id_buku");
                    header("Location: index.php?pesan=Peminjaman berhasil dicatat&tipe=success");
                    exit;
                }
                $error = "Gagal menyimpan: " . mysqli_error($conn);
            }
        }
    }
}

// Ambil data untuk dropdown
$listAnggota = mysqli_query($conn, "SELECT id_anggota, kode_anggota, nama FROM anggota WHERE status='aktif' ORDER BY nama");
$listBuku    = mysqli_query($conn, "SELECT id_buku, kode_buku, judul, stok FROM buku WHERE stok > 0 ORDER BY judul");


require_once '../includes/header.php';
?>

<div class="page-header">
<<<<< anggota1-database
    <h2><span class="page-icon"><i class="fas fa-hand-holding-heart"></i></span> Catat Peminjaman</h2>
    <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
</div>

<?php if ($pesan): ?>
<div class="alert alert-custom alert-auto mb-3" style="background:<?= $tipe=='success'?'#d1e7dd':'#fdecea' ?>;color:<?= $tipe=='success'?'#0a3622':'#842029' ?>">
    <i class="fas fa-<?= $tipe=='success'?'check-circle':'times-circle' ?>"></i> <?= htmlspecialchars($pesan) ?>
</div>
<?php endif; ?>

<div class="konten-kartu">
    <div class="kartu-header">
        <h5><i class="fas fa-plus me-2"></i>Form Peminjaman</h5>
    </div>
    <div class="kartu-body">
        <form method="POST" class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Anggota</label>
                <select name="id_anggota" class="form-select" required>
                    <option value="">-- Pilih Anggota --</option>
                    <?php while ($a = mysqli_fetch_assoc($anggotaQ)): ?>
                        <option value="<?= (int)$a['id_anggota'] ?>"><?= htmlspecialchars($a['nama']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Buku (stok tersedia)</label>
                <select name="id_buku" class="form-select" required>
                    <option value="">-- Pilih Buku --</option>
                    <?php while ($b = mysqli_fetch_assoc($bukuQ)): ?>
                        <option value="<?= (int)$b['id_buku'] ?>">
                            <?= htmlspecialchars($b['judul']) ?> (Stok: <?= (int)$b['stok'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Tanggal Kembali Rencana</label>
                <input type="date" name="tgl_kembali_rencana" class="form-control" required>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-hijau"><i class="fas fa-save me-1"></i>Simpan Peminjaman</button>
=======
    <h2><span class="page-icon"><i class="fas fa-exchange-alt"></i></span> Catat Peminjaman</h2>
    <a href="index.php" class="btn btn-outline-secondary" style="border-radius:8px;font-size:.875rem">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if ($error): ?>
<div class="alert alert-custom mb-3" style="background:#fdecea;color:#842029">
    <i class="fas fa-times-circle"></i> <?= $error ?>
</div>
<?php endif; ?>

<div class="konten-kartu" style="max-width:650px">
    <div class="kartu-header"><h5><i class="fas fa-hand-holding-heart me-2"></i>Form Peminjaman Buku</h5></div>
    <div class="kartu-body">
        <form method="POST" onsubmit="return validasiPeminjaman()">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Anggota Peminjam <span class="text-danger">*</span></label>
                    <select id="id_anggota" name="id_anggota" class="form-select" required>
                        <option value="">-- Pilih Anggota --</option>
                        <?php while ($a = mysqli_fetch_assoc($listAnggota)): ?>
                        <option value="<?= $a['id_anggota'] ?>" <?= ($_POST['id_anggota'] ?? '') == $a['id_anggota'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['kode_anggota'] . ' — ' . $a['nama']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Buku yang Dipinjam <span class="text-danger">*</span></label>
                    <select id="id_buku" name="id_buku" class="form-select" required>
                        <option value="">-- Pilih Buku --</option>
                        <?php while ($b = mysqli_fetch_assoc($listBuku)): ?>
                        <option value="<?= $b['id_buku'] ?>" <?= ($_POST['id_buku'] ?? '') == $b['id_buku'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['kode_buku'] . ' — ' . $b['judul'] . ' (Stok: ' . $b['stok'] . ')') ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                    <?php if (mysqli_num_rows($listBuku) == 0): ?>
                    <div class="form-text text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Semua buku sedang habis stok.</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                    <input type="date" id="tgl_pinjam" name="tgl_pinjam" class="form-control"
                           value="<?= $_POST['tgl_pinjam'] ?? date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Rencana Tanggal Kembali <span class="text-danger">*</span></label>
                    <input type="date" id="tgl_kembali_rencana" name="tgl_kembali_rencana" class="form-control"
                           value="<?= $_POST['tgl_kembali_rencana'] ?? '' ?>" required>
                </div>
                <div class="col-12">
                    <div class="p-3 rounded" style="background:#f4f9f5;font-size:.85rem;color:#2d6a4f">
                        <i class="fas fa-info-circle me-1"></i>
                        Denda keterlambatan: <strong>Rp 1.000 per hari</strong>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-aksen"><i class="fas fa-save me-1"></i> Catat Peminjaman</button>
                <a href="index.php" class="btn btn-outline-secondary" style="border-radius:8px">Batal</a>

            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<<<<< anggota1-database

=======

