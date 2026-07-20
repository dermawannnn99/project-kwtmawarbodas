// ==========================================
// SIDEBAR TOGGLE
// ==========================================
function openSidebar() {
    const s = document.getElementById('sidebar'), o = document.getElementById('sidebar-overlay');
    s.classList.remove('-translate-x-full');
    o.classList.remove('hidden');
    setTimeout(() => o.classList.remove('opacity-0'), 10);
}
function closeSidebar() {
    const s = document.getElementById('sidebar'), o = document.getElementById('sidebar-overlay');
    s.classList.add('-translate-x-full');
    o.classList.add('opacity-0');
    setTimeout(() => o.classList.add('hidden'), 250);
}

// ==========================================
// SECTION NAVIGATION
// ==========================================
const sectionTitles = { overview: 'Ringkasan', tambah: 'Kelola Produk', inventory: 'Data Inventory' };

function showSection(name) {
    document.querySelectorAll('.section-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
    document.getElementById('section-' + name).classList.add('active');
    const navBtn = document.querySelector('[data-section="' + name + '"]');
    if (navBtn) navBtn.classList.add('active');
    document.getElementById('topbar-title').textContent = sectionTitles[name] || name;
    if (window.innerWidth < 1024) closeSidebar();
}

// ==========================================
// NOTIFIKASI — backdrop gelap + card tengah
// ==========================================
let _notifTimer = null;

/**
 * Tampilkan notifikasi overlay.
 * @param {'create'|'edit'|'delete'|'toggle'} action  - jenis aksi
 * @param {boolean} isError                            - sukses atau gagal
 * @param {string}  [customDesc]                       - override deskripsi (opsional)
 */
function showNotif(action, isError, customDesc) {
    clearTimeout(_notifTimer);

    const configs = {
        create: {
            ok:  { title: 'Produk Berhasil Ditambahkan',  desc: 'Data produk baru sudah tersimpan ke database.',           iconClass: 'ph-bold ph-check-circle', iconColor: '#16a34a', iconBg: '#dcfce7' },
            err: { title: 'Gagal Menambahkan Produk',     desc: 'Terjadi kesalahan saat menyimpan produk baru.',            iconClass: 'ph-bold ph-warning-circle', iconColor: '#dc2626', iconBg: '#fee2e2' }
        },
        edit: {
            ok:  { title: 'Produk Berhasil Diperbarui',   desc: 'Perubahan data produk sudah tersimpan.',                  iconClass: 'ph-bold ph-check-circle', iconColor: '#16a34a', iconBg: '#dcfce7' },
            err: { title: 'Gagal Memperbarui Produk',     desc: 'Terjadi kesalahan saat menyimpan perubahan produk.',       iconClass: 'ph-bold ph-warning-circle', iconColor: '#dc2626', iconBg: '#fee2e2' }
        },
        delete: {
            ok:  { title: 'Produk Berhasil Dihapus',      desc: 'Data produk telah dihapus secara permanen.',              iconClass: 'ph-bold ph-check-circle', iconColor: '#16a34a', iconBg: '#dcfce7' },
            err: { title: 'Gagal Menghapus Produk',       desc: 'Terjadi kesalahan saat mencoba menghapus produk.',         iconClass: 'ph-bold ph-warning-circle', iconColor: '#dc2626', iconBg: '#fee2e2' }
        },
        toggle: {
            ok:  { title: 'Status Katalog Diperbarui',    desc: 'Visibilitas produk di katalog berhasil diubah.',          iconClass: 'ph-bold ph-check-circle', iconColor: '#16a34a', iconBg: '#dcfce7' },
            err: { title: 'Gagal Mengubah Visibilitas',   desc: 'Terjadi kesalahan saat mengubah status tampil produk.',   iconClass: 'ph-bold ph-warning-circle', iconColor: '#dc2626', iconBg: '#fee2e2' }
        },
        upload: {
            err: { title: 'File Tidak Valid',             desc: 'Periksa kembali format dan ukuran file gambar.',          iconClass: 'ph-bold ph-warning-circle', iconColor: '#dc2626', iconBg: '#fee2e2' }
        },
        qr: {
            err: { title: 'Gagal Memuat Gambar QR',       desc: 'Tidak dapat memuat QR untuk didownload. Coba lagi.',      iconClass: 'ph-bold ph-warning-circle', iconColor: '#dc2626', iconBg: '#fee2e2' }
        },
        db: {
            err: { title: 'Koneksi Database Bermasalah',  desc: 'Tidak dapat terhubung ke database. Periksa server.',      iconClass: 'ph-bold ph-warning-circle', iconColor: '#dc2626', iconBg: '#fee2e2' }
        }
    };

    const key  = isError ? 'err' : 'ok';
    const cfg  = (configs[action] && configs[action][key]) || configs.db.err;

    const backdrop  = document.getElementById('notif-backdrop');
    const card      = document.getElementById('notif-card');
    const iconWrap  = document.getElementById('notif-icon-wrap');
    const icon      = document.getElementById('notif-icon');
    const titleEl   = document.getElementById('notif-title');
    const descEl    = document.getElementById('notif-desc');

    // Isi konten
    titleEl.textContent       = cfg.title;
    descEl.textContent        = customDesc || cfg.desc;
    icon.className            = cfg.iconClass + ' text-4xl';
    icon.style.color          = cfg.iconColor;
    iconWrap.style.background = cfg.iconBg;

    // Reset animasi
    card.classList.remove('notif-card-enter', 'notif-card-exit');

    // Tampilkan backdrop
    backdrop.style.opacity = '0';
    backdrop.classList.remove('hidden');
    backdrop.style.display = 'flex';

    // Paksa reflow lalu jalankan animasi masuk
    backdrop.getBoundingClientRect();
    backdrop.style.opacity = '1';
    card.classList.add('notif-card-enter');

    // Auto-dismiss setelah 3 detik
    _notifTimer = setTimeout(() => {
        card.classList.remove('notif-card-enter');
        card.classList.add('notif-card-exit');
        backdrop.style.opacity = '0';
        setTimeout(() => {
            backdrop.style.display = 'none';
            backdrop.classList.add('hidden');
            card.classList.remove('notif-card-exit');
        }, 240);
    }, 3000);
}

// ==========================================
// FILE UPLOAD — preview & validasi
// ==========================================
const inputImage        = document.getElementById('input-image');
const imagePreview      = document.getElementById('image-preview');
const uploadPlaceholder = document.getElementById('upload-placeholder');
const imageFilename     = document.getElementById('image-filename');

inputImage.addEventListener('change', () => {
    const file = inputImage.files[0];
    if (!file) return;
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    const allowedExts  = ['jpg', 'jpeg', 'png'];
    const ext = file.name.split('.').pop().toLowerCase();
    if (!allowedTypes.includes(file.type) || !allowedExts.includes(ext)) {
        showNotif('upload', true, 'Format tidak valid. Hanya JPG dan PNG yang diperbolehkan.');
        inputImage.value = ''; return;
    }
    if (file.size > 5 * 1024 * 1024) {
        showNotif('upload', true, 'Ukuran file terlalu besar. Maksimal 5 MB.');
        inputImage.value = ''; return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.src = e.target.result;
        imagePreview.classList.remove('hidden');
        uploadPlaceholder.classList.add('hidden');
        imageFilename.textContent = file.name;
        imageFilename.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
});
