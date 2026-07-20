<!-- Modal Konfirmasi Hapus -->
<div id="modal-confirm-delete" class="fixed inset-0 bg-black/70 z-[130] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center">
        <div class="bg-red-100 w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="ph-bold ph-trash text-red-600 text-2xl"></i>
        </div>
        <h3 class="font-black text-xl text-gray-900 mb-2">Hapus Produk?</h3>
        <p class="text-gray-500 text-sm mb-6">Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 rounded-xl font-bold transition-all text-sm">Batal</button>
            <button id="btn-confirm-delete" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-xl font-bold transition-all text-sm">Hapus</button>
        </div>
    </div>
</div>
