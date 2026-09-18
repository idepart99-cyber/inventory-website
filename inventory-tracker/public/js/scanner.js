let scannerStream = null;

function openBarcodeScannerModal() {
    document.getElementById('scannerModal').classList.remove('hidden');
    document.getElementById('manualScannerInput').focus();
}

function closeBarcodeScannerModal() {
    document.getElementById('scannerModal').classList.add('hidden');
    stopCameraScanner();
}

async function startCameraScanner() {
    const video = document.getElementById('scannerVideo');
    const placeholder = document.getElementById('scannerPlaceholder');

    try {
        scannerStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' }
        });
        video.srcObject = scannerStream;
        video.classList.remove('hidden');
        placeholder.classList.add('hidden');
        video.play();
    } catch (err) {
        alert('Could not access device camera: ' + err.message + '. Please use the manual input or USB barcode gun below.');
    }
}

function stopCameraScanner() {
    if (scannerStream) {
        scannerStream.getTracks().forEach(track => track.stop());
        scannerStream = null;
    }
    const video = document.getElementById('scannerVideo');
    const placeholder = document.getElementById('scannerPlaceholder');
    if (video) video.classList.add('hidden');
    if (placeholder) placeholder.classList.remove('hidden');
}

async function lookupBarcode(code) {
    if (!code || !code.trim()) return;
    code = code.trim();

    const box = document.getElementById('scannerResultBox');
    const nameEl = document.getElementById('scanResultName');
    const skuEl = document.getElementById('scanResultSku');
    const stockEl = document.getElementById('scanResultStock');
    const viewLink = document.getElementById('scanResultViewLink');
    const inLink = document.getElementById('scanResultStockInLink');

    try {
        const res = await fetch('/api/barcode/' + encodeURIComponent(code));
        const data = await res.json();

        if (data.success && data.product) {
            const p = data.product;
            nameEl.textContent = p.name;
            skuEl.textContent = p.sku;
            stockEl.innerHTML = 'Total Stock: <strong class="text-slate-900 font-mono">' + p.total_stock + ' ' + p.unit + '</strong> | Selling: $' + parseFloat(p.selling_price).toFixed(2);
            viewLink.href = '/products/' + p.id;
            inLink.href = '/stock/in?product_id=' + p.id;
            box.classList.remove('hidden');
        } else {
            alert('Product with Barcode / SKU "' + code + '" not found in system.');
        }
    } catch (e) {
        alert('Barcode search error: ' + e.message);
    }
}
