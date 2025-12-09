// shared-data.js (REVISI FINAL)

// Sistem manajemen data peminjaman fasilitas

class DataManager {
  constructor() {
    this.STORAGE_KEY = 'peminjamanData';
    this.initData();
    this.repairData(); // Auto fix corrupted data
  }

  // Inisialisasi data awal
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

  // Perbaiki data yang korup ("...")
  repairData() {
    const data = this.getData();
    if (!data) return;

    let changed = false;

    // Fix Pinjam Fasilitas Defaults
    const defaults = {
      'p1': {
        waktuPengambilan: '28 Oktober 2025 | 18.00 WIB',
        waktuPengembalian: '28 Oktober 2025 | 20.00 WIB'
      },
      'p2': {
        waktuPengambilan: '28 Oktober 2025 | 17.00 WIB',
        waktuPengembalian: '30 Oktober 2025 | 08.00 WIB'
      },
      'p3': {
        waktuPengambilan: '29 Oktober 2025 | 20.00 WIB',
        waktuPengembalian: '30 Oktober 2025 | 17.00 WIB'
      }
    };

    if (data.pinjamFasilitas) {
      data.pinjamFasilitas.forEach(item => {
        if (defaults[item.id]) {
          if (item.waktuPengambilan === '...' || !item.waktuPengambilan) {
            item.waktuPengambilan = defaults[item.id].waktuPengambilan;
            changed = true;
          }
          if (item.waktuPengembalian === '...' || !item.waktuPengembalian) {
            item.waktuPengembalian = defaults[item.id].waktuPengembalian;
            changed = true;
          }
        }
      });
    }

    if (changed) {
      this.saveData(data);
    }

    // Clean up "Zombie" items:
    // If item is in Riwayat and has both times taken (completed),
    // it should NOT be in the source list.
    let cleaned = false;
    data.riwayat.forEach(r => {
      // Check if "full cycle" (ambil & kembali/terima) is done
      // Note: check logic for '...' is sufficient? 
      // User's screenshot shows valid times in Riwayat.
      const isComplete = r.waktuAmbil !== '...' && r.waktuKembali !== '...';

      if (isComplete) {
        let sourceArray = r.source === 'dashboard' ? data.dashboard : data.pinjamFasilitas;
        const idx = sourceArray.findIndex(s => s.id === r.originalId);

        if (idx !== -1) {
          // Remove from source
          sourceArray.splice(idx, 1);
          cleaned = true;
        }
      }
    });

    if (cleaned) {
      // Renumber source arrays
      data.dashboard.forEach((item, i) => item.no = i + 1);
      data.pinjamFasilitas.forEach((item, i) => item.no = i + 1);
      this.saveData(data);
    }
  }

  // Ambil semua data
  getData() {
    return JSON.parse(localStorage.getItem(this.STORAGE_KEY));
  }

  // Simpan data
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

