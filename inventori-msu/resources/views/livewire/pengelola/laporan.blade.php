{{-- resources/views/livewire/pengelola/laporan.blade.php --}}

@push('head')
  <style>
    body {
      background: #fff;
      font-family: "Poppins", sans-serif;
    }

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
      box-shadow: 0 10px 24px rgba(0, 0, 0, .1);
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
      box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
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

    .table-bordered {
      border: 1.5px solid #b7c1cc !important;
    }

    .table-bordered> :not(caption)>* {
      border-width: 1.5px 0;
    }

    .table-bordered> :not(caption)>*>* {
      border-color: #b7c1cc !important;
      padding: .8rem .75rem;
      vertical-align: middle;
      font-size: .95rem;
    }

    .badge-status {
      display: inline-block;
      min-width: 130px;
      text-align: center;
      border-radius: 9999px;
      padding: .35rem .6rem;
      font-weight: 600;
      font-size: .8rem;
      white-space: nowrap;
    }

    .status-sedang {
      background: #f3f7cf;
      color: #6a7b00;
    }

    .status-sudah {
      background: #d9f3e7;
      color: #0b492c;
    }

    .status-terlambat {
      background: #7a2c2c;
      color: #fff;
    }

    .status-pending {
      background: #fff3cd;
      color: #856404;
    }

    .status-approved {
      background: #cff4fc;
      color: #055160;
    }

    .status-ditolak {
      background: #f8d7da;
      color: #721c24;
    }

    .status-selesai {
      background: #28a745;
      color: #fff;
    }

    .status-menunggu-submit {
      background: #e2e3e5;
      color: #383d41;
    }

    #fSearch:focus {
      border-color: #000;
      box-shadow: none;
      outline: none;
    }

    @media print {

      .shadow-soft,
      .shadow-strong,
      .table-wrapper {
        box-shadow: none !important;
      }

      .table thead th {
        background: #eee !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      .table-bordered {
        border: 2px solid #000 !important;
      }

      .table-bordered> :not(caption)>*>* {
        border-color: #000 !important;
      }

      .navbar {
        display: none;
      }
    }
  </style>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

