@extends('layouts.admin')
@section('title', 'Scan QR Pesanan')
@section('content')
<style>
.cashier-scan{max-width:760px;margin:0 auto}
.cashier-scan-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px}
.cashier-scan-head h1{margin:0}
.cashier-scan-card{background:#fff;border-radius:20px;padding:clamp(18px,4vw,30px);box-shadow:0 5px 24px #2b1d1410}
.cashier-video{width:100%;min-height:260px;aspect-ratio:4/3;object-fit:cover;border-radius:14px;background:#211710}
.cashier-scan-status{padding:12px 14px;border-radius:10px;background:var(--cream2);color:var(--text);margin:14px 0;font-size:.9rem}
.cashier-scan-status.error{background:#fbe4e1;color:#8a2a21}
.cashier-scan-status.ok{background:#e3f4e8;color:#1e5a37}
.cashier-manual{display:grid;grid-template-columns:1fr auto;gap:10px;align-items:end}
@media(max-width:520px){.cashier-manual{grid-template-columns:1fr}.cashier-scan-head{display:block}}
</style>
<div class="cashier-scan">
  <div class="cashier-scan-head">
    <div><p class="eyebrow">Arana Space · Kasir</p><h1 class="serif">Scan QR Pesanan</h1><p class="muted small">Arahkan kamera ke QR Code yang ditampilkan pelanggan.</p></div>
    <a class="btn light sm" href="{{ route('admin.orders') }}">Daftar Pesanan</a>
  </div>
  <div class="cashier-scan-card">
    <video id="cashier-qr-video" class="cashier-video" playsinline muted></video>
    <div id="cashier-qr-status" class="cashier-scan-status" role="status" aria-live="polite">Menyiapkan pemindai QR…</div>
    <div class="row">
      <button class="btn" type="button" id="cashier-start-scan">Mulai Scan</button>
      <button class="btn light" type="button" id="cashier-stop-scan" disabled>Hentikan Kamera</button>
    </div>
    <div style="border-top:1px solid #eee;margin:24px 0 18px"></div>
    <form id="cashier-manual-form" class="cashier-manual">
      <div><label for="cashier-order-number">Masukkan nomor pesanan manual</label><input id="cashier-order-number" name="order_number" placeholder="Contoh: BN261121ABCD" autocomplete="off" required></div>
      <button class="btn dark" type="submit">Cari Pesanan</button>
    </form>
  </div>
</div>
@endsection
@push('head')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" defer></script>
@endpush
@push('head')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const video = document.getElementById('cashier-qr-video');
  const statusBox = document.getElementById('cashier-qr-status');
  const startButton = document.getElementById('cashier-start-scan');
  const stopButton = document.getElementById('cashier-stop-scan');
  const manualForm = document.getElementById('cashier-manual-form');
  const numberInput = document.getElementById('cashier-order-number');
  let scanner = null;
  let busy = false;

  const setStatus = (message, type = '') => {
    statusBox.textContent = message;
    statusBox.className = 'cashier-scan-status ' + type;
  };

  const lookupOrder = async (orderNumber) => {
    if (busy) return;
    busy = true;
    setStatus('QR terbaca. Memeriksa pesanan…');
    try {
      const response = await fetch(@json(route('admin.orders.scan.lookup')), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': @json(csrf_token())
        },
        body: JSON.stringify({order_number: orderNumber.trim()})
      });
      const payload = await response.json();
      if (!response.ok || !payload.redirect_url) throw new Error(payload.message || 'Pesanan tidak ditemukan.');
      setStatus('Pesanan ditemukan. Membuka detail…', 'ok');
      window.location.assign(payload.redirect_url);
    } catch (error) {
      setStatus(error.message || 'Gagal membaca QR pesanan.', 'error');
      busy = false;
    }
  };

  const stopScanner = async () => {
    if (!scanner) return;
    try { await scanner.stop(); } catch (_) {}
    try { await scanner.clear(); } catch (_) {}
    scanner = null;
    stopButton.disabled = true;
    startButton.disabled = false;
  };

  startButton.addEventListener('click', async () => {
    if (typeof Html5Qrcode === 'undefined') {
      setStatus('Library pemindai belum termuat. Periksa koneksi internet lalu muat ulang halaman.', 'error');
      return;
    }
    try {
      scanner = new Html5Qrcode('cashier-qr-video');
      startButton.disabled = true;
      setStatus('Meminta izin kamera…');
      await scanner.start(
        {facingMode: 'environment'},
        {fps: 10, qrbox: {width: 240, height: 240}},
        async decodedText => {
          await stopScanner();
          let orderNumber = decodedText.trim();
          try {
            const parsed = new URL(orderNumber, window.location.origin);
            const match = parsed.pathname.match(/\/order\/([^/]+)/);
            if (match) orderNumber = decodeURIComponent(match[1]);
          } catch (_) {}
          if (!/^[a-zA-Z0-9_-]{3,40}$/.test(orderNumber)) {
            setStatus('QR bukan QR pesanan Arana Space. Gunakan QR dari halaman konfirmasi pesanan.', 'error');
            startButton.disabled = false;
            return;
          }
          await lookupOrder(orderNumber);
        },
        () => {}
      );
      stopButton.disabled = false;
      setStatus('Kamera aktif. Arahkan QR pesanan ke dalam kotak pemindai.');
    } catch (error) {
      scanner = null;
      startButton.disabled = false;
      stopButton.disabled = true;
      setStatus('Kamera tidak dapat dibuka. Izinkan akses kamera atau gunakan pencarian manual. Pastikan situs dibuka melalui HTTPS.', 'error');
    }
  });

  stopButton.addEventListener('click', async () => {
    await stopScanner();
    setStatus('Pemindaian dihentikan.');
  });

  manualForm.addEventListener('submit', async event => {
    event.preventDefault();
    await lookupOrder(numberInput.value);
  });

  window.addEventListener('pagehide', () => {
    if (scanner) scanner.stop().catch(() => {});
  });
});
</script>
@endpush
