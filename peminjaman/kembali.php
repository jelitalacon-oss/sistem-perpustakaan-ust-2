<?php
// ============================================
// FILE: peminjaman/kembali.php
// Halaman: Proses Pengembalian
// ============================================

require_once '../includes/auth.php';
require_once '../config/db.php';

$pageTitle  = 'Proses Pengembalian — Perpustakaan UST';
$activePage = 'peminjaman';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: index.php?pesan=".urlencode('ID peminjaman tidak valid.')."&tipe=error");
    exit;
}

$q = mysqli_query($conn, "
    SELECT p.*, a.nama, b.judul
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.id_pinjam = '$id'
    LIMIT 1
");
$row = mysqli_fetch_assoc($q);

if (!$row) {
    header("Location: index.php?pesan=".urlencode('Data peminjaman tidak ditemukan.')."&tipe=error");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tgl_kembali_nyata = $_POST['tgl_kembali_nyata'] ?? date('Y-m-d');

    if (!$tgl_kembali_nyata) {
        header("Location: kembali.php?id=$id&pesan=".urlencode('Tanggal pengembalian wajib diisi.')."&tipe=error");
        exit;
    }

    $denda = hitungDenda($row['tgl_kembali_rencana'], $tgl_kembali_nyata);
    $id_petugas = (int)($_SESSION['admin_id'] ?? 0);

    mysqli_begin_transaction($conn);
    try {
        // update peminjaman
        $stmt = mysqli_prepare($conn, "
            UPDATE peminjaman
            SET status='dikembalikan',
                tgl_kembali_nyata=?,
                denda=?,
                id_petugas=?
            WHERE id_pinjam=? AND status='dipinjam'
        ");
        mysqli_stmt_bind_param($stmt, 'siii', $tgl_kembali_nyata, $denda, $id_petugas, $id);
        mysqli_stmt_execute($stmt);

        // kembalikan stok buku
        $stmt2 = mysqli_prepare($conn, "UPDATE buku SET stok = stok + 1 WHERE id_buku=?");
        mysqli_stmt_bind_param($stmt2, 'i', $row['id_buku']);
        mysqli_stmt_execute($stmt2);

        mysqli_commit($conn);

        header("Location: index.php?pesan=".urlencode('Pengembalian berhasil diproses.')."&tipe=success");
        exit;
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        header("Location: kembali.php?id=$id&pesan=".urlencode('Gagal memproses pengembalian.')."&tipe=error");
        exit;
    }
}

$pesan = $_GET['pesan'] ?? '';
$tipe  = $_GET['tipe'] ?? 'success';

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><span class="page-icon"><i class="fas fa-undo"></i></span> Proses Pengembalian</h2>
    <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
</div>

<?php if ($pesan): ?>
<div class="alert alert-custom alert-auto mb-3" style="background:<?= $tipe=='success'?'#d1e7dd':'#fdecea' ?>;color:<?= $tipe=='success'?'#0a3622':'#842029' ?>">
    <i class="fas fa-<?= $tipe=='success'?'check-circle':'times-circle' ?>"></i> <?= htmlspecialchars($pesan) ?>
</div>
<?php endif; ?>

<div class="konten-kartu">
    <div class="kartu-header">
        <h5><i class="fas fa-clipboard-check me-2"></i>Detail Peminjaman</h5>
    </div>
    <div class="kartu-body">
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <div class="p-3 rounded" style="background:#f7fbf7;border:1px solid #dde8dd;">
                    <div class="text-muted small">Anggota</div>
                    <div class="fw-semibold"><i class="fas fa-user-circle me-1 text-muted"></i><?= htmlspecialchars($row['nama']) ?></div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="p-3 rounded" style="background:#f7fbf7;border:1px solid #dde8dd;">
                    <div class="text-muted small">Buku</div>
                    <div class="fw-semibold"><i class="fas fa-book me-1 text-muted"></i><?= htmlspecialchars($row['judul']) ?></div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="p-3 rounded" style="background:#f7fbf7;border:1px solid #dde8dd;">
                    <div class="text-muted small">Tanggal Pinjam</div>
                    <div class="fw-semibold"><?= formatTanggal($row['tgl_pinjam']) ?></div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="p-3 rounded" style="background:#f7fbf7;border:1px solid #dde8dd;">
                    <div class="text-muted small">Tanggal Kembali Rencana</div>
                    <div class="fw-semibold"><?= formatTanggal($row['tgl_kembali_rencana']) ?></div>
                </div>
            </div>
        </div>

        <?php if ($row['status'] !== 'dipinjam'): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <p>Pengembalian sudah diproses (status: <strong><?= htmlspecialchars($row['status']) ?></strong>).</p>
            </div>
        <?php else: ?>
            <form method="POST" class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Tanggal Pengembalian Nyata</label>
                    <input type="date" name="tgl_kembali_nyata" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-12 col-md-6">
                    <?php
                        $dendaPreview = hitungDenda($row['tgl_kembali_rencana'], date('Y-m-d'));
                    ?>
                    <div class="p-3 rounded" style="background:#fff7ed;border:1px solid #fed7aa;">
                        <div class="text-muted small">Estimasi Denda Hari Ini</div>
                        <div class="fw-semibold"><?= $dendaPreview ? formatRupiah($dendaPreview) : 'Rp 0' ?></div>
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-hijau"><i class="fas fa-undo me-1"></i>Proses Pengembalian</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

