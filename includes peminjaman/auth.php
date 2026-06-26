<?php
// ============================================
// FILE: includes/auth.php
// Cek session login — include di setiap halaman
// ============================================
session_start();

if (!isset($_SESSION['admin_login'])) {
    header("Location: " . ($baseUrl ?? '../') . "login.php");
    exit;
}
?>
