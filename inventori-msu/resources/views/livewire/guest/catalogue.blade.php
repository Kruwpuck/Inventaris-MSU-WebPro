@push('styles')
    @if($category == 'barang')
        <link rel="stylesheet" href="{{ asset('fe-guest/barang.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('fe-guest/ruangan.css') }}">
    @endif
@endpush

<main class="container py-4">

    <!-- HERO -->
    <section class="hero mb-4 reveal-up">
      <img src="{{ asset('fe-guest/plaza1.png') }}" alt="Plaza Masjid">
      <div class="caption">
        <div>
          <h1 class="mb-2 drop-in">{{ $category == 'barang' ? 'Peminjaman Barang' : 'Peminjaman Ruang' }}</h1>
          <p class="m-0">Semua urusan peminjaman dan perizinan kini bisa dilakukan secara online.</p>
        </div>
      </div>
    </section>

    <!-- SEARCH -->
    <section class="mb-3 reveal-up">
      <div class="search-wrap">
        <i class="bi bi-search"></i>
        <input id="searchInput" type="text" class="form-control search-input"
          placeholder="Cari {{ $category == 'barang' ? 'barang' : 'ruang' }}… (mis. {{ $category == 'barang' ? 'proyektor, sound system' : 'aula, rapat' }})">
        <button id="clearSearch" class="btn btn-clear" type="button" aria-label="Bersihkan pencarian">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <small class="text-muted">Ketik untuk memfilter daftar di bawah secara langsung.</small>
    </section>

    <!-- SCHEDULE FILTER -->
    <section class="schedule-filter mb-4 reveal-up">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <h5 class="card-title mb-3 fw-bold"><i class="bi bi-clock me-2"></i>Waktu Peminjaman</h5>
          <div class="row g-3">
            <!-- Row 1: Start Info -->
            <div class="col-md-3">
              <label class="form-label small fw-bold">Tanggal Pakai</label>
              <input type="date" class="form-control" id="filterDateStart">
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold">Jam Pakai</label>
              <input type="time" class="form-control" id="filterTimeStart">
            </div>

            <!-- Row 2: End Info -->
            <div class="col-md-3">
              <label class="form-label small fw-bold">Tanggal Kembali</label>
              <input type="date" class="form-control" id="filterDateEnd">
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold">Jam Kembali</label>
              <input type="time" class="form-control" id="filterTimeEnd">
            </div>
            <div class="col-12 d-flex align-items-end mt-3 gap-2">
              <div class="text-muted small me-auto" id="filterResultText">Silakan tentukan waktu peminjaman dulu.</div>
              <button id="btnShowCalendar" class="btn btn-outline-secondary" type="button" title="Lihat Jadwal Harian">
                <i class="bi bi-calendar3"></i>
              </button>
              <button class="btn btn-success" id="btnCheckAvailability">
                <i class="bi bi-search me-1"></i> Cek Ketersediaan
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <h3 class="section-title mb-3 reveal-up">Daftar {{ $category == 'barang' ? 'Barang' : 'Ruangan' }}</h3>

    <!-- GRID -->
    <section class="items-grid" id="itemsGrid">
        @foreach($items as $item)
        <div class="col reveal-up">
            <article class="item-card tap-anim" data-type="{{ $category == 'barang' ? 'barang' : 'ruang' }}" data-max="{{ $item->stock }}">
                <div class="item-thumb">
                    <img src="{{ asset('fe-guest/' . $item->image_path) }}" 
                         alt="{{ $item->name }}"
                         onerror="this.onerror=null; this.src='{{ asset('fe-guest/' . $item->image_path) }}'; this.onerror=function(){this.src='https://placehold.co/400';}">
                    
                    <span class="badge-status">Active</span>
                    
                    <div class="qty-actions">
                        <button class="qty-btn" type="button" data-action="inc" aria-label="Kurangi/Batalkan">−</button>
                        <button class="qty-btn" type="button" data-action="dec" aria-label="Tambah/Pilih">＋</button>
                    </div>
                </div>
                <div class="item-body">
                    <div class="item-title fw-bold">{{ $item->name }}</div>
                    <div class="item-desc text-muted small mb-2">{{ $item->description ?? '-' }}</div>
                    <div class="item-meta">
                        {{ $category == 'barang' ? 'Sisa' : 'Tersedia' }} : 
                        <span class="sisa">{{ $category == 'barang' ? $item->stock : 1 }}</span>
                    </div>
                </div>
            </article>
        </div>
        @endforeach
    </section>

    <p id="emptyState" class="text-center text-muted d-none mt-4">Tidak ada {{ $category == 'barang' ? 'barang' : 'ruang' }} yang cocok dengan pencarian.</p>

</main>
