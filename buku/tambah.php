<?php
// ============================================
// FILE: buku/tambah.php
// Dibuat oleh: Anggota 2
// ============================================
$pageTitle  = 'Tambah Buku — Perpustakaan UST';
$activePage = 'buku';
require_once '../includes/auth.php';
require_once '../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode      = bersihkan($conn, $_POST['kode_buku']);
    $judul     = bersihkan($conn, $_POST['judul']);
    $pengarang = bersihkan($conn, $_POST['pengarang']);
    $penerbit  = bersihkan($conn, $_POST['penerbit']);
    $tahun     = bersihkan($conn, $_POST['tahun_terbit']);
    $kategori  = bersihkan($conn, $_POST['kategori']);
    $stok      = (int)$_POST['stok'];

    // Cek kode unik
    $cek = mysqli_query($conn, "SELECT id_buku FROM buku WHERE kode_buku='$kode'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Kode buku <strong>$kode</strong> sudah digunakan. Gunakan kode lain.";
    } elseif (!$kode || !$judul || !$pengarang) {
        $error = "Kode buku, judul, dan pengarang wajib diisi.";
    } else {
        $sql = "INSERT INTO buku (kode_buku, judul, pengarang, penerbit, tahun_terbit, kategori, stok)
                VALUES ('$kode','$judul','$pengarang','$penerbit','$tahun','$kategori','$stok')";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?pesan=Buku berhasil ditambahkan&tipe=success");
            exit;
        } else {
            $error = "Gagal menyimpan: " . mysqli_error($conn);
        }
    }
}

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><span class="page-icon"><i class="fas fa-book"></i></span> Tambah Buku</h2>
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
        <h5><i class="fas fa-plus me-2"></i>Form Tambah Buku</h5>
    </div>
    <div class="kartu-body">
        <form method="POST" onsubmit="return validasiBuku()">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kode Buku <span class="text-danger">*</span></label>
                    <input type="text" id="kode_buku" name="kode_buku" class="form-control"
                           placeholder="mis. BK006" value="<?= htmlspecialchars($_POST['kode_buku'] ?? '') ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Judul Buku <span class="text-danger">*</span></label>
                    <input type="text" id="judul" name="judul" class="form-control"
                           placeholder="Masukkan judul buku" value="<?= htmlspecialchars($_POST['judul'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pengarang <span class="text-danger">*</span></label>
                    <input type="text" name="pengarang" class="form-control"
                           placeholder="Nama pengarang" value="<?= htmlspecialchars($_POST['pengarang'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Penerbit</label>
                    <input type="text" name="penerbit" class="form-control"
                           placeholder="Nama penerbit" value="<?= htmlspecialchars($_POST['penerbit'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" class="form-control"
                           placeholder="2024" min="1900" max="2030"
                           value="<?= htmlspecialchars($_POST['tahun_terbit'] ?? date('Y')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">-- Pilih --</option>
                        <?php foreach(['Teknologi','Sains','Matematika','Bahasa','Sosial','Ekonomi','Lainnya'] as $k): ?>
                        <option value="<?= $k ?>" <?= ($_POST['kategori'] ?? '') == $k ? 'selected' : '' ?>><?= $k ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stok</label>
                    <input type="number" id="stok" name="stok" class="form-control"
                           min="0" value="<?= htmlspecialchars($_POST['stok'] ?? 1) ?>">
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-hijau">
                    <i class="fas fa-save me-1"></i> Simpan Buku
                </button>
                <a href="index.php" class="btn btn-outline-secondary" style="border-radius:8px">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
