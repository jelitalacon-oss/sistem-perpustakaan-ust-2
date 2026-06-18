<?php
// ============================================
// FILE: buku/edit.php
// Dibuat oleh: Anggota 2
// ============================================
$pageTitle  = 'Edit Buku — Perpustakaan UST';
$activePage = 'buku';
require_once '../includes/auth.php';
require_once '../config/db.php';

$id   = (int)($_GET['id'] ?? 0);
$buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku WHERE id_buku=$id"));

if (!$buku) {
    header("Location: index.php?pesan=Buku tidak ditemukan&tipe=danger");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode      = bersihkan($conn, $_POST['kode_buku']);
    $judul     = bersihkan($conn, $_POST['judul']);
    $pengarang = bersihkan($conn, $_POST['pengarang']);
    $penerbit  = bersihkan($conn, $_POST['penerbit']);
    $tahun     = bersihkan($conn, $_POST['tahun_terbit']);
    $kategori  = bersihkan($conn, $_POST['kategori']);
    $stok      = (int)$_POST['stok'];

    $cek = mysqli_query($conn, "SELECT id_buku FROM buku WHERE kode_buku='$kode' AND id_buku != $id");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Kode buku sudah digunakan buku lain.";
    } else {
        $sql = "UPDATE buku SET kode_buku='$kode', judul='$judul', pengarang='$pengarang',
                penerbit='$penerbit', tahun_terbit='$tahun', kategori='$kategori', stok=$stok
                WHERE id_buku=$id";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?pesan=Data buku berhasil diperbarui&tipe=success");
            exit;
        }
        $error = "Gagal memperbarui: " . mysqli_error($conn);
    }
}

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><span class="page-icon"><i class="fas fa-book"></i></span> Edit Buku</h2>
    <a href="index.php" class="btn btn-outline-secondary" style="border-radius:8px;font-size:.875rem">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if ($error): ?>
<div class="alert alert-custom mb-3" style="background:#fdecea;color:#842029">
    <i class="fas fa-times-circle"></i> <?= $error ?>
</div>
<?php endif; ?>

<div class="konten-kartu" style="max-width:700px">
    <div class="kartu-header">
        <h5><i class="fas fa-edit me-2"></i>Edit Data Buku</h5>
    </div>
    <div class="kartu-body">
        <form method="POST" onsubmit="return validasiBuku()">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kode Buku *</label>
                    <input type="text" id="kode_buku" name="kode_buku" class="form-control"
                           value="<?= htmlspecialchars($buku['kode_buku']) ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Judul Buku *</label>
                    <input type="text" id="judul" name="judul" class="form-control"
                           value="<?= htmlspecialchars($buku['judul']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pengarang *</label>
                    <input type="text" name="pengarang" class="form-control" value="<?= htmlspecialchars($buku['pengarang']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penerbit</label>
                    <input type="text" name="penerbit" class="form-control" value="<?= htmlspecialchars($buku['penerbit'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" class="form-control" value="<?= $buku['tahun_terbit'] ?>" min="1900" max="2030">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">-- Pilih --</option>
                        <?php foreach(['Teknologi','Sains','Matematika','Bahasa','Sosial','Ekonomi','Lainnya'] as $k): ?>
                        <option value="<?= $k ?>" <?= $buku['kategori'] == $k ? 'selected' : '' ?>><?= $k ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stok</label>
                    <input type="number" id="stok" name="stok" class="form-control" value="<?= $buku['stok'] ?>" min="0">
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-hijau"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                <a href="index.php" class="btn btn-outline-secondary" style="border-radius:8px">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
