// ====== Animasi judul hero & reveal on scroll ======
function initAnimations() {
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('show');
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal-up').forEach(el => {
        if (!el.classList.contains('show')) io.observe(el);
    });
    document.querySelector('.drop-in')?.classList.add('show');
}

window.addEventListener('load', initAnimations);
// Re-init after Livewire updates
document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', ({ el, component }) => {
        initAnimations();
    });
});

// Tap animation (mobile)
function addTapAnimation(el) {
    el.addEventListener('touchstart', () => el.classList.add('tap-active'), { passive: true });
    el.addEventListener('touchend', () => setTimeout(() => el.classList.remove('tap-active'), 150));
    el.addEventListener('touchcancel', () => el.classList.remove('tap-active'));
}
document.querySelectorAll('.tap-anim').forEach(addTapAnimation);

// ====== Module: MSU Dates (UI Only - localStorage) ======
window.MSUDates = (function () {
    const KEY = 'msu_dates_v1';
    function get() { try { return JSON.parse(localStorage.getItem(KEY) || '{}'); } catch (e) { return {} } }
    function set(d) { localStorage.setItem(KEY, JSON.stringify(d)); }
    function isSet() { const d = get(); return Boolean(d.start && d.end); }
    function formatRange() {
        const d = get(); if (!d.start || !d.end) return '';
        const extra = []; if (d.time) extra.push(`Jam ${d.time}`);
        if (d.duration) extra.push(`${d.duration} jam`);
        const s = extra.length ? ` (${extra.join(', ')})` : '';
        return `${d.start} → ${d.end}${s}`;
    }
    return { get, set, isSet, formatRange };
})();

// ====== Render & set DateBar (UI visual helper) ======
(function initDateBar() {
    const inpStart = document.getElementById('dateStart');
    const inpEnd = document.getElementById('dateEnd'); // Catalogue might not have this, used in Home
    const inpTime = document.getElementById('timeStart');
    const selDur = document.getElementById('durationSel');
    const btnSet = document.getElementById('btnSetDates');
    const lbl = document.querySelector('.js-daterange');

    if (!btnSet) return; // Exit if not on page

    const saved = window.MSUDates.get();
    if (inpStart && saved.start) inpStart.value = saved.start;
    if (inpEnd && saved.end) inpEnd.value = saved.end;
    if (btnSet) {
        btnSet.addEventListener('click', () => {
            const s = inpStart?.value;
            // Simple save purely for UI state persistence across pages
            if (s) window.MSUDates.set({ start: s, end: s, time: inpTime?.value, duration: selDur?.value });
            if (lbl) lbl.textContent = `Tanggal dipilih: ${window.MSUDates.formatRange()}`;
            alert('Tanggal tersimpan (Simulasi). Silahkan pilih barang.');
        });
    }
})();

// toast util preserved for generic use if needed
window.showToastSuccess = function (text) {
    const wrap = document.getElementById('toastStack');
    if (!wrap) return;
    const id = 't' + Date.now();
    wrap.insertAdjacentHTML('beforeend', `<div id="${id}" class="toast align-items-center text-bg-success border-0 show">
       <div class="d-flex"><div class="toast-body">${text}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`);
    setTimeout(() => document.getElementById(id)?.remove(), 3000);
}

