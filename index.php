<?php
// ============================================
// FILE: index.php (Dashboard Utama)
// Frontend: Anggota 4 | Data: Anggota 1
// ============================================
$baseUrl    = '';
require_once 'includes/auth.php';
$pageTitle  = 'Dashboard — Perpustakaan UST';
$activePage = 'dashboard';
require_once 'config/db.php';
require_once 'includes/header.php';

// Ambil statistik
$totalBuku     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM buku"))[0];
$totalAnggota  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM anggota WHERE status='aktif'"))[0];
$totalDipinjam = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE status='dipinjam'"))[0];
$totalTerlambat= mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE status='dipinjam' AND tgl_kembali_rencana < CURDATE()"))[0];

// Peminjaman terbaru
$qTerbaru = mysqli_query($conn, "
    SELECT p.id_pinjam, a.nama AS nama_anggota, b.judul,
           p.tgl_pinjam, p.tgl_kembali_rencana, p.status
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN buku b ON p.id_buku = b.id_buku
    ORDER BY p.created_at DESC LIMIT 6
");
?>

<div class="page-header">
    <h2><span class="page-icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard</h2>
    <span class="text-muted small"><i class="fas fa-calendar me-1"></i><?= date('l, d F Y') ?></span>
</div>

<!-- KARTU STATISTIK -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card hijau">
            <div class="stat-icon"><i class="fas fa-book"></i></div>
            <div class="stat-num"><?= $totalBuku ?></div>
            <div class="stat-label">Total Buku</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card biru">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-num"><?= $totalAnggota ?></div>
            <div class="stat-label">Anggota Aktif</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card kuning">
            <div class="stat-icon"><i class="fas fa-exchange-alt"></i></div>
            <div class="stat-num"><?= $totalDipinjam ?></div>
            <div class="stat-label">Sedang Dipinjam</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card merah">
            <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-num"><?= $totalTerlambat ?></div>
            <div class="stat-label">Terlambat</div>
        </div>
    </div>
</div>

<!-- AKSI CEPAT -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="konten-kartu">
            <div class="kartu-header">
                <h5><i class="fas fa-bolt me-2 text-warning"></i>Aksi Cepat</h5>
            </div>
            <div class="kartu-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="buku/tambah.php" class="btn btn-hijau">
                        <i class="fas fa-plus me-1"></i> Tambah Buku
                    </a>
                    <a href="anggota/tambah.php" class="btn btn-hijau">
                        <i class="fas fa-user-plus me-1"></i> Daftar Anggota
                    </a>
                    <a href="peminjaman/pinjam.php" class="btn btn-aksen">
                        <i class="fas fa-hand-holding-heart me-1"></i> Catat Peminjaman
                    </a>
                    <a href="peminjaman/kembali.php" class="btn btn-outline-secondary" style="border-radius:8px;font-size:.875rem;">
                        <i class="fas fa-undo me-1"></i> Proses Pengembalian
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABEL PEMINJAMAN TERBARU -->
<div class="konten-kartu">
    <div class="kartu-header">
        <h5><i class="fas fa-clock me-2"></i>Peminjaman Terbaru</h5>
        <a href="peminjaman/index.php" class="btn btn-sm btn-hijau">Lihat Semua</a>
    </div>
    <div class="kartu-body p-0">
        <?php if (mysqli_num_rows($qTerbaru) > 0): ?>
        <div class="table-responsive">
        <table class="tabel-utama">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Anggota</th>
                    <th>Judul Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php $no = 1; while ($r = mysqli_fetch_assoc($qTerbaru)): 
                $terlambat = ($r['status'] == 'dipinjam' && $r['tgl_kembali_rencana'] < date('Y-m-d'));
                $badgeClass = $r['status'] == 'dikembalikan' ? 'badge-kembali' :
                              ($terlambat ? 'badge-terlambat' : 'badge-dipinjam');
                $badgeTeks  = $r['status'] == 'dikembalikan' ? 'Dikembalikan' :
                              ($terlambat ? 'Terlambat' : 'Dipinjam');
            ?>
            <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td><i class="fas fa-user-circle text-muted me-1"></i><?= htmlspecialchars($r['nama_anggota']) ?></td>
                <td><?= htmlspecialchars($r['judul']) ?></td>
                <td><?= formatTanggal($r['tgl_pinjam']) ?></td>
                <td><?= formatTanggal($r['tgl_kembali_rencana']) ?></td>
                <td><span class="badge-status <?= $badgeClass ?>"><?= $badgeTeks ?></span></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Belum ada data peminjaman.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
