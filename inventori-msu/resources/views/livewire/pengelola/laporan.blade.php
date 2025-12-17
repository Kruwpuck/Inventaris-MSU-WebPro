{{-- resources/views/livewire/pengelola/laporan.blade.php --}}

@push('head')
  <style>
    body { background: #fff; font-family: "Poppins", sans-serif; }

    .toolbar .btn,
    .toolbar .form-select,
    .toolbar .form-control,
    .toolbar .input-group-text { border-radius: 9999px; }

    .toolbar .btn-pill{
      border-radius:9999px;border:1px solid #0b492c;color:#0b492c;background:#fff;
    }
    .toolbar .btn-pill:hover,.toolbar .btn-pill:focus{
      color:#0b492c;background:#f5fbf7;border-color:#0b492c;
    }

    .toolbar .dropdown-menu{
      border-radius:14px;border:1px solid #0b492c;
      box-shadow:0 10px 24px rgba(0,0,0,.1);
      padding:6px 0;min-width:100%;
    }
    .toolbar .dropdown-item{ padding:.5rem 1rem;border-radius:10px; }
    .toolbar .dropdown-item.active,.toolbar .dropdown-item:hover{
      background:#e9f6ef;color:#0b492c;
    }

    .table-wrapper{
      background:#fff;border-radius:18px;box-shadow:0 6px 18px rgba(0,0,0,.06);padding:16px;
    }
    .table thead th{
      color:#5f6b77;font-weight:700;font-size:.9rem;white-space:nowrap;vertical-align:middle;
      background:#f7f8fa;border-bottom:2px solid #b7c1cc !important;
    }
    .table-bordered{ border:1.5px solid #b7c1cc !important; }
    .table-bordered> :not(caption)>*{ border-width:1.5px 0; }
    .table-bordered> :not(caption)>*>*{
      border-color:#b7c1cc !important;padding:.8rem .75rem;vertical-align:middle;font-size:.95rem;
    }

    .badge-status{
      display:inline-block;min-width:130px;text-align:center;border-radius:9999px;
      padding:.35rem .6rem;font-weight:600;font-size:.8rem;white-space:nowrap;
    }
    .status-sedang{ background:#f3f7cf;color:#6a7b00; }
    .status-sudah{ background:#d9f3e7;color:#0b492c; }
    .status-terlambat{ background:#7a2c2c;color:#fff; }
    .status-pending{ background:#fff3cd;color:#856404; }
    .status-approved{ background:#cff4fc;color:#055160; }
    .status-ditolak{ background:#f8d7da;color:#721c24; }
    .status-selesai{ background:#28a745;color:#fff; }
    .status-menunggu-submit{ background:#e2e3e5;color:#383d41; }

    #fSearch:focus{ border-color:#000; box-shadow:none; outline:none; }

    @media print{
      .shadow-soft,.shadow-strong,.table-wrapper{ box-shadow:none !important; }
      .table thead th{ background:#eee !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
      .table-bordered{ border:2px solid #000 !important; }
      .table-bordered> :not(caption)>*>*{ border-color:#000 !important; }
      .navbar{ display:none; }
    }
  </style>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

<div class="container pt-5">
  <div style="height: 84px"></div>

  <div class="toolbar d-flex flex-wrap gap-3 align-items-center mb-4">

    <div class="btn-group">
      <button type="button" class="btn btn-outline-success px-4" style="border-color:#0b492c;color:#0b492c"
        onclick="downloadLaporan('xlsx')">
        <i class="bi bi-download me-2"></i>Unduh
      </button>
      <button type="button" class="btn btn-outline-success dropdown-toggle dropdown-toggle-split"
        data-bs-toggle="dropdown" style="border-color:#0b492c;color:#0b492c">
        <span class="visually-hidden">Toggle Dropdown</span>
      </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#" onclick="downloadLaporan('xlsx')">.XLSX</a></li>
        <li><a class="dropdown-item" href="#" onclick="downloadLaporan('csv')">.CSV</a></li>
        <li><a class="dropdown-item" href="#" onclick="downloadLaporan('pdf')">.PDF</a></li>
      </ul>
    </div>

    {{-- Periode: CUSTOM ONLY --}}
    <div class="dropdown">
      <button class="btn btn-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" id="btnPeriode">
        Pilih Tanggal
      </button>

      <div class="dropdown-menu p-3" style="min-width: 320px;">
        <div class="mb-2 fw-semibold" style="color:#0b492c">Custom Range</div>

        <div class="row g-2">
          <div class="col-6">
            <label class="form-label small mb-1">Dari</label>
            <input id="fFrom" type="date" class="form-control">
          </div>
          <div class="col-6">
            <label class="form-label small mb-1">Sampai</label>
            <input id="fTo" type="date" class="form-control">
          </div>
        </div>

        <button id="btnApplyRange" class="btn btn-success w-100 mt-3" type="button"
          style="background-color:#0b492c;border-color:#0b492c">
          Terapkan
        </button>
      </div>
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

    {{-- Search (UI TIDAK DIUBAH) --}}
    <div class="input-group" style="max-width:320px">
      <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
      <input id="fSearch" type="text" class="form-control" placeholder="Cari laporan...">
    </div>
  </div>

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
              $statusClass = match ($r->status) {
                'Sedang Dipinjam' => 'status-sedang',
                'Menunggu Submit' => 'status-menunggu-submit',
                'Sudah Kembali' => 'status-sudah',
                'Terlambat' => 'status-terlambat',
                'Siap Diambil' => 'status-approved',
                'Menunggu Approve' => 'status-pending',
                'Ditolak' => 'status-ditolak',
                default => 'status-sedang'
              };
            @endphp

            <tr data-kategori="{{ $r->kategori }}" data-status="{{ $r->status }}">
              <td>{{ $i + 1 }}</td>
              <td class="td-nama">{{ $r->nama_item }}</td>
              <td>{{ $r->kategori }}</td>
              <td>{{ $r->peminjam }}</td>
              <td>{{ $r->tgl_pinjam }}</td>
              <td>{{ $r->jatuh_tempo }}</td>
              <td>{{ $r->tgl_kembali }}</td>
              <td class="text-center td-jumlah">{{ $r->jumlah }}</td>
              <td class="text-center">
                <span class="badge-status {{ $statusClass }}">{{ $r->status }}</span>
              </td>
            </tr>
          @endforeach
        </tbody>

      </table>
    </div>
  </div>

  <div class="card shadow-strong p-3 mb-5">
    <h6 class="mb-3">Top 10 yang paling sering dipinjam (ikut filter)</h6>
    <canvas id="chartTop"></canvas>
  </div>
</div>

@push('scripts')
  <script>
    const exportBase = @json(route('pengelola.laporan.export', ['format' => '__fmt__']));

    // parser tanggal (support MM/DD/YYYY, YYYY-MM-DD, dan yg ada jamnya)
    function parseDateAny(s) {
      if (!s) return null;
      s = String(s).trim();
      if (!s || s === "-") return null;

      s = s.split(" ")[0];

      if (s.includes("-")) {
        const p = s.split("-");
        if (p.length >= 3) {
          const y = parseInt(p[0], 10);
          const m = parseInt(p[1], 10);
          const d = parseInt(p[2], 10);
          if (y && m && d) return new Date(y, m - 1, d);
        }
      }

      if (s.includes("/")) {
        const p = s.split("/");
        if (p.length >= 3) {
          const m = parseInt(p[0], 10);
          const d = parseInt(p[1], 10);
          const y = parseInt(p[2], 10);
          if (y && m && d) return new Date(y, m - 1, d);
        }
      }

      return null;
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

    // state
    let rangeFrom = "";
    let rangeTo = "";
    let vKategori = "all";
    let vStatus = "all";

    const inputSearch = document.getElementById("fSearch");
    const tbody = document.getElementById("tbodyLaporan");
    const inputFrom = document.getElementById("fFrom");
    const inputTo = document.getElementById("fTo");
    const btnApply = document.getElementById("btnApplyRange");
    const btnPeriode = document.getElementById("btnPeriode");

    // ===== CHART =====
    let chartTop = null;

    function buildTop10FromVisibleRows() {
      if (!tbody) return { labels: [], data: [], isDummy: false };

      const mapCount = new Map();

      Array.from(tbody.querySelectorAll("tr")).forEach((tr) => {
        // chart IGNORE search, hanya ikut base filter
        if (tr.dataset.baseVisible !== "1") return;

        const nama = tr.querySelector(".td-nama")?.textContent?.trim() || "-";
        const jumlah = parseInt(tr.querySelector(".td-jumlah")?.textContent || "1", 10) || 1;
        mapCount.set(nama, (mapCount.get(nama) || 0) + jumlah);
      });

      const sorted = Array.from(mapCount.entries())
        .sort((a, b) => b[1] - a[1])
        .slice(0, 10);

      if (sorted.length === 0) {
        return {
          labels: ["Proyektor","Meja","Speaker","Terpal","Sofa","Hijab","Ruang Utama","Selasar","Zoom","Ruang VIP"],
          data: [12,10,9,8,7,6,5,4,3,2],
          isDummy: true
        };
      }

      return {
        labels: sorted.map(x => x[0]),
        data: sorted.map(x => x[1]),
        isDummy: false
      };
    }

    function renderOrUpdateChart() {
      const ctx = document.getElementById("chartTop");
      if (!ctx || !window.Chart) return;

      const top = buildTop10FromVisibleRows();
      const barColor = "#a0cdf0";
      const barBorder = "#ffffff";

      if (chartTop) {
        chartTop.data.labels = top.labels;
        chartTop.data.datasets[0].data = top.data;
        chartTop.data.datasets[0].backgroundColor = barColor;
        chartTop.data.datasets[0].borderColor = barBorder;
        chartTop.update();
        return;
      }

      chartTop = new Chart(ctx, {
        type: "bar",
        data: {
          labels: top.labels,
          datasets: [{
            label: "Dipinjam",
            data: top.data,
            backgroundColor: barColor,
            borderColor: barBorder,
            borderWidth: 1
          }],
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: true },
            tooltip: { callbacks: { label: (c) => c.parsed.y + (top.isDummy ? " (Contoh)" : "") } }
          },
          scales: { y: { beginAtZero: true } }
        },
      });
    }

    function formatBtnRangeLabel(from, to) {
      if (!from && !to) return "Pilih Tanggal";
      if (from && !to) return `Dari ${from}`;
      if (!from && to) return `Sampai ${to}`;

      const f = new Date(from + "T00:00:00");
      const t = new Date(to + "T00:00:00");
      const fmt = (d) => String(d.getMonth()+1).padStart(2,'0') + "/" + String(d.getDate()).padStart(2,'0') + "/" + d.getFullYear();
      return `${fmt(f)} - ${fmt(t)}`;
    }

    // ===== FILTER =====
    function filterTable() {
      if (!tbody) return;

      // ✅ FIX: filter aktif kalau from ATAU to terisi (tidak harus dua-duanya)
      const from = rangeFrom ? new Date(rangeFrom + "T00:00:00") : null;
      const to = rangeTo ? new Date(rangeTo + "T23:59:59") : null;

      const q = (inputSearch?.value || "").toLowerCase().trim();
      let rowIdx = 0;

      // baseVisible: tanggal(custom) + kategori + status (tanpa search)
      Array.from(tbody.querySelectorAll("tr")).forEach((tr) => {
        const kategori = tr.getAttribute("data-kategori") || "";
        const status = tr.getAttribute("data-status") || "";

        const tglPinjamText = tr.children[4]?.textContent || "";
        const tglPinjam = parseDateAny(tglPinjamText);

        let okPeriode = true;
        if (from || to) {
          okPeriode = !!tglPinjam;
          if (okPeriode && from) okPeriode = tglPinjam >= from;
          if (okPeriode && to) okPeriode = tglPinjam <= to;
        }

        const okKategori = vKategori === "all" || kategori === vKategori;
        const okStatus = vStatus === "all" || status === vStatus;

        tr.dataset.baseVisible = (okPeriode && okKategori && okStatus) ? "1" : "0";
      });

      // tampil tabel: baseVisible + search (search semua kolom)
      Array.from(tbody.querySelectorAll("tr")).forEach((tr) => {
        const baseVisible = tr.dataset.baseVisible === "1";

        const cellsText = Array.from(tr.children)
          .map(td => (td.textContent || "").toLowerCase())
          .join(" ");

        const okSearch = (q === "") || cellsText.includes(q);
        const visibleForTable = baseVisible && okSearch;

        tr.style.display = visibleForTable ? "" : "none";

        if (visibleForTable) {
          rowIdx += 1;
          tr.children[0].textContent = rowIdx;
        }
      });

      renderOrUpdateChart();
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

    // ===== DOWNLOAD =====
    function downloadLaporan(format) {
      const q = encodeURIComponent(inputSearch?.value || "");
      const urlBase = exportBase.replace('__fmt__', format);

      const url =
        urlBase
        + `?periode=custom`
        + `&from=${encodeURIComponent(rangeFrom || "")}`
        + `&to=${encodeURIComponent(rangeTo || "")}`
        + `&kategori=${encodeURIComponent(vKategori)}`
        + `&status=${encodeURIComponent(vStatus)}`
        + `&q=${q}`;

      window.location.href = url;
    }
    window.downloadLaporan = downloadLaporan;

    window.addEventListener("load", () => {
      hookupFilterDropdown("btnKategori", (v) => (vKategori = v));
      hookupFilterDropdown("btnStatus", (v) => (vStatus = v));

      if (inputSearch) inputSearch.addEventListener("input", filterTable);

      if (btnApply) btnApply.addEventListener("click", () => {
        rangeFrom = inputFrom?.value || "";
        rangeTo = inputTo?.value || "";

        // swap kalau kebalik
        if (rangeFrom && rangeTo && rangeFrom > rangeTo) {
          const tmp = rangeFrom;
          rangeFrom = rangeTo;
          rangeTo = tmp;
          if (inputFrom) inputFrom.value = rangeFrom;
          if (inputTo) inputTo.value = rangeTo;
        }

        if (btnPeriode) btnPeriode.textContent = formatBtnRangeLabel(rangeFrom, rangeTo);

        const dd = bootstrap.Dropdown.getOrCreateInstance(btnPeriode);
        dd.hide();

        filterTable();
      });

      syncMenuWidth();
      window.addEventListener("resize", syncMenuWidth);

      // ✅ FIX: TIDAK set default hari ini (biar ga ngunci hasil)
      rangeFrom = inputFrom?.value || "";
      rangeTo = inputTo?.value || "";
      if (btnPeriode) btnPeriode.textContent = formatBtnRangeLabel(rangeFrom, rangeTo);

      filterTable();
    });

    document.addEventListener("livewire:init", () => {
      if (window.Livewire) {
        Livewire.hook('morph.updated', () => setTimeout(filterTable, 0));
      }
    });
    document.addEventListener("livewire:navigated", () => filterTable());
  </script>
@endpush
