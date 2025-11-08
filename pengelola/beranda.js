// ===============================
// DATA
// ===============================
const barangOptions = [
  "Proyektor", "Karpet", "Speaker", "Meja",
  "Sofa", "Akun Zoom MSU", "Terpal",
  "Peralatan Bukber", "Hijab"
];

const fasilitasOptions = [
  "Ruang Utama", "Ruang Tamu VIP", "Pelataran Masjid",
  "Selasar / Teras Selatan Masjid", "Selasar / Teras Utara Masjid",
  "Selasar / Teras Timur Masjid", "Plaza Masjid",
  "Lantai 2 Timur Masjid", "Lantai 2 Selatan Masjid",
  "Lantai 2 Utara Masjid", "Halaman Masjid"
];

// ===============================
// ELEMEN
// ===============================
const kategoriBtn   = document.getElementById('kategoriBtn');
const switchLinks   = document.querySelectorAll('.dropdown-menu [data-switch]');
const labelNama     = document.getElementById('labelNama');
const selectNama    = document.getElementById('selectNama');
const quickSearch   = document.getElementById('quickSearch');
const gridBarang    = document.getElementById('gridBarang');
const gridFasilitas = document.getElementById('gridFasilitas');
const searchForm    = document.querySelector('.bg-white form'); // form utama

// ===============================
// FUNGSI BANTUAN
// ===============================
function fillSelect(options, placeholder) {
  selectNama.innerHTML = "";
  const opt0 = document.createElement('option');
  opt0.selected = true;
  opt0.textContent = placeholder;
  selectNama.appendChild(opt0);
  options.forEach(txt => {
    const o = document.createElement('option');
    o.textContent = txt;
    selectNama.appendChild(o);
  });
}

function switchTo(type) {
  const isBarang = (type === 'barang');
  kategoriBtn.textContent = isBarang ? "Barang" : "Fasilitas";
  labelNama.textContent = isBarang ? "Nama Barang" : "Nama Fasilitas";
  quickSearch.placeholder = isBarang ? "Ketik nama barang..." : "Ketik nama fasilitas...";
  fillSelect(isBarang ? barangOptions : fasilitasOptions, isBarang ? "Pilih Barang" : "Pilih Fasilitas");
  gridBarang.classList.toggle('d-none', !isBarang);
  gridFasilitas.classList.toggle('d-none', isBarang);
}

// ===============================
// EVENT UTAMA
// ===============================
document.addEventListener('DOMContentLoaded', function () {
  fillSelect(barangOptions, "Pilih Barang");

  // toggle barang/fasilitas
  switchLinks.forEach(a => {
    a.addEventListener('click', (e) => {
      e.preventDefault();
      switchTo(a.getAttribute('data-switch'));
    });
  });

  // event tombol "Cari"
  searchForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const keyword = quickSearch.value.trim().toLowerCase();
    const activeGrid = gridBarang.classList.contains('d-none') ? gridFasilitas : gridBarang;

    // ambil semua card di grid aktif
    const cards = activeGrid.querySelectorAll('.card');
    cards.forEach(card => {
      const title = card.querySelector('.card-title').textContent.toLowerCase();
      const desc = card.querySelector('.card-text').textContent.toLowerCase();

      // sembunyikan yang tidak cocok
      if (title.includes(keyword) || desc.includes(keyword) || keyword === '') {
        card.parentElement.style.display = ''; // tampilkan kolom
      } else {
        card.parentElement.style.display = 'none';
      }
    });
  });
});
