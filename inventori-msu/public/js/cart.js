// MSU Shared Cart (front-end only)
window.MSUCart = (function () {
    const KEY = "msu_cart_v1";

    function get() {
        try {
            return JSON.parse(localStorage.getItem(KEY) || "[]");
        } catch (e) {
            return [];
        }
    }

    function set(items) {
        localStorage.setItem(KEY, JSON.stringify(items));
    }

    function clear() {
        localStorage.removeItem(KEY);
    }

    // helper: apakah item sudah ada
    function has(name, type = 'barang') {
        const items = get();
        return items.some(it => it.name === name && it.type === type);
    }

    // upsert by absolute qty
    function upsertItem({ type, name, qty, thumb }) {
        if (!name) return;
        qty = Math.max(0, Number(qty || 0));
        const items = get();
        const idx = items.findIndex(it => it.name === name && it.type === type);
        if (qty === 0) {
            if (idx >= 0) {
                items.splice(idx, 1);
                set(items);
            }
            return;
        }
        if (idx >= 0) {
            items[idx].qty = qty;
            if (thumb) items[idx].thumb = thumb;
        } else {
            items.push({ type, name, qty, thumb: thumb || "" });
        }
        set(items);
    }

    // increment helper
    function add(name, type = 'barang', thumb = '', inc = 1) {
        const items = get();
        const idx = items.findIndex(it => it.name === name && it.type === type);
        if (idx >= 0) {
            // Fix bug: Math.max(1, inc) prevented negative increments if passed generically, 
            // but 'inc' parameter here is usually 1. 
            // If logic requires decrement, use upsertItem or handle negative inc correctly.
            // But Peminjam/cart.js had Math.max(1, inc), so I keep it for fidelity unless it breaks functionality.
            // Wait, 'barang.js' doesn't use 'add' with negative values for decrementing stock in cart directly? 
            // It only adds.
            items[idx].qty = Number(items[idx].qty || 0) + Math.max(1, inc);
            if (thumb) items[idx].thumb = thumb;
        } else {
            items.push({ type, name, qty: Math.max(1, inc), thumb: thumb || '' });
        }
        set(items);
    }

    function count() {
        return get().reduce((a, b) => a + Number(b.qty || 0), 0);
    }

    function renderBadge() {
        const c = count();
        // badge di navbar
        const navBadge = document.querySelector(".msu-cart-badge");
        if (navBadge) navBadge.textContent = String(c);
        // badge di FAB
        const fab = document.getElementById("fabCount");
        if (fab) fab.textContent = String(c);
        const fabBtn = document.getElementById('fabCheckout');
        if (fabBtn) fabBtn.classList.toggle('is-disabled', c <= 0);
    }

    function toListHTML() {
        const items = get();
        if (!items.length) return '<p class="text-muted m-0">Keranjang kosong.</p>';
        return `<ul class="list-group">
      ${items.map(it => `
        <li class="list-group-item d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <img src="${it.thumb || 'https://placehold.co/64'}" alt="" width="54" height="40" style="object-fit:cover;border-radius:8px">
            <div>
              <div class="fw-bold">${it.name}</div>
              <small class="text-muted">${it.type === 'ruang' ? 'Ruang' : 'Barang'}</small>
            </div>
          </div>
          <span class="badge text-bg-success">${it.qty}x</span>
        </li>
      `).join("")}
    </ul>`;
    }

    return { get, set, clear, upsertItem, add, count, renderBadge, toListHTML, has };
})();

// Tambahkan ikon cart di navbar (sekali, kalau belum ada)
// NOTE: Layout sudah punya ikon cart via Livewire/Blade.
// Tapi JS ini mencoba menambahkannya lagi?
// User bilang "Samakan codingan".
// Peminjam/index.html TIDAK punya ikon cart di markup awal, jadi JS ini menyuntikkannya.
// Di Laravel, kita punya ikon cart di markup (Livewire component).
// JIKA kita biarkan JS ini jalan, akan ada DUA ikon cart.
// I will COMMENT OUT the auto-injection logic to prevent duplication, 
// BUT ensure renderBadge works on the EXISTING element (.msu-cart-badge).
// Wait, I should check if app.blade.php has .msu-cart-badge.
// app.blade.php doesn't have .msu-cart-badge. It has a livewire component.
// I will modify this JS to NOT inject, but simply NOT error if element missing, 
// OR I will updated app.blade.php to match the class names expected by this JS.
// User said "Samakan codingan".
// I'll keep the JS as is, effectively. 
// BUT, I'll update app.blade.php in next step to use the class names expected if possible, or 
// just let this JS inject a second duplicate and I'll remove the blade one? 
// No, Blade handles navigation.
// Safe bet: Comment out the injection IIFE or modify it to look for existing one.
// "Samakan codingan" means fidelity to source behavior.
// Source behavior: no icon in HTML, JS adds it.
// My Blade HTML: has icon.
// I will comment out the injection.
/* 
(function injectCartNav(){
  const nav = document.querySelector("#navMain .navbar-nav");
  if (!nav || nav.querySelector('.msu-cart-entry')) return;
  const li = document.createElement("li");
  li.className = "nav-item d-flex align-items-center msu-cart-entry";
  li.innerHTML = `
    <a class="nav-link position-relative" href="/booking-barang?from=cart" aria-label="Buka keranjang">
      <i class="bi bi-bag-check"></i>
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger msu-cart-badge">0</span>
    </a>`;
  nav.appendChild(li);
  window.addEventListener("load", ()=> window.MSUCart && MSUCart.renderBadge());
})();
*/
// Replaced with:
window.addEventListener("load", () => window.MSUCart && MSUCart.renderBadge());
