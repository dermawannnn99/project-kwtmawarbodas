<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Profil KWT Mawar Bodas II</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.cdnfonts.com/css/satoshi" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@1&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Satoshi', 'sans-serif'] },
                    colors: {
                        brand: { light: '#E4F0EE', DEFAULT: '#1E6472', dark: '#123F48', accent: '#D4A017' }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/shared-utils.js"></script>
    <style>
        /* Watermark dekoratif tahun di section sejarah */
        .year-watermark {
            font-size: clamp(6rem, 18vw, 14rem);
            font-weight: 900;
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 2px #1E647218;
            user-select: none;
            pointer-events: none;
        }

        /* ── Diagram Struktur Organisasi — SVG approach ── */

        /* Semua card harus position:relative + z-index > 0
           supaya tampil DI ATAS layer SVG yang z-index:0 */
        .org-card {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased selection:bg-brand selection:text-white">

<!-- ===== NAVBAR ===== -->
<nav class="bg-white/80 backdrop-blur-md shadow-sm fixed w-full z-50 top-0 border-b border-gray-100">
    <div class="site-container">
        <div class="flex justify-between h-16 items-center">
            <div class="flex-shrink-0 flex items-center">
                <a href="index.php" class="text-lg md:text-2xl font-black text-brand flex items-center gap-2 tracking-tight">
                    <img src="assets/img/logo-kwt.png" alt="Logo KWT" class="w-8 h-8 object-contain"> KWT Mawar Bodas II
                </a>
            </div>
            <div class="hidden md:flex space-x-8 items-center">
                <a href="profil.php" class="text-brand font-semibold border-b-2 border-brand pb-0.5">Profil</a>
                <a href="index.php#qrcode" class="text-gray-600 hover:text-brand font-medium transition-colors">Validasi QR Kemasan</a>
                <a href="index.php#produk" class="text-gray-600 hover:text-brand font-medium transition-colors">Katalog Produk</a>
                <a href="index.php#kontak" class="text-gray-600 hover:text-brand font-medium transition-colors">Hubungi</a>
                <a href="pages/login.php" class="bg-brand/10 hover:bg-brand text-brand hover:text-white px-4 py-2 rounded-xl font-bold transition-all">Login</a>
            </div>
            <div class="md:hidden flex items-center gap-3">
                <a href="pages/login.php" class="text-brand font-bold text-sm bg-brand/10 px-3 py-1.5 rounded-lg">Login</a>
                <button onclick="openMobileMenu()" aria-label="Buka menu" class="text-gray-700 hover:text-brand p-1">
                    <i class="ph-bold ph-list text-2xl"></i>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- ===== MOBILE DRAWER ===== -->
<div id="mobile-menu-overlay" onclick="closeMobileMenu()"
     class="fixed inset-0 bg-black/50 z-[60] hidden opacity-0 transition-opacity duration-300"></div>
<div id="mobile-menu-panel"
     class="fixed top-0 right-0 h-full w-72 max-w-[80%] bg-white z-[70] shadow-2xl translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <span class="font-black text-brand text-base tracking-tight">Menu</span>
        <button onclick="closeMobileMenu()" class="text-gray-500 hover:text-brand p-1"><i class="ph-bold ph-x text-xl"></i></button>
    </div>
    <nav class="flex-1 overflow-y-auto py-2">
        <a href="profil.php" onclick="closeMobileMenu()" class="flex items-center gap-3 px-5 py-3 text-brand font-semibold bg-brand-light border-b border-gray-50">
            <i class="ph-bold ph-user-circle text-lg"></i> Profil</a>
        <a href="index.php#qrcode" onclick="closeMobileMenu()" class="flex items-center gap-3 px-5 py-3 text-gray-700 font-medium hover:bg-brand-light hover:text-brand transition-colors border-b border-gray-50">
            <i class="ph-bold ph-qr-code text-lg text-brand/70"></i> Validasi QR Kemasan</a>
        <a href="index.php#produk" onclick="closeMobileMenu()" class="flex items-center gap-3 px-5 py-3 text-gray-700 font-medium hover:bg-brand-light hover:text-brand transition-colors border-b border-gray-50">
            <i class="ph-bold ph-storefront text-lg text-brand/70"></i> Katalog Produk</a>
        <a href="index.php#kontak" onclick="closeMobileMenu()" class="flex items-center gap-3 px-5 py-3 text-gray-700 font-medium hover:bg-brand-light hover:text-brand transition-colors border-b border-gray-50">
            <i class="ph-bold ph-chat-circle-text text-lg text-brand/70"></i> Hubungi</a>
    </nav>
</div>

<!-- ===== 1. HERO MINI ===== -->
<section class="relative bg-brand-dark pt-28 pb-20 md:pt-36 md:pb-24 lg:pt-40 lg:pb-28 overflow-hidden">
    <!-- Layer 1: Foto kegiatan KWT sebagai background -->
    <div class="absolute inset-0 z-0">
        <img src="assets/img/foto-kwt-kegiatan.jpg" class="w-full h-full object-cover" alt="">
    </div>
    <!-- Layer 2: Overlay gradient brand-dark pekat supaya teks tetap kontras -->
    <div class="absolute inset-0 z-[1] bg-gradient-to-b from-brand-dark/90 to-brand-dark/95"></div>
    <!-- Layer 3: Dekorasi bulat blur di atas overlay -->
    <div class="absolute -top-20 -right-20 w-80 h-80 bg-brand/30 rounded-full blur-3xl opacity-40 pointer-events-none z-[2]"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-brand-light/10 rounded-full blur-2xl pointer-events-none z-[2]"></div>
    <!-- Konten -->
    <div class="site-container relative z-10">
        <p class="text-sm mb-5">
            <a href="index.php" class="text-white/60 hover:text-white transition-colors">Beranda</a>
            <span class="text-white/40 mx-2">/</span>
            <span class="text-white/90 font-medium">Profil</span>
        </p>
        <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight mb-3 leading-tight max-w-2xl">
            Profil KWT Mawar Bodas II
        </h1>
        <p class="text-white/70 text-base md:text-lg">Kelompok Wanita Tani Desa Bengle, Kecamatan Majalaya</p>
    </div>
</section>

<!-- ===== STATS STRIP (mengambang antara Hero dan Sejarah) ===== -->
<div class="site-container relative z-10 -mt-8 mb-0">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 px-6 py-5 grid grid-cols-3 divide-x divide-gray-100">
        <div class="text-center px-4">
            <p class="text-2xl md:text-3xl font-black text-brand">2019</p>
            <p class="text-xs text-gray-500 uppercase tracking-wide mt-0.5">Tahun Berdiri</p>
        </div>
        <div class="text-center px-4">
            <p class="text-2xl md:text-3xl font-black text-brand">21</p>
            <p class="text-xs text-gray-500 uppercase tracking-wide mt-0.5">Anggota Aktif</p>
        </div>
        <div class="text-center px-4">
            <p class="text-2xl md:text-3xl font-black text-brand">Bengle</p>
            <p class="text-xs text-gray-500 uppercase tracking-wide mt-0.5">Desa</p>
        </div>
    </div>
</div>

<!-- ===== 2. SEJARAH — bg-white + watermark tahun dekoratif ===== -->
<section class="reveal-left relative bg-white py-14 md:py-16 lg:py-20 overflow-hidden">
    <!-- Watermark angka tahun raksasa di background -->
    <div class="year-watermark absolute -right-8 top-1/2 -translate-y-1/2 select-none pointer-events-none">2019</div>
    <!-- Blob aksen kiri -->
    <div class="absolute top-0 left-0 w-72 h-72 bg-brand-light/40 rounded-full blur-3xl opacity-60 pointer-events-none -translate-x-1/2 -translate-y-1/2"></div>
    <div class="site-container relative z-10">
        <div class="max-w-3xl">
            <!-- Garis aksen vertikal + judul -->
            <div class="flex items-start gap-5 mb-6">
                <div class="w-1 h-16 bg-brand rounded-full shrink-0 mt-1"></div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-brand mb-1">Tentang Kami</p>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-900">Sejarah Kami</h2>
                </div>
            </div>
            <p class="text-gray-600 leading-relaxed text-base md:text-lg pl-6">
                Kaum ibu yang tergabung dalam KWT Mawar Bodas II awalnya adalah bagian dari anggota Kelompok Tani Dewasa,
                yaitu Kelompok Tani Mawar Bodas 2. Seiring bertambahnya ibu-ibu yang berminat mengikuti kegiatan kelompok
                tani, dibentuklah kelompok khusus wanita dengan nama Kelompok Wanita Tani (KWT) Mawar Bodas II, yang
                kemudian dikukuhkan secara resmi oleh Kepala Desa Bengle pada tanggal 19 Maret 2019.
            </p>
        </div>
    </div>
</section>

<!-- ===== 3. PROFIL UMUM + LOKASI — bg-brand-light, card gabungan sejajar ===== -->
<section class="reveal-right py-12 md:py-16 lg:py-18 bg-brand-light">
    <div class="site-container">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-1 h-10 bg-brand rounded-full shrink-0"></div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-brand-dark">Profil &amp; Lokasi</h2>
        </div>
        <!-- Card gabungan — overflow-hidden, grid stretch agar kolom sama tinggi -->
        <div class="rounded-2xl overflow-hidden shadow-lg border border-gray-100 grid grid-cols-1 lg:grid-cols-2">

            <!-- Kolom kiri: data profil -->
            <div class="bg-white p-8 md:p-10 flex flex-col justify-center">
                <!-- Nama & tanggal -->
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <p class="text-xs font-bold uppercase tracking-widest text-brand/60 mb-1">Nama Kelompok</p>
                    <p class="font-extrabold text-xl text-gray-900">KWT Mawar Bodas II</p>
                </div>
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <p class="text-xs font-bold uppercase tracking-widest text-brand/60 mb-1">Tanggal Berdiri</p>
                    <p class="font-bold text-gray-900 text-base flex items-center gap-2">
                        <i class="ph-bold ph-calendar-check text-brand"></i> 11 Maret 2019
                    </p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-brand/60 mb-2">Alamat Lengkap</p>
                    <p class="font-medium text-gray-700 text-base md:text-lg leading-relaxed flex items-start gap-2">
                        <i class="ph-bold ph-map-pin text-brand mt-0.5 shrink-0"></i>
                        <span>Perum Citra Kebun Mas Blok L, Desa Bengle, Kecamatan Majalaya, Kabupaten Karawang, Jawa Barat</span>
                    </p>
                </div>
            </div>

            <!-- Kolom kanan: peta — h-full, min-h untuk mobile -->
            <div class="relative min-h-[300px] lg:min-h-0">
                <iframe
                    src="https://maps.google.com/maps?q=-6.32953,107.35292&z=17&output=embed"
                    class="absolute inset-0 w-full h-full"
                    style="border:0;display:block;"
                    allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                <!-- Tombol overlay di sudut kanan bawah peta -->
                <a href="https://maps.app.goo.gl/hFEDspN6qsLYPoKb8" target="_blank" rel="noopener noreferrer"
                   class="absolute bottom-3 right-3 z-10 inline-flex items-center gap-1.5 bg-white/90 backdrop-blur-sm text-brand border border-brand/30 hover:bg-brand hover:text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all shadow-sm">
                    <i class="ph-bold ph-map-pin"></i> Buka di Google Maps
                </a>
            </div>

        </div>
    </div>
</section>

<!-- ===== 4. STRUKTUR ORGANISASI — SVG connector lines ===== -->
<section class="py-12 md:py-16 lg:py-20 bg-white">
    <div class="site-container">
        <div class="text-center mb-10">
            <p class="text-xs font-bold uppercase tracking-widest text-brand mb-2">KWT Mawar Bodas II</p>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-900">Struktur Organisasi</h2>
        </div>

        <!--
            #orgchart-wrapper: position relative — menjadi anchor koordinat SVG.
            SVG #orgchart-lines adalah elemen PERTAMA (z-index 0, di belakang card).
            Semua card pakai class org-card (position relative, z-index 1) supaya
            tampil di atas SVG.
        -->
        <div id="orgchart-wrapper" class="relative max-w-2xl mx-auto w-full select-none">

            <!-- SVG layer: digambar ulang oleh drawOrgChartLines() -->
            <svg id="orgchart-lines"
                 style="position:absolute;inset:0;width:100%;height:100%;pointer-events:none;z-index:0;"
                 aria-hidden="true"></svg>

            <!-- ── DESKTOP LAYOUT (md: ke atas) ── -->
            <!-- Pelindung — full-width center -->
            <div class="hidden md:flex justify-center mb-0">
                <div id="node-pelindung"
                     class="org-card rounded-xl border-2 border-brand/30 bg-white shadow-sm px-6 py-4 text-center w-56">
                    <p class="text-xs font-bold uppercase tracking-widest text-brand mb-1">Pelindung</p>
                    <p class="font-bold text-gray-900 text-sm">Lia Amallia, M.Pd</p>
                    <p class="text-xs text-gray-500 mt-0.5">Kepala Desa Bengle</p>
                </div>
            </div>

            <!-- Spacer vertikal antar Pelindung → Pembina (desktop) -->
            <div class="hidden md:block h-10"></div>

            <!-- Pembina — full-width center -->
            <div class="hidden md:flex justify-center mb-0">
                <div id="node-pembina"
                     class="org-card rounded-xl border-2 border-brand/30 bg-white shadow-sm px-6 py-4 text-center w-56">
                    <p class="text-xs font-bold uppercase tracking-widest text-brand mb-1">Pembina</p>
                    <p class="font-bold text-gray-900 text-sm">PKK Desa Bengle</p>
                </div>
            </div>

            <!-- Spacer vertikal antar Pembina → grid 3 kolom (desktop) -->
            <div class="hidden md:block h-14"></div>

            <!-- Grid 3 kolom: Sekretaris | Ketua | Bendahara (desktop) -->
            <div class="hidden md:grid grid-cols-3 gap-6 mb-0">
                <div class="flex justify-center">
                    <div id="node-sekretaris"
                         class="org-card rounded-xl border-2 border-brand/30 bg-white shadow-sm px-4 py-4 text-center w-full max-w-[180px]">
                        <p class="text-xs font-bold uppercase tracking-widest text-brand mb-1">Sekretaris</p>
                        <p class="font-bold text-gray-900 text-sm">Evi Suzana</p>
                    </div>
                </div>
                <div class="flex justify-center">
                    <div id="node-ketua"
                         class="org-card rounded-xl border-2 border-brand bg-brand shadow-md px-4 py-5 text-center w-full max-w-[180px]">
                        <p class="text-xs font-bold uppercase tracking-widest text-white/80 mb-1">Ketua</p>
                        <p class="font-bold text-white text-base">Isnaniah</p>
                    </div>
                </div>
                <div class="flex justify-center">
                    <div id="node-bendahara"
                         class="org-card rounded-xl border-2 border-brand/30 bg-white shadow-sm px-4 py-4 text-center w-full max-w-[180px]">
                        <p class="text-xs font-bold uppercase tracking-widest text-brand mb-1">Bendahara</p>
                        <p class="font-bold text-gray-900 text-sm">Holijah</p>
                    </div>
                </div>
            </div>

            <!-- Spacer vertikal antar grid → Anggota (desktop) -->
            <div class="hidden md:block h-14"></div>

            <!-- Anggota — full-width center (desktop) -->
            <div class="hidden md:flex justify-center">
                <div id="node-anggota"
                     class="org-card rounded-xl border-2 border-brand/30 bg-white shadow-sm px-8 py-4 text-center w-56">
                    <p class="text-xs font-bold uppercase tracking-widest text-brand mb-1">Anggota</p>
                    <p class="font-bold text-gray-900 text-sm">21 Orang</p>
                </div>
            </div>

            <!-- ── MOBILE LAYOUT (di bawah md:) ── -->
            <!--
                Hierarki mobile:
                Pelindung → Pembina → Ketua → Anggota  (rantai utama, garis SVG vertikal)
                Sekretaris + Bendahara = dua kotak kecil sejajar di bawah Ketua,
                ditampilkan sebagai 2-kolom grid tanpa garis SVG (hubungan lateral).
                Semua card pakai lebar max-w-xs seragam + items-center supaya center-x sama.
            -->
            <div class="flex flex-col items-center gap-0 md:hidden w-full">

                <!-- Pelindung mobile -->
                <div id="node-pelindung-m"
                     class="org-card rounded-xl border-2 border-brand/30 bg-white shadow-sm px-6 py-4 text-center w-full max-w-xs">
                    <p class="text-xs font-bold uppercase tracking-widest text-brand mb-1">Pelindung</p>
                    <p class="font-bold text-gray-900 text-sm">Lia Amallia, M.Pd</p>
                    <p class="text-xs text-gray-500 mt-0.5">Kepala Desa Bengle</p>
                </div>

                <div class="h-10"></div><!-- spacer garis 1 -->

                <!-- Pembina mobile -->
                <div id="node-pembina-m"
                     class="org-card rounded-xl border-2 border-brand/30 bg-white shadow-sm px-6 py-4 text-center w-full max-w-xs">
                    <p class="text-xs font-bold uppercase tracking-widest text-brand mb-1">Pembina</p>
                    <p class="font-bold text-gray-900 text-sm">PKK Desa Bengle</p>
                </div>

                <div class="h-10"></div><!-- spacer garis 2 -->

                <!-- Ketua mobile — card utama, di tengah rantai hierarki -->
                <div id="node-ketua-m"
                     class="org-card rounded-xl border-2 border-brand bg-brand shadow-md px-6 py-5 text-center w-full max-w-xs">
                    <p class="text-xs font-bold uppercase tracking-widest text-white/80 mb-1">Ketua</p>
                    <p class="font-bold text-white text-lg">Isnaniah</p>
                </div>

                <!-- Sekretaris & Bendahara: 2-kolom grid di bawah Ketua,
                     tanpa garis SVG — hubungan lateral cukup lewat layout sejajar. -->
                <div class="w-full max-w-xs mt-6 mb-0 grid grid-cols-2 gap-2">
                    <div id="node-sekretaris-m"
                         class="org-card rounded-xl border-2 border-brand/20 bg-brand/5 px-3 py-3 text-center">
                        <p class="text-xs font-bold uppercase tracking-widest text-brand mb-1">Sekretaris</p>
                        <p class="font-semibold text-gray-800 text-xs">Evi Suzana</p>
                    </div>
                    <div id="node-bendahara-m"
                         class="org-card rounded-xl border-2 border-brand/20 bg-brand/5 px-3 py-3 text-center">
                        <p class="text-xs font-bold uppercase tracking-widest text-brand mb-1">Bendahara</p>
                        <p class="font-semibold text-gray-800 text-xs">Holijah</p>
                    </div>
                </div>

                <div class="h-10"></div><!-- spacer garis 3: Ketua → Anggota -->

                <!-- Anggota mobile -->
                <div id="node-anggota-m"
                     class="org-card rounded-xl border-2 border-brand/30 bg-white shadow-sm px-8 py-4 text-center w-full max-w-xs">
                    <p class="text-xs font-bold uppercase tracking-widest text-brand mb-1">Anggota</p>
                    <p class="font-bold text-gray-900 text-sm">21 Orang</p>
                </div>

            </div><!-- /mobile layout -->

        </div><!-- /#orgchart-wrapper -->
    </div>
</section>

<!-- ===== 5. KEGIATAN USAHA — bg-brand-dark, wallpaper tekstur + glass card ===== -->
<section class="reveal-left py-12 md:py-16 lg:py-20 bg-brand-dark relative overflow-hidden">
    <!-- Layer wallpaper tekstur — sama seperti di index.php hero -->
    <div class="absolute inset-0 z-0 pointer-events-none"
         style="background-image:url('assets/img/wallpaper.png');background-size:cover;background-position:center;opacity:0.065;"></div>
    <!-- Dekorasi blob di atas wallpaper -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-brand/30 rounded-full blur-3xl opacity-30 pointer-events-none z-[1]"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-brand-light/10 rounded-full blur-2xl pointer-events-none z-[1]"></div>
    <div class="site-container relative z-10">
        <div class="text-center mb-12">
            <p class="text-xs font-bold uppercase tracking-widest text-brand-light/60 mb-2">Apa yang Kami Lakukan</p>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-white">Kegiatan Usaha</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-3xl mx-auto">

            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 flex items-start gap-4">
                <div class="bg-brand-light/20 w-12 h-12 rounded-xl flex items-center justify-center shrink-0">
                    <i class="ph-bold ph-plant text-xl text-brand-light"></i>
                </div>
                <div>
                    <p class="font-bold text-white mb-1">Penanaman Bibit Hortikultura</p>
                    <p class="text-sm text-white/60 leading-relaxed">Budidaya tanaman hortikultura produktif oleh anggota kelompok.</p>
                </div>
            </div>

            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 flex items-start gap-4">
                <div class="bg-brand-light/20 w-12 h-12 rounded-xl flex items-center justify-center shrink-0">
                    <i class="ph-bold ph-tree text-xl text-brand-light"></i>
                </div>
                <div>
                    <p class="font-bold text-white mb-1">Pemanfaatan Lahan Pekarangan</p>
                    <p class="text-sm text-white/60 leading-relaxed">Optimalisasi lahan pekarangan rumah untuk ketahanan pangan keluarga.</p>
                </div>
            </div>

            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 flex items-start gap-4">
                <div class="bg-brand-light/20 w-12 h-12 rounded-xl flex items-center justify-center shrink-0">
                    <i class="ph-bold ph-cookie text-xl text-brand-light"></i>
                </div>
                <div>
                    <p class="font-bold text-white mb-1">Pembuatan Aneka Makanan Ringan</p>
                    <p class="text-sm text-white/60 leading-relaxed">Produksi camilan dan olahan pangan rumahan berkualitas untuk dipasarkan.</p>
                </div>
            </div>

            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 flex items-start gap-4">
                <div class="bg-brand-light/20 w-12 h-12 rounded-xl flex items-center justify-center shrink-0">
                    <i class="ph-bold ph-carrot text-xl text-brand-light"></i>
                </div>
                <div>
                    <p class="font-bold text-white mb-1">Produk Olahan Paria dan Terong</p>
                    <p class="text-sm text-white/60 leading-relaxed">Inovasi olahan sayuran lokal menjadi produk bernilai jual tinggi.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ===== 6. DAFTAR ANGGOTA — bg-gray-50, tanpa reveal ===== -->
<section class="py-12 md:py-16 lg:py-20 bg-gray-50">
    <div class="site-container">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-900">Daftar Anggota</h2>
            <p class="text-gray-500 mt-2">21 anggota aktif</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-w-3xl mx-auto">
            <?php
            $anggota = [
                'Suwarsih','Zulaekah','Kursiah','Rizka Nurfadlika','Milawati',
                'Titin Herawati','Siti Sulastri','Herti','Kurnia Suhartini',
                'Triyantini Widyastuti','Icah Kaesah','Mariam','Anik Rahayu',
                'Maria Elisabeth','Elly Aliyah Nuraliyah','Daryati','Annisah',
                'Dini Wulandini','Elly Manisah','Masitah','Ida Farida',
            ];
            foreach ($anggota as $i => $nama):
            ?>
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 flex items-center gap-2 hover:border-brand/30 hover:bg-brand-light/30 transition-colors">
                <span class="text-xs font-bold text-brand/50 shrink-0 w-5 text-right"><?= $i+1 ?>.</span>
                <span class="font-medium"><?= htmlspecialchars($nama) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== 7. KONTAK PERSON ===== -->
<section class="reveal-right py-8 md:py-12 lg:py-14 bg-white">
    <div class="site-container">
        <div class="w-full bg-gradient-to-r from-brand-light to-white rounded-2xl border border-brand/20 shadow-sm overflow-hidden">
            <div class="px-5 py-6 md:px-10 md:py-9 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 sm:gap-6">
                <!-- Teks kiri -->
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl md:text-2xl lg:text-3xl font-extrabold text-gray-900 tracking-tight mb-1">Ingin Bekerja Sama atau Bertanya?</h2>
                    <p class="text-gray-500 text-sm leading-relaxed mb-3">Hubungi kontak person kami, Ibu Isnaniah, untuk informasi lebih lanjut seputar KWT Mawar Bodas II.</p>
                    <!-- Alamat -->
                    <p class="text-xs text-gray-400 leading-relaxed flex items-start gap-1.5">
                        <i class="ph-bold ph-map-pin text-brand shrink-0 mt-0.5"></i>
                        <span>Perum Citra Kebun Mas Blok L, Desa Bengle, Kecamatan Majalaya, Kabupaten Karawang, Jawa Barat</span>
                    </p>
                </div>
                <!-- Tombol kanan -->
                <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-3">
                    <a href="https://wa.me/6285711547232" target="_blank" rel="noopener noreferrer"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold px-6 py-3 rounded-xl transition-all shadow-md shadow-brand/20 text-sm">
                        <i class="ph-bold ph-whatsapp-logo text-base"></i> Hubungi Isnaniah
                    </a>
                    <a href="https://maps.app.goo.gl/hFEDspN6qsLYPoKb8" target="_blank" rel="noopener noreferrer"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white border border-brand/30 hover:bg-brand-light text-brand font-semibold px-6 py-3 rounded-xl transition-all text-sm">
                        <i class="ph-bold ph-map-pin text-base"></i> Lihat Lokasi
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="bg-gray-900 text-gray-300">
    <div class="site-container py-14 lg:py-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 lg:gap-16">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-white rounded-xl p-1.5 shrink-0 flex items-center justify-center w-9 h-9">
                        <img src="assets/img/logo-kwt.png" alt="Logo KWT" class="w-6 h-6 object-contain">
                    </div>
                    <span class="font-black text-white text-lg tracking-tight">KWT Mawar Bodas II</span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed max-w-xs">Olahan kuliner nusantara instan, higienis, dan terjamin keasliannya lewat teknologi QR Code Dinamis.</p>
            </div>
            <div>
                <h4 class="font-bold text-white text-sm uppercase tracking-widest mb-4">Navigasi</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="index.php#produk" class="text-gray-400 hover:text-brand transition-colors flex items-center gap-2"><i class="ph-bold ph-storefront text-base"></i> Katalog Produk</a></li>
                    <li><a href="index.php#qrcode" class="text-gray-400 hover:text-brand transition-colors flex items-center gap-2"><i class="ph-bold ph-qr-code text-base"></i> Validasi QR Kemasan</a></li>
                    <li><a href="pages/login.php" class="text-gray-400 hover:text-brand transition-colors flex items-center gap-2"><i class="ph-bold ph-sign-in text-base"></i> Login Admin</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white text-sm uppercase tracking-widest mb-4">Kontak</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="https://wa.me/6281381690100" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-brand transition-colors flex items-center gap-2"><i class="ph-bold ph-whatsapp-logo text-base"></i> +62 813-8169-0100</a></li>
                    <li><a href="https://instagram.com/bundahabib19" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-brand transition-colors flex items-center gap-2"><i class="ph-bold ph-instagram-logo text-base"></i> @bundahabib19</a></li>
                    <li><a href="https://facebook.com/bundahabib19" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-brand transition-colors flex items-center gap-2"><i class="ph-bold ph-facebook-logo text-base"></i> bundahabib19</a></li>
                    <li class="flex items-center gap-2 text-gray-400"><i class="ph-bold ph-map-pin text-base shrink-0"></i><span>Kelompok Wanita Tani Mawar Bodas II</span></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 mt-10 pt-6 text-center text-xs text-gray-500">
            &copy; <?php echo date('Y'); ?> KWT Mawar Bodas II. All rights reserved.
        </div>
    </div>
</footer>

<!-- ===== JS ===== -->
<script>
function openMobileMenu() {
    const o = document.getElementById('mobile-menu-overlay');
    const p = document.getElementById('mobile-menu-panel');
    if (!o || !p) return;
    o.classList.remove('hidden');
    o.getBoundingClientRect();
    o.classList.add('opacity-100'); o.classList.remove('opacity-0');
    p.classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}
function closeMobileMenu() {
    const o = document.getElementById('mobile-menu-overlay');
    const p = document.getElementById('mobile-menu-panel');
    if (!o || !p) return;
    o.classList.remove('opacity-100'); o.classList.add('opacity-0');
    p.classList.add('translate-x-full');
    document.body.style.overflow = '';
    setTimeout(() => o.classList.add('hidden'), 300);
}

// ==========================================
// SCROLL REVEAL
// ==========================================
let _sro = null;
function initScrollReveal() {
    _sro = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                _sro.unobserve(e.target);
                // Kalau section organisasi baru masuk viewport, redraw garis
                // setelah transisi selesai (delay 120ms cukup untuk fade-in CSS)
                if (e.target.contains(document.getElementById('orgchart-wrapper'))) {
                    setTimeout(drawOrgChartLines, 120);
                }
            }
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal-left, .reveal-right').forEach(el => _sro.observe(el));
    setTimeout(() => {
        document.querySelectorAll('.reveal-left:not(.visible), .reveal-right:not(.visible)').forEach(el => el.classList.add('visible'));
    }, 3000);
}
window.addEventListener('beforeunload', () => { if (_sro) _sro.disconnect(); });

// ==========================================
// ORG CHART — SVG LINE DRAWING
// ==========================================

/**
 * Kembalikan bounding box sebuah elemen RELATIF terhadap #orgchart-wrapper.
 * Dipakai untuk menghitung koordinat titik dalam viewBox SVG.
 */
function _relRect(el, wrapperRect) {
    const r = el.getBoundingClientRect();
    return {
        top:    r.top    - wrapperRect.top,
        left:   r.left   - wrapperRect.left,
        right:  r.right  - wrapperRect.left,
        bottom: r.bottom - wrapperRect.top,
        width:  r.width,
        height: r.height,
        cx:     r.left   - wrapperRect.left + r.width  / 2,  // center-x
        cy:     r.top    - wrapperRect.top  + r.height / 2,  // center-y
        topCx:  r.left   - wrapperRect.left + r.width  / 2,  // top center-x
        topY:   r.top    - wrapperRect.top,                   // top edge y
        botY:   r.bottom - wrapperRect.top,                   // bottom edge y
        botCx:  r.left   - wrapperRect.left + r.width  / 2,  // bottom center-x
    };
}

/** Buat elemen SVG dengan namespace yang benar */
function _svgEl(tag, attrs) {
    const el = document.createElementNS('http://www.w3.org/2000/svg', tag);
    for (const [k, v] of Object.entries(attrs)) el.setAttribute(k, v);
    return el;
}

/** Shorthand buat <line> dengan style standar */
function _line(svg, x1, y1, x2, y2) {
    svg.appendChild(_svgEl('line', {
        x1, y1, x2, y2,
        stroke: '#1E6472',
        'stroke-opacity': '0.35',
        'stroke-width': '2',
        fill: 'none',
        'stroke-linecap': 'round',
    }));
}

/** Shorthand buat <path> dengan style standar */
function _path(svg, d) {
    svg.appendChild(_svgEl('path', {
        d,
        stroke: '#1E6472',
        'stroke-opacity': '0.35',
        'stroke-width': '2',
        fill: 'none',
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
    }));
}

function drawOrgChartLines() {
    const wrapper = document.getElementById('orgchart-wrapper');
    const svg     = document.getElementById('orgchart-lines');
    if (!wrapper || !svg) return;

    const wRect = wrapper.getBoundingClientRect();
    if (wRect.width === 0 || wRect.height === 0) return; // belum di-render

    // Update viewBox agar match ukuran wrapper saat ini
    svg.setAttribute('viewBox', `0 0 ${wRect.width} ${wRect.height}`);

    // Hapus semua garis lama
    svg.innerHTML = '';

    const isMobile = window.innerWidth < 768; // breakpoint Tailwind md = 768px

    if (!isMobile) {
        // ── DESKTOP ──
        // Gunakan node-* tanpa suffix -m (elemen desktop)
        const elPelindung  = document.getElementById('node-pelindung');
        const elPembina    = document.getElementById('node-pembina');
        const elSekretaris = document.getElementById('node-sekretaris');
        const elKetua      = document.getElementById('node-ketua');
        const elBendahara  = document.getElementById('node-bendahara');
        const elAnggota    = document.getElementById('node-anggota');

        if (!elPelindung || !elPembina || !elSekretaris || !elKetua || !elBendahara || !elAnggota) return;

        const rP  = _relRect(elPelindung,  wRect);
        const rPb = _relRect(elPembina,    wRect);
        const rS  = _relRect(elSekretaris, wRect);
        const rK  = _relRect(elKetua,      wRect);
        const rB  = _relRect(elBendahara,  wRect);
        const rA  = _relRect(elAnggota,    wRect);

        // 1. Pelindung bawah → Pembina atas
        _line(svg, rP.botCx, rP.botY, rPb.topCx, rPb.topY);

        // 2. Pembina bawah → titik percabangan (mid-point antara Pembina dan baris 3)
        //    Kemudian cabang ke Sekretaris, Ketua, Bendahara
        //    Pakai path: turun vertikal dari Pembina ke junction-y,
        //    lalu garis H ke kiri & kanan, lalu drop ke tiap card
        const junctionY = rPb.botY + (rK.topY - rPb.botY) * 0.5; // tepat di tengah spacer

        // Trunk: Pembina bottom → junction
        _line(svg, rPb.botCx, rPb.botY, rPb.botCx, junctionY);

        // Garis H: dari cx Sekretaris ke cx Bendahara lewat junctionY
        _line(svg, rS.topCx, junctionY, rB.topCx, junctionY);

        // Drop vertikal dari junction ke atas masing-masing card
        _line(svg, rS.topCx, junctionY, rS.topCx, rS.topY);
        _line(svg, rK.topCx, junctionY, rK.topCx, rK.topY);
        _line(svg, rB.topCx, junctionY, rB.topCx, rB.topY);

        // 3. Ketua bawah → Anggota atas
        _line(svg, rK.botCx, rK.botY, rA.topCx, rA.topY);

    } else {
        // ── MOBILE ──
        // Rantai utama: Pelindung → Pembina → Ketua → Anggota
        // Dari Ketua bercabang ke Sekretaris & Bendahara (grid 2-kolom lateral).
        //
        // Garis yang digambar:
        //   [1] Pelindung bawah → Pembina atas
        //   [2] Pembina bawah  → Ketua atas
        //   [3] Ketua bawah → junctionY (trunk cabang)
        //   [3H] Garis H di junctionY: dari cx Sekretaris ke cx Bendahara
        //   [3a] Drop dari junctionY → atas Sekretaris
        //   [3b] Drop dari junctionY → atas Bendahara
        //   [4] Max-botY(Sek,Ben) → Anggota atas
        //
        // cx = wRect.width/2 dipakai sebagai sumbu trunk vertikal utama.

        const elPelindung  = document.getElementById('node-pelindung-m');
        const elPembina    = document.getElementById('node-pembina-m');
        const elKetua      = document.getElementById('node-ketua-m');
        const elSekretaris = document.getElementById('node-sekretaris-m'); // untuk ref botY grid
        const elBendahara  = document.getElementById('node-bendahara-m');  // exit point grid
        const elAnggota    = document.getElementById('node-anggota-m');

        if (!elPelindung || !elPembina || !elKetua || !elAnggota) return;

        const rP  = _relRect(elPelindung, wRect);
        const rPb = _relRect(elPembina,   wRect);
        const rK  = _relRect(elKetua,     wRect);
        const rA  = _relRect(elAnggota,   wRect);

        // Sumbu x tunggal = tepat tengah wrapper
        const cx = wRect.width / 2;

        // [1] Pelindung bawah → Pembina atas
        _line(svg, cx, rP.botY, cx, rPb.topY);

        // [2] Pembina bawah → Ketua atas
        _line(svg, cx, rPb.botY, cx, rK.topY);

        // [3] Cabang dari Ketua ke Sekretaris & Bendahara
        // Pola: trunk vertikal dari bawah Ketua turun ke junctionY,
        // lalu garis H dari cx Sekretaris ke cx Bendahara,
        // lalu drop vertikal ke atas masing-masing card.
        if (elSekretaris && elBendahara) {
            const rS  = _relRect(elSekretaris, wRect);
            const rBh = _relRect(elBendahara,  wRect);

            // junctionY = tepat di tengah ruang antara bawah Ketua dan atas card lateral
            const junctionY = rK.botY + (Math.min(rS.topY, rBh.topY) - rK.botY) * 0.5;

            // Trunk: bawah Ketua → junctionY (di cx tengah wrapper)
            _line(svg, cx, rK.botY, cx, junctionY);

            // Garis H: dari cx Sekretaris ke cx Bendahara di junctionY
            // Pastikan rentang H mencakup cx (titik trunk) supaya tidak ada celah
            const hLeft  = Math.min(rS.cx, rBh.cx, cx);
            const hRight = Math.max(rS.cx, rBh.cx, cx);
            _line(svg, hLeft, junctionY, hRight, junctionY);

            // Drop ke atas Sekretaris
            _line(svg, rS.cx, junctionY, rS.cx, rS.topY);

            // Drop ke atas Bendahara
            _line(svg, rBh.cx, junctionY, rBh.cx, rBh.topY);

            // [4] Bawah grid → Anggota atas (pakai max botY kedua card)
            const gridBotY = Math.max(rS.botY, rBh.botY);
            _line(svg, cx, gridBotY, cx, rA.topY);
        } else {
            // Fallback kalau card lateral tidak ditemukan
            _line(svg, cx, rK.botY, cx, rA.topY);
        }
    }
}

// Debounce helper — cegah redraw terlalu sering saat resize
let _orgResizeTimer = null;
function _debouncedDraw() {
    clearTimeout(_orgResizeTimer);
    _orgResizeTimer = setTimeout(drawOrgChartLines, 150);
}

window.addEventListener('resize', _debouncedDraw);

document.addEventListener('DOMContentLoaded', () => {
    initScrollReveal();
    // Panggil sekali langsung setelah DOM siap
    drawOrgChartLines();
    // Panggil lagi setelah 300ms untuk antisipasi font/layout shift awal
    setTimeout(drawOrgChartLines, 300);
});
</script>
</body>
</html>
