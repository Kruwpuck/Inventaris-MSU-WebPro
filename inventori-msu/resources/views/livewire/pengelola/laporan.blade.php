{{-- resources/views/livewire/pengelola/laporan.blade.php --}}

@push('head')
  <style>
    body { background:#fff; font-family:"Poppins",sans-serif; }

    .toolbar .btn,
    .toolbar .form-select,
    .toolbar .form-control,
    .toolbar .input-group-text {
      border-radius: 9999px;
    }

    .toolbar .btn-pill {
      border-radius: 9999px;
      border: 1px solid #0b492c;
      color: #0b492c;
      background: #fff;
    }
    .toolbar .btn-pill:hover,
    .toolbar .btn-pill:focus {
      color: #0b492c;
      background: #f5fbf7;
      border-color: #0b492c;
    }

    .toolbar .dropdown-menu {
      border-radius: 14px;
      border: 1px solid #0b492c;
      box-shadow: 0 10px 24px rgba(0,0,0,.1);
      padding: 6px 0;
      min-width: 100%;
    }
    .toolbar .dropdown-item {
      padding: .5rem 1rem;
      border-radius: 10px;
    }
    .toolbar .dropdown-item.active,
    .toolbar .dropdown-item:hover {
      background: #e9f6ef;
      color: #0b492c;
    }

    .table-wrapper {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 6px 18px rgba(0,0,0,.06);
      padding: 16px;
    }

    .table thead th {
      color: #5f6b77;
      font-weight: 700;
      font-size: .9rem;
      white-space: nowrap;
      vertical-align: middle;
      background: #f7f8fa;
      border-bottom: 2px solid #b7c1cc !important;
    }

    .table-bordered { border: 1.5px solid #b7c1cc !important; }
    .table-bordered > :not(caption) > * { border-width: 1.5px 0; }
    .table-bordered > :not(caption) > * > * {
      border-color: #b7c1cc !important;
      padding: .8rem .75rem;
      vertical-align: middle;
      font-size: .95rem;
    }

    .badge-status {
      display: inline-block;
      min-width: 130px;
      text-align:center;
      border-radius: 9999px;
      padding: .35rem .6rem;
      font-weight: 600;
      font-size: .8rem;
      white-space: nowrap;
    }
    .status-sedang { background:#f3f7cf; color:#6a7b00; }
    .status-sudah { background:#d9f3e7; color:#0b492c; }
    .status-terlambat { background:#7a2c2c; color:#fff; }

    #fSearch:focus { border-color:#000; box-shadow:none; outline:none; }

    @media print {
      .shadow-soft, .shadow-strong, .table-wrapper { box-shadow: none !important; }
      .table thead th {
        background: #eee !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .table-bordered { border: 2px solid #000 !important; }
      .table-bordered > :not(caption) > * > * { border-color:#000 !important; }
      .navbar { display:none; }
    }
  </style>

  {{-- Chart.js --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

<div class="container pt-5">
  <div style="height: 84px"></div>

  {{-- Toolbar --}}
  <div class="toolbar d-flex flex-wrap gap-3 align-items-center mb-4">

    {{-- Unduh split button --}}
    <div class="btn-group">
      <button type="button" class="btn btn-outline-success px-4" style="border-color:#0b492c;color:#0b492c">
        <i class="bi bi-download me-2"></i>Unduh
      </button>
      <button type="button" class="btn btn-outline-success dropdown-toggle dropdown-toggle-split"
              data-bs-toggle="dropdown" style="border-color:#0b492c;color:#0b492c">
        <span class="visually-hidden">Toggle Dropdown</span>
      </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#">.XLSX</a></li>
        <li><a class="dropdown-item" href="#">.CSV</a></li>
        <li><a class="dropdown-item" href="#">.PDF</a></li>
      </ul>
    </div>

    {{-- Periode --}}
    <div class="dropdown">
      <button class="btn btn-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" id="btnPeriode">
        Bulan Ini
      </button>
      <ul class="dropdown-menu" aria-labelledby="btnPeriode">
        <li><a class="dropdown-item" href="#" data-value="2w">2 Minggu Terakhir</a></li>
        <li><a class="dropdown-item active" href="#" data-value="1m">Bulan Ini</a></li>
        <li><a class="dropdown-item" href="#" data-value="prev1m">Bulan Lalu</a></li>
        <li><a class="dropdown-item" href="#" data-value="all">Semua Waktu</a></li>
      </ul>
    </div>

    {{-- Kategori --}}
    <div class="dropdown">
      <button class="btn btn-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" id="btnKategori">
        Semua Kategori
      </button>
      <ul class="dropdown-menu" aria-labelledby="btnKategori">
        <li><a class="dropdown-item active" href="#" data-value="all">Semua Kategori</a></li>
        <li><a class="dropdown-item" href="#" data-value="Barang">Barang</a></li>
        <li><a class="dropdown-item" href="#" data-value="Ruangan">Ruangan</a></li>
      </ul>
    </div>

    {{-- Status --}}
    <div class="dropdown">
      <button class="btn btn-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" id="btnStatus">
        Semua Status
      </button>
      <ul class="dropdown-menu" aria-labelledby="btnStatus">
        <li><a class="dropdown-item active" href="#" data-value="all">Semua Status</a></li>
        <li><a class="dropdown-item" href="#" data-value="Sedang Dipinjam">Sedang Dipinjam</a></li>
        <li><a class="dropdown-item" href="#" data-value="Sudah Kembali">Sudah Kembali</a></li>
        <li><a class="dropdown-item" href="#" data-value="Terlambat">Terlambat</a></li>
      </ul>
    </div>

    <div class="ms-auto"></div>

    {{-- Search --}}
    <div class="input-group" style="max-width:320px">
      <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
      <input id="fSearch" type="text" class="form-control" placeholder="Cari laporan...">
    </div>
  </div>

  {{-- TABLE --}}
  <div class="table-wrapper mb-4">
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th style="width:48px">No</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Peminjam</th>
            <th>Tgl Pinjam</th>
            <th>Jatuh Tempo</th>
            <th>Tgl Kembali</th>
            <th class="text-center">Jumlah</th>
            <th class="text-center">Status</th>
          </tr>
        </thead>

        <tbody id="tbodyLaporan">
          @foreach($laporans as $i => $r)
            @php
              $statusClass = match($r->status) {
                'Sedang Dipinjam' => 'status-sedang',
                'Sudah Kembali'  => 'status-sudah',
                'Terlambat'      => 'status-terlambat',
                default          => 'status-sedang'
              };
            @endphp

            <tr data-kategori="{{ $r->kategori }}" data-status="{{ $r->status }}">
              <td>{{ $i+1 }}</td>
              <td>{{ $r->nama_item }}</td>
              <td>{{ $r->kategori }}</td>
              <td>{{ $r->peminjam }}</td>
              <td>{{ $r->tgl_pinjam }}</td>
              <td>{{ $r->jatuh_tempo }}</td>
              <td>{{ $r->tgl_kembali }}</td>
              <td class="text-center">{{ $r->jumlah }}</td>
              <td class="text-center">
                <span class="badge-status {{ $statusClass }}">{{ $r->status }}</span>
              </td>
            </tr>
          @endforeach
        </tbody>

      </table>
    </div>
  </div>

  {{-- ANALISIS TREN --}}
  <div class="card shadow-strong p-3 mb-5">
    <h6 class="mb-3">Top 10 yang paling sering dipinjam (hasil filter manual nanti)</h6>
    <canvas id="chartTop"></canvas>
  </div>
</div>

@push('scripts')
<script>
  // =============== UTIL TANGGAL ===============
  function parseMDY(s) {
    if (!s || s.trim() === "-") return null;
    const [m, d, y] = s.split("/").map(n => parseInt(n, 10));
    if (!m || !d || !y) return null;
    return new Date(y, m - 1, d);
  }

  function startOfToday() {
    const t = new Date();
    return new Date(t.getFullYear(), t.getMonth(), t.getDate());
  }

  function hookupFilterDropdown(btnId, onChange) {
    const btn = document.getElementById(btnId);
    if (!btn) return;

    const menu = btn.nextElementSibling;
    if (!menu) return;

    menu.querySelectorAll(".dropdown-item").forEach((item) => {
      item.addEventListener("click", (e) => {
        e.preventDefault();
        menu.querySelectorAll(".dropdown-item").forEach(i => i.classList.remove("active"));
        item.classList.add("active");
        btn.textContent = item.textContent.trim();
        const value = item.getAttribute("data-value");
        onChange && onChange(value);
        filterTable();
      });
    });
  }

  let vPeriode = "1m",
      vKategori = "all",
      vStatus = "all";

  const inputSearch = document.getElementById("fSearch");
  const tbody = document.getElementById("tbodyLaporan");

  function filterTable() {
    if (!tbody) return;

    const today = startOfToday();
    let from = null, to = null;

    if (vPeriode === "2w") {
      from = new Date(today); from.setDate(from.getDate() - 13);
      to = new Date(today);
    } else if (vPeriode === "1m") {
      from = new Date(today.getFullYear(), today.getMonth(), 1);
      to = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    } else if (vPeriode === "prev1m") {
      from = new Date(today.getFullYear(), today.getMonth() - 1, 1);
      to = new Date(today.getFullYear(), today.getMonth(), 0);
    }

    const q = (inputSearch?.value || "").toLowerCase().trim();
    let rowIdx = 0;

    Array.from(tbody.querySelectorAll("tr")).forEach((tr) => {
      const kategori = tr.getAttribute("data-kategori");
      const status = tr.getAttribute("data-status");
      const cellsText = Array.from(tr.children).map(td => td.textContent).join(" ").toLowerCase();
      const tglPinjam = parseMDY(tr.children[4].textContent);

      let okPeriode = true;
      if (from && to) okPeriode = !!tglPinjam && tglPinjam >= from && tglPinjam <= to;

      const okKategori = vKategori === "all" || kategori === vKategori;
      const okStatus = vStatus === "all" || status === vStatus;
      const okSearch = q === "" || cellsText.includes(q);

      const visible = okPeriode && okKategori && okStatus && okSearch;

      tr.style.display = visible ? "" : "none";
      if (visible) {
        rowIdx += 1;
        tr.children[0].textContent = rowIdx;
      }
    });
  }

  function syncMenuWidth() {
    document.querySelectorAll(".toolbar .dropdown").forEach((d) => {
      const btn = d.querySelector("button");
      const menu = d.querySelector(".dropdown-menu");
      if (btn && menu) menu.style.minWidth = btn.offsetWidth + "px";
    });

    document.querySelectorAll(".btn-group .dropdown-menu").forEach((menu) => {
      const group = menu.closest(".btn-group");
      if (group) {
        const totalWidth = Array.from(group.querySelectorAll("button"))
          .reduce((w, b) => w + b.offsetWidth, 0);
        menu.style.minWidth = totalWidth + "px";
      }
    });
  }

  window.addEventListener("load", () => {
    hookupFilterDropdown("btnPeriode", (v) => (vPeriode = v));
    hookupFilterDropdown("btnKategori", (v) => (vKategori = v));
    hookupFilterDropdown("btnStatus", (v) => (vStatus = v));

    if (inputSearch) inputSearch.addEventListener("input", filterTable);

    syncMenuWidth();
    window.addEventListener("resize", syncMenuWidth);

    filterTable();

    // Chart dummy (sama kayak HTML)
    const ctx = document.getElementById("chartTop");
    if (ctx && window.Chart) {
      new Chart(ctx, {
        type: "bar",
        data: {
          labels: [
            "Proyektor","Meja","Speaker","Terpal","Sofa","Hijab",
            "Ruang Utama","Selasar","Zoom","Ruang VIP"
          ],
          datasets: [{
            label: "Dipinjam",
            data: [12,10,9,8,7,6,5,4,3,2],
          }],
        },
        options: {
          responsive: true,
          plugins: { legend: { display: true } },
        },
      });
    }
  });
</script>
@endpush
