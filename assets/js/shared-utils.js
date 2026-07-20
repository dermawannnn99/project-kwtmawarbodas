/**
 * shared-utils.js
 * Fungsi utilitas bersama yang dipakai oleh index.php (main.js) dan admin.php (render.js).
 * PENTING: Jika ada perubahan di sini, pastikan sinkron di kedua halaman.
 */

/**
 * [SEC-6] Escape karakter HTML berbahaya untuk mencegah XSS.
 * Wajib dipakai sebelum memasukkan data dari server ke innerHTML / template literal.
 * @param {*} str
 * @returns {string}
 */
function escHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

/**
 * [QUAL-1] Format angka ke format mata uang Rupiah.
 * @param {number} num
 * @returns {string}
 */
function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(num);
}
