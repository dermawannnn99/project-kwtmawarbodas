        // ==========================================
        // SISTEM TOAST
        // ==========================================
        function showToast(msg, isError=false) {
            const wrap = document.createElement('div');
            wrap.className = [
                'fixed top-5 right-5 z-[300] flex items-start gap-3 px-4 py-3.5 rounded-2xl shadow-lg',
                'max-w-xs w-full text-sm font-medium',
                'transition-all duration-300 -translate-y-3 opacity-0',
                isError
                    ? 'bg-white border border-red-100 text-gray-800'
                    : 'bg-white border border-green-100 text-gray-800'
            ].join(' ');

            const dot = document.createElement('span');
            dot.className = [
                'mt-0.5 shrink-0 w-2 h-2 rounded-full',
                isError ? 'bg-red-500' : 'bg-green-500'
            ].join(' ');

            const icon = document.createElement('i');
            icon.className = [
                'text-lg shrink-0',
                isError ? 'ph-bold ph-warning-circle text-red-500' : 'ph-bold ph-check-circle text-green-600'
            ].join(' ');

            const text = document.createElement('span');
            text.className = 'leading-snug';
            text.textContent = msg;

            wrap.appendChild(icon);
            wrap.appendChild(text);
            document.body.appendChild(wrap);

            setTimeout(() => {
                wrap.classList.remove('-translate-y-3', 'opacity-0');
                wrap.classList.add('translate-y-0', 'opacity-100');
            }, 10);

            setTimeout(() => {
                wrap.classList.add('opacity-0', '-translate-y-1');
                setTimeout(() => wrap.remove(), 300);
            }, 3500);
        }

        // ==========================================
        // KRISTALISASI DATABASE CRUD AJAX (MySQL)
        // ==========================================
        let productsCache = [];
        
        async function fetchProducts() {
            try {
                const res = await fetch('index.php?action=get_products');
                const data = await res.json();
                if (data.status === 'success') {
                    productsCache = data.data;
                    renderPublicProducts();
                    // renderAdminProducts hanya dipanggil kalau elemennya ada (halaman admin)
                    if (document.getElementById('admin-product-list')) renderAdminProducts();
                } else {
                    showToast(data.message, true);
                }
            } catch (e) {
                console.error(e);
                showToast("Sistem tidak dapat terhubung ke server MySQL database local Anda.", true);
            }
        }

        // CATATAN: form admin (deleteProduct, editProduct, resetForm) sudah dipindah
        // sepenuhnya ke pages/admin.php. Fungsi-fungsi itu tidak ada di sini
        // untuk mencegah JS error akibat elemen form yang tidak ada di index.php.

        // Tampilkan modal download QR Kemasan
        window.showQR = (batchCode) => {
            document.getElementById('modal-generate-qr').classList.remove('hidden');
            document.getElementById('qr-batch-text').innerText = "Kode Batch: " + batchCode;
            
            // Cari path URL folder tempat Anda menginstal program agar QR mengarah ke URL dinamis yang sama
            const currentUrl = window.location.href.split('?')[0]; 
            const targetUrl = `${currentUrl}?scan=${batchCode}`;
            
            // Panggil API goqr.me untuk mendapatkan gambar kode QR yang bisa dipindai
            document.getElementById('qr-generated-img').src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(targetUrl)}`;
        }

        // ==========================================
        // RENDERING UI FRONTEND
        // ==========================================
        // [QUAL-1] formatRupiah dan escHtml sudah dipindah ke assets/js/shared-utils.js
        // yang di-load sebelum main.js di index.php

        function renderPublicProducts() {
            const listContainer = document.getElementById('public-product-list');
            // Filter: hanya tampilkan produk yang is_visible = 1
            const visible = productsCache.filter(p => parseInt(p.is_visible ?? 1) === 1);
            if (visible.length === 0) {
                listContainer.innerHTML = '<div class="col-span-full text-center text-gray-400 py-12">Belum ada katalog menu di database MySQL Anda.</div>';
                return;
            }
            // [SEC-6] Semua data dari server di-escape dengan escHtml() sebelum masuk innerHTML
            listContainer.innerHTML = visible.map(p => `
                <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all flex flex-col h-full group">
                    <div class="relative overflow-hidden h-56 bg-gray-100">
                        <img src="${escHtml(p.image_url)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500'">
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex justify-between items-start mb-2 gap-4">
                            <h3 class="text-xl font-extrabold text-gray-900 line-clamp-1">${escHtml(p.name)}</h3>
                            <span class="font-extrabold text-brand shrink-0 text-lg">${formatRupiah(p.price)}</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-6 flex-grow line-clamp-3">${escHtml(p.description)}</p>
                        <div class="flex gap-2 items-center justify-between mt-auto pt-4 border-t border-gray-100">
                            <span class="text-xs font-semibold text-gray-400">Exp: ${escHtml(p.exp_date)}</span>
                            <a href="#qrcode" class="bg-brand/10 hover:bg-brand text-brand hover:text-white px-4 py-2 rounded-xl text-xs font-extrabold transition-all">Pindai QR Kemasan</a>
                        </div>
                    </div>
                </div>
            `).join('');
            applyCardReveal();
        }

        function renderAdminProducts() {
            const container = document.getElementById('admin-product-list');
            if (!container) return;
            if (productsCache.length === 0) {
                container.innerHTML = '<div class="text-center py-12 text-gray-400">Database kosong.</div>';
                return;
            }
            // [SEC-6] escHtml di semua interpolasi data produk
            container.innerHTML = productsCache.map(p => `
                <div class="bg-white border border-gray-100 rounded-2xl p-4 flex gap-4 items-center shadow-sm hover:shadow-md transition-all">
                    <img src="${escHtml(p.image_url)}" class="w-16 h-16 rounded-xl object-cover shrink-0" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=200'">
                    <div class="flex-grow min-w-0">
                        <h5 class="font-extrabold text-gray-900 truncate">${escHtml(p.name)}</h5>
                        <p class="text-xs text-gray-500 mt-0.5">Batch: <span class="font-extrabold text-brand">${escHtml(p.batch_code)}</span> | Exp: ${escHtml(p.exp_date)}</p>
                    </div>
                    <div class="flex gap-1.5 shrink-0">
                        <button onclick="showQR('${escHtml(p.batch_code)}')" class="bg-green-50 hover:bg-green-100 text-green-700 p-2.5 rounded-xl transition-colors" title="Lihat & Download QR Kemasan"><i class="ph-bold ph-qr-code text-lg"></i></button>
                        <button onclick="editProduct(${p.id})" class="bg-blue-50 hover:bg-blue-100 text-blue-700 p-2.5 rounded-xl transition-colors" title="Edit Data"><i class="ph-bold ph-pencil-simple text-lg"></i></button>
                        <button onclick="deleteProduct(${p.id})" class="bg-red-50 hover:bg-red-100 text-red-700 p-2.5 rounded-xl transition-colors" title="Hapus"><i class="ph-bold ph-trash text-lg"></i></button>
                    </div>
                </div>
            `).join('');
        }

        // ==========================================
        // SMART CAMERA QR-CODE SCANNER RENDER
        // ==========================================
        let html5QrCode;

        // --- State kamera ---
        let cameraDevices  = [];   // daftar semua kamera dari getCameras()
        let activeCamIndex = 0;    // index kamera yang sedang aktif
        let torchOn        = false; // state flash/torch

        // Helper: sembunyikan/tampilkan tombol kontrol kamera
        function _showScannerControls(show) {
            const ctrl = document.getElementById('scanner-controls');
            if (show) ctrl.classList.remove('hidden');
            else       ctrl.classList.add('hidden');
        }

        // Helper: cek & setup tombol flash setelah kamera berhasil start
        async function _setupFlashButton() {
            const btnFlash = document.getElementById('btn-flash');
            btnFlash.classList.add('hidden'); // sembunyikan dulu
            torchOn = false;
            document.getElementById('flash-icon').className  = 'ph-bold ph-lightning text-lg';
            document.getElementById('flash-label').textContent = 'Flash';

            try {
                // Ambil video track yang aktif dari elemen video di qr-reader
                const videoEl = document.querySelector('#qr-reader video');
                if (!videoEl) return;
                const track = videoEl.srcObject && videoEl.srcObject.getVideoTracks()[0];
                if (!track) return;

                const caps = track.getCapabilities ? track.getCapabilities() : {};
                if (caps.torch) {
                    // Device mendukung torch — tampilkan tombol sebagai flex
                    btnFlash.classList.remove('hidden');
                    btnFlash.classList.add('flex');
                }
            } catch (e) { /* device tidak support — tombol tetap tersembunyi */ }
        }

        window.startScanner = async () => {
            // Verifikasi Protokol Keamanan Browser (Wajib HTTPS di luar local server)
            const isSecure = window.location.protocol === 'https:' || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
            
            if (!isSecure) {
                showToast("Kesalahan Protokol Keamanan! Akses kamera wajib HTTPS.", true);
                document.getElementById('scanner-placeholder').innerHTML = `
                    <div class="text-red-500 p-6 text-center max-w-xs mx-auto">
                        <i class="ph-fill ph-warning-octagon text-5xl mb-3"></i>
                        <p class="font-bold text-lg text-gray-900">Browser Membatasi Kamera</p>
                        <p class="text-xs text-gray-400 mt-2">Browser memblokir kamera di protokol HTTP tidak aman. Silakan gunakan 'localhost' di XAMPP Anda atau pasang sertifikat SSL (HTTPS) di web-server Anda.</p>
                    </div>
                `;
                return;
            }

            document.getElementById('scanner-placeholder').classList.add('hidden');
            document.getElementById('qr-reader').classList.remove('hidden');
            document.getElementById('btn-stop-scan').classList.remove('hidden');

            try {
                // Hentikan & bersihkan instance lama tuntas sebelum bikin yang baru,
                // supaya hardware kamera dilepas browser sepenuhnya dan tidak terkunci
                if (html5QrCode) {
                    try {
                        if (html5QrCode.isScanning) await html5QrCode.stop();
                        html5QrCode.clear(); // hapus elemen video + overlay dari DOM
                    } catch (_) { /* abaikan error cleanup */ }
                    html5QrCode = null;
                    // Jeda 250ms — beri waktu browser melepas resource kamera sebelum diminta ulang
                    await new Promise(resolve => setTimeout(resolve, 250));
                }

                html5QrCode = new Html5Qrcode("qr-reader");
                
                const config = { 
                    fps: 12, 
                    qrbox: (width, height) => {
                        const minDimension = Math.min(width, height);
                        const size = minDimension * 0.85;
                        return { width: size, height: size };
                    }
                };

                // Membaca semua unit video camera yang tersedia pada perangkat secara dinamis
                // Simpan ke cameraDevices agar tidak perlu getCameras() ulang saat switch
                const devices = await Html5Qrcode.getCameras();
                cameraDevices = devices && devices.length > 0 ? devices : [];
                
                if (cameraDevices.length > 0) {
                    // Default: pilih kamera belakang kalau ada
                    activeCamIndex = 0;
                    for (let i = 0; i < cameraDevices.length; i++) {
                        const label = cameraDevices[i].label.toLowerCase();
                        if (label.includes('back') || label.includes('rear') || label.includes('environment') || label.includes('belakang')) {
                            activeCamIndex = i;
                            break;
                        }
                    }

                    await html5QrCode.start(
                        cameraDevices[activeCamIndex].id,
                        config,
                        onScanSuccess,
                        onScanFailure
                    );
                    showToast("Sensor lensa kamera berhasil diaktifkan.");
                } else {
                    // Fallback alternatif kedua jika enumerasi devices tertutup sistem
                    await html5QrCode.start(
                        { facingMode: "user" },
                        config,
                        onScanSuccess,
                        onScanFailure
                    );
                    showToast("Webcam default laptop diaktifkan.");
                }

                // Tampilkan kontrol, setup tombol switch & flash
                _showScannerControls(true);

                // Tombol switch: tampilkan hanya kalau ada lebih dari 1 kamera
                const btnSwitch = document.getElementById('btn-switch-cam');
                if (cameraDevices.length > 1) {
                    btnSwitch.classList.remove('hidden');
                    btnSwitch.classList.add('flex');
                } else {
                    btnSwitch.classList.add('hidden');
                }

                // Setup flash: perlu delay singkat agar video track sudah siap
                setTimeout(_setupFlashButton, 600);

            } catch (error) {
                console.error("Gagal inisialisasi kamera:", error);
                // Bedakan pesan error berdasarkan jenis — jangan selalu bilang "izin browser"
                const name = error && (error.name || (error.message || ''));
                let errMsg;
                if (name.includes('NotAllowedError') || name.includes('PermissionDenied')) {
                    errMsg = "Akses kamera ditolak. Izinkan hak akses kamera di pengaturan browser.";
                } else if (name.includes('NotReadableError') || name.includes('TrackStartError') || name.includes('Concurrent')) {
                    errMsg = "Kamera sedang digunakan aplikasi lain. Tutup tab/aplikasi lain lalu coba lagi.";
                } else if (name.includes('NotFoundError') || name.includes('DevicesNotFound')) {
                    errMsg = "Kamera tidak terdeteksi di perangkat ini.";
                } else {
                    errMsg = "Gagal mengaktifkan kamera. Coba beberapa saat lagi atau refresh halaman.";
                }
                showToast(errMsg, true);
                stopScanner();
            }
        }

        window.stopScanner = async () => {
            document.getElementById('scanner-placeholder').classList.remove('hidden');
            document.getElementById('qr-reader').classList.add('hidden');
            document.getElementById('btn-stop-scan').classList.add('hidden');
            _showScannerControls(false);

            // Reset state flash
            torchOn = false;
            
            document.getElementById('scanner-placeholder').innerHTML = `
                <div class="bg-brand-light w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ph-fill ph-qr-code text-4xl text-brand"></i>
                </div>
                <p class="text-gray-900 font-bold text-lg">Pratinjau Scanner Kamera</p>
                <p class="text-gray-400 text-sm max-w-xs mx-auto mt-2">Izinkan hak akses kamera pada dialog browser saat diminta untuk memulai peninjauan</p>
            `;

            if (html5QrCode && html5QrCode.isScanning) {
                try {
                    await html5QrCode.stop();
                    html5QrCode.clear(); // bersihkan elemen video + overlay dari DOM
                } catch (err) {
                    console.error(err);
                }
            }
            html5QrCode = null;
        }

        // Switch ke kamera berikutnya (cycle index)
        window.switchCamera = async () => {
            if (!html5QrCode || cameraDevices.length <= 1) return;

            // Matikan flash sebelum switch
            torchOn = false;
            document.getElementById('flash-icon').className   = 'ph-bold ph-lightning text-lg';
            document.getElementById('flash-label').textContent = 'Flash';

            try {
                if (html5QrCode.isScanning) await html5QrCode.stop();

                // Cycle ke index berikutnya
                activeCamIndex = (activeCamIndex + 1) % cameraDevices.length;

                const config = {
                    fps: 12,
                    qrbox: (width, height) => {
                        const minDimension = Math.min(width, height);
                        const size = minDimension * 0.85;
                        return { width: size, height: size };
                    }
                };

                await html5QrCode.start(
                    cameraDevices[activeCamIndex].id,
                    config,
                    onScanSuccess,
                    onScanFailure
                );

                const label = cameraDevices[activeCamIndex].label || `Kamera ${activeCamIndex + 1}`;
                showToast(`Beralih ke: ${label}`);

                // Setup ulang tombol flash untuk kamera baru
                setTimeout(_setupFlashButton, 600);

            } catch (err) {
                console.error("Gagal switch kamera:", err);
                showToast("Gagal beralih kamera.", true);
            }
        }

        // Toggle flash/torch on-off
        window.toggleFlash = async () => {
            try {
                const videoEl = document.querySelector('#qr-reader video');
                if (!videoEl) return;
                const track = videoEl.srcObject && videoEl.srcObject.getVideoTracks()[0];
                if (!track) return;

                torchOn = !torchOn;
                await track.applyConstraints({ advanced: [{ torch: torchOn }] });

                // Update ikon & label tombol
                const icon  = document.getElementById('flash-icon');
                const label = document.getElementById('flash-label');
                if (torchOn) {
                    icon.className   = 'ph-fill ph-lightning text-lg text-yellow-500';
                    label.textContent = 'Flash On';
                } else {
                    icon.className   = 'ph-bold ph-lightning text-lg';
                    label.textContent = 'Flash';
                }
            } catch (e) {
                console.error("Gagal toggle flash:", e);
                showToast("Flash tidak dapat diaktifkan di perangkat ini.", true);
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            stopScanner();
            // Ekstrak parameter 'scan=' dari format URL (https://domain.com/?scan=LZT-1234)
            try {
                const url = new URL(decodedText);
                const batchParam = url.searchParams.get("scan");
                if (batchParam) {
                    processBatchCode(batchParam);
                } else {
                    showToast("QR Code tidak mengandung nomor batch yang terdaftar.", true);
                }
            } catch(e) {
                // Jika isinya bukan URL melainkan langsung kode batch mentah
                processBatchCode(decodedText);
            }
        }
        
        function onScanFailure(error) { /* Mengabaikan scanning noise log */ }

        // Beep pendek via Web Audio API — dipanggil saat scan QR valid
        function playSuccessBeep() {
            try {
                const ctx  = new (window.AudioContext || window.webkitAudioContext)();
                const osc  = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(920, ctx.currentTime);
                // Envelope: fade in 8ms → tahan penuh → fade out 60ms terakhir
                gain.gain.setValueAtTime(0, ctx.currentTime);
                gain.gain.linearRampToValueAtTime(0.35, ctx.currentTime + 0.008);
                gain.gain.setValueAtTime(0.35, ctx.currentTime + 0.19);
                gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.25);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.25);
            } catch (e) { /* Autoplay policy browser — abaikan diam-diam */ }
        }

        // Kirim verifikasi scan ke backend MySQL
        async function processBatchCode(batch) {
            try {
                const res = await fetch(`index.php?action=scan_batch&batch_code=${batch}`);
                const data = await res.json();
                
                if (data.status === 'success') {
                    const p = data.data;
                    playSuccessBeep();

                    document.getElementById('res-name').innerText = p.name;
                    document.getElementById('res-price').innerText = formatRupiah(p.price);
                    document.getElementById('res-batch').innerText = p.batch_code;
                    document.getElementById('res-prod').innerText = p.prod_date;
                    document.getElementById('res-exp').innerText = p.exp_date;

                    // Hitung status kedaluwarsa (konsisten dengan threshold H-30 di admin)
                    const now   = new Date();
                    const in30  = new Date(); in30.setDate(now.getDate() + 30);
                    const exp   = new Date(p.exp_date);
                    const diffMs   = exp - now;
                    const diffDays = Math.ceil(diffMs / (1000 * 60 * 60 * 24));

                    let statusBadge;
                    let priceColor;
                    if (exp < now) {
                        statusBadge = `<span class="text-red-600">Sudah Kedaluwarsa — Jangan Dikonsumsi</span>`;
                        priceColor = 'text-red-600';
                    } else if (exp <= in30) {
                        statusBadge = `<span class="text-amber-500">Segera Kedaluwarsa — ${diffDays} hari lagi</span>`;
                        priceColor = 'text-amber-500';
                    } else {
                        statusBadge = `<span class="text-emerald-600">Aman Dikonsumsi</span>`;
                        priceColor = 'text-emerald-600';
                    }
                    document.getElementById('res-status-badge').innerHTML = statusBadge;
                    document.getElementById('res-price').className = `font-bold text-sm ${priceColor}`;
                    
                    document.getElementById('modal-qr').classList.remove('hidden');
                    setTimeout(() => {
                        document.getElementById('modal-qr-content').classList.remove('scale-95', 'opacity-0');
                    }, 50);
                } else {
                    showToast(data.message, true);
                }
            } catch (e) { 
                showToast("Validasi batch ke database MySQL lokal gagal terputus.", true); 
            }
        }

        window.closeQRModal = () => {
            document.getElementById('modal-qr-content').classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                document.getElementById('modal-qr').classList.add('hidden');
            }, 300);
        }

        // ==========================================
        // SCROLL REVEAL — Intersection Observer
        // Animasi ulang setiap kali elemen masuk/keluar viewport
        // ==========================================
        let _scrollRevealObserver = null; // [UI-1] simpan referensi untuk cleanup

        function initScrollReveal() {
            _scrollRevealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    } else {
                        entry.target.classList.remove('visible');
                    }
                });
            }, { threshold: 0.15 });

            document.querySelectorAll('.reveal-left, .reveal-right').forEach(el => _scrollRevealObserver.observe(el));
        }

        // Terapkan reveal-card + stagger ke product card yang di-render via JS
        let cardObserver = null;

        function applyCardReveal() {
            // [UI-1] Disconnect observer lama sebelum bikin yang baru
            if (cardObserver) cardObserver.disconnect();

            const cards = document.querySelectorAll('#public-product-list > div:not(.col-span-full)');
            cards.forEach((card, i) => {
                card.classList.add('reveal-card');
                card.classList.remove('visible');
                card.style.transitionDelay = (i * 65) + 'ms';
            });

            cardObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    } else {
                        entry.target.classList.remove('visible');
                    }
                });
            }, { threshold: 0.1 });

            cards.forEach(card => cardObserver.observe(card));
        }

        // Hero entry animation
        function initHeroAnimation() {
            const heroLeft  = document.querySelector('.hero-enter');
            const heroRight = document.querySelector('.hero-enter-right');
            if (!heroLeft && !heroRight) return;
            if (heroLeft)  setTimeout(() => heroLeft.classList.add('visible'), 150);
            if (heroRight) setTimeout(() => heroRight.classList.add('visible'), 350);
        }

        // ==========================================
        // INITIAL LOAD SETUP
        // ==========================================
        document.addEventListener('DOMContentLoaded', () => {
            fetchProducts();
            initScrollReveal();
            initHeroAnimation();
            
            // Periksa fitur Auto-Scan lewat URL link (ketika user langsung memindai QR bawaan smartphone kamera)
            // [SEC-7] Gunakan json_encode untuk escaping konteks JS yang aman
            const autoScanBatch = <?php echo json_encode($auto_scan_batch, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
            if (autoScanBatch !== '') {
                processBatchCode(autoScanBatch);
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });

        // [UI-1] Cleanup semua observer saat halaman di-unload untuk mencegah memory leak
        window.addEventListener('beforeunload', () => {
            if (cardObserver)          cardObserver.disconnect();
            if (_scrollRevealObserver) _scrollRevealObserver.disconnect();
        });

        // Toggle Modal Admin Panel — dihapus, admin sekarang halaman tersendiri (pages/admin.php)
