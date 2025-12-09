/* =======================
   cart-page.js
   Replaces booking-barang.js functionality for Livewire Cart Page
   ======================= */

/* ---------- Util ---------- */
function toRupiah(n) { return new Intl.NumberFormat('id-ID').format(Number(n || 0)); }
function todayISO() {
  const t = new Date(); t.setHours(0, 0, 0, 0);
  return t.toISOString().split('T')[0];
}

/* ---------- Donasi Quick Fill ---------- */
function initDonation() {
  document.querySelectorAll('.btn-donasi').forEach(btn => {
    btn.addEventListener('click', () => {
      const amt = btn.getAttribute('data-amt');
      const input = document.getElementById('donationAmount');
      if (input) {
        input.value = amt;
        input.dispatchEvent(new Event('input'));
      }
    });
  });

  const donInput = document.getElementById('donationAmount');
  if (donInput) {
    donInput.addEventListener('input', () => {
      const hidden = document.getElementById('donationInput');
      if (hidden) {
        hidden.value = donInput.value;
        hidden.dispatchEvent(new Event('input'));
      }
    });
  }
}

/* ---------- CART RENDER (list ke bawah) ---------- */
function renderCartList() {
  const listEl = document.getElementById('cartList');
  if (listEl && window.MSUCart) {
    listEl.innerHTML = MSUCart.toListHTML();
  }
}

/* Hapus semua cart */
document.getElementById('clearCartBtn')?.addEventListener('click', () => {
  if (!confirm("Yakin ingin menghapus semua dari keranjang?")) return;
  if (window.MSUCart) MSUCart.clear();
  renderCartList();
  buildTabsFromCart(); // refresh panel kiri
  window.MSUCart?.renderBadge();
});

/* ---------- PANEL KIRI: Tabs Horizontal Multi-Item ---------- */
// NOTE: ID elements must match blade
const tabsUL = document.getElementById('itemTabs');
const tabsContent = document.getElementById('itemTabContent');

function fallbackThumbFor(name) {
  const lower = (name || '').toLowerCase();
  // Simple logic to try asset path if possible, or placeholder
  // We assume images are in assets folder. logic roughly matches blueprint
  if (lower.includes('proyektor')) return '/assets/proyektor.jpeg';
  if (lower.includes('sound')) return '/assets/sound.jpeg';
  if (lower.includes('karpet')) return '/assets/karpet.jpeg';
  if (lower.includes('terpal')) return '/assets/terpal.jpeg';
  return '/assets/plaza.jpeg';
}

function getBookedDaysFor(itemName, y, m) {
  const base = (itemName || '').toLowerCase();
  if (base.includes('proyektor')) return [5, 12, 19];
  if (base.includes('sound')) return [7, 14, 21];
  if (base.includes('karpet')) return [3, 9, 27];
  return [10, 20];
}

function isToday(y, m, d) {
  const t = new Date();
  return y === t.getFullYear() && m === t.getMonth() && d === t.getDate();
}

function getBookingsFor(itemName, y, m, day) {
  // Dummy Data
  const res = [];
  if (y === 2025 && m === 10 && day === 7) {
    res.push({ slot: 'Pagi (1/3)', kegiatan: 'Latihan Paduan Suara', pj: 'UKM PSM' });
  }
  return res;
}

function renderBookingList(container, itemName, y, m, day) {
  const box = container.querySelector('.booking-list-body');
  const headerDate = container.querySelector('.booking-list-header .date-label');
  if (!box) return;

  const bookings = getBookingsFor(itemName, y, m, day);
  if (headerDate) headerDate.textContent = new Date(y, m, day).toLocaleDateString('id-ID', { day: 'numeric', month: 'long' });

  if (!bookings.length) {
    box.innerHTML = `<div class="booking-list-empty">Belum ada peminjaman tercatat pada tanggal ini.</div>`;
    return;
  }

  box.innerHTML = bookings.map(b => `
        <div class="booking-list-item">
          <div class="bli-slot">${b.slot}</div>
          <div class="bli-main">${b.kegiatan}</div>
          <div class="bli-meta">PJ: ${b.pj}</div>
        </div>
      `).join('');
}

