// shared-data.js
// Sistem manajemen data peminjaman fasilitas

// Struktur data untuk menyimpan semua peminjaman
class DataManager {
  constructor() {
    this.STORAGE_KEY = 'peminjamanData';
    this.initData();
  }

  // Inisialisasi data default jika belum ada
  initData() {
    if (!localStorage.getItem(this.STORAGE_KEY)) {
      const initialData = {
        dashboard: [
          {
            id: 'd1',
            no: 1,
            nama: 'UKM Al-Fath',
            waktuPengambilan: '18.00 WIB',
            waktuPengembalian: '20.00 WIB',
            fasilitas: 'Ruangan VIP | Aula Syamsul Ulum',
            sudahAmbil: false,
            sudahTerima: false
          },
          {
            id: 'd2',
            no: 2,
            nama: 'HIPMI',
            waktuPengambilan: '29 Oktober 2025',
            waktuPengembalian: '30 Oktober 2025',
            fasilitas: 'Proyektor, Kabel HDMI, Pointer',
            sudahAmbil: false,
            sudahTerima: false
          }
        ],
        pinjamFasilitas: [
          {
            id: 'p1',
            no: 1,
            nama: 'UKM Al-Fath',
            waktuPengambilan: '28 Oktober 2025 | 18.00 WIB',
            waktuPengembalian: '28 Oktober 2025 | 20.00 WIB',
            fasilitas: 'Ruangan VIP | Aula Syamsul Ulum',
            sudahAmbil: false,
            sudahKembali: false
          },
          {
            id: 'p2',
            no: 2,
            nama: 'HIPMI',
            waktuPengambilan: '28 Oktober 2025 | 17.00 WIB',
            waktuPengembalian: '30 Oktober 2025 | 08.00 WIB',
            fasilitas: 'Proyektor, Kabel HDMI, Pointer',
            sudahAmbil: false,
            sudahKembali: false
          },
          {
            id: 'p3',
            no: 3,
            nama: 'HMIT',
            waktuPengambilan: '29 Oktober 2025 | 20.00 WIB',
            waktuPengembalian: '30 Oktober 2025 | 17.00 WIB',
            fasilitas: 'Hijab',
            sudahAmbil: false,
            sudahKembali: false
          }
        ],
        riwayat: []
      };
      localStorage.setItem(this.STORAGE_KEY, JSON.stringify(initialData));
    }
  }

  // Ambil semua data
  getData() {
    return JSON.parse(localStorage.getItem(this.STORAGE_KEY));
  }

  // Simpan semua data
  saveData(data) {
    localStorage.setItem(this.STORAGE_KEY, JSON.stringify(data));
  }

  // Format waktu saat ini
  getCurrentTime() {
    const now = new Date();
    const options = { 
      day: 'numeric', 
      month: 'long', 
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      timeZone: 'Asia/Jakarta'
    };
    return now.toLocaleString('id-ID', options) + ' WIB';
  }

  // Pindahkan data dari dashboard/pinjamFasilitas ke riwayat
  moveToRiwayat(source, id, checkType) {
    const data = this.getData();
    let item = null;
    let sourceArray = source === 'dashboard' ? data.dashboard : data.pinjamFasilitas;

    // Cari item berdasarkan id
    const index = sourceArray.findIndex(i => i.id === id);
    if (index === -1) return;

    item = sourceArray[index];

    // Cek apakah sudah ada di riwayat
    let riwayatItem = data.riwayat.find(r => r.originalId === id && r.source === source);

    if (!riwayatItem) {
      // Buat item baru di riwayat
      riwayatItem = {
        id: 'r' + Date.now(),
        originalId: id,
        source: source,
        no: data.riwayat.length + 1,
        nama: item.nama,
        waktuAmbil: '...',
        waktuKembali: '...',
        fasilitas: item.fasilitas,
        isSubmitted: false,
        isCancelled: false
      };
      data.riwayat.push(riwayatItem);
    }

    // Update waktu sesuai checklist
    if (checkType === 'ambil') {
      riwayatItem.waktuAmbil = this.getCurrentTime();
      item.sudahAmbil = true;
    } else if (checkType === 'terima' || checkType === 'kembali') {
      riwayatItem.waktuKembali = this.getCurrentTime();
      if (source === 'dashboard') {
        item.sudahTerima = true;
      } else {
        item.sudahKembali = true;
      }
    }

    // Cek apakah kedua checkbox sudah tercentang
    const bothChecked = source === 'dashboard' 
      ? (item.sudahAmbil && item.sudahTerima)
      : (item.sudahAmbil && item.sudahKembali);

    if (bothChecked) {
      // Hapus dari sumber
      sourceArray.splice(index, 1);

      // Re-number items
      sourceArray.forEach((item, idx) => {
        item.no = idx + 1;
      });
    }

    this.saveData(data);
  }

