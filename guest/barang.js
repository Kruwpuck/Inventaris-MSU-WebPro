/* ===== Animasi: tandai target & observe (sama dengan Ruangan) ===== */
function markRevealTargets(){
  document.querySelectorAll(`
    .navbar-masjid,
    .hero,
    .search-wrap,
    .section-title,
    .items-grid .col,
    .checkout-bar
  `).forEach(el => el.classList.add('reveal-up'));
}
function initRevealObserver(){
  const io = new IntersectionObserver((entries,o)=>{
    entries.forEach(e=>{
      if(e.isIntersecting){ e.target.classList.add('show'); o.unobserve(e.target); }
    });
  }, { threshold:.12, rootMargin:"0px 0px -40px 0px" });
  document.querySelectorAll('.reveal-up').forEach(el=>io.observe(el));
}
// drop-in untuk judul hero
window.addEventListener('load', () => {
  document.querySelector('.drop-in')?.classList.add('show');
});
// inisialisasi reveal-up
window.addEventListener('DOMContentLoaded', ()=>{
  markRevealTargets();
  initRevealObserver();
});

/* ===== Samakan semua gambar dengan gambar item pertama ===== */
window.addEventListener('DOMContentLoaded', () => {
  const firstImg = document.querySelector('.items-grid .item-thumb img');
  if (!firstImg) return;
  const src = firstImg.getAttribute('src') || '';
  document.querySelectorAll('.items-grid .item-thumb img').forEach((img, idx) => {
    if (idx === 0) return;
    img.setAttribute('src', src);
  });

  // Init stok maksimum dari nilai Sisa awal
  document.querySelectorAll('.item-card').forEach(card => {
    const sisaEl = card.querySelector('.sisa');
    if (!sisaEl) return;
    const initial = Number(sisaEl.textContent.trim() || '0');
    card.dataset.max = Number.isNaN(initial) ? 0 : initial;
    sisaEl.textContent = String(initial);
    updateState(card, initial);
  });

  refreshCheckoutState();
});

/* ===== Update UI per kartu ===== */
function updateState(card, sisa){
  const max = Number(card.dataset.max || 0);
  const badge = card.querySelector('.badge-status');
  const minusBtn = card.querySelector('.qty-btn[data-action="inc"]'); // − menambah sisa
  const plusBtn  = card.querySelector('.qty-btn[data-action="dec"]'); // ＋ mengurangi sisa

  if (badge){
    if (sisa === 0){ badge.textContent='Habis'; badge.style.background='#a94442'; }
    else { badge.textContent='Active'; badge.style.background='#167c73'; }
  }
  if (minusBtn) minusBtn.disabled = (sisa >= max);
  if (plusBtn)  plusBtn.disabled  = (sisa <= 0);
}

/* ===== Hitung total item dipilih & toggle Checkout ===== */
function refreshCheckoutState(){
  let selected = 0;
  document.querySelectorAll('.item-card').forEach(card => {
    const max = Number(card.dataset.max || 0);
    const sisa = Number(card.querySelector('.sisa')?.textContent?.trim() || '0');
    selected += Math.max(0, max - sisa); // yang diambil = max - sisa
  });
  const selectedCount = document.getElementById('selectedCount');
  const checkoutBtn   = document.getElementById('checkoutBtn');
  const navBadge      = document.querySelector('.badge.rounded-pill'); // kalau ada

  if (selectedCount) selectedCount.textContent = String(selected);
  if (checkoutBtn)   checkoutBtn.disabled = (selected <= 0);
  if (navBadge)      navBadge.textContent = String(selected);
}