  // Pindahkan data ke riwayat
  moveToRiwayat(source, id, checkType, isChecked) {
    const data = this.getData();
    let sourceArray = source === 'dashboard' ? data.dashboard : data.pinjamFasilitas;

    const index = sourceArray.findIndex(i => i.id === id);
    if (index === -1) return false;

    const item = sourceArray[index];

    // HANDLE UNCHECK
    if (!isChecked) {
      if (checkType === 'ambil') {
        item.sudahAmbil = false;
      } else if (checkType === 'terima' || checkType === 'kembali') {
        if (source === 'dashboard') item.sudahTerima = false;
        else item.sudahKembali = false;
      }

      // Remove from riwayat if partial (optional, but requested to "cancel" action)
      // If uncheck "ambil", logic implies we are reverting the action.
      // We iterate to find if there's a riwayat item to update or remove.
      const riIndex = data.riwayat.findIndex(r => r.originalId === id && r.source === source);
      if (riIndex !== -1) {
        const riwayatItem = data.riwayat[riIndex];
        // If we uncheck 'ambil', revert time.
        if (checkType === 'ambil') {
          riwayatItem.waktuAmbil = '...';
        }
        if (checkType === 'terima' || checkType === 'kembali') {
          riwayatItem.waktuKembali = '...';
        }

        // If both are reset, maybe remove from riwayat?
        if (riwayatItem.waktuAmbil === '...' && riwayatItem.waktuKembali === '...') {
          data.riwayat.splice(riIndex, 1);
          // Renumber
          data.riwayat.forEach((itm, idx) => itm.no = idx + 1);
        }
      }

      this.saveData(data);
      return true;
    }

    // HANDLE CHECK
    const willCheckAmbil = checkType === 'ambil' ? true : item.sudahAmbil;
    const willCheckKembali =
      source === 'dashboard'
        ? (checkType === 'terima' ? true : item.sudahTerima)
        : (checkType === 'kembali' ? true : item.sudahKembali);

    const bothChecked = willCheckAmbil && willCheckKembali;

    // NOTIFIKASI saat centang dua-duanya
    if (bothChecked) {
      const ok = confirm("Apakah fasilitasnya sudah kembali?");
      if (!ok) {
        return false; // batalkan centang
      }
    }

    // Cari di riwayat atau Buat baru
    let riwayatItem = data.riwayat.find(
      r => r.originalId === id && r.source === source
    );

    if (!riwayatItem) {
      riwayatItem = {
        id: 'r' + Date.now(),
        originalId: id,
        source: source,
        no: data.riwayat.length + 1,
        nama: item.nama,
        fasilitas: item.fasilitas,
        waktuAmbil: '...',
        waktuKembali: '...',
        // Simpan waktu asli agar bisa restore
        waktuAsliAmbil: item.waktuPengambilan,
        waktuAsliKembali: item.waktuPengembalian,
        isSubmitted: false
      };
      data.riwayat.push(riwayatItem);
    }

    // Update waktu sesuai centang
    if (checkType === 'ambil') {
      riwayatItem.waktuAmbil = this.getCurrentTime();
      item.sudahAmbil = true;
    }
    if (checkType === 'terima' || checkType === 'kembali') {
      riwayatItem.waktuKembali = this.getCurrentTime();
      if (source === 'dashboard') item.sudahTerima = true;
      else item.sudahKembali = true;
    }

    // Jika dua-duanya sudah centang → hapus dari sumber
    if (bothChecked) {
      sourceArray.splice(index, 1);
      // Renumber
      sourceArray.forEach((itm, idx) => {
        itm.no = idx + 1;
      });
    }

    this.saveData(data);
    return true;
  }

  // Cancel riwayat
  cancelRiwayat(riwayatId) {
    const data = this.getData();
    const riIndex = data.riwayat.findIndex(r => r.id === riwayatId);
    if (riIndex === -1) return;

    const riwayatItem = data.riwayat[riIndex];

    // Cek apakah item masih ada di source (belum dihapus karena baru partial check)
    // atau sudah dihapus (both checked).
    let sourceArray = riwayatItem.source === 'dashboard' ? data.dashboard : data.pinjamFasilitas;
    let existingItemIndex = sourceArray.findIndex(i => i.id === riwayatItem.originalId);

    if (existingItemIndex !== -1) {
      // Item masih ada di source (Duplicate prevention)
      // Kita hanya reset statusnya.
      const item = sourceArray[existingItemIndex];
      item.sudahAmbil = false;
      if (riwayatItem.source === 'dashboard') item.sudahTerima = false;
      else item.sudahKembali = false;
    } else {
      // Item sudah tidak ada, kembalikan (Restore)
      sourceArray.push({
        id: riwayatItem.originalId,
        no: sourceArray.length + 1,
        nama: riwayatItem.nama,
        // Restore waktu asli, handle potential undefined
        waktuPengambilan: riwayatItem.waktuAsliAmbil || '...',
        waktuPengembalian: riwayatItem.waktuAsliKembali || '...',
        fasilitas: riwayatItem.fasilitas,
        sudahAmbil: false,
        sudahKembali: false,
        sudahTerima: false // Reset flag for dashboard
      });
    }

    // Hapus dari riwayat
    data.riwayat.splice(riIndex, 1);

    // Renumber riwayat
    data.riwayat.forEach((item, i) => {
      item.no = i + 1;
    });

    this.saveData(data);
  }

  // Submit riwayat
  submitRiwayat(riwayatId) {
    const data = this.getData();
    const item = data.riwayat.find(r => r.id === riwayatId);

    if (item) {
      item.isSubmitted = true;
      this.saveData(data);
      return true;
    }
    return false;
  }
}

// Instance global
const dataManager = new DataManager();