  // Cancel riwayat - kembalikan ke pinjamFasilitas
  cancelRiwayat(riwayatId) {
    const data = this.getData();
    const riwayatIndex = data.riwayat.findIndex(r => r.id === riwayatId);

    if (riwayatIndex === -1) return;

    const riwayatItem = data.riwayat[riwayatIndex];
    const originalSource = riwayatItem.source;
    const originalId = riwayatItem.originalId;

    // Hapus dari riwayat
    data.riwayat.splice(riwayatIndex, 1);

    // Re-number riwayat
    data.riwayat.forEach((item, idx) => {
      item.no = idx + 1;
    });

    // Kembalikan ke pinjamFasilitas (reset checkbox)
    const newItem = {
      id: originalId,
      no: data.pinjamFasilitas.length + 1,
      nama: riwayatItem.nama,
      waktuPengambilan: '...',
      waktuPengembalian: '...',
      fasilitas: riwayatItem.fasilitas,
      sudahAmbil: false,
      sudahKembali: false
    };

    data.pinjamFasilitas.push(newItem);

    this.saveData(data);
  }

  // Submit riwayat - disable buttons
  submitRiwayat(riwayatId) {
    const data = this.getData();
    const riwayatItem = data.riwayat.find(r => r.id === riwayatId);

    if (riwayatItem) {
      riwayatItem.isSubmitted = true;
      this.saveData(data);
      return true;
    }
    return false;
  }
}

// Instance global
const dataManager = new DataManager();

// Fungsi untuk dashboard.html
function initDashboard() {
  const data = dataManager.getData();
  const tbody = document.querySelector('table tbody');

  if (!tbody) return;

  tbody.innerHTML = '';

  data.dashboard.forEach((item, index) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${item.no}</td>
      <td>${item.nama}</td>
      <td>${item.waktuPengambilan}</td>
      <td>${item.waktuPengembalian}</td>
      <td>
        <button class="detail-btn" data-detail="${item.fasilitas}">Detail Peminjaman</button>
      </td>
      <td><input type="checkbox" data-id="${item.id}" data-type="ambil" ${item.sudahAmbil ? 'checked' : ''}></td>
      <td><input type="checkbox" data-id="${item.id}" data-type="terima" ${item.sudahTerima ? 'checked' : ''}></td>
    `;
    tbody.appendChild(tr);
  });

  // Event listener untuk checkbox
  document.querySelectorAll('table tbody input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      if (this.checked) {
        const id = this.getAttribute('data-id');
        const type = this.getAttribute('data-type');
        dataManager.moveToRiwayat('dashboard', id, type);
        initDashboard(); // Refresh tampilan
      }
    });
  });

  // Re-attach modal event listeners
  attachModalListeners();
}

// Fungsi untuk pinjamFasilitas.html
function initPinjamFasilitas() {
  const data = dataManager.getData();
  const tbody = document.querySelector('table tbody');

  if (!tbody) return;

  tbody.innerHTML = '';

  data.pinjamFasilitas.forEach((item, index) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${item.no}</td>
      <td>${item.nama}</td>
      <td>${item.waktuPengambilan}</td>
      <td>${item.waktuPengembalian}</td>
      <td>
        <button class="detail-btn" data-detail="${item.fasilitas}">Detail Peminjaman</button>
      </td>
      <td><input type="checkbox" data-id="${item.id}" data-type="ambil" ${item.sudahAmbil ? 'checked' : ''}></td>
      <td><input type="checkbox" data-id="${item.id}" data-type="kembali" ${item.sudahKembali ? 'checked' : ''}></td>
    `;
    tbody.appendChild(tr);
  });

  // Event listener untuk checkbox
  document.querySelectorAll('table tbody input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      if (this.checked) {
        const id = this.getAttribute('data-id');
        const type = this.getAttribute('data-type');
        dataManager.moveToRiwayat('pinjamFasilitas', id, type);
        initPinjamFasilitas(); // Refresh tampilan
      }
    });
  });

  // Re-attach modal event listeners
  attachModalListeners();
}

