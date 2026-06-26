<?php
// ============================================
// FILE: peminjaman/kembali.php
// Dibuat oleh: Anggota 3 — Logika pengembalian & denda
// ============================================
$pageTitle  = 'Pengembalian Buku — Perpustakaan UST';
$activePage = 'peminjaman';
require_once '../includes/auth.php';
require_once '../config/db.php';

$id   = (int)($_GET['id'] ?? 0);
$data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT p.*, a.nama AS nama_anggota, a.kode_anggota,
           b.judul, b.kode_buku, b.id_buku AS bid
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.id_pinjam=$id AND p.status='dipinjam'
"));

if (!$data) {
    header("Location: index.php?pesan=Data peminjaman tidak ditemukan atau sudah dikembalikan&tipe=danger");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tgl_nyata = bersihkan($conn, $_POST['tgl_kembali_nyata']);

    if (!$tgl_nyata) {
        $error = "Tanggal pengembalian wajib diisi.";
    } else {
        // Hitung denda
        $denda  = hitungDenda($data['tgl_kembali_rencana'], $tgl_nyata);
        $status = $denda > 0 ? 'terlambat' : 'dikembalikan';

        // Update peminjaman
        $sql = "UPDATE peminjaman SET
                    tgl_kembali_nyata='$tgl_nyata',
                    denda=$denda,
                    status='$status'
                WHERE id_pinjam=$id";

        if (mysqli_query($conn, $sql)) {
            // Kembalikan stok buku
            mysqli_query($conn, "UPDATE buku SET stok = stok + 1 WHERE id_buku={$data['bid']}");

            $pesan = $denda > 0
                ? "Buku dikembalikan dengan denda " . formatRupiah($denda)
                : "Buku berhasil dikembalikan tepat waktu";
            header("Location: index.php?pesan=" . urlencode($pesan) . "&tipe=" . ($denda > 0 ? 'warning' : 'success'));
            exit;
        }
        $error = "Gagal memproses: " . mysqli_error($conn);
    }
}

// Preview denda untuk hari ini
$dendaHariIni = hitungDenda($data['tgl_kembali_rencana']);

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><span class="page-icon"><i class="fas fa-undo"></i></span> Proses Pengembalian</h2>
    <a href="index.php" class="btn btn-outline-secondary" style="border-radius:8px;font-size:.875rem">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if ($error): ?>
<div class="alert alert-custom mb-3" style="background:#fdecea;color:#842029">
    <i class="fas fa-times-circle"></i> <?= $error ?>
</div>
<?php endif; ?>

<!-- Info Peminjaman -->
<div class="konten-kartu" style="max-width:650px;margin-bottom:1.5rem">
    <div class="kartu-header"><h5><i class="fas fa-info-circle me-2"></i>Detail Peminjaman</h5></div>
    <div class="kartu-body">
        <div class="row g-2" style="font-size:.9rem">
            <div class="col-5 text-muted">Anggota</div>
            <div class="col-7"><strong><?= htmlspecialchars($data['nama_anggota']) ?></strong> <small class="text-muted">(<?= htmlspecialchars($data['kode_anggota']) ?>)</small></div>
            <div class="col-5 text-muted">Buku</div>
            <div class="col-7"><strong><?= htmlspecialchars($data['judul']) ?></strong> <small class="text-muted">(<?= htmlspecialchars($data['kode_buku']) ?>)</small></div>
            <div class="col-5 text-muted">Tanggal Pinjam</div>
            <div class="col-7"><?= formatTanggal($data['tgl_pinjam']) ?></div>
            <div class="col-5 text-muted">Batas Kembali</div>
            <div class="col-7">
                <?= formatTanggal($data['tgl_kembali_rencana']) ?>
                <?php if ($dendaHariIni > 0): ?>
                <span class="badge-status badge-terlambat ms-2">Terlambat <?= round((strtotime(date('Y-m-d')) - strtotime($data['tgl_kembali_rencana'])) / 86400) ?> hari</span>
                <?php endif; ?>
            </div>
            <?php if ($dendaHariIni > 0): ?>
            <div class="col-5 text-muted">Denda saat ini</div>
            <div class="col-7"><span class="text-danger fw-bold"><?= formatRupiah($dendaHariIni) ?></span></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Form Pengembalian -->
<div class="konten-kartu" style="max-width:650px">
    <div class="kartu-header"><h5><i class="fas fa-check-circle me-2"></i>Form Pengembalian</h5></div>
    <div class="kartu-body">
        <form method="POST">
            <input type="hidden" id="tgl_kembali_rencana_asli" value="<?= $data['tgl_kembali_rencana'] ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pengembalian Nyata <span class="text-danger">*</span></label>
                    <input type="date" id="tgl_kembali_nyata" name="tgl_kembali_nyata" class="form-control"
                           value="<?= date('Y-m-d') ?>" onchange="hitungDendaPreview()" required>
                </div>
                <div class="col-12">
                    <div id="preview_denda" class="p-3 rounded" style="background:#f4f9f5;font-size:.88rem">
                        <!-- diisi JS -->
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-aksen"><i class="fas fa-check me-1"></i> Konfirmasi Pengembalian</button>
                <a href="index.php" class="btn btn-outline-secondary" style="border-radius:8px">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
// Hitung denda segera saat halaman load
document.addEventListener('DOMContentLoaded', hitungDendaPreview);
</script>

<?php require_once '../includes/footer.php'; ?>
