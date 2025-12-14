<div>
  @push('styles')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap">
  @endpush

  <!-- HERO -->
  <header id="beranda" class="hero">
    <div class="hero-bg" style="background-image:url('{{ asset('assets/plaza1.png') }}');"></div>
    <div class="hero-overlay"></div>
    <div class="container text-center">
      <h1 class="hero-title drop-in">
        SELAMAT DATANG DI MASJID <br /> SYAMSUL ULUM
      </h1>
    </div>
  </header>

  <!-- PROMO -->
  <section class="promo">
    <div class="container text-center position-relative">
      <h2 class="promo-title reveal-up">
        Satu langkah menuju <span class="text-highlight">kemudahan beraktivitas</span> di MSU
      </h2>
      <p class="promo-subtitle mt-2 reveal-up" style="transition-delay:.1s">
        Semua urusan peminjaman dan perizinan kini bisa dilakukan secara online.
      </p>
      <a href="#pinjam-barang" class="btn btn-panduan mt-3 reveal-up" style="transition-delay:.2s">
        Baca Panduan
      </a>
    </div>
  </section>

  <!-- DESKRIPSI -->
  <section class="py-5">
    <div class="container">
      <h3 class="section-title mb-3">Deskripsi Aplikasi</h3>
      <p class="mb-0">
        Aplikasi Inventory MSU memudahkan pengecekan ketersediaan, peminjaman, dan pengembalian barang/fasilitas secara tertib dan tercatat.
      </p>
    </div>
  </section>

  <!-- FLOW PEMINJAMAN -->
  <section class="pb-4">
    <div class="container">
      <h3 class="section-title mb-4">Flow Peminjaman</h3>
      <div class="row g-4 flow-steps">
        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim reveal-up">
            <span class="flow-step-badge">1</span>
            <div class="flow-thumb">
              <img src="{{ asset('assets/peminjam.jpeg') }}" alt="Mengisi Form Peminjaman" class="img-fluid">
            </div>
            <div class="flow-caption">Mengisi Form Peminjaman</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim reveal-up" style="transition-delay:.05s">
            <span class="flow-step-badge">2</span>
            <div class="flow-thumb">
              <img src="{{ asset('assets/setuju.jpeg') }}" alt="Pengelola Menyetujui" class="img-fluid">
            </div>
            <div class="flow-caption">Pengelola Menyetujui</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim reveal-up" style="transition-delay:.1s">
            <span class="flow-step-badge">3</span>
            <div class="flow-thumb">
              <img src="{{ asset('assets/jabat.jpg') }}" alt="Pengurus Melakukan COD" class="img-fluid">
            </div>
            <div class="flow-caption">Pengurus Melakukan COD</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim reveal-up" style="transition-delay:.15s">
            <span class="flow-step-badge">4</span>
            <div class="flow-thumb">
              <img src="{{ asset('assets/balik.jpg') }}" alt="Pengembalian & Selesai" class="img-fluid">
            </div>
            <div class="flow-caption">Pengembalian & Selesai</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- WAKTU PEMINJAMAN (Datebar) -->
  <div class="container">
    <section class="datebar-v2 mb-4 reveal-up" id="bookingMetaBar">
        <h3 class="section-title mb-4 d-flex align-items-center gap-2" style="font-size:1.25rem; padding-left: 4px;">
          <i class="bi bi-clock-history"></i> Waktu Peminjaman
        </h3>

        <div class="datebar-content">
            <div class="inputs-row d-flex gap-4 align-items-end mb-4">
                <div class="flex-fill">
                    <label for="dateStart" class="form-label mb-2 fw-bold"><i class="bi bi-calendar-event me-2"></i>Tanggal Pakai</label>
                    <input id="dateStart" type="date" class="form-control" style="padding: 10px;">
                </div>
                <div class="flex-fill">
                    <label for="timeStart" class="form-label mb-2 fw-bold"><i class="bi bi-alarm me-2"></i>Jam Mulai</label>
                    <input id="timeStart" type="time" class="form-control" style="padding: 10px;">
                </div>
                <div class="flex-fill">
                    <label for="durationSel" class="form-label mb-2 fw-bold"><i class="bi bi-hourglass-split me-2"></i>Durasi</label>
                    <select id="durationSel" class="form-select" style="padding: 10px;">
                    <option value="1">1 jam</option>
                    <option value="2">2 jam</option>
                    <option value="3">3 jam</option>
                    <option value="4">4 jam</option>
                    <option value="8">Seharian</option>
                    </select>
                </div>
            </div>
            <div class="action-row">
                <button id="btnSetDates" class="btn btn-success w-100 py-2 fw-bold" style="font-size: 1rem;"><i class="bi bi-search me-2"></i>Cek Ketersediaan</button>
                <div class="small text-muted mt-2 js-daterange text-center" aria-live="polite"></div>
            </div>
        </div>
    </section>
  </div>

  <!-- PINJAM BARANG -->
  <section id="pinjam-barang" class="py-4">
    <div class="container">
      <h3 class="section-title mb-3">Pinjam Barang</h3>
      <div class="items-grid">
        @forelse($items as $item)
        <div class="col reveal-up">
            <article class="item-card tap-anim h-100" data-type="barang" data-max="{{ $item->stock }}">
                <div class="item-thumb">
                <img src="{{ asset('assets/' . $item->image_path) }}" alt="{{ $item->name }}" class="img-fluid" onerror="this.onerror=null; this.src='{{ asset('assets/' . $item->image_path) }}'; this.onerror=function(){this.src='https://placehold.co/400';}">
                <span class="badge-status">Active</span>
                <div class="qty-actions">
                    <button type="button" class="qty-btn" data-action="inc">−</button>
                    <button type="button" class="qty-btn" data-action="dec">＋</button>
                </div>
                </div>
                <div class="item-body">
                <div class="item-title">{{ $item->name }}</div>
                <div class="item-meta">Sisa : <span class="sisa">{{ $item->stock }}</span></div>
                </div>
            </article>
        </div>
        @empty
        <div class="col"><p class="text-muted text-center w-100">Tidak ada barang.</p></div>
        @endforelse

        <div class="col reveal-up">
          <a href="{{ route('guest.catalogue.barang') }}" class="cta-card tap-anim h-100 text-decoration-none">
            <div class="cta-plus">+</div>
            <div>Cari Barang</div>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- PINJAM RUANGAN -->
  <section id="pinjam-fasilitas" class="py-4">
    <div class="container">
      <h3 class="section-title mb-3">Pinjam Ruangan</h3>
      <div class="items-grid">
        @forelse($facilities as $room)
        <div class="col reveal-up">
            <article class="item-card tap-anim h-100" data-type="ruang" data-max="1">
                <div class="item-thumb">
                <img src="{{ asset('assets/' . $room->image_path) }}" alt="{{ $room->name }}" class="img-fluid" onerror="this.onerror=null; this.src='{{ asset('assets/' . $room->image_path) }}'; this.onerror=function(){this.src='https://placehold.co/400';}">
                <span class="badge-status">Active</span>
                <div class="qty-actions">
                    <button type="button" class="qty-btn" data-action="inc">−</button>
                    <button type="button" class="qty-btn" data-action="dec">＋</button>
                </div>
                </div>
                <div class="item-body">
                <div class="item-title">{{ $room->name }}</div>
                <div class="item-meta">Sisa : <span class="sisa">1</span></div>
                </div>
            </article>
        </div>
        @empty
        @endforelse
        
        <div class="col reveal-up">
          <a href="{{ route('guest.catalogue.ruangan') }}" class="cta-card tap-anim h-100 text-decoration-none">
            <div class="cta-plus">+</div>
            <div>Cari Ruang</div>
          </a>


  @push('scripts')
    <script src="{{ asset('js/barang.js') }}?v={{ time() }}"></script>
  @endpush
</div>
