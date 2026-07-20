// ==========================================
// QR GENERATOR
// ==========================================
let currentQRBatch = '';

window.showQR = (batchCode) => {
    currentQRBatch = batchCode;
    document.getElementById('modal-generate-qr').classList.remove('hidden');
    document.getElementById('qr-batch-text').innerText = 'Kode Batch: ' + batchCode;
    const targetUrl = `${window.location.origin}${window.location.pathname.replace('pages/admin.php', '')}index.php?scan=${batchCode}`;
    const img = document.getElementById('qr-generated-img');
    img.src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(targetUrl)}`;
};

// DOWNLOAD QR — gabungkan QR image + teks batch ke Canvas lalu download PNG
window.downloadQR = () => {
    const srcImg    = document.getElementById('qr-generated-img');
    const canvas    = document.getElementById('qr-download-canvas');
    const batchCode = currentQRBatch;

    // Ukuran canvas: 300×340 (QR 250×250 + padding + teks bawah)
    const QR_SIZE   = 250;
    const PADDING   = 25;
    const TEXT_H    = 50;
    const W         = QR_SIZE + PADDING * 2;
    const H         = QR_SIZE + PADDING * 2 + TEXT_H;

    canvas.width  = W;
    canvas.height = H;
    const ctx = canvas.getContext('2d');

    const draw = (imgEl) => {
        // Background putih
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, W, H);

        // Gambar QR di tengah
        ctx.drawImage(imgEl, PADDING, PADDING, QR_SIZE, QR_SIZE);

        // Garis tipis pemisah antara QR dan teks
        ctx.strokeStyle = '#e5e7eb';
        ctx.lineWidth   = 1;
        ctx.beginPath();
        ctx.moveTo(PADDING, PADDING + QR_SIZE + 12);
        ctx.lineTo(W - PADDING, PADDING + QR_SIZE + 12);
        ctx.stroke();

        // Teks kode batch di bawah
        ctx.fillStyle   = '#111827';
        ctx.font        = 'bold 18px Satoshi, Inter, sans-serif';
        ctx.textAlign   = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(batchCode, W / 2, PADDING + QR_SIZE + 12 + (TEXT_H / 2) + 2);

        // Trigger download
        const link     = document.createElement('a');
        link.download  = `qr-${batchCode}.png`;
        link.href      = canvas.toDataURL('image/png');
        link.click();
    };

    // Pastikan gambar sudah ter-load dan tidak kena CORS block
    if (srcImg.complete && srcImg.naturalWidth > 0) {
        try {
            draw(srcImg);
        } catch (e) {
            // Fallback: load ulang dengan crossOrigin anonymous via Image baru
            loadAndDraw(srcImg.src, draw, batchCode);
        }
    } else {
        loadAndDraw(srcImg.src, draw, batchCode);
    }
};

function loadAndDraw(src, drawFn, batchCode) {
    const img   = new Image();
    img.crossOrigin = 'anonymous';
    img.onload  = () => drawFn(img);
    img.onerror = () => {
        showNotif('qr', true);
    };
    // Tambahkan cache-bust kecil agar browser tidak pakai cache yang non-CORS
    img.src = src + (src.includes('?') ? '&' : '?') + '_cb=' + Date.now();
}