/* ===== LOGIKA QTY: (+) mengurangi sisa, (−) menambah sisa ===== */
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.qty-btn');
  if (!btn) return;

  const card = btn.closest('.item-card');
  if (!card) return;

  const sisaEl = card.querySelector('.sisa');
  let sisa = Number(sisaEl.textContent.trim() || '0');
  const max = Number(card.dataset.max || 0);
  const action = btn.dataset.action; // "dec" atau "inc"

  if (action === 'dec') sisa = Math.max(0, sisa - 1);   // ＋ → kurangi sisa
  if (action === 'inc') sisa = Math.min(max, sisa + 1); // − → tambah sisa

  sisaEl.textContent = String(sisa);
  updateState(card, sisa);
  refreshCheckoutState();
});

/* ===== Fokus zoom via klik (persist) ===== */
document.addEventListener('click', (e) => {
  const card = e.target.closest('.item-card');
  if (!card) return;
  if (e.target.closest('.qty-btn')) return;

  const grid = card.closest('.items-grid');
  const already = card.classList.contains('is-expanded');

  grid.classList.remove('has-expanded');
  grid.querySelectorAll('.item-card').forEach(c => c.classList.remove('is-expanded'));

  if (!already){
    card.classList.add('is-expanded');
    grid.classList.add('has-expanded');
  }
});

/* ===== Search realtime ===== */
const q = document.getElementById('searchInput');
const clearBtn = document.getElementById('clearSearch');
const gridEl = document.getElementById('itemsGrid');
const emptyState = document.getElementById('emptyState');

function applyFilter() {
  const term = (q?.value || '').trim().toLowerCase();
  if (!gridEl) return;
  let visible = 0;

  gridEl.querySelectorAll('.col').forEach(col => {
    const title = col.querySelector('.item-title')?.textContent?.toLowerCase() || '';
    const match = title.includes(term);
    col.style.display = match ? '' : 'none';
    if (match) visible += 1;
  });

  if (emptyState) emptyState.classList.toggle('d-none', visible > 0);
}
q?.addEventListener('input', applyFilter);
clearBtn?.addEventListener('click', () => { if (q){ q.value=''; applyFilter(); } });

/* ===== Checkout (demo) ===== */
document.getElementById('checkoutBtn')?.addEventListener('click', () => {
  const picked = [];
  document.querySelectorAll('.item-card').forEach(card => {
    const name = card.querySelector('.item-title')?.textContent?.trim() || 'Item';
    const max  = Number(card.dataset.max || 0);
    const sisa = Number(card.querySelector('.sisa')?.textContent?.trim() || '0');
    const qty  = Math.max(0, max - sisa);
    if (qty > 0) picked.push(`${name} × ${qty}`);
  });
  if (!picked.length) return;
  alert(`Checkout:\n- ${picked.join('\n- ')}`);
});

/* ===== Tap animation (opsional) ===== */
function addTapAnimation(el){
  el.addEventListener('touchstart', ()=>el.classList.add('tap-active'), {passive:true});
  el.addEventListener('touchend',   ()=>setTimeout(()=>el.classList.remove('tap-active'),150));
  el.addEventListener('touchcancel',()=>el.classList.remove('tap-active'));
}
document.querySelectorAll('.tap-anim').forEach(addTapAnimation);

/* MSU CART INTEGRATION */
function msuCollectSelections_barang(){ 
  const items = [];
  document.querySelectorAll('.item-card').forEach(card => {
    const name = card.querySelector('.item-title')?.textContent?.trim() || '{barang}';
    const thumb = card.querySelector('.item-thumb img')?.getAttribute('src') || '';
    const max  = Number(card.dataset.max || 0);
    const sisa = Number(card.querySelector('.sisa')?.textContent?.trim() || '0');
    const qty  = Math.max(0, max - sisa);
    if (qty>0) items.push({name, qty, thumb});
  });
  return items;
}
document.getElementById('checkoutBtn')?.addEventListener('click', ()=>{
  const picked = msuCollectSelections_barang();
  if (!picked.length) return;
  picked.forEach(it=> MSUCart.upsertItem({type:'barang', name: it.name, qty: it.qty, thumb: it.thumb}));
  MSUCart.renderBadge();
  window.location.href = 'bookingbarang.html?from=cart';
});