function renderCalendarFor(container, itemName, refDate) {
  const calTitle = container.querySelector('.cal-title');
  const calGrid = container.querySelector('.calendar-grid');
  const y = refDate.getFullYear(), m = refDate.getMonth();

  const first = new Date(y, m, 1);
  const startDay = (first.getDay() + 6) % 7;
  const daysInMonth = new Date(y, m + 1, 0).getDate();
  const prevDays = new Date(y, m, 0).getDate();
  const booked = new Set(getBookedDaysFor(itemName, y, m));

  if (calTitle) {
    calTitle.textContent = refDate.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
  }

  let html = '';
  'S N S R K J S'.split(' ').forEach(h => html += `<span class="muted">${h}</span>`);
  for (let i = startDay; i > 0; i--) {
    html += `<span class="muted">${prevDays - i + 1}</span>`;
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const cls = ['day', isToday(y, m, d) ? 'today' : '', booked.has(d) ? 'booked' : ''].join(' ').trim();
    html += `<span class="${cls}" data-day="${d}">${d}</span>`;
  }
  calGrid.innerHTML = html;

  calGrid.querySelectorAll('.day').forEach(el => {
    el.addEventListener('click', () => {
      const d = Number(el.dataset.day);
      calGrid.querySelectorAll('.day').forEach(c => c.classList.remove('selected'));
      el.classList.add('selected');
      renderBookingList(container, itemName, y, m, d);
    });
  });
}

function buildItemPanelHTML(item) {
  const name = item.name || 'Barang';
  // try to get thumb from item, or fallback
  const thumb = item.thumb || fallbackThumbFor(name);
  const qty = Number(item.qty || 0);

  return `
      <div class="item-panel">
        <div class="summary-thumb mb-3">
          <img src="${thumb}" alt="${name}">
          <span class="badge-status">Active</span>
        </div>
        <div class="text-center">
          <div class="title h4 mb-1">${name}</div>
          <div class="text-muted">Dipinjam: <b><span class="qty-display-text">${qty}</span>x</b></div>
  
          <div class="d-flex justify-content-center gap-2 mt-2">
            <button class="btn btn-qty btn-qminus" type="button"><i class="bi bi-dash-lg"></i></button>
            <div class="qty-display">${qty}</div>
            <button class="btn btn-qty btn-qplus" type="button"><i class="bi bi-plus-lg"></i></button>
          </div>
          <small class="text-muted d-block mt-1">Atur jumlah yang akan dipinjam</small>
        </div>
  
        <div class="mini-calendar mt-3">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <button class="cal-nav cal-prev" type="button"><i class="bi bi-chevron-left"></i></button>
            <strong class="cal-title">-</strong>
            <button class="cal-nav cal-next" type="button"><i class="bi bi-chevron-right"></i></button>
          </div>
          <div class="calendar-legend mb-2">
            <span class="legend-box booked"></span><small class="ms-1 me-3">Terbooking</small>
            <span class="legend-box today"></span><small class="ms-1">Hari ini</small>
          </div>
          <div class="calendar-grid" aria-hidden="true"></div>
        </div>
  
        <div class="booking-list mt-3">
          <div class="booking-list-header">
            <span class="bl-title">Agenda</span>
            <span class="date-label"></span>
          </div>
          <div class="booking-list-body mt-2 small"></div>
        </div>
      </div>
    `;
}

