/* =======================
   booking-barang.js (FULL)
   ======================= */

/* ---------- Bootstrap UI ---------- */
window.addEventListener('DOMContentLoaded', () => {
    initUI(); // Initialize UI elements
    initDateConstraints(); // Validation logic

    /* Setup Modal Konfirmasi Hapus */
    const delModalEl = document.getElementById('confirmDeleteModal');
    let delModalFn = null; // callback action
    let delModalInst = null;
    if (delModalEl) {
        delModalInst = new bootstrap.Modal(delModalEl);
        document.getElementById('btnConfirmDelAction')?.addEventListener('click', () => {
            if (delModalFn) delModalFn();
            delModalInst.hide();
        });
    }

    // Modal Validation (Dynamic Inject)
    window.showValidationModal = function (msg) {
        let el = document.getElementById('valModal');
        if (!el) {
            const html = `
            <div class="modal fade" id="valModal" tabindex="-1" style="z-index:9999">
               <div class="modal-dialog modal-dialog-centered modal-sm">
                 <div class="modal-content border-0 shadow-lg rounded-4">
                   <div class="modal-body text-center p-4">
                     <div class="text-danger mb-2"><i class="bi bi-exclamation-circle" style="font-size:3rem"></i></div>
                     <h5 class="fw-bold">Perhatian</h5>
                     <p class="text-muted small mb-4" id="valModalMsg"></p>
                     <button type="button" class="btn btn-danger w-100 rounded-pill" data-bs-dismiss="modal">Mengerti</button>
                   </div>
                 </div>
               </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
            el = document.getElementById('valModal');
        }
        document.getElementById('valModalMsg').textContent = msg;
        
        // Fix: Use getOrCreateInstance to avoid multiple backdrops
        const modal = bootstrap.Modal.getOrCreateInstance(el);
        modal.show();
    };
    // Global helper agar bisa dipanggil di mana saja
    window.openDelConfirm = function (title, msg, onConfirm) {
        if (!delModalInst) {
            if (confirm(msg)) onConfirm(); // Fallback
            return;
        }
        document.getElementById('confirmDelTitle').textContent = title;
        document.getElementById('confirmDelMsg').textContent = msg;
        delModalFn = onConfirm;
        delModalInst.show();
    };

    /* Hapus semua cart */
    document.getElementById('clearCartBtn')?.addEventListener('click', () => {
        window.openDelConfirm(
            'Hapus Semua?',
            'Apakah anda ingin menghapus semua barang dari keranjang?',
            () => {
                if (window.MSUCart) MSUCart.clear();
                renderCartList();
                buildTabsFromCart();
                window.MSUCart?.renderBadge();
            }
        );
    });
});


/* ---------- Date Logic ---------- */
function initDateConstraints() {
    // 1. Get Today in Local YYYY-MM-DD
    const now = new Date();
    const y = now.getFullYear();
    const m = String(now.getMonth() + 1).padStart(2, '0');
    const d = String(now.getDate()).padStart(2, '0');
    const today = `${y}-${m}-${d}`;

    const setMin = (el, val) => { if (el) el.min = val; };

    // 2. Set Min Date
    setMin(loanDate, today);
    setMin(loanDateEnd, today);

    // 3. Logic: EndDate >= StartDate
    if (loanDate) {
        loanDate.addEventListener('change', () => {
            if (loanDateEnd) {
                // Dimatikan tanggal sebelum StartDate
                loanDateEnd.min = loanDate.value;
                // Jika EndDate jadi invalid (sebelum Start), reset/samakan
                if (loanDateEnd.value && loanDateEnd.value < loanDate.value) {
                    loanDateEnd.value = loanDate.value;
                }
            }
            // Trigger check
            if (typeof checkRealtimeAvailability === 'function') checkRealtimeAvailability();
        });
    }
}

/* ---------- Util ---------- */
function toRupiah(n) { return new Intl.NumberFormat('id-ID').format(Number(n || 0)); }
function todayISO() {
    const t = new Date(); t.setHours(0, 0, 0, 0);
    return t.toISOString().split('T')[0];
}

/* ---------- Donasi Quick Fill ---------- */
document.querySelectorAll('.btn-donasi').forEach(btn => {
    btn.addEventListener('click', () => {
        const amt = btn.getAttribute('data-amt');
        const input = document.getElementById('donationAmount');
        if (input) input.value = amt;
    });
});

/* ---------- PANEL KIRI: Tabs Horizontal Multi-Item ---------- */
let tabsUL, tabsContent, listEl;

function initUI() {
    if (!tabsUL) tabsUL = document.getElementById('itemTabs');
    if (!tabsContent) tabsContent = document.getElementById('itemTabContent');
    if (!listEl) listEl = document.getElementById('cartList');
}

/* ---------- CART RENDER (list ke bawah) ---------- */
function renderCartList() {
    initUI();
    if (listEl && window.MSUCart) {
        listEl.innerHTML = MSUCart.toListHTML();  // sudah berbentuk <ul><li> list ke bawah
    }
}

/* Gambar default jika tidak ada thumb */
function fallbackThumbFor(name) {
    const lower = (name || '').toLowerCase();
    if (lower.includes('proyektor')) return 'proyektor.jpeg';
    if (lower.includes('sound')) return 'sound.jpeg';
    if (lower.includes('karpet')) return 'karpet.jpeg';
    if (lower.includes('terpal')) return 'terpal.jpeg';
    return 'https://placehold.co/600x400';
}

/* Booked dates berbeda per barang (contoh) */
function getBookedDaysFor(itemName, y, m) {
    // MAPPING CONTOH: (Diset kosong dulu agar user bisa leluasa tes tanggal)
    // - Proyektor: tanggal 5,12,19
    // - Sound System: 7,14,21
    // - Karpet: 3,9,27
    const base = (itemName || '').toLowerCase();
    // if (base.includes('proyektor')) return [5, 12, 19];
    // if (base.includes('sound')) return [7, 14, 21];
    // if (base.includes('karpet')) return [3, 9, 27];
    // default: kosong
    return [];
}

function isToday(y, m, d) {
    const t = new Date();
    return y === t.getFullYear() && m === t.getMonth() && d === t.getDate();
}

/* ---------- Dummy booking list (1/3 hari) ---------- */
function getBookingsFor(itemName, y, m, day) {
    // Contoh: data dummy untuk beberapa tanggal di November 2025
    const res = [];
    if (y === 2025 && m === 10 && day === 7) {
        res.push(
            { slot: 'Pagi (1/3)', kegiatan: 'Latihan Paduan Suara', pj: 'UKM PSM' },
            { slot: 'Siang (2/3)', kegiatan: 'Lomba Cerdas Cermat', pj: 'Panitia Acara Kampus' }
        );
    } else if (y === 2025 && m === 10 && day === 12) {
        res.push(
            { slot: 'Pagi (1/3)', kegiatan: 'Briefing Panitia Kajian Akbar', pj: 'DKM MSU' },
            { slot: 'Malam (3/3)', kegiatan: 'Gladi Bersih Acara', pj: 'Panitia Acara Kampus' }
        );
    } else if (y === 2025 && m === 10 && day === 19) {
        res.push(
            { slot: 'Siang (2/3)', kegiatan: 'Latihan Tari', pj: 'UKM Seni Tari' }
        );
    }
    return res;
}

/* Mapping label → jam */
function slotTime(label) {
    const lower = (label || '').toLowerCase();
    if (lower.startsWith('pagi')) return '07.00 – 12.00';
    if (lower.startsWith('siang')) return '12.00 – 17.00';
    if (lower.startsWith('malam')) return '17.00 – 22.00';
    return '';
}

/* Render booking list di bawah kalender */
/* Render booking list di bawah kalender */
async function renderBookingList(container, itemName, y, m, day) {
    const box = container.querySelector('.booking-list-body');
    const headerTitle = container.querySelector('.booking-list-header .bl-title'); // "Peminjaman (1/3 hari)"
    const headerDate = container.querySelector('.booking-list-header .date-label');

    if (headerTitle) headerTitle.textContent = 'Peminjaman'; // Request: "Peminjaman" only
    if (!box) return;

    // Set header date
    const dObj = new Date(y, m, day);
    const label = dObj.toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });
    if (headerDate) headerDate.textContent = label;

    box.innerHTML = '<div class="text-center text-muted py-3"><span class="spinner-border spinner-border-sm text-success"></span> Memuat...</div>';

    try {
        const mon = String(m + 1).padStart(2, '0');
        const dd = String(day).padStart(2, '0');
        const iso = `${y}-${mon}-${dd}`;

        const res = await fetch(`/api/peminjaman?date=${iso}`);
        if (!res.ok) throw new Error('Network err');
        const allBookings = await res.json();

        // Filter relevant items
        const relevant = allBookings.filter(b => {
            if (!b.items) return false;
            return b.items.some(it => it.toLowerCase().includes((itemName || '').toLowerCase()));
        });

        // Buckets
        const slots = {
            pagi: [],   // 06:00 - 12:00
            siang: [],  // 12:00 - 18:00
            malam: []   // 18:00 - 20:00 (or later)
        };

        relevant.forEach(b => {
            // Parse time
            const sTime = b.startTime || '00:00:00';
            const hour = parseInt(sTime.split(':')[0], 10);

            if (hour >= 6 && hour < 12) {
                slots.pagi.push(b);
            } else if (hour >= 12 && hour < 18) {
                slots.siang.push(b);
            } else if (hour >= 18) {
                slots.malam.push(b);
            } else {
                // Early morning? Default to Pagi checking logic or ignore. 
                // Assuming 00-06 is rare, put in Pagi or separate?
                // Let's put < 6 in Pagi for safety or check overlap. 
                // Simple logic for now: < 12 is Pagi.
                if (hour < 12) slots.pagi.push(b);
            }
        });

        const renderItem = (b) => {
            const timeStr = `${b.startTime?.slice(0, 5) || '??:??'} - ${b.endTime?.slice(0, 5) || '??:??'}`;
            // Find specific item string: e.g. "Karpet (2)"
            const matchedItemStr = (b.items || []).find(it => it.toLowerCase().includes((itemName || '').toLowerCase())) || '';

            return `
               <div class="mb-2 p-2 border rounded bg-white shadow-sm" style="border-left: 4px solid #198754 !important;">
                 <div class="d-flex justify-content-between align-items-start">
                    <div class="fw-bold text-dark">${b.description || 'Kegiatan'}</div>
                    <div class="fw-bold text-dark">${timeStr}</div>
                 </div>
                 <div class="d-flex justify-content-between mt-1 text-muted small" style="font-size:0.85rem">
                    <div>
                        ${b.borrowerName || 'Peminjam'} 
                        <span class="text-success fw-bold ms-2" style="font-size:0.8rem">
                           <i class="bi bi-box-seam me-1"></i>${matchedItemStr}
                        </span>
                    </div>
                    <div class="text-uppercase" style="font-size:0.75rem">${b.status}</div>
                 </div>
               </div>
             `;
        };

        const renderSection = (title, items) => {
            const content = items.length > 0
                ? items.map(renderItem).join('')
                : '<div class="text-muted small fst-italic py-1">- Kosong -</div>';

            return `
               <div class="mb-3">
                 <h6 class="fw-bold text-secondary mb-2" style="font-size:0.9rem; border-bottom:1px solid #eee; padding-bottom:4px;">
                    ${title}
                 </h6>
                 ${content}
               </div>
             `;
        };

        if (relevant.length === 0) {
            box.innerHTML = `<div class="booking-list-empty">Belum ada peminjaman tercatat untuk item ini pada tanggal ini.</div>`;
        } else {
            box.innerHTML = `
               ${renderSection('Pagi (06.00 - 12.00)', slots.pagi)}
               ${renderSection('Siang (12.00 - 18.00)', slots.siang)}
               ${renderSection('Malam (18.00 - 20.00)', slots.malam)}
            `;
        }

    } catch (e) {
        console.error(e);
        box.innerHTML = `<div class="text-danger small">Gagal memuat data.</div>`;
    }
}

/* Render mini kalender spesifik barang */
function renderCalendarFor(container, itemName, refDate) {
    const calTitle = container.querySelector('.cal-title');
    const calGrid = container.querySelector('.calendar-grid');
    const y = refDate.getFullYear(), m = refDate.getMonth();

    const first = new Date(y, m, 1);
    const startDay = (first.getDay() + 6) % 7; // Senin=0
    const daysInMonth = new Date(y, m + 1, 0).getDate();
    const prevDays = new Date(y, m, 0).getDate();
    const booked = new Set(getBookedDaysFor(itemName, y, m));

    if (calTitle) {
        calTitle.textContent = refDate.toLocaleDateString('id-ID', {
            month: 'long', year: 'numeric'
        });
    }

    let html = '';
    'S N S R K J S'.split(' ').forEach(h => html += `<span class="muted">${h}</span>`);
    for (let i = startDay; i > 0; i--) {
        html += `<span class="muted">${prevDays - i + 1}</span>`;
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const cls = [
            'day',
            isToday(y, m, d) ? 'today' : '',
            booked.has(d) ? 'booked' : ''
        ].join(' ').trim();
        html += `<span class="${cls}" data-day="${d}">${d}</span>`;
    }
    calGrid.innerHTML = html;

    const dayCells = calGrid.querySelectorAll('.day');

    dayCells.forEach(el => {
        el.addEventListener('click', () => {
            const d = Number(el.dataset.day);
            dayCells.forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');
            renderBookingList(container, itemName, y, m, d);
        });
    });

    // Default: pilih hari ini / hari pertama yang terbooking / tgl 1
    let defaultDay = null;
    const today = new Date();
    if (today.getFullYear() === y && today.getMonth() === m) {
        defaultDay = today.getDate();
    }
    if (!defaultDay) {
        for (let d = 1; d <= daysInMonth; d++) {
            if (booked.has(d)) { defaultDay = d; break; }
        }
    }
    if (!defaultDay) defaultDay = 1;

    const defCell = calGrid.querySelector(`.day[data-day="${defaultDay}"]`);
    if (defCell) {
        defCell.classList.add('selected');
        renderBookingList(container, itemName, y, m, defaultDay);
    } else {
        const box = container.querySelector('.booking-list-body');
        const headerDate = container.querySelector('.booking-list-header .date-label');
        if (headerDate) {
            headerDate.textContent = new Date(y, m, 1).toLocaleDateString('id-ID', {
                month: 'long', year: 'numeric'
            });
        }
        if (box) {
            box.innerHTML = `<div class="booking-list-empty">
        Belum ada peminjaman tercatat pada bulan ini.
      </div>`;
        }
    }
}

/* Build satu panel barang (isi tab) */
/* Build satu panel barang (isi tab) */
function buildItemPanelHTML(item) {
    const name = item.name || 'Barang';
    const thumb = item.imageUrl || item.thumb || fallbackThumbFor(name);
    const qty = Number(item.quantity || item.qty || 0);
    const max = (item.maxQty !== undefined && item.maxQty !== null) ? Number(item.maxQty) : 999;
    const isRuang = (item.type === 'ruang');
    const effectiveMax = isRuang ? 1 : max;
    const disablePlus = (qty >= effectiveMax);

    return `
    <div class="item-panel">
      <div class="summary-thumb mb-3">
        <img src="${thumb}" alt="${name}">
        <span class="badge-status">Active</span>
      </div>
      <div class="text-center">
        <div class="title h4 mb-1">${name}</div>
        <div class="text-muted small mb-3">${isRuang ? 'Fasilitas / Ruangan' : 'Barang Inventaris'}</div>

        <div class="d-flex justify-content-center align-items-center gap-3 mb-3">
           <button class="btn btn-sm btn-outline-secondary rounded-circle btn-qminus" 
                   style="width:32px;height:32px"><i class="bi bi-dash"></i></button>
           <span class="fw-bold fs-5 qty-display qty-display-text" style="min-width:40px">${qty}</span>
           <button class="btn btn-sm btn-outline-secondary rounded-circle btn-qplus" 
                   style="width:32px;height:32px" ${disablePlus ? 'disabled' : ''}><i class="bi bi-plus"></i></button>
        </div>
        <div class="text-muted small mb-3 stock-limit-text">
             ${isRuang ? '(Maks 1)' : `(Stok tersedia: ${effectiveMax})`}
        </div>
      </div>

      <div class="mini-calendar mt-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <button class="cal-nav cal-prev" type="button" aria-label="Bulan sebelumnya"><i class="bi bi-chevron-left"></i></button>
          <strong class="cal-title">-</strong>
          <button class="cal-nav cal-next" type="button" aria-label="Bulan berikutnya"><i class="bi bi-chevron-right"></i></button>
        </div>
        <div class="calendar-legend mb-2">
          <span class="legend-box booked"></span><small class="ms-1 me-3">Terbooking/Habis</small>
          <span class="legend-box today"></span><small class="ms-1">Hari ini</small>
        </div>
        <div class="calendar-grid" aria-hidden="true"></div>
      </div>

      <div class="booking-list mt-3">
        <div class="booking-list-header">
          <span class="bl-title">Peminjaman (1/3 hari)</span>
          <span class="date-label"></span>
        </div>
        <div class="booking-list-body mt-2 small"></div>
      </div>
    </div>
  `;
}

/* ---------- Build Tabs dari Keranjang ---------- */
function buildTabsFromCart() {
    initUI();
    if (!tabsUL || !tabsContent) return;
    const cart = (window.MSUCart && MSUCart.get()) || [];

    // Kosongkan dulu
    tabsUL.innerHTML = '';
    tabsContent.innerHTML = '';

    if (!cart.length) {
        tabsContent.innerHTML = `
      <div class="p-3 text-center text-muted border rounded-3">
        Keranjang kosong. Silakan pilih barang/ruang dari halaman sebelumnya.
      </div>`;
        return;
    }

    cart.forEach((it, idx) => {
        const tabId = `tab-${idx}`;
        const panelId = `panel-${idx}`;

        // Tab header (horizontal)
        const li = document.createElement('li');
        li.className = 'nav-item';
        li.innerHTML = `
      <button class="nav-link ${idx === 0 ? 'active' : ''}" id="${tabId}"
              data-bs-toggle="tab" data-bs-target="#${panelId}" type="button" role="tab"
              aria-controls="${panelId}" aria-selected="${idx === 0}">
        ${it.name}
        <span class="badge text-bg-success ms-2">${it.quantity || it.qty || 0}x</span>
      </button>
    `;
        tabsUL.appendChild(li);

        // Panel body
        const pane = document.createElement('div');
        pane.className = `tab-pane fade ${idx === 0 ? 'show active' : ''}`;
        pane.id = panelId;
        pane.setAttribute('role', 'tabpanel');
        pane.setAttribute('aria-labelledby', tabId);

        // Simpan nama item & refDate sebagai state panel
        pane.dataset.itemName = it.name;
        pane.dataset.itemType = it.type;
        pane.dataset.refDate = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString();

        pane.innerHTML = buildItemPanelHTML(it);
        tabsContent.appendChild(pane);
    });

    // Inisialisasi kalender & handler tiap panel
    initPanels();
    // Sinkronkan validasi tanggal sesuai tab aktif
    syncDateBlockWithActiveItem();
    // Render ringkasan keranjang kanan
    renderCartList();

    // TRIGGER CHECK NOW that elements exist
    if (typeof checkRealtimeAvailability === 'function') {
        checkRealtimeAvailability();
    }
}

/* ---------- Helper: Panel aktif & nama item aktif ---------- */
function getActivePanel() {
    return tabsContent.querySelector('.tab-pane.active.show') ||
        tabsContent.querySelector('.tab-pane.active') || null;
}
function getActiveItemName() {
    const pane = getActivePanel();
    return pane ? (pane.dataset.itemName || '') : '';
}

/* ---------- Kalender per-panel & qty ---------- */
function initPanels() {
    tabsContent.querySelectorAll('.tab-pane').forEach(pane => {
        const name = pane.dataset.itemName || '';
        const type = pane.dataset.itemType || 'barang';
        // Ref date dari dataset
        let ref = new Date(pane.dataset.refDate || (new Date().toISOString()));
        // Render pertama
        renderCalendarFor(pane, name, ref);

        // Nav prev/next
        const prevBtn = pane.querySelector('.cal-prev');
        const nextBtn = pane.querySelector('.cal-next');
        prevBtn?.addEventListener('click', () => {
            ref.setMonth(ref.getMonth() - 1);
            pane.dataset.refDate = new Date(ref.getFullYear(), ref.getMonth(), 1).toISOString();
            renderCalendarFor(pane, name, ref);
            if (pane.classList.contains('active')) syncDateBlockWithActiveItem();
        });
        nextBtn?.addEventListener('click', () => {
            ref.setMonth(ref.getMonth() + 1);
            pane.dataset.refDate = new Date(ref.getFullYear(), ref.getMonth(), 1).toISOString();
            renderCalendarFor(pane, name, ref);
            if (pane.classList.contains('active')) syncDateBlockWithActiveItem();
        });

        // Qty +/- per panel → update cart
        const minus = pane.querySelector('.btn-qminus');
        const plus = pane.querySelector('.btn-qplus');
        const qtyBox = pane.querySelector('.qty-display');
        const qtyText = pane.querySelector('.qty-display-text');

        function setQty(newQty) {
            const cart = (window.MSUCart && MSUCart.get()) || [];
            const item = cart.find(it => it.name === name);
            const max = item ? (item.maxQty || 999) : 999;
            const isRuang = (type === 'ruang');
            let effectiveMax = isRuang ? 1 : max;

            // Check Dynamic Max from real-time API
            if (pane.dataset.dynamicMax) {
                const dm = Number(pane.dataset.dynamicMax);
                if (!isNaN(dm)) effectiveMax = dm;
            }

            let clean = Number(newQty || 0);
            if (clean < 0) clean = 0;
            if (clean > effectiveMax) clean = effectiveMax;

            qtyBox.textContent = String(clean);
            qtyText.textContent = String(clean);

            // Update disabled state of plus button
            if (plus) plus.disabled = (clean >= effectiveMax);

            // Update badge pada tab header
            const index = Array.from(tabsContent.children).findIndex(pp => pp === pane);
            const tabButton = document.getElementById(index >= 0 ? `tab-${index}` : '');
            if (tabButton) {
                const badge = tabButton.querySelector('.badge');
                if (badge) badge.textContent = `${clean}x`;
            }

            // Update cart
            MSUCart?.update(name, type, clean);
            MSUCart?.renderBadge();
            renderCartList();

            // Jika qty=0 → rebuild tabs biar panel menghilang
            if (clean === 0) {
                buildTabsFromCart();
            }
        }

        minus?.addEventListener('click', () => {
            const current = Number(qtyBox.textContent || 0);
            if (current <= 1) {
                // Konfirmasi dulu sebelum jadi 0 (hapus)
                window.openDelConfirm(
                    'Hapus Item?',
                    `Apakah anda ingin menghapus "${name}" dari keranjang?`,
                    () => setQty(0)
                );
            } else {
                setQty(current - 1);
            }
        });
        plus?.addEventListener('click', () => {
            const current = Number(qtyBox.textContent || 0);
            setQty(current + 1);
        });


    });

    // Saat ganti tab → sinkron blok tanggal sesuai item aktif
    tabsUL.querySelectorAll('[data-bs-toggle="tab"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', () => {
            syncDateBlockWithActiveItem();
        });
    });
}

/* ---------- Real-time Availability Check ---------- */
/* ---------- Real-time Availability Check ---------- */
const loanDate = document.getElementById('loanDate');
const loanTimeStart = document.getElementById('loanTimeStart');
const loanDateEnd = document.getElementById('loanDateEnd');
const loanTimeEnd = document.getElementById('loanTimeEnd');

async function checkRealtimeAvailability() {
    const sDate = loanDate?.value;
    const sTime = loanTimeStart?.value;
    const eDate = loanDateEnd?.value;
    const eTime = loanTimeEnd?.value;

    if (!sDate || !sTime || !eDate || !eTime) return;

    // Client-side Validation: Past Time
    const startDateTime = new Date(`${sDate}T${sTime}`);
    const now = new Date();
    if (startDateTime < now) {
        showValidationModal("Tanggal/Jam peminjaman sudah terlewat.");

        // Reset Inputs
        if (loanDate) loanDate.value = '';
        if (loanTimeStart) loanTimeStart.value = '';
        if (loanDateEnd) loanDateEnd.value = '';
        if (loanTimeEnd) loanTimeEnd.value = '';

        return;
    }

    // Client-side Validation: Start >= End
    if (eDate && eTime) {
        const endDateTime = new Date(`${eDate}T${eTime}`);
        if (startDateTime >= endDateTime) {
            showValidationModal("Jam Berakhir harus lebih lambat dari Jam Mulai.");

            // Reset Inputs
            if (loanDate) loanDate.value = '';
            if (loanTimeStart) loanTimeStart.value = '';
            if (loanDateEnd) loanDateEnd.value = '';
            if (loanTimeEnd) loanTimeEnd.value = '';

            return;
        }
    }

    try {
        const q = new URLSearchParams({
            startDate: sDate,
            startTime: sTime,
            endDate: eDate,
            endTime: eTime
        });
        const url = `/api/peminjaman/check?${q.toString()}`;
        const res = await fetch(url);
        if (!res.ok) throw new Error('Availability check failed');

        const availabilityData = await res.json(); // Array of {itemId, itemName, available}

        console.log("checkRealtimeAvailability: Data received", availabilityData);

        // --- UPDATE UI PANELS ---
        const tabsContent = document.getElementById('itemTabContent');
        if (!tabsContent) return;

        // Iterate over all panels (items in cart)
        Array.from(tabsContent.querySelectorAll('.tab-pane')).forEach(pane => {
            const rawName = pane.dataset.itemName;
            const type = pane.dataset.itemType;
            if (!rawName) return;

            const normalizedName = rawName.trim().toLowerCase();

            // Robust Match
            const stockInfo = availabilityData.find(s => (s.itemName || '').trim().toLowerCase() === normalizedName);
            let realAvailable = 999;

            if (stockInfo) {
                realAvailable = stockInfo.available;
            }

            // Ruangan max always 1
            if (type === 'ruang') {
                realAvailable = (realAvailable > 0) ? 1 : 0;
            }

            console.log(`Checking ${rawName}: RealAvailable=${realAvailable}`);

            // Update Dataset for immediate enforcement
            pane.dataset.dynamicMax = realAvailable;

            // Update Cart Persistence so re-renders know the truth
            const currentQty = Number(pane.querySelector('.qty-display-text')?.textContent || 0);
            if (MSUCart) {
                // Pass realAvailable as maxQty
                MSUCart.update(rawName, type, currentQty, realAvailable);
            }

            // Update Text "Stok tersedia: X"
            // Use the specific class we added
            const limitEl = pane.querySelector('.stock-limit-text');
            if (limitEl) {
                if (type === 'ruang') {
                    limitEl.textContent = '(Maks 1)';
                } else {
                    limitEl.textContent = `(Stok tersedia: ${realAvailable})`;
                }
            }

            // Auto-clamp if current > realAvailable
            // This prevents "4" showing if only "2" available
            // Disable auto-remove if realAvailable is 0
            // logic: if realAvailable=0, we keep the item in cart but mark it red/disabled
            // so user knows it's out of stock for that date.

            if (realAvailable === 0) {
                const qtyBox = pane.querySelector('.qty-display');
                const qtyText = pane.querySelector('.qty-display-text');

                // Show 0 or keep current? 
                // If we show 0 but don't remove from cart (which requires qty>0), 
                // we might desire to keep it as "1" but invalid.
                // But MSUCart logic: qty=0 => delete.
                // So we keep qty as current (e.g. 1) but visual warning.

                if (qtyBox) qtyBox.classList.add('text-danger');
                const limitEl = pane.querySelector('.stock-limit-text');
                if (limitEl) {
                    limitEl.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-x-circle"></i> Stok Habis!</span>';
                }

                // Disable plus button
                const plus = pane.querySelector('.btn-qplus');
                if (plus) plus.disabled = true;

                // Do NOT update MSUCart to 0 here to prevent disappearance.
                // Instead, we rely on invalid-feedback or disabling submit elsewhere?
                // For now, let's just NOT clamp to 0.
            }
            else if (currentQty > realAvailable) {
                // Determine new valid quantity
                const qtyBox = pane.querySelector('.qty-display');
                const qtyText = pane.querySelector('.qty-display-text');
                if (qtyBox) {
                    qtyBox.textContent = String(realAvailable);
                    qtyBox.classList.remove('text-danger');
                }
                if (qtyText) qtyText.textContent = String(realAvailable);

                if (MSUCart) MSUCart.update(rawName, type, realAvailable, realAvailable);
            } else {
                // Restoration if it became available again
                const qtyBox = pane.querySelector('.qty-display');
                if (qtyBox) qtyBox.classList.remove('text-danger');
            }

            // Update buttons enablement
            const plus = pane.querySelector('.btn-qplus');
            if (plus) plus.disabled = (currentQty >= realAvailable); else {
                console.warn("Stock limit text element not found for", rawName);
            }
            pane.dataset.dynamicMax = realAvailable;
        });

    } catch (e) {
        console.error("Error checking availability:", e);
    }
}

// Add listeners
loanDate?.addEventListener('change', checkRealtimeAvailability);
loanTimeStart?.addEventListener('change', checkRealtimeAvailability);
loanDateEnd?.addEventListener('change', checkRealtimeAvailability);
loanTimeEnd?.addEventListener('change', checkRealtimeAvailability);

// Also trigger on load if date exists (with short delay to ensure rendering)
if (loanDate?.value && loanTimeStart?.value) {
    // Only if tabs exist
    if (document.getElementById('itemTabContent')?.children?.length) {
        checkRealtimeAvailability();
    }
}

/* Legacy syncing (keeping it for non-blocking UI sync if needed, but the real check is above) */
function syncDateBlockWithActiveItem() {
    // Legacy local check code removed or minimized
    if (!loanDate) return;
    const today = new Date(); today.setHours(0, 0, 0, 0);
    loanDate.min = today.toISOString().split('T')[0];
}

/* =========================================================
   SUBMIT VIA MAILTO (Tanpa backend) — kirim ke email peminjam
   ========================================================= */

/* Konfigurasi admin untuk cc/bcc (opsional) */
const MAIL_CC_ADMIN = 'admin@msu.ac.id';   // kosongkan '' jika tidak perlu CC
const MAIL_BCC_ADMIN = '';                  // isi jika ingin BCC

/* Build mailto URL */
function buildMailtoURL({ to, subject, body, cc = '', bcc = '' }) {
    const params = [];
    if (cc) params.push('cc=' + encodeURIComponent(cc));
    if (bcc) params.push('bcc=' + encodeURIComponent(bcc));
    if (subject) params.push('subject=' + encodeURIComponent(subject));
    if (body) params.push('body=' + encodeURIComponent(body));
    const query = params.join('&');
    return `mailto:${encodeURIComponent(to)}${query ? '?' + query : ''}`;
}

/* ---------- Submit: sertakan semua item & donasi → buka draft email ---------- */
/* ---------- Validasi & Modal Helper (Livewire Friendly) ---------- */
(function setupLivewireInteractions() {
    const form = document.getElementById('bookingForm');
    const btnSubmit = document.getElementById('btnSubmit'); // Type button now
    const reqInput = document.getElementById('requirements');
    const ktpInput = document.getElementById('ktpUpload'); // Livewire uses wire:model, but we still check validity visually

    // Helper: Donation Buttons
    document.querySelectorAll('.btn-donasi').forEach(btn => {
        btn.addEventListener('click', () => {
            const amt = btn.getAttribute('data-amt');
            const input = document.getElementById('donationAmount');
            if (input) {
                input.value = amt;
                input.dispatchEvent(new Event('input')); // Trigger Livewire update
            }
        });
    });

    function validateForm() {
        if (!form) return false;

        // Native validity check
        const requiredValid = [...form.querySelectorAll('[required]')].every(el => {
            // For file inputs, Livewire wires them. If they have value/files, it's good.
            if (el.type === 'file') return el.files.length > 0;
            return el.value && el.checkValidity();
        });

        const cartHasQty = (window.MSUCart?.count() || 0) > 0;

        // Update Submit Button State (Optional - Livewire handles this too via loading)
        // But we want to prevent clicking if form invalid
        if (btnSubmit) {
            btnSubmit.disabled = !(requiredValid && cartHasQty);
        }
        return requiredValid && cartHasQty;
    }

    form?.addEventListener('input', validateForm);
    form?.addEventListener('change', validateForm);

    // Initial validation check
    validateForm();

    // Handle "Kirim Booking" button click (Opens Modal)
    btnSubmit?.addEventListener('click', () => {
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            validateForm(); // Re-check to update UI

            // Scroll to first invalid
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });

            return;
        }

        // Check Logic Waktu Client Side (Optional double check)
        // ... (Time validation logic can stay if needed, simplified here)

        // Show Confirmation Modal
        const confirmSubmitModalEl = document.getElementById('confirmSubmitModal');
        const confirmSubmitModal = confirmSubmitModalEl ? new bootstrap.Modal(confirmSubmitModalEl) : null;
        if (confirmSubmitModal) {
            confirmSubmitModal.show();
        } else {
            // Fallback if modal broken: Trigger Livewire direct?
            // Since button inside modal has wire:click, if modal fails, we strictly can't submit via that button.
            // But we can try finding the real submit button if it existed. 
            alert("Error: Modal konfirmasi tidak ditemukan.");
        }
    });

    // File validation limit (Client side visual only)
    const max = 10 * 1024 * 1024;
    [reqInput, ktpInput].forEach(el => {
        if (!el) return;
        el.addEventListener('change', () => {
            if (el.files[0] && el.files[0].size > max) {
                alert('Ukuran file maksimal 10MB.');
                el.value = '';
                validateForm();
            }
        });
    });

    // Cancel Button
    document.getElementById('btnCancel')?.addEventListener('click', () => {
        if (confirm('Batalkan pengisian form dan kembali ke halaman sebelumnya?')) {
            window.location.href = '/barang'; // Route URL
        }
    });

    // Restore Data form LocalStorage (msu_dates_v2) if available
    function restoreDates() {
        try {
            const meta = JSON.parse(localStorage.getItem('msu_dates_v2') || '{}');
            const map = {
                'loanDate': meta.startDate,
                'loanTimeStart': meta.startTime,
                'loanDateEnd': meta.endDate,
                'loanTimeEnd': meta.endTime
            };
            Object.keys(map).forEach(id => {
                if (map[id]) {
                    const el = document.getElementById(id);
                    if (el) {
                        el.value = map[id];
                        el.dispatchEvent(new Event('input')); // Sync with Livewire
                        el.dispatchEvent(new Event('change')); // Also dispatch change
                    }
                }
            });
        } catch (e) { console.error("Error loading stored dates", e); }
    }

    restoreDates();
    document.addEventListener('livewire:init', () => {
        setTimeout(restoreDates, 200); // Small delay to be sure
    });
    document.addEventListener('livewire:navigated', restoreDates); // For SPA mode if used

})();

/* ---------- Bootstrap awal ---------- */
// Panggil sekali saat load agar jika data sudah ada di localStorage (via cart.js init), tabs langsung muncul
if (window.MSUCart) {
    buildTabsFromCart();
    MSUCart.renderBadge();
} else {
    // Fallback jika MSUCart belum siap (async load), tunggu event
    window.addEventListener('load', () => {
        if (window.MSUCart) {
            buildTabsFromCart();
            MSUCart.renderBadge();
        }
    });
}

window.addEventListener('msu:cart-updated', () => {
    if (typeof renderCartList === 'function') renderCartList();
    window.MSUCart?.renderBadge();

    // Validasi Cerdas: Rebuild only if items changed to avoid UI flickering
    const cart = window.MSUCart?.get() || [];
    const tabsContent = document.getElementById('itemTabContent');

    // If not on booking page, do nothing more
    if (!tabsContent) return;

    const existingPanels = tabsContent.querySelectorAll('.tab-pane');

    let needsRebuild = false;
    if (cart.length !== existingPanels.length) {
        needsRebuild = true;
    } else {
        // Check order/names
        cart.forEach((it, i) => {
            if (existingPanels[i] && existingPanels[i].dataset.itemName !== it.name) {
                needsRebuild = true;
            }
        });
    }

    if (needsRebuild) {
        if (typeof buildTabsFromCart === 'function') buildTabsFromCart();
    }
});
