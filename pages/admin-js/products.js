// ==========================================
// [SEC-3] Helper: ambil CSRF token dari meta tag
// ==========================================
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

// Helper: tambahkan CSRF token ke FormData untuk request POST mutasi
function appendCsrf(formData) {
    formData.append('csrf_token', getCsrfToken());
    return formData;
}

// ==========================================
// CRUD AJAX
// ==========================================
let productsCache = [];

async function fetchProducts() {
    try {
        // GET — tidak perlu CSRF token (read-only)
        const res  = await fetch('admin-api.php?action=get_products');
        const data = await res.json();
        if (data.status === 'success') {
            productsCache = data.data;
            updateStats();
            renderOverviewTable();
            renderInventoryCards();
        } else {
            showNotif('db', true, data.message);
        }
    } catch (e) {
        showNotif('db', true, 'Tidak dapat terhubung ke database.');
    }
}

document.getElementById('product-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fileInput = document.getElementById('input-image');
    const isEdit    = document.getElementById('input-id').value !== '';
    if (!isEdit && (!fileInput.files || fileInput.files.length === 0)) {
        showNotif('create', true, 'Foto produk wajib diisi untuk produk baru.'); return;
    }
    const formData = new FormData();
    formData.append('id',             document.getElementById('input-id').value);
    formData.append('name',           document.getElementById('input-name').value);
    formData.append('price',          document.getElementById('input-price').value);
    formData.append('badge',          '');
    formData.append('description',    document.getElementById('input-desc').value);
    formData.append('batch_code',     document.getElementById('input-batch').value);
    formData.append('prod_date',      document.getElementById('input-prod').value);
    formData.append('exp_date',       document.getElementById('input-exp').value);
    formData.append('existing_image', document.getElementById('input-existing-image').value);
    if (fileInput.files && fileInput.files.length > 0) formData.append('product_image', fileInput.files[0]);

    // [SEC-3] Sertakan CSRF token
    appendCsrf(formData);

    try {
        const res  = await fetch('admin-api.php?action=save_product', { method: 'POST', body: formData });
        const json = await res.json();
        if (json.status === 'success') {
            showNotif(isEdit ? 'edit' : 'create', false, json.message);
            resetForm();
            fetchProducts();
        } else {
            showNotif(isEdit ? 'edit' : 'create', true, json.message);
        }
    } catch (e) {
        showNotif(isEdit ? 'edit' : 'create', true, 'Gagal menyimpan data.');
    }
});

// DELETE
let pendingDeleteId = null;
window.confirmDelete  = (id) => { pendingDeleteId = id; document.getElementById('modal-confirm-delete').classList.remove('hidden'); };
window.closeDeleteModal = () => { pendingDeleteId = null; document.getElementById('modal-confirm-delete').classList.add('hidden'); };

document.getElementById('btn-confirm-delete').addEventListener('click', async () => {
    if (!pendingDeleteId) return;

    const fd = new FormData();
    fd.append('id', pendingDeleteId);
    // [SEC-3] Sertakan CSRF token
    appendCsrf(fd);

    try {
        const res  = await fetch('admin-api.php?action=delete_product', { method: 'POST', body: fd });
        const json = await res.json();
        // [BUG-4] Tampilkan notif dulu, lalu await fetchProducts, baru closeDeleteModal
        showNotif('delete', json.status !== 'success', json.message);
        await fetchProducts();
    } catch (e) {
        showNotif('delete', true, 'Gagal menghapus produk.');
    }
    closeDeleteModal();
});

// EDIT — navigasi ke form dan isi data
window.editProduct = (id) => {
    const p = productsCache.find(x => x.id == id);
    if (!p) return;
    document.getElementById('input-id').value             = p.id;
    document.getElementById('input-name').value           = p.name;
    document.getElementById('input-price').value          = p.price;
    document.getElementById('input-desc').value           = p.description;
    document.getElementById('input-batch').value          = p.batch_code;
    document.getElementById('input-prod').value           = p.prod_date;
    document.getElementById('input-exp').value            = p.exp_date;
    document.getElementById('input-existing-image').value = p.image_url;
    imagePreview.src = resolveImagePath(p.image_url);
    imagePreview.classList.remove('hidden');
    uploadPlaceholder.classList.add('hidden');
    imageFilename.textContent = 'Foto saat ini — pilih file baru untuk mengganti';
    imageFilename.classList.remove('hidden');
    document.getElementById('edit-image-info').classList.remove('hidden');
    document.getElementById('form-title').textContent      = 'Edit Informasi Produk';
    document.getElementById('form-icon').className         = 'ph-bold ph-pencil-simple text-brand text-base';
    document.getElementById('form-page-title').textContent = 'Edit Produk';
    showSection('tambah');
    setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 100);
};

// RESET FORM
function resetForm() {
    document.getElementById('product-form').reset();
    document.getElementById('input-id').value              = '';
    document.getElementById('input-existing-image').value  = '';
    document.getElementById('input-batch').value           = 'LZT-' + Math.floor(1000 + Math.random() * 9000);
    document.getElementById('form-title').textContent      = 'Tambah Produk Baru';
    document.getElementById('form-icon').className         = 'ph-bold ph-plus-circle text-brand text-base';
    document.getElementById('form-page-title').textContent = 'Tambah Produk Baru';
    imagePreview.src = '';
    imagePreview.classList.add('hidden');
    uploadPlaceholder.classList.remove('hidden');
    imageFilename.classList.add('hidden');
    imageFilename.textContent = '';
    document.getElementById('edit-image-info').classList.add('hidden');
    inputImage.value = '';
}

// TOGGLE VISIBILITY
window.toggleVisibility = async (id, newVal) => {
    const fd = new FormData();
    fd.append('id', id);
    fd.append('is_visible', newVal);
    // [SEC-3] Sertakan CSRF token
    appendCsrf(fd);

    try {
        const res  = await fetch('admin-api.php?action=toggle_visibility', { method: 'POST', body: fd });
        const json = await res.json();
        showNotif('toggle', json.status !== 'success', json.message);
        fetchProducts();
    } catch (e) {
        showNotif('toggle', true, 'Gagal mengubah status visibilitas.');
    }
};
