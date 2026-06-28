<?php
// ============================================
// FILE: peminjaman/index.php
// Halaman: Daftar Peminjaman
// ============================================

require_once '../includes/auth.php';
require_once '../config/db.php';

$pageTitle  = 'Peminjaman — Perpustakaan UST';
$activePage = 'peminjaman';

$pesan = $_GET['pesan'] ?? '';
$tipe  = $_GET['tipe']  ?? 'success';

$cari  = isset($_GET['cari']) ? bersihkan($conn, $_GET['cari']) : '';
$where = $cari ? "WHERE a.nama LIKE '%$cari%' OR b.judul LIKE '%$cari%' OR p.id_pinjam LIKE '%$cari%'" : '';

$q = mysqli_query($conn, "
    SELECT p.id_pinjam,
           a.nama AS nama_anggota,
           b.judul,
           p.tgl_pinjam,
           p.tgl_kembali_rencana,
           p.tgl_kembali_nyata,
           p.denda,
           p.status,
           pet.nama AS nama_petugas
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN buku b ON p.id_buku = b.id_buku
    LEFT JOIN petugas pet ON p.id_petugas = pet.id_petugas
    $where
    ORDER BY p.created_at DESC
");

require_once '../includes/header.php';
?>

<div class="page-header">
    <h2><span class="page-icon"><i class="fas fa-exchange-alt"></i></span> Daftar Peminjaman</h2>
    <div class="d-flex gap-2 flex-wrap">
        <a href="pinjam.php" class="btn btn-hijau"><i class="fas fa-hand-holding-heart me-1"></i>Catat Peminjaman</a>
    </div>
</div>

<?php if ($pesan): ?>
<div class="alert alert-custom alert-auto mb-3" style="background:<?= $tipe=='success'?'#d1e7dd':'#fdecea' ?>;color:<?= $tipe=='success'?'#0a3622':'#842029' ?>">
    <i class="fas fa-<?= $tipe=='success'?'check-circle':'times-circle' ?>"></i> <?= htmlspecialchars($pesan) ?>
</div>
<?php endif; ?>

<div class="konten-kartu">
    <div class="kartu-header">
        <h5><i class="fas fa-list me-2"></i>Peminjaman (<?= mysqli_num_rows($q) ?> data)</h5>
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="cariInput" class="form-control form-control-sm"
                   placeholder="Cari anggota/judul/pinjam..."
                   value="<?= htmlspecialchars($cari) ?>"
                   oninput="filterTabel('cariInput','tabelPeminjaman')">
        </div>
    </div>
    <div class="kartu-body p-0">
        <div class="table-responsive">
        <table class="tabel-utama" id="tabelPeminjaman">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Anggota</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Rencana</th>
                    <th>Tgl Nyata</th>
                    <th>Denda</th>
                    <th>Status</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($q) > 0):
                $no = 1;
                while ($r = mysqli_fetch_assoc($q)):
                    $terlambat = ($r['status'] == 'dipinjam' && $r['tgl_kembali_rencana'] < date('Y-m-d'));
                    $badgeClass = $r['status'] == 'dikembalikan' ? 'badge-kembali' : ($terlambat ? 'badge-terlambat' : 'badge-dipinjam');
                    $badgeTeks  = $r['status'] == 'dikembalikan' ? 'Dikembalikan' : ($terlambat ? 'Terlambat' : 'Dipinjam');
            ?>
                <tr>
                    <td class="text-muted"><?= $no++ ?></td>
                    <td><i class="fas fa-user-circle text-muted me-1"></i><?= htmlspecialchars($r['nama_anggota']) ?></td>
                    <td><?= htmlspecialchars($r['judul']) ?></td>
                    <td><?= formatTanggal($r['tgl_pinjam']) ?></td>
                    <td><?= formatTanggal($r['tgl_kembali_rencana']) ?></td>
                    <td><?= formatTanggal($r['tgl_kembali_nyata']) ?></td>
                    <td><?= $r['denda'] ? formatRupiah((int)$r['denda']) : '-' ?></td>
                    <td><span class="badge-status <?= $badgeClass ?>"><?= $badgeTeks ?></span></td>
                    <td style="text-align:center;white-space:nowrap">
                        <?php if ($r['status'] == 'dipinjam'): ?>
                            <a href="kembali.php?id=<?= (int)$r['id_pinjam'] ?>" class="btn btn-sm btn-outline-secondary" style="border-radius:6px;font-size:.78rem;">
                                <i class="fas fa-undo me-1"></i> Kembalikan
                            </a>
                        <?php else: ?>
                            <span class="text-muted" style="font-size:.85rem">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="9">
                    <div class="empty-state" id="empty-tabelPeminjaman">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada data peminjaman.</p>
                    </div>
                </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

