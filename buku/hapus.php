<?php
// ============================================
// FILE: buku/hapus.php
// Dibuat oleh: Anggota 2
// ============================================
require_once '../includes/auth.php';
require_once '../config/db.php';

$id = (int)($_GET['id'] ?? 0);

// Cek apakah buku sedang dipinjam
$cek = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COUNT(*) FROM peminjaman WHERE id_buku=$id AND status='dipinjam'"));

if ($cek[0] > 0) {
    header("Location: index.php?pesan=Buku tidak bisa dihapus karena masih dipinjam&tipe=danger");
    exit;
}

if (mysqli_query($conn, "DELETE FROM buku WHERE id_buku=$id")) {
    header("Location: index.php?pesan=Buku berhasil dihapus&tipe=success");
} else {
    header("Location: index.php?pesan=Gagal menghapus buku&tipe=danger");
}
exit;
