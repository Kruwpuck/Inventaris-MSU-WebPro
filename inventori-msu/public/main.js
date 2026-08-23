// main.js
// ====== Animasi judul hero & reveal on scroll ======
window.addEventListener('load', () => {
  document.querySelector('.drop-in')?.classList.add('show');
});

const io = new IntersectionObserver((entries) => {
  entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('show'); io.unobserve(e.target); } });
}, { threshold: 0.12 });

document.querySelectorAll('.reveal-up').forEach(el => io.observe(el));

// Tap animation (mobile)
function addTapAnimation(el) {
  el.addEventListener('touchstart', () => el.classList.add('tap-active'), { passive: true });
  el.addEventListener('touchend', () => setTimeout(() => el.classList.remove('tap-active'), 150));
  el.addEventListener('touchcancel', () => el.classList.remove('tap-active'));
}
document.querySelectorAll('.tap-anim').forEach(addTapAnimation);

// ====== Toast util (Livewire uses session flash, but we can keep this for JS triggers if needed) ======
window.showToastSuccess = function (text) {
  const wrap = document.getElementById('toastStack');
  if (!wrap) return; // alert(text);
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

// ====== Expand visual saat klik kartu (kecuali tombol qty) ======
document.addEventListener('click', (e) => {
  const card = e.target.closest('.item-card'); if (!card) return;
  if (e.target.closest('.qty-btn')) return;
  const grid = card.closest('.items-grid');
  const already = card.classList.contains('is-expanded');
  grid.classList.remove('has-expanded');
  grid.querySelectorAll('.item-card').forEach(c => c.classList.remove('is-expanded'));
  if (!already) { card.classList.add('is-expanded'); grid.classList.add('has-expanded'); }
});

// ====== WhatsApp Operational Hours Check (06.00 - 17.00 WIB) ======
(function initWaOperationalCheck() {
  document.addEventListener('click', function (e) {
    const waLink = e.target.closest('a[href*="wa.me"], a[href*="api.whatsapp.com"], #fabContact, .btn-whatsapp');
    if (!waLink) return;

    try {
      const options = { timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', hour12: false };
      const parts = new Intl.DateTimeFormat('en-GB', options).formatToParts(new Date());
      let hour = 0, minute = 0;
      for (const part of parts) {
        if (part.type === 'hour') hour = parseInt(part.value, 10);
        if (part.type === 'minute') minute = parseInt(part.value, 10);
      }
      if (hour === 24) hour = 0;

      const totalMinutes = hour * 60 + minute;
      const startMinutes = 6 * 60;  // 06:00 WIB (360)
      const endMinutes = 17 * 60;  // 17:00 WIB (1020)

      if (totalMinutes < startMinutes || totalMinutes > endMinutes) {
        e.preventDefault();
        e.stopPropagation();
        alert("Mohon maaf, saat ini diluar Jam Operasional Admin\nJam Operasional Admin: 06.00-17.00 WIB");
      }
    } catch (err) {
      const now = new Date();
      const totalMinutes = now.getHours() * 60 + now.getMinutes();
      if (totalMinutes < 360 || totalMinutes > 1020) {
        e.preventDefault();
        e.stopPropagation();
        alert("Mohon maaf, saat ini diluar Jam Operasional Admin\nJam Operasional Admin: 06.00-17.00 WIB");
      }
    }
  }, true);
})();

