/* ===== Animasi: mark & observe ===== */
function markRevealTargets(){
  document.querySelectorAll(`
    .navbar-masjid,.hero,.search-wrap,.section-title,
    .items-grid .col,.checkout-bar
  `).forEach(el => el.classList.add('reveal-up'));
}
function initRevealObserver(){
  const io = new IntersectionObserver((entries,o)=>{
    entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('show'); o.unobserve(e.target); } });
  },{threshold:.12, rootMargin:"0px 0px -40px 0px"});
  document.querySelectorAll('.reveal-up').forEach(el=>io.observe(el));
}
window.addEventListener('load', ()=> document.querySelector('.drop-in')?.classList.add('show'));
window.addEventListener('DOMContentLoaded', ()=>{ markRevealTargets(); initRevealObserver(); });

/* ===== Samakan gambar ruangan dengan item pertama ===== */
window.addEventListener('DOMContentLoaded', () => {
  const firstImg = document.querySelector('.items-grid .item-thumb img');
  if (!firstImg) return;
  const src = firstImg.getAttribute('src') || '';
  document.querySelectorAll('.items-grid .item-thumb img').forEach((img, idx) => {
    if (idx === 0) return;
    img.setAttribute('src', src);
  });

  // Set stok maksimum (ruangan: 1 per ruang)
  document.querySelectorAll('.item-card').forEach(card => {
    const sisaEl = card.querySelector('.sisa');
    if (!sisaEl) return;
    const initial = Number(sisaEl.textContent.trim() || '1');
    card.dataset.max = 1; // setiap ruang hanya bisa dipilih 1x
    sisaEl.textContent = String(Math.min(1, initial));
    updateState(card, Number(sisaEl.textContent));
  });

  refreshCheckoutState();
});

/* ===== State badge & tombol ===== */
function updateState(card, sisa){
  const badge = card.querySelector('.badge-status');
  const minusBtn = card.querySelector('.qty-btn[data-action="inc"]');
  const plusBtn  = card.querySelector('.qty-btn[data-action="dec"]');

  if (badge){
    if (sisa === 0){ badge.textContent='Habis'; badge.style.background='#a94442'; }
    else { badge.textContent='Active'; badge.style.background='#167c73'; }
  }
  if (minusBtn) minusBtn.disabled = (sisa >= 1);
  if (plusBtn)  plusBtn.disabled  = (sisa <= 0);
}

/* ===== Hitung total ruang dipilih & toggle Checkout ===== */
function refreshCheckoutState(){
  let selected = 0;
  document.querySelectorAll('.item-card').forEach(card => {
    const sisa = Number(card.querySelector('.sisa')?.textContent?.trim() || '0');
    selected += (sisa === 0 ? 1 : 0); // dipilih jika sisa 0
  });
  const countEl = document.getElementById('selectedCount');
  const btn = document.getElementById('checkoutBtn');
  const navBadge = document.querySelector('.badge.rounded-pill');
  if (countEl) countEl.textContent = String(selected);
  if (btn) btn.disabled = (selected <= 0);
  if (navBadge) navBadge.textContent = String(selected);
}

/* ===== LOGIKA QTY: (+) pilih (sisa-1), (−) batal (sisa+1) ===== */
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.qty-btn');
  if (!btn) return;
  const card = btn.closest('.item-card');
  if (!card) return;

  const sisaEl = card.querySelector('.sisa');
  let sisa = Number(sisaEl.textContent.trim() || '0');
  const action = btn.dataset.action;

  if (action === 'dec'){ sisa = Math.max(0, sisa - 1); } // pilih
  if (action === 'inc'){ sisa = Math.min(1, sisa + 1); } // batal pilih

  sisaEl.textContent = String(sisa);
  updateState(card, sisa);
  refreshCheckoutState();
});

/* ===== Klik kartu = fokus zoom (persist) ===== */
document.addEventListener('click', (e) => {
  const card = e.target.closest('.item-card');
  if (!card || e.target.closest('.qty-btn')) return;
  const grid = card.closest('.items-grid');
  const already = card.classList.contains('is-expanded');
  grid.classList.remove('has-expanded');
  grid.querySelectorAll('.item-card').forEach(c => c.classList.remove('is-expanded'));
  if (!already){ card.classList.add('is-expanded'); grid.classList.add('has-expanded'); }
});

/* ===== Search ===== */
const q = document.getElementById('searchInput');
const clearBtn = document.getElementById('clearSearch');
const gridEl = document.getElementById('itemsGrid');
const emptyState = document.getElementById('emptyState');

function applyFilter() {
  const term = (q?.value || '').trim().toLowerCase();
  let visible = 0;
  gridEl.querySelectorAll('.col').forEach(col => {
    const title = col.querySelector('.item-title')?.textContent?.toLowerCase() || '';
    const match = title.includes(term);
    col.style.display = match ? '' : 'none';
    if (match) visible++;
  });
  emptyState?.classList.toggle('d-none', visible > 0);
}
q?.addEventListener('input', applyFilter);
clearBtn?.addEventListener('click', ()=>{ if (q){ q.value=''; applyFilter(); } });

/* ===== Checkout demo ===== */
document.getElementById('checkoutBtn')?.addEventListener('click', () => {
  const picked = [];
  document.querySelectorAll('.item-card').forEach(card => {
    const name = card.querySelector('.item-title')?.textContent?.trim() || 'Ruang';
    const sisa = Number(card.querySelector('.sisa')?.textContent?.trim() || '1');
    if (sisa === 0) picked.push(`${name}`);
  });
  if (!picked.length) return;
  alert(`Checkout ruang:\n- ${picked.join('\n- ')}`);
});

/* ===== Tap animation ===== */
function addTapAnimation(el){
  el.addEventListener('touchstart', ()=>el.classList.add('tap-active'), {passive:true});
  el.addEventListener('touchend',   ()=>setTimeout(()=>el.classList.remove('tap-active'),150));
  el.addEventListener('touchcancel',()=>el.classList.remove('tap-active'));
}
document.querySelectorAll('.tap-anim').forEach(addTapAnimation);

/* MSU CART INTEGRATION */
function msuCollectSelections_ruang(){ 
  const items = [];
  document.querySelectorAll('.item-card').forEach(card => {
    const name = card.querySelector('.item-title')?.textContent?.trim() || '{ruang}';
    const thumb = card.querySelector('.item-thumb img')?.getAttribute('src') || '';
    const sisa = Number(card.querySelector('.sisa')?.textContent?.trim() || '1');
    const qty  = (sisa===0 ? 1 : 0); // ruang: 1 jika dipilih (sisa 0)
    if (qty>0) items.push({name, qty, thumb});
  });
  return items;
}
document.getElementById('checkoutBtn')?.addEventListener('click', ()=>{
  const picked = msuCollectSelections_ruang();
  if (!picked.length) return;
  picked.forEach(it=> MSUCart.upsertItem({type:'ruang', name: it.name, qty: it.qty, thumb: it.thumb}));
  MSUCart.renderBadge();
  window.location.href = 'bookingbarang.html?from=cart';
});
