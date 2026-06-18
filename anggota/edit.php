<?php
// ============================================
// FILE: anggota/edit.php
// Dibuat oleh: Anggota 2
// ============================================
$pageTitle  = 'Edit Anggota — Perpustakaan UST';
$activePage = 'anggota';
require_once '../includes/auth.php';
require_once '../config/db.php';

$id      = (int)($_GET['id'] ?? 0);
$anggota = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM anggota WHERE id_anggota=$id"));
if (!$anggota) { header("Location: index.php?pesan=Anggota tidak ditemukan&tipe=danger"); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode   = bersihkan($conn, $_POST['kode_anggota']);
    $nama   = bersihkan($conn, $_POST['nama']);
    $alamat = bersihkan($conn, $_POST['alamat']);
    $hp     = bersihkan($conn, $_POST['no_hp']);
    $email  = bersihkan($conn, $_POST['email']);
    $tgl    = bersihkan($conn, $_POST['tgl_daftar']);
    $status = $_POST['status'] == 'nonaktif' ? 'nonaktif' : 'aktif';

    $cek = mysqli_query($conn, "SELECT id_anggota FROM anggota WHERE kode_anggota='$kode' AND id_anggota != $id");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Kode anggota sudah dipakai anggota lain.";
    } else {
        $sql = "UPDATE anggota SET kode_anggota='$kode', nama='$nama', alamat='$alamat',
                no_hp='$hp', email='$email', tgl_daftar='$tgl', status='$status'
                WHERE id_anggota=$id";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?pesan=Data anggota berhasil diperbarui&tipe=success"); exit;
        }
        $error = "Gagal memperbarui: " . mysqli_error($conn);
    }
}
require_once '../includes/header.php';
?>
<div class="page-header">
    <h2><span class="page-icon"><i class="fas fa-users"></i></span> Edit Anggota</h2>
    <a href="index.php" class="btn btn-outline-secondary" style="border-radius:8px;font-size:.875rem"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
</div>
<?php if ($error): ?>
<div class="alert alert-custom mb-3" style="background:#fdecea;color:#842029"><i class="fas fa-times-circle"></i> <?= $error ?></div>
<?php endif; ?>
<div class="konten-kartu" style="max-width:650px">
    <div class="kartu-header"><h5><i class="fas fa-edit me-2"></i>Edit Data Anggota</h5></div>
    <div class="kartu-body">
        <form method="POST" onsubmit="return validasiAnggota()">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kode Anggota *</label>
                    <input type="text" id="kode_anggota" name="kode_anggota" class="form-control" value="<?= htmlspecialchars($anggota['kode_anggota']) ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" id="nama" name="nama" class="form-control" value="<?= htmlspecialchars($anggota['nama']) ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars($anggota['alamat'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. HP</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-control" value="<?= htmlspecialchars($anggota['no_hp'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($anggota['email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Daftar</label>
                    <input type="date" name="tgl_daftar" class="form-control" value="<?= $anggota['tgl_daftar'] ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="aktif" <?= $anggota['status']=='aktif'?'selected':'' ?>>Aktif</option>
                        <option value="nonaktif" <?= $anggota['status']=='nonaktif'?'selected':'' ?>>Non-Aktif</option>
                    </select>
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
