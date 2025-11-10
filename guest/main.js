// main.js
// ====== Animasi judul hero & reveal on scroll ======
window.addEventListener('load', () => {
  document.querySelector('.drop-in')?.classList.add('show');
});
const io = new IntersectionObserver((entries) => {
  entries.forEach(e => { if (e.isIntersecting){ e.target.classList.add('show'); io.unobserve(e.target);} });
}, { threshold: 0.12 });
document.querySelectorAll('.reveal-up').forEach(el => io.observe(el));

// Tap animation (mobile)
function addTapAnimation(el){
  el.addEventListener('touchstart', ()=>el.classList.add('tap-active'), {passive:true});
  el.addEventListener('touchend',   ()=>setTimeout(()=>el.classList.remove('tap-active'),150));
  el.addEventListener('touchcancel',()=>el.classList.remove('tap-active'));
}
document.querySelectorAll('.tap-anim').forEach(addTapAnimation);

// ====== Setup stok awal ======
function initCards(){
  document.querySelectorAll('.item-card').forEach(card => {
    const sisaEl = card.querySelector('.sisa');
    if (!sisaEl) return;
    const initial = Number(sisaEl.textContent.trim() || '0');
    const type = card.dataset.type || 'barang';
    card.dataset.max = (type === 'ruang') ? 1 : (Number.isNaN(initial) ? 0 : initial);
    sisaEl.textContent = String(type === 'ruang' ? Math.min(1, initial || 1) : initial);
    updateBadgeAndButtons(card, Number(sisaEl.textContent));
  });
}
initCards();

function updateBadgeAndButtons(card, sisa) {
  const type = card.dataset.type || 'barang';
  const max = Number(card.dataset.max || 0);
  const badge = card.querySelector('.badge-status');
  const minusBtn = card.querySelector('.qty-btn[data-action="inc"]'); // − batal / tambah sisa
  const plusBtn  = card.querySelector('.qty-btn[data-action="dec"]'); // ＋ pilih

  if (badge) {
    if (sisa === 0) { badge.textContent = 'Habis'; badge.style.background = '#a94442'; }
    else { badge.textContent = 'Active'; badge.style.background = '#167c73'; }
  }
  if (type === 'ruang'){
    if (minusBtn) { minusBtn.disabled = (sisa >= 1); minusBtn.style.opacity = minusBtn.disabled ? .6 : 1; }
    if (plusBtn)  { plusBtn.disabled  = (sisa <= 0); plusBtn.style.opacity = plusBtn.disabled ? .6 : 1; }
  } else {
    if (minusBtn) { minusBtn.disabled = (sisa >= max); minusBtn.style.opacity = minusBtn.disabled ? .6 : 1; }
    if (plusBtn)  { plusBtn.disabled  = (sisa <= 0);  plusBtn .style.opacity = plusBtn .disabled ? .6 : 1; }
  }
}

// ====== Toast util ======
function showToastSuccess(text){
  const wrap = document.getElementById('toastStack');
  if (!wrap) return alert(text);
  const id = 't' + Date.now();
  wrap.insertAdjacentHTML('beforeend', `
    <div id="${id}" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body"><i class="bi bi-check2-circle me-1"></i>${text}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>`);
  const el = document.getElementById(id);
  const t = new bootstrap.Toast(el, { delay: 2200 });
  t.show();
  el.addEventListener('hidden.bs.toast', ()=> el.remove());
}

// ====== Modal Konfirmasi Tambah ======
let pendingCard = null;
const confirmModalEl = document.getElementById('confirmAddModal');
const confirmModal = confirmModalEl ? new bootstrap.Modal(confirmModalEl) : null;
const confirmNameEl = document.getElementById('confirmName');
const confirmTypeEl = document.getElementById('confirmType');
const confirmThumbEl = document.getElementById('confirmThumb');

function openConfirm(card){
  pendingCard = card;
  const name = card.querySelector('.item-title')?.textContent?.trim() || 'Item';
  const type = (card.dataset.type || 'barang') === 'ruang' ? 'Fasilitas / Ruangan' : 'Barang';
  const thumb = card.querySelector('.item-thumb img')?.getAttribute('src') || '';
  if (confirmNameEl) confirmNameEl.textContent = name;
  if (confirmTypeEl) confirmTypeEl.textContent = type;
  if (confirmThumbEl) confirmThumbEl.src = thumb;
  if (confirmModal) confirmModal.show();
  else if (window.confirm(`Tambah "${name}" ke keranjang?`)) confirmAddNoRedirect();
}

document.getElementById('confirmAddBtn')?.addEventListener('click', () => {
  confirmAddNoRedirect();
  if (confirmModal) confirmModal.hide();
});

// Tambah ke keranjang TANPA redirect
function confirmAddNoRedirect(){
  if (!pendingCard) return;
  const card = pendingCard; pendingCard = null;

  const type = card.dataset.type || 'barang';
  const sisaEl = card.querySelector('.sisa');
  let sisa = Number(sisaEl.textContent.trim() || (type==='ruang'?1:0));
  // kurangi stok 1
  sisa = Math.max(0, sisa - 1);
  sisaEl.textContent = String(sisa);
  updateBadgeAndButtons(card, sisa);

  const name = card.querySelector('.item-title')?.textContent?.trim() || (type==='ruang'?'Ruang':'Item');
  const thumb = card.querySelector('.item-thumb img')?.getAttribute('src') || '';
  try{
    if (window.MSUCart){
      MSUCart.add(name, type, thumb, 1);
      MSUCart.renderBadge();
    }
  }catch(e){ /* ignore */ }

  showToastSuccess(`${name} ditambahkan ke keranjang.`);
}

// ====== Expand visual saat klik kartu (kecuali tombol qty) ======
document.addEventListener('click', (e) => {
  const card = e.target.closest('.item-card'); if (!card) return;
  if (e.target.closest('.qty-btn')) return;
  const grid = card.closest('.items-grid');
  const already = card.classList.contains('is-expanded');
  grid.classList.remove('has-expanded');
  grid.querySelectorAll('.item-card').forEach(c => c.classList.remove('is-expanded'));
  if (!already){ card.classList.add('is-expanded'); grid.classList.add('has-expanded'); }
});

// ====== Klik tombol qty ======
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.qty-btn');
  if (!btn) return;
  const card = btn.closest('.item-card'); if (!card) return;

  const type = card.dataset.type || 'barang';
  const sisaEl = card.querySelector('.sisa');
  let sisa = Number(sisaEl.textContent.trim() || '0');
  const max = Number(card.dataset.max || 0);
  const action = btn.dataset.action; // "dec" (pilih) | "inc" (batal/restore)

  if (action === 'dec'){
    openConfirm(card);
    return;
  }

  // − : kembalikan stok tampilan (tidak mempengaruhi cart)
  if (type === 'ruang'){
    sisa = Math.min(1, sisa + 1);
  } else {
    sisa = Math.min(max, sisa + 1);
  }
  sisaEl.textContent = String(sisa);
  updateBadgeAndButtons(card, sisa);
});

// FAB → ke halaman booking (jika ada isi keranjang)
document.getElementById('fabCheckout')?.addEventListener('click', ()=>{
  const c = (window.MSUCart ? MSUCart.count() : 0);
  if (c<=0) return;
  window.location.href = 'bookingbarang.html?from=fab';
});

// Inisialisasi badge saat load
window.addEventListener('load', ()=> {
  if (window.MSUCart) MSUCart.renderBadge();
});
