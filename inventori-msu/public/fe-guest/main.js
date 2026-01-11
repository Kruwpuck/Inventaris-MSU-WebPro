// ====== Animasi judul hero & reveal on scroll ======
window.addEventListener('load', () => {
  document.querySelector('.drop-in')?.classList.add('show');
});
const io = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('show');
      io.unobserve(e.target);
    }
  });
}, { threshold: 0.12 });
document.querySelectorAll('.reveal-up').forEach(el => io.observe(el));

// Tap animation (mobile)
function addTapAnimation(el) {
  el.addEventListener('touchstart', () => el.classList.add('tap-active'), { passive: true });
  el.addEventListener('touchend', () => setTimeout(() => el.classList.remove('tap-active'), 150));
  el.addEventListener('touchcancel', () => el.classList.remove('tap-active'));
}
document.querySelectorAll('.tap-anim').forEach(addTapAnimation);

// ====== Module: MSU Dates (Time Range) ======
window.MSUDates = (function () {
  const KEY = 'msu_dates_v2';

  function get() {
    try { return JSON.parse(localStorage.getItem(KEY) || '{}'); }
    catch (e) { return {}; }
  }

  // Accepts: { startDate, startTime, endDate, endTime }
  function set(data) {
    const prev = get();
    // Merge new data with previous to prevent overwrite if calling partial
    const d = { ...prev, ...data };
    localStorage.setItem(KEY, JSON.stringify(d));
  }

  function clear() { localStorage.removeItem(KEY); }

  function isSet() {
    const d = get();
    return Boolean(d.startDate && d.startTime && d.endDate && d.endTime);
  }

  function formatRange() {
    const d = get();
    if (!d.startDate) return '';
    // Format requested: (hour:menit) dd-mm-yyyy
    const fmt = (date, time) => {
      if (!date || !time) return '';
      const [yyyy, mm, dd] = date.split('-');
      return `(${time}) ${dd}-${mm}-${yyyy}`;
    };
    return `${fmt(d.startDate, d.startTime)} → ${fmt(d.endDate, d.endTime)}`;
  }

  function getDetails() {
    return get();
  }

  return { get, set, clear, isSet, formatRange, getDetails };
})();

