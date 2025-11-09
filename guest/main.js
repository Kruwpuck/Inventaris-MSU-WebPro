// Animasi judul hero (atas -> bawah, 1s)
window.addEventListener('load', () => {
  const title = document.querySelector('.drop-in');
  if (title) requestAnimationFrame(() => title.classList.add('show'));
});

// Reveal on scroll (bawah -> atas, 1s)
const io = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('show');
      io.unobserve(e.target);
    }
  });
}, { threshold: 0.12 });
document.querySelectorAll('.reveal-up').forEach(el => io.observe(el));

// Tap animation (HP)
function addTapAnimation(el) {
  el.addEventListener('touchstart', () => el.classList.add('tap-active'), { passive: true });
  el.addEventListener('touchend',   () => setTimeout(()=>el.classList.remove('tap-active'),150));
  el.addEventListener('touchcancel', () => el.classList.remove('tap-active'));
}
document.querySelectorAll('.tap-anim').forEach(addTapAnimation);

/* ========== SET MAX PER ITEM DARI NILAI AWAL DI DOM ========== */
document.querySelectorAll('.item-card').forEach(card => {
  const sisaEl = card.querySelector('.item-body .item-meta .sisa');
  if (!sisaEl) return;
  const initial = Number(sisaEl.textContent.trim());
  // Simpan stok awal sebagai batas atas (max)
  card.dataset.max = Number.isNaN(initial) ? 0 : initial;
  // Normalisasi tampilan
  sisaEl.textContent = String(initial);
  updateBadgeAndButtons(card, initial);
});

/* Helper: update badge & state tombol */
function updateBadgeAndButtons(card, sisa) {
  const max = Number(card.dataset.max || 0);
  const badge = card.querySelector('.badge-status');
  const minusBtn = card.querySelector('.qty-btn[data-action="inc"]'); // (−) menambah sisa
  const plusBtn  = card.querySelector('.qty-btn[data-action="dec"]'); // (＋) mengurangi sisa

  if (badge) {
    if (sisa === 0) {
      badge.textContent = 'Habis';
      badge.style.background = '#a94442';
    } else {
      badge.textContent = 'Active';
      badge.style.background = '#167c73';
    }
  }

  // Disable sesuai batas
  if (minusBtn) {
    minusBtn.disabled = (sisa >= max); // tidak boleh lebih dari stok awal
    minusBtn.style.opacity = minusBtn.disabled ? .6 : 1;
    minusBtn.style.cursor  = minusBtn.disabled ? 'not-allowed' : 'pointer';
  }
  if (plusBtn) {
    plusBtn.disabled = (sisa <= 0); // tidak boleh kurang dari 0
    plusBtn.style.opacity = plusBtn.disabled ? .6 : 1;
    plusBtn.style.cursor  = plusBtn.disabled ? 'not-allowed' : 'pointer';
  }
}

/* ========== LOGIKA QTY: (+) mengurangi sisa, (−) menambah sisa ========== */
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.qty-btn');
  if (!btn) return;

  const card = btn.closest('.item-card');
  if (!card) return;

  const sisaEl = card.querySelector('.item-body .item-meta .sisa');
  if (!sisaEl) return;

  let sisa = Number(sisaEl.textContent.trim());
  if (Number.isNaN(sisa)) sisa = 0;
  const max = Number(card.dataset.max || 0);

  const action = btn.dataset.action; // "dec" (＋) kurangi sisa, "inc" (−) tambah sisa
  if (action === 'dec') {
    // PLUS (＋) → mengurangi sisa
    sisa = Math.max(0, sisa - 1);
  } else if (action === 'inc') {
    // MINUS (−) → menambah sisa, tapi tidak boleh melebihi stok awal (max)
    sisa = Math.min(max, sisa + 1);
  }

  sisaEl.textContent = String(sisa);
  updateBadgeAndButtons(card, sisa);
});

document.addEventListener('click', (e) => {
  const card = e.target.closest('.item-card');
  if (!card) return;

  // Jangan trigger expand kalau klik tombol qty
  if (e.target.closest('.qty-btn')) return;

  const grid = card.closest('.items-grid');
  const col = card.closest('[class*="col-"]');
  if (!grid || !col) return;

  const already = card.classList.contains('is-expanded');

  // reset
  grid.querySelectorAll('.item-card').forEach(c => c.classList.remove('is-expanded'));
  grid.classList.remove('has-expanded');

  // toggle
  if (!already) {
    card.classList.add('is-expanded');
    grid.classList.add('has-expanded');
  }
});