// Fungsi untuk riwayatpinjam.html
function initRiwayat() {
  const data = dataManager.getData();
  const tbody = document.querySelector('table tbody');

  if (!tbody) return;

  tbody.innerHTML = '';

  if (data.riwayat.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Belum ada riwayat peminjaman</td></tr>';
    return;
  }

  data.riwayat.forEach((item, index) => {
    const tr = document.createElement('tr');
    const isDisabled = item.isSubmitted ? 'disabled' : '';

    tr.innerHTML = `
      <td>${item.no}</td>
      <td>${item.nama}</td>
      <td>${item.waktuAmbil}</td>
      <td>${item.waktuKembali}</td>
      <td>
        <button class="detail-btn" data-detail="${item.fasilitas}">Detail Peminjaman</button>
      </td>
      <td><button type="button" class="cancel-btn" data-id="${item.id}" ${isDisabled}>Cancel</button></td>
      <td><input type="submit" class="submit-btn" data-id="${item.id}" value="Submit" ${isDisabled}></td>
    `;
    tbody.appendChild(tr);
  });

  // Event listener untuk cancel button
  document.querySelectorAll('.cancel-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      if (!this.disabled) {
        const id = this.getAttribute('data-id');
        if (confirm('Apakah Anda yakin ingin membatalkan peminjaman ini?')) {
          dataManager.cancelRiwayat(id);
          initRiwayat(); // Refresh tampilan
        }
      }
    });
  });

  // Event listener untuk submit button
  document.querySelectorAll('.submit-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      if (!this.disabled) {
        const id = this.getAttribute('data-id');
        const success = dataManager.submitRiwayat(id);
        if (success) {
          alert('Data berhasil disubmit!');
          initRiwayat(); // Refresh tampilan
        }
      }
    });
  });

  // Re-attach modal event listeners if exists
  attachModalListeners();
}

// Fungsi untuk attach modal listeners (untuk detail peminjaman)
function attachModalListeners() {
  const detailButtons = document.querySelectorAll(".detail-btn");
  const modalBg = document.getElementById("modalBg");
  const detailContent = document.getElementById("detailContent");

  if (!modalBg || !detailContent) return;

  detailButtons.forEach(button => {
    button.addEventListener("click", function () {
      const detail = this.getAttribute("data-detail");
      detailContent.textContent = detail;
      modalBg.style.display = "flex";
    });
  });

  modalBg.addEventListener("click", function(e) {
    if (e.target === modalBg) {
      closeModal();
    }
  });
}

function closeModal() {
  const modalBg = document.getElementById("modalBg");
  if (modalBg) {
    modalBg.style.display = "none";
  }
}

// Auto-initialize berdasarkan halaman
document.addEventListener('DOMContentLoaded', function() {
  const currentPage = window.location.pathname;

  if (currentPage.includes('dashboard.html') || currentPage.endsWith('/')) {
    initDashboard();
  } else if (currentPage.includes('pinjamFasilitas.html')) {
    initPinjamFasilitas();
  } else if (currentPage.includes('riwayatpinjam.html')) {
    initRiwayat();
  }
});
