// [QUAL-1] formatRupiah dan escHtml sudah dipindah ke assets/js/shared-utils.js
// File ini menggunakan kedua fungsi tersebut langsung (dimuat via <script> sebelum render.js)

// Prefix ../ untuk path lokal (uploads/...) karena admin.php ada di folder pages/
// URL eksternal (http/https) dibiarkan apa adanya
function resolveImagePath(url) {
    if (!url) return '';
    if (url.startsWith('http://') || url.startsWith('https://')) return url;
    return '../' + url;
}

// ==========================================
// STATISTIK
// ==========================================
function updateStats() {
    const now  = new Date();
    const in30 = new Date(); in30.setDate(now.getDate() + 30);

    document.getElementById('stat-total').textContent    = productsCache.length;
    document.getElementById('product-count').textContent = productsCache.length + ' Produk';

    document.getElementById('stat-expiring').textContent = productsCache.filter(p => {
        const exp = new Date(p.exp_date);
        return exp >= now && exp <= in30;
    }).length;

    document.getElementById('stat-thismonth').textContent = productsCache.filter(p => {
        if (!p.created_at) return false;
        const d = new Date(p.created_at);
        return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear();
    }).length;
}

// ==========================================
// RENDER TABEL OVERVIEW
// ==========================================
function renderOverviewTable() {
    const tbody = document.getElementById('overview-product-table');
    if (productsCache.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-10 text-gray-400">Belum ada produk.</td></tr>';
        return;
    }
    const now  = new Date();
    const in30 = new Date(); in30.setDate(now.getDate() + 30);

    tbody.innerHTML = productsCache.map(p => {
        const exp        = new Date(p.exp_date);
        const isExpired  = exp < now;
        const isExpiring = !isExpired && exp <= in30;

        let expBadge;
        if (isExpired)       expBadge = '<span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full"><i class="ph-bold ph-x-circle"></i> Expired</span>';
        else if (isExpiring) expBadge = '<span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full"><i class="ph-bold ph-clock-countdown"></i> Segera Exp</span>';
        else                 expBadge = '<span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full"><i class="ph-bold ph-check-circle"></i> Aman</span>';

        const visible  = parseInt(p.is_visible ?? 1);
        const visBadge = visible
            ? '<span class="inline-flex items-center bg-white border border-gray-300 text-gray-700 text-xs font-bold px-2 py-0.5 rounded-full">Tampil</span>'
            : '<span class="inline-flex items-center bg-gray-200 border border-gray-300 text-gray-500 text-xs font-bold px-2 py-0.5 rounded-full">Sembunyi</span>';

        // [SEC-6] Semua data dari server di-escape sebelum masuk innerHTML
        return `<tr class="hover:bg-gray-50 transition-colors">
            <td class="px-5 py-3">
                <div class="flex items-center gap-3">
                    <img src="${escHtml(resolveImagePath(p.image_url))}" class="w-10 h-10 rounded-xl object-cover border border-gray-100 shrink-0"
                        onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100'">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate max-w-[180px]">${escHtml(p.name)}</p>
                        <p class="text-xs text-gray-400">${escHtml(p.batch_code)}</p>
                    </div>
                </div>
            </td>
            <td class="px-3 py-3 hidden sm:table-cell text-sm text-gray-700 font-medium whitespace-nowrap">${formatRupiah(p.price)}</td>
            <td class="px-3 py-3 hidden md:table-cell text-sm text-gray-500 whitespace-nowrap">${escHtml(p.exp_date)}</td>
            <td class="px-3 py-3">${expBadge}</td>
            <td class="px-3 py-3">${visBadge}</td>
        </tr>`;
    }).join('');
}

// ==========================================
// RENDER INVENTORY CARDS
// ==========================================
let filteredCache = [];

function renderInventoryCards() {
    filteredCache = [...productsCache];
    renderFilteredCards();
}

