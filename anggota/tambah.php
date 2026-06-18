<?php
// ============================================
// FILE: anggota/tambah.php
// Dibuat oleh: Anggota 2
// ============================================
$pageTitle  = 'Tambah Anggota — Perpustakaan UST';
$activePage = 'anggota';
require_once '../includes/auth.php';
require_once '../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode   = bersihkan($conn, $_POST['kode_anggota']);
    $nama   = bersihkan($conn, $_POST['nama']);
    $alamat = bersihkan($conn, $_POST['alamat']);
    $hp     = bersihkan($conn, $_POST['no_hp']);
    $email  = bersihkan($conn, $_POST['email']);
    $tgl    = bersihkan($conn, $_POST['tgl_daftar']);
    $status = $_POST['status'] == 'nonaktif' ? 'nonaktif' : 'aktif';

    $cek = mysqli_query($conn, "SELECT id_anggota FROM anggota WHERE kode_anggota='$kode'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Kode anggota <strong>$kode</strong> sudah digunakan.";
    } elseif (!$kode || !$nama) {
        $error = "Kode anggota dan nama wajib diisi.";
    } else {
        $sql = "INSERT INTO anggota (kode_anggota, nama, alamat, no_hp, email, tgl_daftar, status)
                VALUES ('$kode','$nama','$alamat','$hp','$email','$tgl','$status')";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?pesan=Anggota berhasil didaftarkan&tipe=success");
            exit;
        }
        $error = "Gagal menyimpan: " . mysqli_error($conn);
    }
}

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><span class="page-icon"><i class="fas fa-users"></i></span> Daftar Anggota Baru</h2>
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
    <div class="kartu-header"><h5><i class="fas fa-user-plus me-2"></i>Form Pendaftaran Anggota</h5></div>
    <div class="kartu-body">
        <form method="POST" onsubmit="return validasiAnggota()">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kode Anggota *</label>
                    <input type="text" id="kode_anggota" name="kode_anggota" class="form-control"
                           placeholder="mis. AG004" value="<?= htmlspecialchars($_POST['kode_anggota'] ?? '') ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" id="nama" name="nama" class="form-control"
                           placeholder="Nama lengkap" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2"
                              placeholder="Alamat lengkap"><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. HP</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-control"
                           placeholder="081234567890" value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                           placeholder="email@contoh.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Daftar</label>
                    <input type="date" name="tgl_daftar" class="form-control"
                           value="<?= $_POST['tgl_daftar'] ?? date('Y-m-d') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-hijau"><i class="fas fa-save me-1"></i> Daftarkan</button>
                <a href="index.php" class="btn btn-outline-secondary" style="border-radius:8px">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
