<?php
// ============================================
// FILE: login.php
// Halaman Login Admin
// ============================================
session_start();

// Kalau sudah login, langsung ke dashboard
if (isset($_SESSION['admin_login'])) {
    header("Location: index.php");
    exit;
}

require_once 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = bersihkan($conn, $_POST['username']);
    $password = $_POST['password'];

    if (!$username || !$password) {
        $error = "Username dan password wajib diisi.";
    } else {
        $res = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT * FROM petugas WHERE username='$username' LIMIT 1"));

        if ($res && password_verify($password, $res['password'])) {
            $_SESSION['admin_login']  = true;
            $_SESSION['admin_id']     = $res['id_petugas'];
            $_SESSION['admin_nama']   = $res['nama'];
            $_SESSION['admin_user']   = $res['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Perpustakaan UST</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a472a 0%, #2d6a4f 50%, #1a472a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        /* Motif background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle at 20% 20%, rgba(212,168,83,0.08) 0%, transparent 50%),
                              radial-gradient(circle at 80% 80%, rgba(255,255,255,0.04) 0%, transparent 50%);
            pointer-events: none;
        }

        .login-wrap {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        /* Logo area */
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo .ikon-buku {
            width: 72px; height: 72px;
            background: rgba(212,168,83,0.2);
            border: 2px solid rgba(212,168,83,0.4);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #d4a853;
            margin-bottom: 1rem;
        }

        .login-logo h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            color: #fff;
            margin-bottom: 4px;
        }

        .login-logo p {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.55);
            letter-spacing: 0.5px;
        }

        /* Kartu login */
        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .login-card h2 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a472a;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.83rem;
            color: #1a472a;
            margin-bottom: 0.4rem;
        }

        .form-control {
            border: 1.5px solid #dde8dd;
            border-radius: 8px;
            padding: 0.6rem 0.9rem;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: #40916c;
            box-shadow: 0 0 0 3px rgba(64,145,108,0.15);
            outline: none;
        }

        /* Input password dengan toggle */
        .input-pw {
            position: relative;
        }

        .input-pw input {
            padding-right: 2.8rem;
        }

        .toggle-pw {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7c6d;
            cursor: pointer;
            font-size: 0.9rem;
            padding: 0;
        }

        .toggle-pw:hover { color: #1a472a; }

        /* Tombol login */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #2d6a4f, #1a472a);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.7rem;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: opacity 0.2s, transform 0.1s;
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            opacity: 0.92;
            color: #fff;
            transform: translateY(-1px);
        }

        /* Info akun default */
        .info-akun {
            background: #f4f9f5;
            border: 1px solid #dde8dd;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.82rem;
            color: #2d6a4f;
            margin-top: 1.25rem;
            text-align: center;
        }

        .info-akun strong { color: #1a472a; }

        /* Alert error */
        .alert-error {
            background: #fdecea;
            color: #842029;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.25rem;
        }

        /* Footer kecil */
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.78rem;
            color: rgba(255,255,255,0.4);
        }
    </style>
</head>
<body>

<div class="login-wrap">
    <!-- Logo -->
    <div class="login-logo">
        <div class="ikon-buku"><i class="fas fa-book-open"></i></div>
        <h1>Perpustakaan UST</h1>
        <p>Universitas Sarjanawiyata Tamansiswa</p>
    </div>

    <!-- Kartu Form -->
    <div class="login-card">
        <h2><i class="fas fa-lock" style="color:#d4a853"></i> Masuk sebagai Admin</h2>

        <?php if ($error): ?>
        <div class="alert-error">
            <i class="fas fa-times-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                       placeholder="Masukkan username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       autofocus autocomplete="username">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-pw">
                    <input type="password" id="inputPassword" name="password"
                           class="form-control" placeholder="Masukkan password"
                           autocomplete="current-password">
                    <button type="button" class="toggle-pw" onclick="togglePassword()" title="Tampilkan/sembunyikan password">
                        <i class="fas fa-eye" id="ikonMata"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk
            </button>
        </form>

        <div class="info-akun">
            <i class="fas fa-info-circle me-1"></i>
            Akun default — Username: <strong>admin</strong> &nbsp;|&nbsp; Password: <strong>admin123</strong>
        </div>
    </div>

    <div class="login-footer">
        Sistem Peminjaman Buku &copy; <?= date('Y') ?> &mdash; Kelompok Pemrograman Web
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('inputPassword');
    const ikon  = document.getElementById('ikonMata');
    if (input.type === 'password') {
        input.type = 'text';
        ikon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        ikon.className = 'fas fa-eye';
    }
}
</script>
</body>
</html>
