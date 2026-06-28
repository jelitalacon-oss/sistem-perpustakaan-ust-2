<?php
// ============================================
// FILE: peminjaman/pinjam.php
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

require_once '../includes/header.php';
?>

<div class="page-header">
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
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