// ====== Render & set DateBar ======
(function initDateBar() {
  initHomeDateConstraints(); // Add constraint logic
  const inpStart = document.getElementById('dateStart') || document.getElementById('filterDateStart');
  const inpStartTime = document.getElementById('timeStart') || document.getElementById('filterTimeStart');

  const inpEnd = document.getElementById('dateEnd') || document.getElementById('filterDateEnd');
  const inpEndTime = document.getElementById('timeEnd') || document.getElementById('filterTimeEnd');

  const btnSet = document.getElementById('btnSetDates') || document.getElementById('btnCheckAvailability');
  const lbl = document.querySelector('.js-daterange') || document.getElementById('filterResultText');

  // Prefill dari storage
  const saved = window.MSUDates.get();
  if (inpStart && saved.startDate) inpStart.value = saved.startDate;
  if (inpStartTime && saved.startTime) inpStartTime.value = saved.startTime;
  if (inpEnd && saved.endDate) inpEnd.value = saved.endDate;
  if (inpEndTime && saved.endTime) inpEndTime.value = saved.endTime;

  if (lbl) {
    lbl.textContent = window.MSUDates.isSet()
      ? `Jadwal dipilih: ${window.MSUDates.formatRange()}`
      : 'Belum memilih jadwal.';
  }

  // New Global Function for Checking Stock
  window.checkRealTimeStock = async function (isAuto = false) {
    const saved = window.MSUDates.get();
    if (!window.MSUDates.isSet()) return;

    // Visual feedback on button
    const btn = document.getElementById('btnSetDates') || document.getElementById('btnCheckAvailability');
    const originalText = btn ? btn.innerHTML : '';
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Cek...';
    }

    try {
      const q = new URLSearchParams({
        startDate: saved.startDate,
        endDate: saved.endDate,
        startTime: saved.startTime,
        endTime: saved.endTime
      });

      const url = `/api/peminjaman/check?${q.toString()}`;
      const res = await fetch(url);
      if (!res.ok) throw new Error('Network error');
      const data = await res.json();

      // Update UI Cards
      document.querySelectorAll('.item-card').forEach(card => {
        const titleEl = card.querySelector('.item-title');
        const name = titleEl ? titleEl.textContent.trim() : '';

        // Find availability info
        const info = data.find(d => d.itemName === name);
        if (info) {
          card.dataset.max = info.available;
        }
      });

      // Refresh UI with new max values
      if (typeof initCards === 'function') initCards();

      if (!isAuto) {
        showToastSuccess('Ketersediaan diperbarui. Silakan pilih item.');
      }
    } catch (e) {
      console.error(e);
      if (!isAuto) showToastInfo('Gagal mengecek ketersediaan terkini.');
    } finally {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = originalText;
      }
    }
  };

  btnSet?.addEventListener('click', () => {
    const sDate = inpStart?.value || '';
    const sTime = inpStartTime?.value || '';
    const eDate = inpEnd?.value || '';
    const eTime = inpEndTime?.value || '';

    if (!sDate || !sTime || !eDate || !eTime) {
      showToastInfo('Mohon lengkapi Tanggal & Jam Mulai/Selesai.');
      return;
    }

    // Basic validation
    const startDt = new Date(`${sDate}T${sTime}`);
    const now = new Date();
    // Gunakan setSeconds(0,0) agar jika pilih menit yg sama dengan sekarang tetap dianggap valid
    if (startDt < new Date(now.setSeconds(0, 0))) {
      showToastInfo('Jam pakai tidak boleh waktu yang sudah lewat.');
      return;
    }

    if (eDate < sDate) {
      showToastInfo('Tanggal kembali tidak boleh kurang dari tanggal pakai.');
      return;
    }
    if (eDate === sDate && eTime <= sTime) {
      showToastInfo('Jam kembali harus setelah jam pakai (jika di hari yang sama).');
      return;
    }

    window.MSUDates.set({ startDate: sDate, startTime: sTime, endDate: eDate, endTime: eTime });
    if (lbl) {
      lbl.textContent = `Jadwal dipilih: ${window.MSUDates.formatRange()}`;
    }

    // Call API to check availability
    window.checkRealTimeStock();
  });

  // Auto-save on change (Sync across pages)
  function saveState() {
    window.MSUDates.set({
      startDate: inpStart?.value || '',
      startTime: inpStartTime?.value || '',
      endDate: inpEnd?.value || '',
      endTime: inpEndTime?.value || ''
    });
  }

  [inpStart, inpStartTime, inpEnd, inpEndTime].forEach(el => {
    if (el) {
      el.addEventListener('change', saveState);
      el.addEventListener('input', saveState);
    }
  });

  // Auto-run check on load if dates are set
  if (window.MSUDates.isSet()) {
    // Run after a short delay to ensure DOM and other scripts are ready
    setTimeout(() => window.checkRealTimeStock(true), 300);
  }

})();

// ====== Home Page Date Constraints ======
function initHomeDateConstraints() {
  const inpStart = document.getElementById('dateStart');
  const inpEnd = document.getElementById('dateEnd');

  if (!inpStart && !inpEnd) return;

  const now = new Date();
  const y = now.getFullYear();
  const m = String(now.getMonth() + 1).padStart(2, '0');
  const d = String(now.getDate()).padStart(2, '0');
  const today = `${y}-${m}-${d}`;

  if (inpStart) {
    inpStart.min = today;
    inpStart.addEventListener('change', () => {
      if (inpEnd) {
        inpEnd.min = inpStart.value;
        if (inpEnd.value && inpEnd.value < inpStart.value) inpEnd.value = inpStart.value;
      }
    });
  }
  if (inpEnd) inpEnd.min = today;
}

