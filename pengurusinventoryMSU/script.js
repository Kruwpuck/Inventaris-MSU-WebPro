// Ambil data riwayat dari localStorage
function getRiwayat() {
  return JSON.parse(localStorage.getItem("riwayat")) || [];
}

// Simpan ke localStorage
function saveRiwayat(data) {
  localStorage.setItem("riwayat", JSON.stringify(data));
}

// Tambahkan ke riwayat
function tambahKeRiwayat(item) {
  const riwayat = getRiwayat();
  riwayat.push(item);
  saveRiwayat(riwayat);
}

// Hapus dari riwayat
function hapusDariRiwayat(index) {
  const riwayat = getRiwayat();
  riwayat.splice(index, 1);
  saveRiwayat(riwayat);
}

// Render daftar riwayat
function renderRiwayat() {
  const container = document.getElementById("riwayat-list");
  if (!container) return;

  const riwayat = getRiwayat();
  if (riwayat.length === 0) {
    container.innerHTML = `<p>Belum ada data riwayat peminjaman.</p>`;
    return;
  }

  container.innerHTML = "";
  riwayat.forEach((item, index) => {
    const div = document.createElement("div");
    div.className = "card";
    div.innerHTML = `
      <span>${item.nama}</span>
      <div style="display:flex; gap:10px;">
        <button class="btn-override" onclick="overrideItem(${index})">Override</button>
        <button class="btn-submit" onclick="submitItem(${index})">Submit</button>
      </div>
    `;
    container.appendChild(div);
  });
}

function overrideItem(index) {
  const riwayat = getRiwayat();
  const item = riwayat[index];

  const pending = JSON.parse(localStorage.getItem("pending")) || [];
  pending.push(item);
  localStorage.setItem("pending", JSON.stringify(pending));

  hapusDariRiwayat(index);
  renderRiwayat();
  alert(`Peminjaman ${item.nama} dikembalikan ke daftar peminjaman.`);
}

function submitItem(index) {
  const riwayat = getRiwayat();
  const item = riwayat[index];
  hapusDariRiwayat(index);
  renderRiwayat();
  alert(`Data ${item.nama} telah dikirim ke pengelola.`);
}


document.addEventListener("DOMContentLoaded", () => {
  const checkboxes = document.querySelectorAll(".checkbox-area");
  checkboxes.forEach(area => {
    const ambil = area.querySelectorAll('input[type="checkbox"]')[0];
    const terima = area.querySelectorAll('input[type="checkbox"]')[1];
    const label = area.previousElementSibling.querySelector("span");

    function cekChecklist() {
      if (ambil.checked && terima.checked) {
        tambahKeRiwayat({
          nama: label ? label.textContent : "Peminjaman Tanpa Nama"
        });
        alert(`Peminjaman ${label.textContent} masuk ke Riwayat.`);
      }
    }

    ambil.addEventListener("change", cekChecklist);
    terima.addEventListener("change", cekChecklist);
  });

  renderRiwayat();
});
