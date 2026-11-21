const STATUS = {
  pending: 'Pesanan masuk',
  confirmed: 'Dikonfirmasi',
  preparing: 'Sedang dibuat',
  ready: 'Siap disajikan',
  completed: 'Selesai',
  cancelled: 'Dibatalkan'
};
const rupiah = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(n || 0));
const safe = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const dateText = s => s ? new Date(s).toLocaleString('id-ID', { dateStyle:'medium', timeStyle:'short' }) : '-';
let liveChannel = null;
let trackTimer = null;

function styles() {
  if (document.querySelector('#arana-ops-styles')) return;
  const style = document.createElement('style');
  style.id = 'arana-ops-styles';
  style.textContent = `
    .ops-wrap{margin-top:26px}.ops-head{display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap}
    .ops-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px;margin-top:14px}
    .ops-card{background:#fff;border:1px solid #eadfd3;border-radius:16px;padding:16px;box-shadow:0 5px 18px #28170d0a}
    .ops-card h3{margin:0 0 5px}.ops-meta{font-size:.82rem;color:#78695e;display:flex;gap:10px;flex-wrap:wrap}
    .ops-items{margin:12px 0;padding:10px 0;border-top:1px solid #eee;border-bottom:1px solid #eee}
    .ops-item{display:flex;justify-content:space-between;gap:12px;padding:4px 0;font-size:.9rem}
    .ops-note{background:#fff8ed;border-radius:10px;padding:9px 11px;margin:10px 0;font-size:.88rem}
    .ops-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px}.ops-actions .btn{margin:0}
    .ops-pill{display:inline-block;padding:4px 9px;border-radius:30px;background:#f1e8df;font-size:.76rem;font-weight:700}
    .ops-muted{color:#81766e;font-size:.85rem}.ops-kpi{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;margin:12px 0}
    .ops-kpi>div{background:#fff8f0;border-radius:12px;padding:13px}.ops-kpi b{display:block;font-size:1.25rem;margin-top:4px}
    .ops-form{display:flex;gap:10px;flex-wrap:wrap;align-items:end}.ops-form .form-group{margin:5px 0;flex:1;min-width:150px}
    .ops-form input{max-width:100%}.ops-table{width:100%;border-collapse:collapse}.ops-table th,.ops-table td{padding:9px;border-bottom:1px solid #eee;text-align:left;font-size:.84rem;white-space:nowrap}
    .ops-table-wrap{overflow:auto}.ops-empty{padding:18px;background:#fff8f0;border-radius:12px;color:#75675d}
    .ops-danger{background:#fff0ed;border:1px solid #f1d0c8}.ops-success{color:#287347;font-weight:700}
    .track-steps{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin:20px 0}
    .track-step{border-top:4px solid #ddd2c7;padding:10px 4px 0;font-size:.8rem;color:#81766e}
    .track-step.done{border-color:#287347;color:#287347;font-weight:700}
    @media print{body.ops-printing *{visibility:hidden!important}body.ops-printing #ops-report,body.ops-printing #ops-report *{visibility:visible!important}body.ops-printing #ops-report{position:absolute;left:0;top:0;width:100%;margin:0;padding:12px;border:0;box-shadow:none}body.ops-printing #ops-report button,body.ops-printing #ops-report input{display:none!important}}
  `;
  document.head.append(style);
}

function playChime() {
  try {
    const Ctx = window.AudioContext || window.webkitAudioContext;
    if (!Ctx) return;
    const ctx = new Ctx();
    [660, 880].forEach((hz, i) => {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.frequency.value = hz;
      osc.type = 'sine';
      gain.gain.setValueAtTime(0.0001, ctx.currentTime + i * 0.16);
      gain.gain.exponentialRampToValueAtTime(0.12, ctx.currentTime + i * 0.16 + 0.02);
      gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + i * 0.16 + 0.18);
      osc.connect(gain); gain.connect(ctx.destination);
      osc.start(ctx.currentTime + i * 0.16);
      osc.stop(ctx.currentTime + i * 0.16 + 0.2);
    });
    setTimeout(() => ctx.close(), 700);
  } catch (err) { console.warn('Audio notification unavailable:', err); }
}

export function unmountAdminOperations(supabase) {
  if (liveChannel) { supabase.removeChannel(liveChannel); liveChannel = null; }
  if (trackTimer) { clearInterval(trackTimer); trackTimer = null; }
}

