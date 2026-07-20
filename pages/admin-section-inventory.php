        <!-- ================================================ -->
        <!-- SECTION: DATA INVENTORY (full width, detail) -->
        <!-- ================================================ -->
        <div id="section-inventory" class="section-content">
            <div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="text-xl font-black text-gray-900">Data Inventory</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Semua produk beserta status tampil di katalog.</p>
                </div>
                <button onclick="showSection('tambah'); resetForm();"
                    class="flex items-center gap-2 bg-brand hover:bg-brand-dark text-white px-4 py-2 rounded-xl font-bold text-sm transition-all shadow-sm shadow-brand/20">
                    <i class="ph-bold ph-plus"></i> Tambah Produk
                </button>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <!-- Search bar -->
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3 flex-wrap">
                    <div class="relative flex-1 min-w-52">
                        <i class="ph-bold ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" id="inventory-search" placeholder="Cari nama produk atau kode batch..."
                            class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all bg-gray-50"
                            oninput="filterInventory()">
                    </div>
                    <span id="product-count" class="text-xs bg-brand/10 text-brand font-bold px-3 py-1.5 rounded-full shrink-0"></span>
                </div>

                <!-- Inventory cards (full detail per produk) -->
                <div id="inventory-card-list" class="divide-y divide-gray-200">
                    <div class="text-center py-16 text-gray-400">
                        <i class="ph-bold ph-spinner animate-spin text-3xl text-brand mb-2 block mx-auto"></i>
                        Memuat data...
                    </div>
                </div>

                <!-- Empty state -->
                <div id="inventory-empty" class="hidden text-center py-16 px-6">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="ph-bold ph-package text-3xl text-gray-400"></i>
                    </div>
                    <p class="font-bold text-gray-600">Belum ada produk</p>
                    <p class="text-sm text-gray-400 mt-1">Tambahkan produk lewat menu Kelola Produk.</p>
                </div>
            </div>
        </div>
