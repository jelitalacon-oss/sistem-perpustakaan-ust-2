<?php
// ============================================
// FILE: config/db.php
// Dibuat oleh: Anggota 1 (Database Architect)
// Deskripsi: Konfigurasi koneksi database
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'perpustakaan');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("<div style='font-family:sans-serif;padding:20px;background:#fee;border:1px solid red;margin:20px;border-radius:8px;'>
        <h3>❌ Koneksi Database Gagal</h3>
        <p>" . mysqli_connect_error() . "</p>
        <p>Pastikan XAMPP/MySQL sudah berjalan dan database <strong>perpustakaan</strong> sudah diimport.</p>
    </div>");
}

mysqli_set_charset($conn, "utf8");

// ============================================
// FUNGSI HELPER GLOBAL
// ============================================

// Sanitasi input untuk keamanan
function bersihkan($conn, $data) {
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Format tanggal ke Indonesia
function formatTanggal($tgl) {
    if (!$tgl || $tgl == '0000-00-00') return '-';
    $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
              'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $parts = explode('-', $tgl);
    return $parts[2] . ' ' . $bulan[(int)$parts[1]] . ' ' . $parts[0];
}

// Hitung denda (Rp1.000 per hari)
function hitungDenda($tgl_rencana, $tgl_nyata = null) {
    $tgl_nyata = $tgl_nyata ?? date('Y-m-d');
    $selisih = (strtotime($tgl_nyata) - strtotime($tgl_rencana)) / 86400;
    return $selisih > 0 ? (int)$selisih * 1000 : 0;
}

// Format rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>
