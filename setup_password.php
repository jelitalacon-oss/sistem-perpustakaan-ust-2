<?php
// ============================================
// FILE: setup_password.php
// Jalankan SEKALI untuk set password admin
// Setelah selesai, HAPUS file ini!
// ============================================
require_once 'config/db.php';

$username = 'admin';
$password = 'admin123';
$hash     = password_hash($password, PASSWORD_DEFAULT);

// Update atau insert
$cek = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM petugas WHERE username='$username'"));
if ($cek[0] > 0) {
    mysqli_query($conn, "UPDATE petugas SET password='$hash' WHERE username='$username'");
} else {
    mysqli_query($conn, "INSERT INTO petugas (nama, username, password) VALUES ('Administrator', '$username', '$hash')");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Setup Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="card" style="max-width:450px;border-radius:12px">
        <div class="card-body p-4">
            <h4 class="text-success"><i class="fas fa-check-circle me-2"></i>Password berhasil di-set!</h4>
            <hr>
            <p class="mb-1"><strong>Username:</strong> admin</p>
            <p class="mb-3"><strong>Password:</strong> admin123</p>
            <div class="alert alert-warning" style="font-size:.85rem">
                ⚠️ <strong>Penting:</strong> Hapus file <code>setup_password.php</code> ini setelah selesai!
            </div>
            <a href="login.php" class="btn btn-success">Pergi ke Halaman Login</a>
        </div>
    </div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</body>
</html>
