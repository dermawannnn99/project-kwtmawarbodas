# Catatan Update — KWT Mawar Bodas II

---

## UI & Responsiveness

- Navbar brand text responsif di mobile — tidak lagi nabrak tombol Login dan icon QR di layar kecil
- Heading hero, section scanner, dan katalog produk disesuaikan ukurannya per breakpoint (mobile → desktop)
- Paragraf deskripsi hero dikecilkan di mobile supaya tidak makan terlalu banyak ruang
- Gap antar section produk dan "Ada Pertanyaan?" diperkecil supaya lebih proporsional
- `viewport-fit=cover` ditambahkan di semua halaman untuk konsistensi safe-area di Android

---

## Hero Section

- Kata *"Kemasan Pintar"* di heading hero diberi efek italic curly menggunakan font Playfair Display
- Ukuran teks heading dinaikkan di mobile supaya lebih menonjol

---

## Scanner QR

- Tambah tombol **Ganti Kamera** — switch antara kamera depan dan belakang (muncul hanya jika device punya lebih dari 1 kamera)
- Tambah tombol **Flash/Torch** — toggle on/off, hanya muncul jika device support, otomatis hilang di iOS/browser yang tidak mendukung
- Area target scan diperbesar supaya lebih mudah diarahkan ke QR code
- Fix bug kamera tidak bisa dibuka ulang setelah dimatikan tanpa refresh halaman — sekarang `stop()` + `clear()` + jeda 250ms sebelum instance baru dibuat
- Pesan error kamera dibedakan per jenis: izin ditolak, kamera sedang dipakai, kamera tidak terdeteksi, dan error umum

---

## Animasi Scroll Reveal

- Durasi transisi dipercepat dari 1.05s → 0.65s supaya lebih snappy
- Stagger delay antar product card diperkecil dari 100ms → 65ms supaya tidak terasa lag saat banyak card muncul sekaligus

---

## Keamanan (Security Hardening)

Rombakan menyeluruh untuk persiapan production:

- **CSRF Protection** — semua form dan endpoint mutasi data (tambah/edit/hapus produk) sekarang diproteksi token CSRF
- **XSS Prevention** — semua data produk yang dirender ke HTML di-escape menggunakan `escHtml()`, data yang disimpan ke database juga di-strip tag HTML-nya
- **Rate Limiting Login** — percobaan login gagal sekarang dicatat di database (bukan session), akun terkunci 5 menit setelah 5x gagal salah — berlaku meski browser/session diganti
- **Validasi Upload Gambar** — validasi ukuran (max 5MB), tipe file (JPG/PNG), dan MIME type sekarang dilakukan di server, bukan hanya di browser
- **Validasi Input Backend** — semua field produk divalidasi di server: field wajib, format harga, format tanggal, dan format kode batch
- **Session Cookie** — cookie session sekarang pakai flag `HttpOnly`, `SameSite=Lax`, dan `Secure` (aktif di production HTTPS)
- **Konfigurasi .env** — kredensial database dipindah dari hardcode ke file `.env` yang tidak ikut ke repository
- **Security Headers** — `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, dan `Content-Security-Policy` diterapkan di semua halaman
- **Audit Trail Login** — waktu dan IP login terakhir admin dicatat di database
- **File Cleanup** — gambar produk lama otomatis dihapus dari server saat produk diedit dengan foto baru atau dihapus
- **SQL Dump Sinkron** — skema database (`lezatpack_db.sql`) diperbarui sesuai kondisi aktual termasuk tabel baru `login_attempts`

---

## Kode & Struktur

- `escHtml()` dan `formatRupiah()` dipindah ke satu file shared (`assets/js/shared-utils.js`) — tidak lagi duplikat di dua tempat
- IntersectionObserver di-disconnect saat halaman di-unload untuk mencegah memory leak
- Folder `unused/` diblokir via `.htaccess` dan dikecualikan dari repository via `.gitignore`
