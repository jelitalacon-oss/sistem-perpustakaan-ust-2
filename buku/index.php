<?php
// ============================================
// FILE: buku/index.php
// Dibuat oleh: Anggota 2 (Backend Buku & Anggota)
// ============================================
$pageTitle  = 'Data Buku — Perpustakaan UST';
$activePage = 'buku';
require_once '../includes/auth.php';
require_once '../config/db.php';

$pesan = $_GET['pesan'] ?? '';
$tipe  = $_GET['tipe']  ?? 'success';

// Query dengan pencarian
$cari  = isset($_GET['cari']) ? bersihkan($conn, $_GET['cari']) : '';
$where = $cari ? "WHERE judul LIKE '%$cari%' OR pengarang LIKE '%$cari%' OR kode_buku LIKE '%$cari%'" : '';
$data  = mysqli_query($conn, "SELECT * FROM buku $where ORDER BY id_buku DESC");

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><span class="page-icon"><i class="fas fa-book"></i></span> Data Buku</h2>
    <a href="tambah.php" class="btn btn-hijau"><i class="fas fa-plus me-1"></i> Tambah Buku</a>
</div>

<?php if ($pesan): ?>
<div class="alert alert-custom alert-auto mb-3" style="background:<?= $tipe=='success'?'#d1e7dd':'#fdecea' ?>;color:<?= $tipe=='success'?'#0a3622':'#842029' ?>">
    <i class="fas fa-<?= $tipe=='success'?'check-circle':'times-circle' ?>"></i> <?= htmlspecialchars($pesan) ?>
</div>
<?php endif; ?>

<div class="konten-kartu">
    <div class="kartu-header">
        <h5><i class="fas fa-list me-2"></i>Daftar Buku (<?= mysqli_num_rows($data) ?> data)</h5>
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="cariInput" class="form-control form-control-sm"
                   placeholder="Cari judul, pengarang..."
                   value="<?= htmlspecialchars($cari) ?>"
                   oninput="filterTabel('cariInput','tabelBuku')">
        </div>
    </div>
    <div class="kartu-body p-0">
        <div class="table-responsive">
        <table class="tabel-utama" id="tabelBuku">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Judul Buku</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($data) > 0):
                $no = 1;
                while ($r = mysqli_fetch_assoc($data)): ?>
            <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td><code style="font-size:.8rem;background:#f0f4f0;padding:2px 6px;border-radius:4px"><?= htmlspecialchars($r['kode_buku']) ?></code></td>
                <td><strong><?= htmlspecialchars($r['judul']) ?></strong>
                    <?php if ($r['kategori']): ?><br><small class="text-muted"><?= htmlspecialchars($r['kategori']) ?></small><?php endif; ?></td>
                <td><?= htmlspecialchars($r['pengarang']) ?></td>
                <td><?= htmlspecialchars($r['penerbit'] ?? '-') ?></td>
                <td><?= $r['tahun_terbit'] ?? '-' ?></td>
                <td>
                    <span class="badge-status <?= $r['stok'] > 0 ? 'badge-aktif' : 'badge-terlambat' ?>">
                        <?= $r['stok'] ?> buku
                    </span>
                </td>
                <td style="text-align:center;white-space:nowrap">
                    <a href="edit.php?id=<?= $r['id_buku'] ?>" class="btn btn-sm" style="background:#e8f5e9;color:#2d6a4f;border-radius:6px;padding:4px 10px;font-size:.78rem;margin-right:4px">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="hapus.php?id=<?= $r['id_buku'] ?>&nama=<?= urlencode($r['judul']) ?>"
                       onclick="return konfirmasiHapus('<?= addslashes($r['judul']) ?>')"
                       class="btn btn-sm" style="background:#fdecea;color:#c0392b;border-radius:6px;padding:4px 10px;font-size:.78rem">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile;
            else: ?>
            <tr><td colspan="8">
                <div class="empty-state" id="empty-tabelBuku">
                    <i class="fas fa-book-open"></i>
                    <p>Belum ada data buku<?= $cari ? " dengan kata kunci \"$cari\"" : '' ?>.</p>
                </div>
            </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