<div class="container pt-5">
  <div style="height: 84px"></div>

  {{-- Top Filters --}}
  <div class="toolbar d-flex flex-column flex-lg-row gap-3 mb-4">
    
    <div class="d-flex flex-column flex-md-row gap-3 flex-grow-1">
      {{-- Export Group --}}
      <div class="btn-group w-100 w-md-auto">
        <button type="button" class="btn btn-outline-success px-4 d-flex align-items-center" style="border-color:#0b492c;color:#0b492c" 
            onclick="downloadLaporan('xlsx', '{{ $q }}', '{{ $vKategori }}', '{{ $vStatus }}', '{{ $dateFrom }}', '{{ $dateTo }}')">
          <i class="bi bi-download me-2"></i><span>Unduh</span>
        </button>
        <button type="button" class="btn btn-outline-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" style="border-color:#0b492c;color:#0b492c">
          <span class="visually-hidden">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="#" onclick="downloadLaporan('xlsx', '{{ $q }}', '{{ $vKategori }}', '{{ $vStatus }}', '{{ $dateFrom }}', '{{ $dateTo }}')">.XLSX</a></li>
          <li><a class="dropdown-item" href="#" onclick="downloadLaporan('csv', '{{ $q }}', '{{ $vKategori }}', '{{ $vStatus }}', '{{ $dateFrom }}', '{{ $dateTo }}')">.CSV</a></li>
          <li><a class="dropdown-item" href="#" onclick="downloadLaporan('pdf', '{{ $q }}', '{{ $vKategori }}', '{{ $vStatus }}', '{{ $dateFrom }}', '{{ $dateTo }}')">.PDF</a></li>
        </ul>
      </div>

      {{-- Date Range --}}
      <div class="dropdown w-100 w-md-auto">
        <button class="btn btn-pill dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="btnPeriode">
            @if($dateFrom || $dateTo)
                {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') : '...' }} - {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d/m/Y') : '...' }}
            @else
                Pilih Tanggal
            @endif
        </button>
        <div class="dropdown-menu p-3" style="min-width: 320px;">
          <div class="mb-2 fw-semibold" style="color:#0b492c">Rentang Waktu</div>
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label small mb-1">Dari</label>
              <input type="date" class="form-control" wire:model.live="dateFrom">
            </div>
            <div class="col-6">
              <label class="form-label small mb-1">Sampai</label>
              <input type="date" class="form-control" wire:model.live="dateTo">
            </div>
          </div>
          <button class="btn btn-success w-100 mt-3 btn-sm" wire:click="$set('dateFrom', null); $set('dateTo', null)" style="background-color:#0b492c;border-color:#0b492c">Reset</button>
        </div>
      </div>

      {{-- Kategori --}}
      <div class="dropdown w-100 w-md-auto">
        <button class="btn btn-pill dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
          {{ $vKategori === 'all' ? 'Semua Kategori' : $vKategori }}
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item {{ $vKategori == 'all' ? 'active' : '' }}" href="#" wire:click.prevent="$set('vKategori', 'all')">Semua Kategori</a></li>
          <li><a class="dropdown-item {{ $vKategori == 'Barang' ? 'active' : '' }}" href="#" wire:click.prevent="$set('vKategori', 'Barang')">Barang</a></li>
          <li><a class="dropdown-item {{ $vKategori == 'Ruangan' ? 'active' : '' }}" href="#" wire:click.prevent="$set('vKategori', 'Ruangan')">Ruangan</a></li>
        </ul>
      </div>

      {{-- Status --}}
      <div class="dropdown w-100 w-md-auto">
        <button class="btn btn-pill dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
          {{ $vStatus === 'all' ? 'Semua Status' : $vStatus }}
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item {{ $vStatus == 'all' ? 'active' : '' }}" href="#" wire:click.prevent="$set('vStatus', 'all')">Semua Status</a></li>
          <li><a class="dropdown-item {{ $vStatus == 'Sedang Dipinjam' ? 'active' : '' }}" href="#" wire:click.prevent="$set('vStatus', 'Sedang Dipinjam')">Sedang Dipinjam</a></li>
          <li><a class="dropdown-item {{ $vStatus == 'Sudah Kembali' ? 'active' : '' }}" href="#" wire:click.prevent="$set('vStatus', 'Sudah Kembali')">Sudah Kembali</a></li>
          <li><a class="dropdown-item {{ $vStatus == 'Terlambat' ? 'active' : '' }}" href="#" wire:click.prevent="$set('vStatus', 'Terlambat')">Terlambat</a></li>
           <li><a class="dropdown-item {{ $vStatus == 'Siap Diambil' ? 'active' : '' }}" href="#" wire:click.prevent="$set('vStatus', 'Siap Diambil')">Siap Diambil</a></li>
        </ul>
      </div>
    </div>

    {{-- Search --}}
    <div class="input-group w-100 w-lg-auto" style="min-width: 250px;">
      <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
      <input type="text" class="form-control" placeholder="Cari laporan..." wire:model.live.debounce.300ms="q">
    </div>
  </div>

  {{-- Per Page Selector --}}
  <div class="d-flex align-items-center mb-3">
    <span class="me-2 text-muted small">Show</span>
    <select class="form-select form-select-sm w-auto" wire:model.live="perPage">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
    </select>
    <span class="ms-2 text-muted small">data</span>
  </div>

  <div class="table-wrapper mb-4">
    <div class="table-responsive" style="overflow-x: auto;">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th style="width:48px">No</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Peminjam</th>
            <th>Waktu Pinjam</th>
            <th>Jatuh Tempo</th>
            <th>Waktu Kembali</th>
            <th class="text-center">Jumlah</th>
            <th class="text-center">Status</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
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
            <tr>
              <td>{{ $laporans->firstItem() + $loop->index }}</td>
              <td class="td-nama">{{ $r->nama_item }}</td>
              <td>{{ $r->kategori }}</td>
              <td>{{ $r->peminjam }}</td>
              <td>{{ $r->waktu_pinjam }}</td>
              <td>{{ $r->jatuh_tempo }}</td>
              <td>{{ $r->waktu_kembali }}</td>
              <td class="text-center td-jumlah">{{ $r->jumlah }}</td>
              <td class="text-center">
                <span class="badge-status {{ $statusClass }}">{{ $r->status }}</span>
              </td>
              <td>{{ $r->keterangan }}</td>
            </tr>
          @endforeach

          @if($laporans->isEmpty())
          <tr>
            <td colspan="10" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                Tidak ada data laporan.
            </td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>
    
    <div class="mt-3">
        {{ $laporans->links() }}
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
    let chartTop = null;

    function downloadLaporan(format, q, k, s, from, to) {
      const urlBase = exportBase.replace('__fmt__', format);
      const url = new URL(urlBase, window.location.origin);
      
      if(q) url.searchParams.set('q', q);
      if(k && k !== 'all') url.searchParams.set('kategori', k);
      if(s && s !== 'all') url.searchParams.set('status', s);
      if(from) url.searchParams.set('from', from);
      if(to) url.searchParams.set('to', to);
      
      // Mark as custom so controller knows to look for from/to
      url.searchParams.set('periode', 'custom');

      window.location.href = url.toString();
    }
    window.downloadLaporan = downloadLaporan;

    // CHART LOGIC
    const chartLabels = @json($chartLabels);
    const chartValues = @json($chartValues);
    const isDummy = @json($isDummyChart);

    function initChart(labels, values, isDummy) {
        const ctx = document.getElementById("chartTop");
        if (!ctx || !window.Chart) return;
        
        if(chartTop) { chartTop.destroy(); }

        const barColor = "#a0cdf0";
        const barBorder = "#ffffff";

        chartTop = new Chart(ctx, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: "Dipinjam",
                    data: values,
                    backgroundColor: barColor,
                    borderColor: barBorder,
                    borderWidth: 1
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    tooltip: { callbacks: { label: (c) => c.parsed.y + (isDummy ? " (Contoh)" : "") } }
                },
                scales: { y: { beginAtZero: true } }
            },
        });
    }

    // Initialize Chart on Load
    document.addEventListener('DOMContentLoaded', () => {
        initChart(chartLabels, chartValues, isDummy);
    });

    // Listen for livewire updates via dispatch
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('chartUpdate', (data) => {
            const payload = Array.isArray(data) ? data[0] : data;
            if(payload && payload.labels) {
                initChart(payload.labels, payload.values, payload.isDummy);
            }
        });
    });
  </script>
@endpush