// ====== Setup stok awal ======
function initCards() {
  document.querySelectorAll('.item-card').forEach(card => {
    const sisaEl = card.querySelector('.sisa');
    const titleEl = card.querySelector('.item-title');
    if (!sisaEl) return;

    const type = card.dataset.type || 'barang'; // 'barang' or 'ruang'

    // 1. Tentukan Max Stock
    let max = Number(card.dataset.max);

    if (isNaN(max) || !card.hasAttribute('data-max')) {
      let initial = Number(sisaEl.textContent.trim() || '0');
      if (Number.isNaN(initial)) initial = 0;

      if (type === 'ruang') {
        max = 1;
      } else {
        max = initial;
      }
      card.dataset.max = max;
    }

    // 2. Cek cart
    let inCart = 0;
    if (window.MSUCart) {
      const name = titleEl ? titleEl.textContent.trim() : '';
      const found = window.MSUCart.get().find(it => it.name === name && it.type === type);
      if (found) inCart = Number(found.quantity || 0);
    }

    // 3. Sisa efektif
    let currentSisa = max - inCart;
    if (currentSisa < 0) currentSisa = 0;

    // 4. Update UI
    sisaEl.textContent = String(currentSisa);
    updateBadgeAndButtons(card, currentSisa);
  });
}
initCards();
window.addEventListener('msu:cart-updated', initCards);

function updateBadgeAndButtons(card, sisa) {
  const type = card.dataset.type || 'barang';
  const max = Number(card.dataset.max || 0);
  const badge = card.querySelector('.badge-status');
  const minusBtn = card.querySelector('.qty-btn[data-action="inc"]'); // INC is Minus icon
  const plusBtn = card.querySelector('.qty-btn[data-action="dec"]'); // DEC is Plus icon

  // Global Empty check
  if (max <= 0) {
    if (badge) {
      badge.textContent = 'Habis';
      badge.style.background = '#a94442';
    }
    if (minusBtn) { minusBtn.disabled = true; minusBtn.style.opacity = 0.5; minusBtn.style.cursor = 'not-allowed'; }
    if (plusBtn) { plusBtn.disabled = true; plusBtn.style.opacity = 0.5; plusBtn.style.cursor = 'not-allowed'; }
    return;
  }

  // Normal Logic
  if (badge) {
    if (sisa === 0) {
      // If sisa 0 but max > 0, it means "All in Cart" or "Just Ran Out"
      // User says "Status Habis". Ideally we should distinguish.
      // But keeping existing style:
      badge.textContent = 'Habis';
      badge.style.background = '#a94442';
    } else {
      badge.textContent = 'Active';
      badge.style.background = '#167c73';
    }
  }

  if (type === 'ruang') {
    if (minusBtn) { minusBtn.disabled = (sisa >= 1); minusBtn.style.opacity = minusBtn.disabled ? .6 : 1; }
    if (plusBtn) { plusBtn.disabled = (sisa <= 0); plusBtn.style.opacity = plusBtn.disabled ? .6 : 1; }
  } else {
    // BARANG
    if (minusBtn) {
      // Disabled if we are back to max stock (none in cart)
      minusBtn.disabled = (sisa >= max);
      minusBtn.style.opacity = minusBtn.disabled ? .6 : 1;
    }
    if (plusBtn) {
      // Disabled if no stock left to take
      plusBtn.disabled = (sisa <= 0);
      plusBtn.style.opacity = plusBtn.disabled ? .6 : 1;
    }
  }
}

// ====== Toast util ======
function showToastSuccess(text) {
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
  el.addEventListener('hidden.bs.toast', () => el.remove());
}
function showToastInfo(text) {
  const wrap = document.getElementById('toastStack');
  if (!wrap) return alert(text);
  const id = 'i' + Date.now();
  wrap.insertAdjacentHTML('beforeend', `
    <div id="${id}" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="polite" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body"><i class="bi bi-info-circle me-1"></i>${text}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>`);
  const el = document.getElementById(id);
  const t = new bootstrap.Toast(el, { delay: 2400 });
  t.show();
  el.addEventListener('hidden.bs.toast', () => el.remove());
}

// ====== Modal Konfirmasi Tambah ======
let pendingCard = null;
const confirmModalEl = document.getElementById('confirmAddModal');
const confirmModal = confirmModalEl ? new bootstrap.Modal(confirmModalEl) : null;
const confirmNameEl = document.getElementById('confirmName');
const confirmTypeEl = document.getElementById('confirmType');
const confirmThumbEl = document.getElementById('confirmThumb');

