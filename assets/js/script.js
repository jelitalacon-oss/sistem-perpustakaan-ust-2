// ============================================
// SISTEM PERPUSTAKAAN UST
// JavaScript Utama — Dibuat oleh: Anggota 4
// ============================================

// ── Konfirmasi Hapus ──────────────────────
function konfirmasiHapus(nama) {
    return confirm(`Yakin ingin menghapus data "${nama}"?\n\nData yang dihapus tidak bisa dikembalikan.`);
}

// ── Validasi Form Buku ────────────────────
function validasiBuku() {
    const kode   = document.getElementById('kode_buku')?.value.trim();
    const judul  = document.getElementById('judul')?.value.trim();
    const stok   = document.getElementById('stok')?.value;

    if (!kode)  { showAlert('Kode buku wajib diisi.', 'danger'); return false; }
    if (!judul) { showAlert('Judul buku wajib diisi.', 'danger'); return false; }
    if (stok < 0) { showAlert('Stok tidak boleh negatif.', 'danger'); return false; }
    return true;
}

// ── Validasi Form Anggota ─────────────────
function validasiAnggota() {
    const kode  = document.getElementById('kode_anggota')?.value.trim();
    const nama  = document.getElementById('nama')?.value.trim();
    const hp    = document.getElementById('no_hp')?.value.trim();
    const email = document.getElementById('email')?.value.trim();

    if (!kode) { showAlert('Kode anggota wajib diisi.', 'danger'); return false; }
    if (!nama) { showAlert('Nama anggota wajib diisi.', 'danger'); return false; }
    if (hp && !/^[0-9]{10,15}$/.test(hp)) {
        showAlert('Format nomor HP tidak valid (10-15 digit angka).', 'danger');
        return false;
    }
    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showAlert('Format email tidak valid.', 'danger');
        return false;
    }
    return true;
}

// ── Validasi Form Peminjaman ──────────────
function validasiPeminjaman() {
    const anggota   = document.getElementById('id_anggota')?.value;
    const buku      = document.getElementById('id_buku')?.value;
    const tglPinjam = document.getElementById('tgl_pinjam')?.value;
    const tglKembali= document.getElementById('tgl_kembali_rencana')?.value;

    if (!anggota)    { showAlert('Pilih anggota terlebih dahulu.', 'danger'); return false; }
    if (!buku)       { showAlert('Pilih buku terlebih dahulu.', 'danger'); return false; }
    if (!tglPinjam)  { showAlert('Tanggal pinjam wajib diisi.', 'danger'); return false; }
    if (!tglKembali) { showAlert('Tanggal kembali rencana wajib diisi.', 'danger'); return false; }
    if (tglKembali <= tglPinjam) {
        showAlert('Tanggal kembali harus setelah tanggal pinjam.', 'danger');
        return false;
    }
    return true;
}

// ── Tampilkan Alert ───────────────────────
function showAlert(pesan, tipe = 'success') {
    const ikon = tipe === 'success' ? 'fa-check-circle' :
                 tipe === 'danger'  ? 'fa-times-circle' :
                 tipe === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';

    const warna = tipe === 'success' ? '#d1e7dd' :
                  tipe === 'danger'  ? '#fdecea' :
                  tipe === 'warning' ? '#fff3cd' : '#cfe2ff';

    const warnaT = tipe === 'success' ? '#0a3622' :
                   tipe === 'danger'  ? '#842029' :
                   tipe === 'warning' ? '#664d03' : '#084298';

    const el = document.createElement('div');
    el.style.cssText = `
        position:fixed; top:80px; right:20px; z-index:9999;
        background:${warna}; color:${warnaT};
        padding:14px 20px; border-radius:10px;
        font-size:0.88rem; font-weight:500;
        box-shadow:0 4px 15px rgba(0,0,0,0.12);
        display:flex; align-items:center; gap:8px;
        max-width:340px; animation: slideIn 0.3s ease;
    `;
    el.innerHTML = `<i class="fas ${ikon}"></i> ${pesan}`;
    document.body.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity 0.3s'; }, 3000);
    setTimeout(() => el.remove(), 3400);
}

// ── Hitung Denda Real-time ────────────────
function hitungDendaPreview() {
    const tglRencana = document.getElementById('tgl_kembali_rencana_asli')?.value;
    const tglNyata   = document.getElementById('tgl_kembali_nyata')?.value;
    const dendaEl    = document.getElementById('preview_denda');

    if (!tglRencana || !tglNyata || !dendaEl) return;

    const selisih = Math.floor((new Date(tglNyata) - new Date(tglRencana)) / 86400000);
    if (selisih > 0) {
        const denda = selisih * 1000;
        dendaEl.innerHTML = `<span class="text-danger fw-bold">
            <i class="fas fa-exclamation-triangle me-1"></i>
            Terlambat ${selisih} hari &mdash; Denda: Rp ${denda.toLocaleString('id-ID')}
        </span>`;
    } else {
        dendaEl.innerHTML = `<span class="text-success">
            <i class="fas fa-check-circle me-1"></i> Tepat waktu, tidak ada denda.
        </span>`;
    }
}

// ── Search Filter Tabel ───────────────────
function filterTabel(inputId, tabelId) {
    const kata   = document.getElementById(inputId)?.value.toLowerCase();
    const baris  = document.querySelectorAll(`#${tabelId} tbody tr`);
    let ada = 0;
    baris.forEach(b => {
        const cocok = b.textContent.toLowerCase().includes(kata);
        b.style.display = cocok ? '' : 'none';
        if (cocok) ada++;
    });
    const kosong = document.getElementById('empty-' + tabelId);
    if (kosong) kosong.style.display = ada === 0 ? '' : 'none';
}

// ── Set tanggal default form peminjaman ──
document.addEventListener('DOMContentLoaded', () => {
    const hari = new Date().toISOString().split('T')[0];
    const tglPinjamEl  = document.getElementById('tgl_pinjam');
    const tglKembaliEl = document.getElementById('tgl_kembali_rencana');

    if (tglPinjamEl && !tglPinjamEl.value)  tglPinjamEl.value = hari;
    if (tglKembaliEl && !tglKembaliEl.value) {
        const seminggu = new Date();
        seminggu.setDate(seminggu.getDate() + 7);
        tglKembaliEl.value = seminggu.toISOString().split('T')[0];
    }

    // Auto-hide alert PHP setelah 4 detik
    document.querySelectorAll('.alert-auto').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        }, 4000);
    });
});

const style = document.createElement('style');
style.textContent = `@keyframes slideIn { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:translateX(0); } }`;
document.head.appendChild(style);
