<?php
// ============================================
// FILE: anggota/index.php
// Dibuat oleh: Anggota 2
// ============================================
$pageTitle  = 'Data Anggota — Perpustakaan UST';
$activePage = 'anggota';
require_once '../includes/auth.php';
require_once '../config/db.php';

$pesan = $_GET['pesan'] ?? '';
$tipe  = $_GET['tipe']  ?? 'success';
$cari  = isset($_GET['cari']) ? bersihkan($conn, $_GET['cari']) : '';
$where = $cari ? "WHERE nama LIKE '%$cari%' OR kode_anggota LIKE '%$cari%' OR no_hp LIKE '%$cari%'" : '';
$data  = mysqli_query($conn, "SELECT * FROM anggota $where ORDER BY id_anggota DESC");

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><span class="page-icon"><i class="fas fa-users"></i></span> Data Anggota</h2>
    <a href="tambah.php" class="btn btn-hijau"><i class="fas fa-user-plus me-1"></i> Daftar Anggota</a>
</div>

<?php if ($pesan): ?>
<div class="alert alert-custom alert-auto mb-3" style="background:<?= $tipe=='success'?'#d1e7dd':'#fdecea' ?>;color:<?= $tipe=='success'?'#0a3622':'#842029' ?>">
    <i class="fas fa-<?= $tipe=='success'?'check-circle':'times-circle' ?>"></i> <?= htmlspecialchars($pesan) ?>
</div>
<?php endif; ?>

<div class="konten-kartu">
    <div class="kartu-header">
        <h5><i class="fas fa-list me-2"></i>Daftar Anggota (<?= mysqli_num_rows($data) ?> data)</h5>
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="cariInput" class="form-control form-control-sm"
                   placeholder="Cari nama, kode..."
                   oninput="filterTabel('cariInput','tabelAnggota')">
        </div>
    </div>
    <div class="kartu-body p-0">
        <div class="table-responsive">
        <table class="tabel-utama" id="tabelAnggota">
            <thead>
                <tr>
                    <th>No</th><th>Kode</th><th>Nama</th><th>No HP</th>
                    <th>Email</th><th>Tgl Daftar</th><th>Status</th><th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($data) > 0):
                $no = 1;
                while ($r = mysqli_fetch_assoc($data)): ?>
            <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td><code style="font-size:.8rem;background:#f0f4f0;padding:2px 6px;border-radius:4px"><?= htmlspecialchars($r['kode_anggota']) ?></code></td>
                <td><strong><?= htmlspecialchars($r['nama']) ?></strong>
                    <?php if ($r['alamat']): ?><br><small class="text-muted"><?= htmlspecialchars(substr($r['alamat'],0,40)) ?>...</small><?php endif; ?></td>
                <td><?= htmlspecialchars($r['no_hp'] ?? '-') ?></td>
                <td><?= htmlspecialchars($r['email'] ?? '-') ?></td>
                <td><?= formatTanggal($r['tgl_daftar']) ?></td>
                <td><span class="badge-status badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
                <td style="text-align:center;white-space:nowrap">
                    <a href="edit.php?id=<?= $r['id_anggota'] ?>" class="btn btn-sm" style="background:#e8f5e9;color:#2d6a4f;border-radius:6px;padding:4px 10px;font-size:.78rem;margin-right:4px">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="hapus.php?id=<?= $r['id_anggota'] ?>"
                       onclick="return konfirmasiHapus('<?= addslashes($r['nama']) ?>')"
                       class="btn btn-sm" style="background:#fdecea;color:#c0392b;border-radius:6px;padding:4px 10px;font-size:.78rem">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile;
            else: ?>
            <tr><td colspan="8">
                <div class="empty-state"><i class="fas fa-users"></i><p>Belum ada data anggota.</p></div>
            </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
