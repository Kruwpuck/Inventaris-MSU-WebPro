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

// ====== Setup stok awal (max) untuk semua kartu ======
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

// ====== Helper: update badge & state tombol ======
function updateBadgeAndButtons(card, sisa) {
  const type = card.dataset.type || 'barang';
  const max = Number(card.dataset.max || 0);
  const badge = card.querySelector('.badge-status');
  const minusBtn = card.querySelector('.qty-btn[data-action="inc"]'); // − tambah sisa / batal
  const plusBtn  = card.querySelector('.qty-btn[data-action="dec"]'); // ＋ kurangi sisa / pilih

  if (badge) {
    if (sisa === 0) { badge.textContent = 'Habis'; badge.style.background = '#a94442'; }
    else { badge.textContent = 'Active'; badge.style.background = '#167c73'; }
  }
  if (type === 'ruang'){
    if (minusBtn) { minusBtn.disabled = (sisa >= 1); minusBtn.style.opacity = minusBtn.disabled ? .6 : 1; }
    if (plusBtn)  { plusBtn.disabled  = (sisa <= 0); plusBtn .style.opacity = plusBtn .disabled ? .6 : 1; }
  } else {
    if (minusBtn) { minusBtn.disabled = (sisa >= max); minusBtn.style.opacity = minusBtn.disabled ? .6 : 1; }
    if (plusBtn)  { plusBtn.disabled  = (sisa <= 0);  plusBtn .style.opacity = plusBtn .disabled ? .6 : 1; }
  }
}

// ====== Logika qty tombol (klik) ======
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.qty-btn');
  if (!btn) return;
  const card = btn.closest('.item-card'); if (!card) return;

  const type = card.dataset.type || 'barang';
  const sisaEl = card.querySelector('.sisa');
  let sisa = Number(sisaEl.textContent.trim() || '0');
  const max = Number(card.dataset.max || 0);
  const action = btn.dataset.action; // "dec" | "inc"

  if (type === 'ruang'){
    if (action === 'dec') sisa = Math.max(0, sisa - 1);
    if (action === 'inc') sisa = Math.min(1, sisa + 1);
  } else {
    if (action === 'dec') sisa = Math.max(0, sisa - 1);
    if (action === 'inc') sisa = Math.min(max, sisa + 1);
  }

  sisaEl.textContent = String(sisa);
  updateBadgeAndButtons(card, sisa);
  refreshCheckoutState();
  refreshFab(); // sinkron FAB
});

// ====== Fokus zoom via klik (persist) ======
document.addEventListener('click', (e) => {
  const card = e.target.closest('.item-card'); if (!card) return;
  if (e.target.closest('.qty-btn')) return;

  const grid = card.closest('.items-grid');
  const already = card.classList.contains('is-expanded');
  grid.classList.remove('has-expanded');
  grid.querySelectorAll('.item-card').forEach(c => c.classList.remove('is-expanded'));
  if (!already){ card.classList.add('is-expanded'); grid.classList.add('has-expanded'); }
});

// ====== Hitung total dipilih dan set tombol checkout ======
function refreshCheckoutState(){
  let selected = 0;
  document.querySelectorAll('.item-card').forEach(card => {
    const type = card.dataset.type || 'barang';
    const max  = Number(card.dataset.max || 0);
    const sisa = Number(card.querySelector('.sisa')?.textContent?.trim() || (type==='ruang'?1:0));
    if (type === 'ruang'){ selected += (sisa === 0 ? 1 : 0); }
    else { selected += Math.max(0, max - sisa); }
  });
  const selectedCount = document.getElementById('selectedCount');
  const checkoutBtn   = document.getElementById('checkoutBtn');
  if (selectedCount) selectedCount.textContent = String(selected);
  if (checkoutBtn)   checkoutBtn.disabled = (selected <= 0);
}
refreshCheckoutState();

// ====== Checkout bar: masukkan ke MSUCart & redirect ======
document.getElementById('checkoutBtn')?.addEventListener('click', doCheckout);
function doCheckout(){
  const picked = [];
  document.querySelectorAll('.item-card').forEach(card => {
    const type = card.dataset.type || 'barang';
    const name = card.querySelector('.item-title')?.textContent?.trim() || (type==='ruang'?'Ruang':'Item');
    const thumb = card.querySelector('.item-thumb img')?.getAttribute('src') || '';
    const max  = Number(card.dataset.max || 0);
    const sisa = Number(card.querySelector('.sisa')?.textContent?.trim() || (type==='ruang'?1:0));

    const qty  = (type === 'ruang') ? (sisa===0 ? 1 : 0) : Math.max(0, max - sisa);
    if (qty > 0) picked.push({type, name, qty, thumb});
  });

  if (!picked.length) return;
  try{
    picked.forEach(it => MSUCart.upsertItem(it));
    MSUCart.renderBadge?.();
  }catch(e){ /* ignore */ }

  window.location.href = 'bookingbarang.html?from=home';
}

// ====== === Floating Checkout (FAB) === ======
function msuSelectedCountFromCards(){
  let total = 0;
  document.querySelectorAll('.item-card').forEach(card=>{
    const type = card.dataset.type || 'barang';
    const max  = Number(card.dataset.max || (type==='ruang'?1:0));
    const sisa = Number(card.querySelector('.sisa')?.textContent?.trim() || (type==='ruang'?1:0));
    const qty  = (type==='ruang') ? (sisa===0 ? 1 : 0) : Math.max(0, max - sisa);
    total += qty;
  });
  return total;
}
function refreshFab(){
  const c = msuSelectedCountFromCards();
  const el = document.getElementById('fabCheckout');
  const badge = document.getElementById('fabCount');
  if (badge) badge.textContent = String(c);
  if (el) el.classList.toggle('is-disabled', c<=0);
}
document.getElementById('fabCheckout')?.addEventListener('click', ()=>{
  const c = msuSelectedCountFromCards();
  if (c<=0) return;
  doCheckout();
});
window.addEventListener('DOMContentLoaded', refreshFab);
