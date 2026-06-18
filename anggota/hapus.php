<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
$id = (int)($_GET['id'] ?? 0);
$cek = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM peminjaman WHERE id_anggota=$id AND status='dipinjam'"));
if ($cek[0] > 0) {
    header("Location: index.php?pesan=Anggota tidak bisa dihapus karena masih meminjam buku&tipe=danger"); exit;
}
mysqli_query($conn, "DELETE FROM anggota WHERE id_anggota=$id");
header("Location: index.php?pesan=Anggota berhasil dihapus&tipe=success"); exit;