function openConfirm(card) {
  // Jika item SUDAH ada di cart → langsung tambah tanpa modal
  const name = card.querySelector('.item-title')?.textContent?.trim() || 'Item';
  const typeKey = (card.dataset.type || 'barang');
  if (window.MSUCart?.has(name, typeKey)) {
    // Pastikan tanggal dipilih (beri info saja)
    if (!window.MSUDates.isSet()) {
      showToastInfo('Pilih tanggal pakai & durasi untuk cek ketersediaan.');
    }
    confirmAddNoRedirect(card); // langsung eksekusi tambah
    return;
  }

  // Jika belum ada, tampilkan modal konfirmasi
  pendingCard = card;
  const type = (typeKey === 'ruang') ? 'Fasilitas / Ruangan' : 'Barang';
  const thumb = card.querySelector('.item-thumb img')?.getAttribute('src') || '';
  if (confirmNameEl) confirmNameEl.textContent = name;
  if (confirmTypeEl) confirmTypeEl.textContent = type;
  if (confirmThumbEl) confirmThumbEl.src = thumb;
  if (confirmModal) confirmModal.show();
  else if (window.confirm(`Tambah "${name}" ke keranjang?`)) confirmAddNoRedirect(card);
}

document.getElementById('confirmAddBtn')?.addEventListener('click', () => {
  if (pendingCard) {
    confirmAddNoRedirect(pendingCard);
    pendingCard = null;
  }
  if (confirmModal) confirmModal.hide();
});

// Tambah ke keranjang TANPA redirect (bisa dipanggil langsung/dari modal)
function confirmAddNoRedirect(card) {
  if (!card) return;
  const type = card.dataset.type || 'barang';
  const sisaEl = card.querySelector('.sisa');
  let sisa = Number(sisaEl?.textContent.trim() || (type === 'ruang' ? 1 : 0));

  // Kurangi stok tampilan 1
  sisa = Math.max(0, sisa - 1);
  if (sisaEl) sisaEl.textContent = String(sisa);
  updateBadgeAndButtons(card, sisa);

  const name = card.querySelector('.item-title')?.textContent?.trim() || (type === 'ruang' ? 'Ruang' : 'Item');
  const thumb = card.querySelector('.item-thumb img')?.getAttribute('src') || '';

  try {
    if (window.MSUCart) {
      MSUCart.add(name, type, thumb, 1);
      MSUCart.renderBadge();
    }
  } catch (e) { /* ignore */ }

  // Info tanggal
  if (!window.MSUDates.isSet()) {
    showToastInfo('Belum memilih tanggal. Kamu tetap bisa melanjutkan, tapi disarankan pilih tanggal & durasi.');
  } else {
    showToastSuccess(`${name} ditambahkan (periode ${window.MSUDates.formatRange()}).`);
    return;
  }
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
  if (!already) {
    card.classList.add('is-expanded');
    grid.classList.add('has-expanded');
  }
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

  if (action === 'dec') {
    if (!window.MSUDates.isSet()) {
      showToastInfo('Pilih tanggal pakai & durasi untuk cek ketersediaan.');
    }
    openConfirm(card);
    return;
  }

  // − : kembalikan stok tampilan (tidak mempengaruhi cart)
  if (type === 'ruang') {
    sisa = Math.min(1, sisa + 1);
  } else {
    sisa = Math.min(max, sisa + 1);
  }
  sisaEl.textContent = String(sisa);
  updateBadgeAndButtons(card, sisa);
});

// Link to specific page if cart not empty
document.getElementById('fabCheckout')?.addEventListener('click', () => {
  const c = (window.MSUCart ? MSUCart.count() : 0);
  if (c <= 0) return;
  window.location.href = '/form?from=fab';
});

// Inisialisasi badge saat load
window.addEventListener('load', () => {
  if (window.MSUCart) MSUCart.renderBadge();
});