function filterInventory() {
    const q = document.getElementById('inventory-search').value.toLowerCase();
    filteredCache = productsCache.filter(p =>
        p.name.toLowerCase().includes(q) || p.batch_code.toLowerCase().includes(q)
    );
    renderFilteredCards();
}

function renderFilteredCards() {
    const container = document.getElementById('inventory-card-list');
    const emptyEl   = document.getElementById('inventory-empty');
    const now       = new Date();
    const in30      = new Date(); in30.setDate(now.getDate() + 30);

    if (filteredCache.length === 0) {
        container.innerHTML = '';
        emptyEl.classList.remove('hidden');
        return;
    }
    emptyEl.classList.add('hidden');

    container.innerHTML = filteredCache.map(p => {
        const exp        = new Date(p.exp_date);
        const isExpired  = exp < now;
        const isExpiring = !isExpired && exp <= in30;

        let expBadge = '';
        if (isExpired)       expBadge = '<span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-full"><i class="ph-bold ph-x-circle"></i> Expired</span>';
        else if (isExpiring) expBadge = '<span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full"><i class="ph-bold ph-clock-countdown"></i> Segera Exp</span>';

        const visible = parseInt(p.is_visible ?? 1);
        const rowBg   = isExpired ? 'bg-red-50/30' : (isExpiring ? 'bg-amber-50/30' : '');

        // [UI-2] Fallback deskripsi kosong dengan placeholder italic
        const descText = p.description
            ? escHtml(p.description)
            : '<em class="text-gray-300">Tidak ada deskripsi</em>';

        // [SEC-6] Semua data dari server di-escape
        return `
        <div class="${rowBg} px-5 py-4 hover:bg-gray-50 transition-colors border-b border-gray-200 last:border-b-0">
            <div class="flex gap-4">
                <div class="shrink-0">
                    <img src="${escHtml(resolveImagePath(p.image_url))}" class="w-16 h-16 rounded-xl object-cover border border-gray-100"
                        onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=200'">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3 flex-wrap">
                        <div class="min-w-0">
                            <p class="font-bold text-gray-900 truncate">${escHtml(p.name)}</p>
                            <p class="text-xs text-brand font-bold mt-0.5">${escHtml(p.batch_code)}</p>
                        </div>
                        <p class="font-black text-gray-900 text-base shrink-0 whitespace-nowrap">${formatRupiah(p.price)}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-gray-500">
                        <span><i class="ph-bold ph-calendar-check inline mr-0.5"></i> Produksi: ${escHtml(p.prod_date)}</span>
                        <span class="flex items-center gap-1.5"><i class="ph-bold ph-calendar-x inline mr-0.5"></i> Expired: ${escHtml(p.exp_date)} ${expBadge}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1.5 line-clamp-1">${descText}</p>
                </div>
            </div>

            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between gap-3 flex-wrap">
                <select onchange="toggleVisibility(${p.id}, this.value)"
                    class="text-xs font-semibold border rounded-xl px-2.5 py-1.5 outline-none cursor-pointer transition-all bg-white border-gray-200 text-gray-700">
                    <option value="1" ${visible ? 'selected' : ''}>Tampil di Katalog</option>
                    <option value="0" ${!visible ? 'selected' : ''}>Sembunyi dari Katalog</option>
                </select>
                <div class="flex gap-2 flex-wrap">
                    <button onclick="showQR('${escHtml(p.batch_code)}')"
                        class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-xl transition-colors text-xs font-semibold">
                        <i class="ph-bold ph-qr-code text-sm"></i> Cetak QR
                    </button>
                    <button onclick="editProduct(${p.id})"
                        class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-xl transition-colors text-xs font-semibold">
                        <i class="ph-bold ph-pencil-simple text-sm"></i> Edit
                    </button>
                    <button onclick="confirmDelete(${p.id})"
                        class="flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-xl transition-colors text-xs font-semibold">
                        <i class="ph-bold ph-trash text-sm"></i> Hapus
                    </button>
                </div>
            </div>
        </div>`;
    }).join('');
}
