        <!-- ================================================ -->
        <!-- SECTION: KELOLA PRODUK (form tambah/edit, full width) -->
        <!-- ================================================ -->
        <div id="section-tambah" class="section-content">
            <div class="mb-6 flex items-center justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="text-xl font-black text-gray-900" id="form-page-title">Tambah Produk Baru</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Isi semua field lalu klik Simpan.</p>
                </div>
                <button onclick="resetForm()"
                    class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-semibold text-sm transition-all">
                    <i class="ph-bold ph-arrow-counter-clockwise"></i> Reset Form
                </button>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gray-900 px-5 py-4 flex items-center gap-2">
                    <i class="ph-bold ph-plus-circle text-brand text-base" id="form-icon"></i>
                    <h2 class="font-bold text-white text-sm" id="form-title">Tambah Produk Baru</h2>
                </div>

                <form id="product-form" class="p-6" enctype="multipart/form-data">
                    <input type="hidden" id="input-id">
                    <input type="hidden" id="input-existing-image" name="existing_image">

                    <!-- Grid 2 kolom: kiri (Grup 1+2) | kanan (Grup 3) -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6 mb-6">

                        <!-- KOLOM KIRI: Grup 1 + Grup 2 -->
                        <div class="space-y-6">

                            <!-- GRUP 1: Informasi Dasar -->
                            <div>
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="w-5 h-5 rounded-full bg-brand/20 flex items-center justify-center shrink-0 text-brand font-black text-xs">1</span>
                                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Informasi Dasar</h3>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Produk <span class="text-red-400">*</span></label>
                                        <input type="text" id="input-name" required placeholder="Contoh: Rendang Daging Sapi"
                                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all text-sm bg-white">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Harga (IDR) <span class="text-red-400">*</span></label>
                                            <input type="number" id="input-price" required placeholder="65000"
                                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all text-sm bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kode Batch <span class="text-red-400">*</span></label>
                                            <input type="text" id="input-batch" required placeholder="LZT-XXXX"
                                                class="w-full px-3.5 py-2.5 border border-amber-200 rounded-xl bg-amber-50/60 font-bold text-brand focus:ring-2 focus:ring-brand/20 outline-none transition-all text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-dashed border-gray-100"></div>

                            <!-- GRUP 2: Detail Produksi -->
                            <div>
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="w-5 h-5 rounded-full bg-brand/20 flex items-center justify-center shrink-0 text-brand font-black text-xs">2</span>
                                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Detail Produksi</h3>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Produksi <span class="text-red-400">*</span></label>
                                        <input type="date" id="input-prod" required
                                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/20 outline-none transition-all text-sm bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Batas Expired <span class="text-red-400">*</span></label>
                                        <input type="date" id="input-exp" required
                                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/20 outline-none transition-all text-sm bg-white">
                                    </div>
                                </div>
                            </div>

                        </div><!-- /KOLOM KIRI -->

                        <!-- KOLOM KANAN: Grup 3 -->
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="w-5 h-5 rounded-full bg-brand/20 flex items-center justify-center shrink-0 text-brand font-black text-xs">3</span>
                                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Media & Deskripsi</h3>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Foto Produk <span class="text-red-400">*</span></label>
                                    <label for="input-image"
                                        class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-brand transition-all group overflow-hidden">
                                        <div id="upload-placeholder" class="text-center pointer-events-none">
                                            <i class="ph-bold ph-cloud-arrow-up text-3xl text-gray-400 group-hover:text-brand transition-colors mb-1 block"></i>
                                            <p class="text-xs font-semibold text-gray-500 group-hover:text-brand">Klik untuk pilih gambar</p>
                                            <p class="text-xs text-gray-400">JPG / PNG — maks. 5 MB</p>
                                        </div>
                                        <img id="image-preview" src="" alt="Preview" class="hidden h-full w-full object-cover pointer-events-none">
                                    </label>
                                    <input type="file" id="input-image" name="product_image" accept=".jpg,.jpeg,.png,image/jpeg,image/png" class="hidden">
                                    <p id="image-filename" class="text-xs text-gray-400 mt-1 truncate hidden"></p>
                                    <div id="edit-image-info" class="hidden mt-2 flex items-start gap-1.5 bg-blue-50 border border-blue-100 rounded-xl px-3 py-2">
                                        <i class="ph-bold ph-info text-blue-500 text-sm shrink-0 mt-0.5"></i>
                                        <p class="text-xs text-blue-700">Biarkan kosong untuk mempertahankan foto saat ini.</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi Ringkas <span class="text-red-400">*</span></label>
                                    <textarea id="input-desc" required rows="6" placeholder="Deskripsikan produk secara singkat..."
                                        class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand/20 outline-none transition-all resize-none text-sm bg-white"></textarea>
                                </div>
                            </div>
                        </div><!-- /KOLOM KANAN -->

                    </div><!-- /grid 2 kolom -->

                    <!-- Action buttons — full width di bawah kedua kolom -->
                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <button type="submit"
                            class="flex-1 bg-brand hover:bg-brand-dark text-white py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2 text-sm">
                            <i class="ph-bold ph-floppy-disk"></i> Simpan Data
                        </button>
                        <button type="button" onclick="resetForm()"
                            class="px-5 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-bold transition-all text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
