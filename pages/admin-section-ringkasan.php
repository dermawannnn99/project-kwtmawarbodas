        <!-- ================================================ -->
        <!-- SECTION: RINGKASAN -->
        <!-- ================================================ -->
        <div id="section-overview" class="section-content active">
            <div class="mb-6">
                <h1 class="text-xl font-black text-gray-900">Ringkasan</h1>
                <p class="text-sm text-gray-500 mt-0.5">Kondisi produk KWT Mawar Bodas II saat ini.</p>
            </div>

            <!-- Stat cards — tanpa emoji, pakai Phosphor Icons -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Produk</p>
                    <p class="text-3xl font-black text-gray-900 leading-tight" id="stat-total">—</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Segera Expired</p>
                    <p class="text-3xl font-black text-gray-900 leading-tight" id="stat-expiring">—</p>
                    <p class="text-xs text-gray-400">dalam 30 hari</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Ditambahkan Bulan Ini</p>
                    <p class="text-3xl font-black text-gray-900 leading-tight" id="stat-thismonth">—</p>
                </div>
            </div>

            <!-- Tabel ringkasan — dengan kolom visibilitas -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                        <i class="ph-bold ph-rows text-brand"></i> Daftar Produk
                    </h2>
                    <button onclick="showSection('inventory')" class="text-xs font-semibold text-brand hover:underline">
                        Lihat semua →
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Produk</th>
                                <th class="text-left px-3 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider hidden sm:table-cell">Harga</th>
                                <th class="text-left px-3 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider hidden md:table-cell">Expired</th>
                                <th class="text-left px-3 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Exp. Status</th>
                                <th class="text-left px-3 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Katalog</th>
                            </tr>
                        </thead>
                        <tbody id="overview-product-table" class="divide-y divide-gray-50">
                            <tr><td colspan="5" class="text-center py-10 text-gray-400">
                                <i class="ph-bold ph-spinner animate-spin text-2xl block mx-auto mb-2 text-brand"></i>Memuat...
                            </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