/* ------------------------------
   DASHBOARD PAGE
--------------------------------*/
function initDashboard() {
  const data = dataManager.getData();
  const tbody = document.querySelector('table tbody');
  if (!tbody) return;

  tbody.innerHTML = '';

  data.dashboard.forEach(item => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${item.no}</td>
      <td>${item.nama}</td>
      <td>${item.waktuPengambilan}</td>
      <td>${item.waktuPengembalian}</td>
      <td><button class="detail-btn" data-detail="${item.fasilitas}">Detail Peminjaman</button></td>
      <td><input type="checkbox" data-id="${item.id}" data-type="ambil" ${item.sudahAmbil ? 'checked' : ''}></td>
      <td><input type="checkbox" data-id="${item.id}" data-type="terima" ${item.sudahTerima ? 'checked' : ''}></td>
    `;
    tbody.appendChild(tr);
  });

  document.querySelectorAll('table tbody input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', function () {
      const id = this.getAttribute('data-id');
      const type = this.getAttribute('data-type');

      const ok = dataManager.moveToRiwayat('dashboard', id, type, this.checked);
      if (!ok) {
        // Revert check if action cancelled
        this.checked = !this.checked;
      } else {
        initDashboard();
      }
    });
  });

  attachModalListeners();
}

/* ------------------------------
   PINJAM FASILITAS PAGE
--------------------------------*/
function initPinjamFasilitas() {
  const data = dataManager.getData();
  const tbody = document.querySelector('table tbody');
  if (!tbody) return;

  tbody.innerHTML = '';

  data.pinjamFasilitas.forEach(item => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${item.no}</td>
      <td>${item.nama}</td>
      <td>${item.waktuPengambilan}</td>
      <td>${item.waktuPengembalian}</td>
      <td><button class="detail-btn" data-detail="${item.fasilitas}">Detail Peminjaman</button></td>
      <td><input type="checkbox" data-id="${item.id}" data-type="ambil" ${item.sudahAmbil ? 'checked' : ''}></td>
      <td><input type="checkbox" data-id="${item.id}" data-type="kembali" ${item.sudahKembali ? 'checked' : ''}></td>
    `;
    tbody.appendChild(tr);
  });

  document.querySelectorAll('table tbody input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', function () {
      const id = this.getAttribute('data-id');
      const type = this.getAttribute('data-type');

      const ok = dataManager.moveToRiwayat('pinjamFasilitas', id, type, this.checked);
      if (!ok) {
        // Revert check if action cancelled
        this.checked = !this.checked;
      } else {
        initPinjamFasilitas();
      }
    });
  });

  attachModalListeners();
}

/* ------------------------------
   RIWAYAT PAGE
--------------------------------*/
function initRiwayat() {
  const data = dataManager.getData();
  const tbody = document.querySelector('table tbody');
  if (!tbody) return;

  tbody.innerHTML = '';

  if (data.riwayat.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;">Belum ada riwayat peminjaman</td></tr>`;
    return;
  }

  data.riwayat.forEach(item => {
    const disabled = item.isSubmitted ? 'disabled' : '';

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${item.no}</td>
      <td>${item.nama}</td>
      <td>${item.waktuAmbil}</td>
      <td>${item.waktuKembali}</td>
      <td><button class="detail-btn" data-detail="${item.fasilitas}">Detail Peminjaman</button></td>
      <td><button class="cancel-btn" data-id="${item.id}" ${disabled}>Cancel</button></td>
      <td><button class="submit-btn" data-id="${item.id}" ${disabled}>Submit</button></td>
    `;
    tbody.appendChild(tr);
  });

  // Cancel
  document.querySelectorAll('.cancel-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      if (confirm("Anda yakin ingin membatalkan peminjaman ini?")) {
        dataManager.cancelRiwayat(id);
        initRiwayat();
      }
    });
  });

  // Submit
  document.querySelectorAll('.submit-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      if (dataManager.submitRiwayat(id)) {
        alert("Data berhasil disubmit!");
        initRiwayat();
      }
    });
  });

  attachModalListeners();
}

/* ------------------------------
   MODAL DETAIL
--------------------------------*/
/* ------------------------------
   MODAL DETAIL (BOOTSTRAP)
--------------------------------*/
function attachModalListeners() {
  const detailButtons = document.querySelectorAll(".detail-btn");

  // We don't need custom modal logic anymore.
  // We just need to update content before showing.
  // Assuming Bootstrap JS handles the show/hide.

  detailButtons.forEach(btn => {
    // Remove old listeners to avoid duplicates if called multiple times?
    // Actually, init functions clear innerHTML so buttons are new.

    btn.addEventListener("click", function () {
      const content = this.getAttribute("data-detail");
      const contentEl = document.getElementById("detailContent");
      if (contentEl) contentEl.textContent = content;

      // Show modal programmatically
      const myModal = new bootstrap.Modal(document.getElementById('detailModal'));
      myModal.show();
    });
  });
}

// closeModal is handled by Bootstrap data-bs-dismiss

// Auto inisialisasi halaman
document.addEventListener('DOMContentLoaded', () => {
  const page = window.location.pathname;
  if (page.includes('dashboard.html')) initDashboard();
  else if (page.includes('pinjamFasilitas.html')) initPinjamFasilitas();
  else if (page.includes('riwayatpinjam.html')) initRiwayat();
});


