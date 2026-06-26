# 📚 Sistem Peminjaman Buku Perpustakaan

Aplikasi web dinamis berbasis CRUD untuk pengelolaan perpustakaan.  
**Tugas Pemrograman Web — Fakultas Teknik, Universitas Sarjanawiyata Tamansiswa**

---

## 🛠️ Teknologi yang Digunakan

| Teknologi | Keterangan |
|-----------|------------|
| HTML5 | Struktur halaman |
| Bootstrap 5.3 | Responsive design |
| JavaScript (Vanilla) | Validasi form & interaksi UI |
| PHP Native | Backend & logika bisnis |
| MySQL | Database |
| GitHub | Kolaborasi tim |

---

## 👥 Pembagian Tugas Kelompok

| Anggota | Peran | File yang Dikerjakan |
|---------|-------|----------------------|
| **Yohana Jelita** | Database Architect + Project Lead | `perpustakaan.sql`, `config/db.php`, setup GitHub |
| **Zulfa Dzariyah Rahmawati** | Backend Buku & Anggota | `buku/*.php`, `anggota/*.php` |
| **Anggota 3** | Backend Peminjaman | `peminjaman/*.php` |
| **Anggota 4** | Frontend & UI/UX | `includes/`, `assets/css/`, `assets/js/`, `index.php` |

---

## 📁 Struktur Folder

```
perpustakaan/
├── config/
│   └── db.php              # Konfigurasi database
├── buku/
│   ├── index.php           # Daftar buku
│   ├── tambah.php          # Tambah buku baru
│   ├── edit.php            # Edit data buku
│   └── hapus.php           # Hapus buku
├── anggota/
│   ├── index.php           # Daftar anggota
│   ├── tambah.php          # Daftar anggota baru
│   ├── edit.php            # Edit data anggota
│   └── hapus.php           # Hapus anggota
├── peminjaman/
│   ├── index.php           # Riwayat peminjaman
│   ├── pinjam.php          # Catat peminjaman
│   └── kembali.php         # Proses pengembalian + denda
├── includes/
│   ├── header.php          # Template header + navbar
│   └── footer.php          # Template footer
├── assets/
│   ├── css/style.css       # Styling utama
│   └── js/script.js        # JavaScript validasi & interaksi
├── index.php               # Dashboard utama
├── perpustakaan.sql        # File database
└── README.md
```

---

## ⚙️ Cara Instalasi

### Prasyarat
- XAMPP (PHP 7.4+ dan MySQL)
- Browser modern

### Langkah-langkah

**1. Clone repository**
```bash
git clone https://github.com/[username]/perpustakaan.git
```

**2. Pindahkan ke folder htdocs**
```
C:/xampp/htdocs/perpustakaan/
```

**3. Import database**
- Buka `http://localhost/phpmyadmin`
- Buat database baru: `perpustakaan`
- Klik **Import** → pilih file `perpustakaan.sql` → klik **Go**

**4. Jalankan aplikasi**
```
http://localhost/perpustakaan/
```

---

## ✨ Fitur Utama

- **Dashboard** — statistik ringkasan (total buku, anggota, peminjaman aktif, terlambat)
- **Manajemen Buku** — CRUD lengkap dengan pencarian real-time
- **Manajemen Anggota** — CRUD dengan validasi data
- **Peminjaman** — validasi stok otomatis, cegah double pinjam
- **Pengembalian** — kalkulasi denda otomatis (Rp1.000/hari), update stok
- **Filter & Pencarian** — filter status peminjaman, search tabel
- **Responsive** — tampil baik di desktop maupun mobile

---

## 👨‍💻 Cara Berkontribusi (Git Workflow)

```bash
# Setiap anggota kerja di branch masing-masing
git checkout -b nama-anggota/fitur

# Setelah selesai, push ke GitHub
git add .
git commit -m "feat: deskripsi perubahan"
git push origin nama-anggota/fitur

# Minta review ke Anggota 1 untuk merge ke main
```
