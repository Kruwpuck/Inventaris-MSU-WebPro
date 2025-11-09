// =============== UTIL ===============
function getEls() {
  const btnKategori = document.getElementById('kategoriBtn');
  const gridBarang  = document.getElementById('gridBarang');
  const gridFasil   = document.getElementById('gridFasilitas');
  const input       = document.getElementById('quickSearch');
  // form: cari form terdekat dari input (tidak perlu id)
  const form        = input ? input.closest('form') : null;
  return { btnKategori, gridBarang, gridFasil, input, form };
}

function getActiveGrid(grids) {
  if (grids.gridFasil && !grids.gridFasil.classList.contains('d-none')) return grids.gridFasil;
  return grids.gridBarang; 
}

function normalize(str) {
  return (str || '').toLowerCase().trim();
}

// Sembunyikan/lihat kolom card 
function setCardColumnDisplay(card, show) {
  const col = card.closest('.col-12, .col-sm-6, .col-md-4, .col-lg-3') || card.parentElement;
  if (col) col.style.display = show ? '' : 'none';
}

// =============== GRID SWITCH ===============
function setActiveGrid(type, grids, keepQuery = true) {
  const isBarang = type === 'barang';
  if (grids.gridBarang)  grids.gridBarang.classList.toggle('d-none', !isBarang);
  if (grids.gridFasil)   grids.gridFasil.classList.toggle('d-none', isBarang);

  // Update label tombol
  if (grids.btnKategori) {
    grids.btnKategori.textContent = isBarang ? 'Barang' : 'Fasilitas';
  }

  // Terapkan filter saat ini ke grid yang baru
  if (keepQuery && grids.input) {
    filterCards(grids.input.value, grids);
  }
}

// =============== FILTER ===============
function filterCards(query, grids) {
  const grid = getActiveGrid(grids);
  if (!grid) return;

  const q = normalize(query);
  const cards = grid.querySelectorAll('.card');

  cards.forEach(card => {
    const title = card.querySelector('.card-title')?.innerText || '';
    const desc  = card.querySelector('.card-text')?.innerText || '';
    const blob  = (title + ' ' + desc + ' ' + card.innerText);
    const match = normalize(blob).includes(q);
    setCardColumnDisplay(card, q === '' ? true : match);
  });
}

// =============== BOOT ===============
(function initBeranda() {
  const els = getEls();
  if (!els.input) return;

  // Submit search
  if (els.form) {
    els.form.addEventListener('submit', (e) => {
      e.preventDefault();
      filterCards(els.input.value, els);
    });
  }

  // Live search saat mengetik
  els.input.addEventListener('input', () => filterCards(els.input.value, els));

  // Dropdown kategori: ganti grid
  if (els.btnKategori) {
    const menu = els.btnKategori.parentElement?.querySelector('.dropdown-menu');
    if (menu) {
      menu.querySelectorAll('[data-switch]').forEach(item => {
        item.addEventListener('click', (e) => {
          e.preventDefault();
          const type = item.getAttribute('data-switch'); 
          setActiveGrid(type, els, true);
        });
      });
    }
  }

  filterCards('', els);
})();