export function mountAdminOperations({ supabase, profile, toast }) {
  if (!profile || !['admin','cashier','kitchen'].includes(profile.role)) return;
  styles();
  unmountAdminOperations(supabase);
  const host = document.querySelector('#main .container');
  if (!host) return;

  // The legacy status selector allowed every staff role to choose every state.
  // Replace it with read-only status text; controlled actions are rendered below.
  document.querySelectorAll('select[data-status]').forEach(el => {
    const label = document.createElement('span');
    label.className = 'ops-pill';
    label.textContent = STATUS[el.value] || el.value;
    el.replaceWith(label);
  });

  const isKitchen = profile.role === 'kitchen';
  const canCashier = ['admin','cashier'].includes(profile.role);
  const section = document.createElement('section');
  section.id = 'ops-panel';
  section.className = 'ops-wrap';
  section.innerHTML = `
    <div class="panel">
      <div class="ops-head"><div><div class="eyebrow">— OPERASIONAL REAL-TIME —</div><h2 class="serif">Notifikasi Pesanan</h2><p class="ops-muted">Pesanan baru akan muncul otomatis. Aktifkan suara melalui tombol ini.</p></div>
      <button class="btn sm" id="ops-sound">Aktifkan Suara</button></div>
      <p id="ops-connection" class="ops-muted" role="status">Menghubungkan notifikasi…</p>
    </div>
    <div class="panel">
      <div class="ops-head"><div><div class="eyebrow">— ${isKitchen ? 'KITCHEN DISPLAY SYSTEM' : 'ORDER MONITOR'} —</div><h2 class="serif">${isKitchen ? 'Antrean Dapur' : 'Pesanan Terbaru'}</h2><p class="ops-muted">${isKitchen ? 'Setiap pesanan tampil dalam kartu. Saat menekan Siap Disajikan, status otomatis menjadi Selesai.' : 'Pantau pembayaran dan batalkan pesanan yang masih aktif.'}</p></div><button class="btn sm light" id="ops-refresh">Muat Ulang</button></div>
      <div id="ops-orders" class="ops-grid"><div class="ops-empty">Memuat pesanan…</div></div>
    </div>
    ${canCashier ? `
    <div class="panel">
      <div class="eyebrow">— SHIFT KASIR —</div><h2 class="serif">Buka & Tutup Shift</h2>
      <div id="ops-shift"><div class="ops-empty">Memuat shift…</div></div>
      <div class="ops-head" style="margin-top:18px"><h3>Laporan Shift</h3><button class="btn sm light" id="ops-shift-refresh">Perbarui</button></div>
      <div class="ops-table-wrap"><table class="ops-table"><thead><tr><th>Kasir</th><th>Dibuka</th><th>Modal awal</th><th>Penjualan tunai</th><th>Non-tunai</th><th>Uang seharusnya</th><th>Uang fisik</th><th>Selisih</th><th>Status</th></tr></thead><tbody id="ops-shift-rows"><tr><td colspan="9">Memuat…</td></tr></tbody></table></div>
    </div>` : ''}
    ${profile.role === 'admin' ? `
    <div class="panel" id="ops-report">
      <div class="eyebrow">— LAPORAN TRANSAKSI —</div><h2 class="serif">Laporan Penjualan</h2>
      <form id="ops-report-form" class="ops-form">
        <div class="form-group"><label>Dari tanggal</label><input type="date" name="from" required></div>
        <div class="form-group"><label>Sampai tanggal</label><input type="date" name="to" required></div>
        <button class="btn">Tampilkan</button><button type="button" class="btn light" id="ops-xlsx">Unduh Excel (.xlsx)</button><button type="button" class="btn light" id="ops-csv">Unduh CSV</button><button type="button" class="btn light" id="ops-pdf">Cetak / Simpan PDF</button>
      </form>
      <div id="ops-report-content" style="margin-top:14px"><div class="ops-empty">Pilih periode laporan.</div></div>
    </div>` : ''}
  `;
  host.append(section);

  let ordersCache = [];
  let soundOn = false;
  let reportRows = [];
  let currentShift = null;
  const notifiedKitchenOrders = new Set();
  const orderHost = section.querySelector('#ops-orders');

  async function loadOrders() {
    const { data, error } = await supabase.from('orders')
      .select('id,order_number,customer_name,customer_phone,order_type,cafe_table_id,order_status,payment_status,payment_method,subtotal,discount_total,grand_total,customer_note,created_at,ready_at,order_items(product_name,quantity,line_total,note),cafe_tables(table_number)')
      .order('created_at', { ascending: false }).limit(80);
    if (error) { orderHost.innerHTML = '<div class="ops-empty">Gagal memuat pesanan: ' + safe(error.message) + '</div>'; return; }
    ordersCache = data || [];
    if (isKitchen) ordersCache.filter(o => o.payment_status === 'paid' && ['pending','confirmed','preparing'].includes(o.order_status)).forEach(o => notifiedKitchenOrders.add(String(o.id)));
    const shown = isKitchen ? ordersCache.filter(o => o.payment_status === 'paid' && ['pending','confirmed','preparing'].includes(o.order_status)) : ordersCache;
    if (!shown.length) { orderHost.innerHTML = '<div class="ops-empty">Belum ada pesanan.</div>'; return; }
    orderHost.innerHTML = shown.map(o => {
      const items = (o.order_items || []).map(i => '<div class="ops-item"><span>' + Number(i.quantity) + '× ' + safe(i.product_name) + (i.note ? ' <small>('+safe(i.note)+')</small>' : '') + '</span><b>' + rupiah(i.line_total) + '</b></div>').join('');
      const table = o.cafe_tables?.table_number || (o.order_type === 'dine_in' ? 'Meja #' + o.cafe_table_id : 'Bawa pulang');
      const payment = o.payment_method ? o.payment_method.toUpperCase() + ' · ' + o.payment_status : 'Belum dipilih';
      let actions = '';
      if (isKitchen) {
        if (o.payment_status === 'paid' && (o.order_status === 'pending' || o.order_status === 'confirmed')) actions = '<button class="btn sm" data-kitchen-start="' + o.id + '">Mulai dibuat</button>';
        if (o.order_status === 'preparing') actions = '<button class="btn sm" data-kitchen-ready="' + o.id + '">Siap Disajikan</button>';
      } else {
        if (canCashier && o.payment_method === 'cash' && o.payment_status !== 'paid' && o.order_status !== 'cancelled') actions += '<button class="btn sm" data-cash-confirm="' + o.id + '">Konfirmasi Tunai</button>';
        if (canCashier && !['completed','cancelled'].includes(o.order_status)) actions += '<button class="btn sm danger" data-order-cancel="' + o.id + '">Batalkan</button>';
      }
      return '<article class="ops-card"><div class="ops-head"><h3>' + safe(o.order_number) + '</h3><span class="ops-pill">' + safe(STATUS[o.order_status] || o.order_status) + '</span></div><div class="ops-meta"><span>' + dateText(o.created_at) + '</span><span>' + safe(table) + '</span><span>' + safe(o.customer_name || 'Pelanggan') + '</span></div><div class="ops-meta" style="margin-top:6px"><span>Pembayaran: ' + safe(payment) + '</span><span>Diskon: ' + rupiah(o.discount_total) + '</span></div><div class="ops-items">' + items + '</div>' + (o.customer_note ? '<div class="ops-note"><b>Catatan:</b> ' + safe(o.customer_note) + '</div>' : '') + '<div class="ops-head"><b>' + rupiah(o.grand_total) + '</b>' + (o.order_type === 'dine_in' ? '<span class="ops-pill">Dine-in</span>' : '<span class="ops-pill">Takeaway</span>') + '</div><div class="ops-actions">' + actions + '</div></article>';
    }).join('');
  }

  async function loadShift() {
    const { data, error } = await supabase.from('cashier_shifts').select('*')
      .eq('cashier_id', profile.id).eq('status','open').order('opened_at',{ascending:false}).limit(1).maybeSingle();
    if (error) { section.querySelector('#ops-shift').innerHTML = '<div class="ops-empty">Gagal memuat shift: ' + safe(error.message) + '</div>'; return; }
    currentShift = data;
    const el = section.querySelector('#ops-shift');
    if (!data) {
      el.innerHTML = '<form id="ops-open-shift" class="ops-form"><div class="form-group"><label>Modal awal kas (Rp)</label><input name="opening_cash" type="number" min="0" step="1000" required placeholder="Contoh: 200000"></div><button class="btn">Buka Shift</button></form>';
    } else {
      el.innerHTML = '<div class="ops-kpi"><div>Shift dibuka<b>' + dateText(data.opened_at) + '</b></div><div>Modal awal<b>' + rupiah(data.opening_cash) + '</b></div><div>Status<b class="ops-success">Aktif</b></div></div><form id="ops-close-shift" class="ops-form"><div class="form-group"><label>Uang tunai fisik di laci (Rp)</label><input name="closing_cash" type="number" min="0" step="1000" required placeholder="Hitung uang fisik"></div><button class="btn">Tutup Shift</button></form>';
    }
  }

  async function loadShiftRows() {
    const { data, error } = await supabase.from('cashier_shifts').select('*').order('opened_at',{ascending:false}).limit(50);
    const body = section.querySelector('#ops-shift-rows');
    if (error) { body.innerHTML = '<tr><td colspan="9">' + safe(error.message) + '</td></tr>'; return; }
    const rows = data || [];
    let names = {};
    if (profile.role === 'admin' && rows.length) {
      const ids = [...new Set(rows.map(x=>x.cashier_id))];
      const p = await supabase.from('profiles').select('id,full_name').in('id',ids);
      (p.data || []).forEach(x=>names[x.id]=x.full_name || x.id.slice(0,8));
    }
    body.innerHTML = rows.map(x => '<tr><td>' + safe(names[x.cashier_id] || (x.cashier_id===profile.id ? (profile.full_name || 'Saya') : x.cashier_id.slice(0,8))) + '</td><td>' + dateText(x.opened_at) + '</td><td>' + rupiah(x.opening_cash) + '</td><td>' + rupiah(x.cash_sales) + '</td><td>' + rupiah(x.non_cash_sales) + '</td><td>' + rupiah(x.expected_cash) + '</td><td>' + rupiah(x.closing_cash) + '</td><td>' + rupiah(x.cash_difference) + '</td><td>' + safe(x.status) + '</td></tr>').join('') || '<tr><td colspan="9">Belum ada laporan shift.</td></tr>';
  }

  async function loadReport(from, to) {
    const area = section.querySelector('#ops-report-content');
    area.innerHTML = '<div class="ops-empty">Memuat laporan…</div>';
    const { data, error } = await supabase.from('orders').select('id,order_number,customer_name,customer_phone,order_type,order_status,payment_status,payment_method,subtotal,discount_total,grand_total,created_at,order_items(product_name,quantity,unit_price,line_total)')
      .gte('created_at', new Date(from + 'T00:00:00').toISOString()).lte('created_at', new Date(to + 'T23:59:59.999').toISOString())
      .order('created_at',{ascending:false});
    if (error) { area.innerHTML = '<div class="ops-empty">Gagal memuat laporan: ' + safe(error.message) + '</div>'; return; }
    reportRows = data || [];
    const paid = reportRows.filter(x=>x.payment_status==='paid' && x.order_status!=='cancelled');
    const revenue = paid.reduce((sum,x)=>sum+Number(x.grand_total||0),0);
    const discounts = paid.reduce((sum,x)=>sum+Number(x.discount_total||0),0);
    const cash = paid.filter(x=>x.payment_method==='cash').reduce((sum,x)=>sum+Number(x.grand_total||0),0);
    const qris = paid.filter(x=>x.payment_method==='qris').reduce((sum,x)=>sum+Number(x.grand_total||0),0);
    const sold = {};
    reportRows.filter(o=>o.payment_status==='paid'&&o.order_status!=='cancelled').forEach(o=>(o.order_items||[]).forEach(i=>{sold[i.product_name]=(sold[i.product_name]||0)+Number(i.quantity||0)}));
    const unpaid = reportRows.filter(x => x.payment_status !== 'paid' && x.order_status !== 'cancelled');
    const cancelled = reportRows.filter(x => x.order_status === 'cancelled');
    const itemText = o => (o.order_items || []).map(i => Number(i.quantity) + '× ' + safe(i.product_name)).join('<br>') || '-';
    area.innerHTML = `<div class="ops-kpi"><div>Transaksi lunas<b>${paid.length}</b></div><div>Belum lunas<b>${unpaid.length}</b></div><div>Dibatalkan<b>${cancelled.length}</b></div><div>Omzet dibayar<b>${rupiah(revenue)}</b></div><div>Tunai / QRIS<b>${rupiah(cash)} / ${rupiah(qris)}</b></div></div><h3>Rincian Transaksi</h3><div class="ops-table-wrap"><table class="ops-table"><thead><tr><th>Nomor</th><th>Waktu</th><th>Pelanggan</th><th>Kontak</th><th>Jenis</th><th>Item Pesanan</th><th>Pembayaran</th><th>Diskon</th><th>Total</th><th>Status</th></tr></thead><tbody>${reportRows.map(o=>'<tr><td>'+safe(o.order_number)+'</td><td>'+dateText(o.created_at)+'</td><td>'+safe(o.customer_name||'-')+'</td><td>'+safe(o.customer_phone||'-')+'</td><td>'+safe(o.order_type)+'</td><td>'+itemText(o)+'</td><td>'+safe((o.payment_method||'-')+' / '+o.payment_status)+'</td><td>'+rupiah(o.discount_total)+'</td><td>'+rupiah(o.grand_total)+'</td><td>'+safe(STATUS[o.order_status]||o.order_status)+'</td></tr>').join('')}</tbody></table></div><h3 style="margin-top:18px">Menu Terjual</h3><div class="ops-table-wrap"><table class="ops-table"><thead><tr><th>Menu</th><th>Jumlah</th></tr></thead><tbody>${Object.entries(sold).map(([name,qty])=>'<tr><td>'+safe(name)+'</td><td>'+qty+'</td></tr>').join('')||'<tr><td colspan="2">Belum ada menu terjual pada periode ini.</td></tr>'}</tbody></table></div>`;
  }

  section.addEventListener('click', async e => {
    const target = e.target.closest('button');
    if (!target) return;
    try {
      if (target.id === 'ops-sound') {
        soundOn = !soundOn;
        target.textContent = soundOn ? 'Suara Aktif ✓' : 'Aktifkan Suara';
        if (soundOn) playChime();
      } else if (target.id === 'ops-refresh') await loadOrders();
      else if (target.id === 'ops-shift-refresh') { await loadShiftRows(); await loadShift(); }
      else if (target.dataset.kitchenStart) {
        const {error} = await supabase.rpc('kitchen_update_order',{p_order_id:Number(target.dataset.kitchenStart),p_status:'preparing'});
        if (error) throw error;
        toast('Pesanan mulai dibuat.'); await loadOrders();
      } else if (target.dataset.kitchenReady) {
        const {error} = await supabase.rpc('kitchen_update_order',{p_order_id:Number(target.dataset.kitchenReady),p_status:'ready'});
        if (error) throw error;
        toast('Pesanan selesai dan siap diantar staf.'); await loadOrders();
      } else if (target.dataset.cashConfirm) {
        const {error} = await supabase.rpc('confirm_cash_payment',{p_order_id:Number(target.dataset.cashConfirm)});
        if (error) throw error;
        toast('Pembayaran tunai dikonfirmasi.'); await loadOrders();
      } else if (target.dataset.orderCancel) {
        if (!confirm('Batalkan pesanan ini?')) return;
        const {error} = await supabase.from('orders').update({order_status:'cancelled'}).eq('id',Number(target.dataset.orderCancel));
        if (error) throw error;
        toast('Pesanan dibatalkan.'); await loadOrders();
      } else if (target.id === 'ops-xlsx') {
        const summary = [['LAPORAN PENJUALAN ARANA SPACE'],['Periode', section.querySelector('[name="from"]').value, 's.d.', section.querySelector('[name="to"]').value],[],['Nomor','Waktu','Pelanggan','WhatsApp','Jenis','Item Pesanan','Metode','Status pembayaran','Subtotal','Diskon','Total','Status pesanan']];
        reportRows.forEach(o=>summary.push([o.order_number,o.created_at,o.customer_name||'',o.customer_phone||'',o.order_type,(o.order_items||[]).map(i=>Number(i.quantity)+'x '+i.product_name).join('; '),o.payment_method||'',o.payment_status,Number(o.subtotal||0),Number(o.discount_total||0),Number(o.grand_total||0),o.order_status]));
        const paidRows = reportRows.filter(o=>o.payment_status==='paid' && o.order_status!=='cancelled');
        const sold = {};
        paidRows.forEach(o=>(o.order_items||[]).forEach(i=>{sold[i.product_name]=(sold[i.product_name]||0)+Number(i.quantity||0)}));
        const menu = [['Menu','Jumlah terjual'],...Object.entries(sold)];
        const { default: XLSX } = await import(/* @vite-ignore */ 'https://cdn.sheetjs.com/xlsx-0.20.3/package/xlsx.mjs');
        const summarySheet = XLSX.utils.aoa_to_sheet(summary);
        const menuSheet = XLSX.utils.aoa_to_sheet(menu);
        summarySheet['!cols'] = [{wch:22},{wch:22},{wch:14},{wch:14},{wch:18},{wch:16},{wch:16},{wch:16},{wch:18}];
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, summarySheet, 'Transaksi');
        XLSX.utils.book_append_sheet(workbook, menuSheet, 'Menu Terjual');
        XLSX.writeFile(workbook, 'laporan-arana-space.xlsx');
      } else if (target.id === 'ops-csv') {
        const rows = [['Nomor','Waktu','Pelanggan','WhatsApp','Jenis','Item Pesanan','Metode','Status pembayaran','Subtotal','Diskon','Total','Status pesanan']];
        reportRows.forEach(o=>rows.push([o.order_number,o.created_at,o.customer_name||'',o.customer_phone||'',o.order_type,(o.order_items||[]).map(i=>Number(i.quantity)+'x '+i.product_name).join('; '),o.payment_method||'',o.payment_status,o.subtotal,o.discount_total,o.grand_total,o.order_status]));
        const csv = '\uFEFF' + rows.map(r=>r.map(v=>'"'+String(v??'').replace(/"/g,'""')+'"').join(',')).join('\r\n');
        const blob = new Blob([csv],{type:'text/csv;charset=utf-8;'});
        const url = URL.createObjectURL(blob); const a=document.createElement('a'); a.href=url; a.download='laporan-arana-space.csv'; a.click(); URL.revokeObjectURL(url);
      } else if (target.id === 'ops-pdf') {
        document.body.classList.add('ops-printing');
        window.print();
        setTimeout(()=>document.body.classList.remove('ops-printing'),800);
      }
    } catch (err) { toast(err.message || 'Operasi gagal.', true); }
  });

  section.addEventListener('submit', async e => {
    const form = e.target;
    if (form.id === 'ops-open-shift') {
      e.preventDefault();
      const amount = Number(new FormData(form).get('opening_cash'));
      try {
        const {error} = await supabase.rpc('open_cashier_shift',{p_opening_cash:amount});
        if (error) throw error;
        toast('Shift berhasil dibuka.'); await loadShift(); await loadShiftRows();
      } catch(err) { toast(err.message || 'Gagal membuka shift.',true); }
    }
    if (form.id === 'ops-close-shift') {
      e.preventDefault();
      const amount = Number(new FormData(form).get('closing_cash'));
      try {
        const {data,error} = await supabase.rpc('close_cashier_shift',{p_closing_cash:amount});
        if (error) throw error;
        toast('Shift ditutup. Selisih: '+rupiah(data.cash_difference));
        await loadShift(); await loadShiftRows();
      } catch(err) { toast(err.message || 'Gagal menutup shift.',true); }
    }
    if (form.id === 'ops-report-form') {
      e.preventDefault();
      const d = Object.fromEntries(new FormData(form));
      if (d.from > d.to) { toast('Tanggal awal tidak boleh melewati tanggal akhir.',true); return; }
      await loadReport(d.from,d.to);
    }
  });

  const now = new Date();
  const today = new Date(now.getTime()-now.getTimezoneOffset()*60000).toISOString().slice(0,10);
  const from = section.querySelector('[name="from"]');
  const to = section.querySelector('[name="to"]');
  if (from && to) { from.value=today; to.value=today; loadReport(today,today); }
  loadOrders();
  if (canCashier) { loadShift(); loadShiftRows(); }

  liveChannel = supabase.channel('arana-ops-' + profile.id)
     .on('postgres_changes',{event:'INSERT',schema:'public',table:'orders'},payload=>{
      if (isKitchen && payload.new?.payment_status !== 'paid') return;
      if (isKitchen) notifiedKitchenOrders.add(String(payload.new?.id));
      if (soundOn) playChime();
      toast('Pesanan baru masuk: ' + (payload.new?.order_number || ''));
      loadOrders();
    })
    .on('postgres_changes',{event:'UPDATE',schema:'public',table:'orders'},payload=>{
      const order = payload.new || {};
      if (isKitchen && order.payment_status === 'paid' && ['pending','confirmed'].includes(order.order_status) && !notifiedKitchenOrders.has(String(order.id))) {
        notifiedKitchenOrders.add(String(order.id));
        if (soundOn) playChime();
        toast('Pembayaran diterima. Pesanan masuk antrean dapur: ' + (order.order_number || ''));
      }
      loadOrders();
    })
    .subscribe(status=>{
      const el = section.querySelector('#ops-connection');
      if (el) el.textContent = status === 'SUBSCRIBED' ? '● Terhubung · Notifikasi real-time aktif' : 'Menghubungkan ulang notifikasi…';
    });
}

