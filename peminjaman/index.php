<?php
// FILE: peminjaman/index.php
<<<< anggota1-database
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
=======
// Dibuat oleh: Anggota 3 (Backend Peminjaman)
// ============================================
$pageTitle  = 'Data Peminjaman — Perpustakaan UST';
$activePage = 'peminjaman';
require_once '../includes/auth.php';
require_once '../config/db.php';

$pesan = $_GET['pesan'] ?? '';
$tipe  = $_GET['tipe']  ?? 'success';

// Filter status
$filter = isset($_GET['filter']) ? bersihkan($conn, $_GET['filter']) : '';
$where  = $filter ? "WHERE p.status='$filter'" : '';

$data = mysqli_query($conn, "
    SELECT p.*, a.nama AS nama_anggota, a.kode_anggota,
           b.judul, b.kode_buku
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN buku b ON p.id_buku = b.id_buku
                  
    $where
    ORDER BY p.created_at DESC
");

require_once '../includes/header.php';
?>

<div class="page-header">
<<<<<< anggota1-database
    <h2><span class="page-icon"><i class="fas fa-exchange-alt"></i></span> Daftar Peminjaman</h2>
    <div class="d-flex gap-2 flex-wrap">
        <a href="pinjam.php" class="btn btn-hijau"><i class="fas fa-hand-holding-heart me-1"></i>Catat Peminjaman</a>
    </div>
=======
    <h2><span class="page-icon"><i class="fas fa-exchange-alt"></i></span> Data Peminjaman</h2>
    <a href="pinjam.php" class="btn btn-aksen"><i class="fas fa-plus me-1"></i> Catat Peminjaman</a>

</div>

<?php if ($pesan): ?>
<div class="alert alert-custom alert-auto mb-3" style="background:<?= $tipe=='success'?'#d1e7dd':'#fdecea' ?>;color:<?= $tipe=='success'?'#0a3622':'#842029' ?>">
    <i class="fas fa-<?= $tipe=='success'?'check-circle':'times-circle' ?>"></i> <?= htmlspecialchars($pesan) ?>
</div>
<?php endif; ?>

<<<<<< anggota1-database
<div class="konten-kartu">
    <div class="kartu-header">
        <h5><i class="fas fa-list me-2"></i>Peminjaman (<?= mysqli_num_rows($q) ?> data)</h5>
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="cariInput" class="form-control form-control-sm"
                   placeholder="Cari anggota/judul/pinjam..."
                   value="<?= htmlspecialchars($cari) ?>"
                   oninput="filterTabel('cariInput','tabelPeminjaman')">
=======
<!-- Filter Tab -->
<div class="konten-kartu">
    <div class="kartu-header">
        <h5><i class="fas fa-filter me-2"></i>Filter</h5>
    </div>
    <div class="kartu-body">
        <div class="d-flex gap-2 flex-wrap">
            <a href="index.php" class="btn btn-sm <?= !$filter ? 'btn-hijau' : 'btn-outline-secondary' ?>" style="border-radius:20px">Semua</a>
            <a href="?filter=dipinjam" class="btn btn-sm <?= $filter=='dipinjam' ? 'btn-aksen' : 'btn-outline-secondary' ?>" style="border-radius:20px">Dipinjam</a>
            <a href="?filter=dikembalikan" class="btn btn-sm <?= $filter=='dikembalikan' ? 'btn-hijau' : 'btn-outline-secondary' ?>" style="border-radius:20px">Dikembalikan</a>
            <a href="?filter=terlambat" class="btn btn-sm <?= $filter=='terlambat' ? '' : 'btn-outline-secondary' ?>" 
               style="border-radius:20px;<?= $filter=='terlambat' ? 'background:#fdecea;color:#842029;border:none' : '' ?>">Terlambat</a>
        </div>
    </div>
</div>

<div class="konten-kartu">
    <div class="kartu-header">
        <h5><i class="fas fa-list me-2"></i>Riwayat Peminjaman (<?= mysqli_num_rows($data) ?> data)</h5>
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="cariInput" class="form-control form-control-sm"
                   placeholder="Cari anggota, judul..."
                   oninput="filterTabel('cariInput','tabelPinjam')">

        </div>
    </div>
    <div class="kartu-body p-0">
        <div class="table-responsive">
<<<<<< anggota1-database
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
=======
        <table class="tabel-utama" id="tabelPinjam">
            <thead>
                <tr>
                    <th>No</th><th>Anggota</th><th>Buku</th>
                    <th>Tgl Pinjam</th><th>Kembali</th>
                    <th>Denda</th><th>Status</th><th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($data) > 0):
                $no = 1;
                while ($r = mysqli_fetch_assoc($data)):
                    $terlambat  = ($r['status'] == 'dipinjam' && $r['tgl_kembali_rencana'] < date('Y-m-d'));
                    $dendaCalc  = $terlambat ? hitungDenda($r['tgl_kembali_rencana']) : $r['denda'];
                    $badgeClass = $r['status'] == 'dikembalikan' ? 'badge-kembali' :
                                  ($terlambat ? 'badge-terlambat' : 'badge-dipinjam');
                    $badgeTeks  = $r['status'] == 'dikembalikan' ? 'Dikembalikan' :
                                  ($terlambat ? 'Terlambat' : 'Dipinjam');
            ?>
            <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td>
                    <strong><?= htmlspecialchars($r['nama_anggota']) ?></strong><br>
                    <small class="text-muted"><?= htmlspecialchars($r['kode_anggota']) ?></small>
                </td>
                <td>
                    <?= htmlspecialchars($r['judul']) ?><br>
                    <small class="text-muted"><?= htmlspecialchars($r['kode_buku']) ?></small>
                </td>
                <td><?= formatTanggal($r['tgl_pinjam']) ?></td>
                <td>
                    <?= formatTanggal($r['tgl_kembali_rencana']) ?>
                    <?php if ($r['tgl_kembali_nyata']): ?>
                    <br><small class="text-success">Kembali: <?= formatTanggal($r['tgl_kembali_nyata']) ?></small>
                    <?php endif; ?>
                </td>
                <td><?= $dendaCalc > 0 ? '<span class="text-danger fw-bold">'.formatRupiah($dendaCalc).'</span>' : '<span class="text-muted">-</span>' ?></td>
                <td><span class="badge-status <?= $badgeClass ?>"><?= $badgeTeks ?></span></td>
                <td style="text-align:center">
                    <?php if ($r['status'] == 'dipinjam'): ?>
                    <a href="kembali.php?id=<?= $r['id_pinjam'] ?>" class="btn btn-sm btn-aksen" style="font-size:.78rem;padding:4px 10px;border-radius:6px">
                        <i class="fas fa-undo me-1"></i>Kembalikan
                    </a>
                    <?php else: ?>
                    <span class="text-muted" style="font-size:.8rem">Selesai</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile;
            else: ?>
            <tr><td colspan="8">
                <div class="empty-state"><i class="fas fa-exchange-alt"></i><p>Belum ada data peminjaman.</p></div>
            </td></tr>

            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<<<<<< anggota1-database