// ====== Search realtime (from barang.js) ======
(function initSearch() {
  const q = document.getElementById('searchInput');
  const clearBtn = document.getElementById('clearSearch');
  const gridEl = document.getElementById('itemsGrid');
  const emptyState = document.getElementById('emptyState');

  function applyFilter() {
    const term = (q?.value || '').trim().toLowerCase();
    if (!gridEl) return;
    let visible = 0;
    gridEl.querySelectorAll('.col').forEach(col => {
      // Find title locally within the card
      const title = col.querySelector('.item-title')?.textContent?.toLowerCase() || '';
      const match = title.includes(term);
      // Toggle visibility of the COLUMN (parent of card)
      if (col.classList.contains('col')) { // Safe check
        col.style.display = match ? '' : 'none';
      }
      if (match) visible++;
    });
    if (emptyState) emptyState.classList.toggle('d-none', visible > 0);
  }

  if (q) q.addEventListener('input', applyFilter);
  if (clearBtn) clearBtn.addEventListener('click', () => {
    if (q) { q.value = ''; applyFilter(); }
  });
})();

/* ====== Live Schedule / Transparency Feature ====== */
// ... (Keeping rest of file same)
// Inject Modal HTML if missing
(function injectScheduleModal() {
  if (document.getElementById('scheduleModal')) return;
  const modalHTML = `
  <div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
        <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #167c73, #125c56);">
          <h5 class="modal-title fw-bold"><i class="bi bi-calendar-event me-2"></i>Jadwal Peminjaman</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <div class="d-flex align-items-center justify-content-between px-4 py-3 bg-light border-bottom">
            <button class="btn btn-sm btn-outline-secondary rounded-circle" id="schedPrevDay"><i class="bi bi-chevron-left"></i></button>
            <div class="fw-bold fs-5 text-dark" id="schedDateDisplay">-</div>
            <button class="btn btn-sm btn-outline-secondary rounded-circle" id="schedNextDay"><i class="bi bi-chevron-right"></i></button>
          </div>
          <div class="p-4" style="min-height: 200px; max-height: 60vh; overflow-y: auto;">
            <div id="schedContent" class="d-flex flex-column gap-3">
              <!-- Content injected here -->
              <div class="text-center text-muted py-5"><span class="spinner-border text-success"></span> Memuat data...</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', modalHTML);
})();

// Logic
(function initScheduleLogic() {
  const modalEl = document.getElementById('scheduleModal');
  const dateDisplay = document.getElementById('schedDateDisplay');
  const contentBox = document.getElementById('schedContent');
  let currentTargetDate = new Date(); // default today

  let myModal = null; // initialized on first open

  async function loadScheduleFor(dateObj) {
    if (!contentBox) return;
    contentBox.innerHTML = '<div class="text-center text-muted py-5"><span class="spinner-border text-success spinner-border-sm"></span> Memuat data...</div>';

    // Update display
    const dateStr = dateObj.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    if (dateDisplay) dateDisplay.textContent = dateStr;

    // Use local time for date string
    const year = dateObj.getFullYear();
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const day = String(dateObj.getDate()).padStart(2, '0');
    const isoDate = `${year}-${month}-${day}`;

    try {
      const res = await fetch(`/api/peminjaman?date=${isoDate}`);
      if (!res.ok) throw new Error("Gagal mengambil data");
      const bookings = await res.json(); // Array of DTOs

      if (bookings.length === 0) {
        contentBox.innerHTML = `
          <div class="text-center py-5 text-muted opacity-75">
            <i class="bi bi-calendar-check fs-1 d-block mb-3 text-success"></i>
            <div>Tidak ada peminjaman tercatat pada tanggal ini.</div>
            <small>Fasilitas dan barang tersedia untuk dipinjam.</small>
          </div>`;
        return;
      }

      // Categorize
      const slots = { pagi: [], siang: [], malam: [] };
      bookings.forEach(b => {
        const timeStart = b.startTime || '00:00:00';
        const h = parseInt(timeStart.split(':')[0], 10);
        if (h >= 6 && h < 12) slots.pagi.push(b);
        else if (h >= 12 && h < 18) slots.siang.push(b);
        else if (h >= 18) slots.malam.push(b);
        else {
          // 00-06 -> Pagi default
          if (h < 12) slots.pagi.push(b);
        }
      });

      const renderCard = (b) => {
        const timeLabel = `${b.startTime?.slice(0, 5) || '??:??'} - ${b.endTime?.slice(0, 5) || '??:??'}`;

        // Define styles based on status
        let statusColor, badgeClass;
        const statusUpper = String(b.status || '').toUpperCase();
        switch (statusUpper) {
          case 'APPROVED':
            statusColor = '#198754'; // success green
            badgeClass = 'bg-success';
            break;
          case 'COMPLETED':
            statusColor = '#0dcaf0'; // info cyan
            badgeClass = 'bg-info text-dark';
            break;
          case 'REJECTED':
            statusColor = '#dc3545'; // danger red
            badgeClass = 'bg-danger';
            break;
          case 'RETURNED':
            statusColor = '#6c757d'; // secondary gray
            badgeClass = 'bg-secondary';
            break;
          case 'PENDING':
          default:
            statusColor = '#ffc107'; // warning yellow
            badgeClass = 'bg-warning text-dark';
            break;
        }

        const itemsHTML = (b.items || []).map(it => `
            <span class="badge bg-light text-dark border fw-normal me-1 mb-1">
               <i class="bi bi-box-seam me-1 text-secondary"></i>${it}
            </span>
         `).join('');

        return `
          <div class="card mb-3 border-0 shadow-sm" style="border-left: 5px solid ${statusColor} !important;">
            <div class="card-body py-3">
              <div class="d-flex justify-content-between align-items-start mb-2">
                 <div>
                    <div class="fw-bold text-dark mb-1" style="font-size:1rem;">${(b.description || 'Kegiatan').charAt(0) + '*'}</div>
                    <div class="text-muted small"><i class="bi bi-clock me-1"></i>${timeLabel}</div>
                 </div>
                 <span class="badge ${badgeClass} text-uppercase shadow-sm" style="font-size:0.7rem; letter-spacing:0.5px;">${b.status}</span>
              </div>
              
              <div class="d-flex flex-wrap align-items-center gap-1 mb-2">
                 ${itemsHTML || '<small class="text-muted fst-italic">Tanpa detail items</small>'}
              </div>

              <div class="border-top pt-2 mt-2 d-flex justify-content-between align-items-center">
                 <div class="small text-muted">
                    <i class="bi bi-person me-1"></i>${(b.borrowerName || 'Peminjam').charAt(0) + '*'}
                 </div>
                 <div class="small fw-bold text-secondary text-uppercase" style="font-size:0.7rem">
                    ${(b.department || '').charAt(0) + '*'}
                 </div>
              </div>
            </div>
          </div>`;
      };

      const renderSection = (title, items) => {
        // Always render section title, but if empty show placeholder
        const list = items.length > 0 ? items.map(renderCard).join('') : '<div class="text-muted fst-italic small ms-2">- Kosong -</div>';
        return `
           <div class="mb-4">
             <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">${title}</h6>
             ${list}
           </div>
         `;
      };

      contentBox.innerHTML = `
        ${renderSection('Pagi (06.00 - 12.00)', slots.pagi)}
        ${renderSection('Siang (12.00 - 18.00)', slots.siang)}
        ${renderSection('Malam (18.00 - 20.00)', slots.malam)}
      `;

    } catch (err) {
      console.error(err);
      contentBox.innerHTML = `<div class="text-center text-danger py-4"><i class="bi bi-exclamation-circle me-2"></i> Gagal memuat data jadwal.</div>`;
    }
  }

  // Global method to open
  window.openScheduleModal = function () {
    let d = new Date();
    const saved = window.MSUDates?.get();
    if (saved && saved.startDate) {
      d = new Date(saved.startDate);
    }
    currentTargetDate = d;

    if (!myModal && modalEl) myModal = new bootstrap.Modal(modalEl);
    if (myModal) myModal.show();

    loadScheduleFor(currentTargetDate);
  }

  // Prev/Next handlers
  document.getElementById('schedPrevDay')?.addEventListener('click', () => {
    currentTargetDate.setDate(currentTargetDate.getDate() - 1);
    loadScheduleFor(currentTargetDate);
  });
  document.getElementById('schedNextDay')?.addEventListener('click', () => {
    currentTargetDate.setDate(currentTargetDate.getDate() + 1);
    loadScheduleFor(currentTargetDate);
  });

  // Attach to button if exists (auto init)
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('#btnShowCalendar');
    if (btn) {
      window.openScheduleModal();
    }
  });
})();