function buildTabsFromCart() {
  if (!tabsUL || !tabsContent) return;
  const cart = (window.MSUCart && MSUCart.get()) || [];

  tabsUL.innerHTML = '';
  tabsContent.innerHTML = '';

  if (!cart.length) {
    tabsContent.innerHTML = `<div class="p-3 text-center text-muted border rounded-3">Keranjang kosong.</div>`;
    return;
  }

  cart.forEach((it, idx) => {
    const tabId = `tab-${idx}`;
    const panelId = `panel-${idx}`;
    const li = document.createElement('li');
    li.className = 'nav-item';

    // Bootstrap Tab button
    li.innerHTML = `
        <button class="nav-link ${idx === 0 ? 'active' : ''}" id="${tabId}"
                data-bs-toggle="tab" data-bs-target="#${panelId}" type="button" role="tab">
          ${it.name} <span class="badge text-bg-success ms-2">${it.qty}x</span>
        </button>
      `;
    tabsUL.appendChild(li);

    const pane = document.createElement('div');
    pane.className = `tab-pane fade ${idx === 0 ? 'show active' : ''}`;
    pane.id = panelId;
    pane.dataset.itemName = it.name;
    pane.dataset.refDate = new Date().toISOString();
    pane.innerHTML = buildItemPanelHTML(it);
    tabsContent.appendChild(pane);
  });

  initPanels();
  renderCartList();
}

function initPanels() {
  tabsContent.querySelectorAll('.tab-pane').forEach(pane => {
    const name = pane.dataset.itemName || '';
    let ref = new Date(pane.dataset.refDate);
    renderCalendarFor(pane, name, ref);

    // Buttons
    pane.querySelector('.cal-prev')?.addEventListener('click', () => {
      ref.setMonth(ref.getMonth() - 1);
      pane.dataset.refDate = ref.toISOString();
      renderCalendarFor(pane, name, ref);
    });
    pane.querySelector('.cal-next')?.addEventListener('click', () => {
      ref.setMonth(ref.getMonth() + 1);
      pane.dataset.refDate = ref.toISOString();
      renderCalendarFor(pane, name, ref);
    });

    // Qty Logic
    const qtyBox = pane.querySelector('.qty-display');
    const qtyText = pane.querySelector('.qty-display-text');

    function updateQty(newQ) {
      newQ = Math.max(0, newQ);
      if (qtyBox) qtyBox.textContent = String(newQ);
      if (qtyText) qtyText.textContent = String(newQ);

      // Update Cart
      MSUCart.upsertItem({ type: 'barang', name: name, qty: newQ });
      MSUCart.renderBadge();
      renderCartList();

      // Update tab badge
      const idx = Array.from(tabsContent.children).indexOf(pane);
      const tabBtn = document.getElementById(`tab-${idx}`);
      if (tabBtn) {
        const b = tabBtn.querySelector('.badge');
        if (b) b.textContent = `${newQ}x`;
      }

      if (newQ === 0) buildTabsFromCart(); // Rebuild if removed

      syncJsonInput();
    }

    pane.querySelector('.btn-qminus')?.addEventListener('click', () => {
      const c = Number(qtyBox?.textContent || 0);
      updateQty(c - 1);
    });
    pane.querySelector('.btn-qplus')?.addEventListener('click', () => {
      const c = Number(qtyBox?.textContent || 0);
      updateQty(c + 1);
    });
  });
}

function syncJsonInput() {
  const input = document.getElementById('cartJsonInput');
  if (input && window.MSUCart) {
    input.value = JSON.stringify(MSUCart.get());
    input.dispatchEvent(new Event('input'));
  }
}

function validateAndSync() {
  const form = document.getElementById('bookingForm');
  const btn = document.getElementById('btnSubmit');
  if (!form || !btn) return;

  // Check HTML5 validity + Custom logic
  const isValid = form.checkValidity(); // Native check
  // Also check cart length
  const hasItems = (MSUCart && MSUCart.count() > 0);

  btn.disabled = !(isValid && hasItems);

  // Always sync cart json
  syncJsonInput();
}

// Init
function initCartPage() {
  initDonation();
  buildTabsFromCart();

  const form = document.getElementById('bookingForm');
  if (form) {
    form.addEventListener('input', validateAndSync);
    form.addEventListener('change', validateAndSync);
    validateAndSync(); // initial
  }
}

document.addEventListener('livewire:navigated', initCartPage);
document.addEventListener('DOMContentLoaded', initCartPage);
