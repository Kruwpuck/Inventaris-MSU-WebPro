<div>
@push('styles')
<link rel="stylesheet" href="{{ asset('fe-guest/styles.css') }}">
@endpush
  <!-- HERO -->
  <header id="beranda" class="hero">
    <div class="hero-bg" style="background-image:url('{{ asset('fe-guest/plaza1.png') }}');"></div>
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
      <a href="{{ asset('fe-guest/panduan_layanan.pdf') }}" target="_blank" class="btn btn-panduan mt-3 reveal-up" style="transition-delay:.2s">
        Baca Panduan
      </a>
    </div>
  </section>

  <!-- DESKRIPSI -->
  <section class="py-5">
    <div class="container">
      <h3 class="section-title mb-3">Deskripsi Aplikasi</h3>
      <p class="mb-0">
        Aplikasi Inventory MSU memudahkan pengecekan ketersediaan, peminjaman, dan pengembalian barang/fasilitas secara
        tertib dan tercatat.
      </p>
    </div>
  </section>

  <!-- FLOW PEMINJAMAN (bernomor + panah) -->
  <section class="pb-4">
    <div class="container">
      <h3 class="section-title mb-4">Flow Peminjaman</h3>

      <div class="row g-4 flow-steps">
        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim reveal-up">
            <span class="flow-step-badge">1</span>
            <div class="flow-thumb">
              <img src="{{ asset('fe-guest/peminjam.jpeg') }}" alt="Mengisi Form Peminjaman" class="img-fluid">
            </div>
            <div class="flow-caption">Mengisi Form Peminjaman</div>
          </div>
        </div>

        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim reveal-up" style="transition-delay:.05s">
            <span class="flow-step-badge">2</span>
            <div class="flow-thumb">
              <img src="{{ asset('fe-guest/setuju.jpeg') }}" alt="Pengelola Menyetujui" class="img-fluid">
            </div>
            <div class="flow-caption">Pengelola Menyetujui</div>
          </div>
        </div>

        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim reveal-up" style="transition-delay:.1s">
            <span class="flow-step-badge">3</span>
            <div class="flow-thumb">
              <img src="{{ asset('fe-guest/jabat.jpg') }}" alt="Pengurus Melakukan COD" class="img-fluid">
            </div>
            <div class="flow-caption">Pengurus Melakukan COD</div>
          </div>
        </div>

        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim reveal-up" style="transition-delay:.15s">
            <span class="flow-step-badge">4</span>
            <div class="flow-thumb">
              <img src="{{ asset('fe-guest/balik.jpg') }}" alt="Pengembalian & Selesai" class="img-fluid">
            </div>
            <div class="flow-caption">Pengembalian & Selesai</div>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- WAKTU PEMINJAMAN -->
  <section class="datebar">
    <div class="container">
      <div class="reveal-up">
        <h5 class="section-title mb-3"><i class="bi bi-clock me-2"></i>Waktu Peminjaman</h5>
        <div class="row g-3">

          <!-- Row 1: Start Info -->
          <div class="col-md-3">
            <label class="form-label small fw-bold">Tanggal Pakai</label>
            <input id="dateStart" type="date" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Jam Pakai</label>
            <input id="timeStart" type="time" class="form-control">
          </div>

          <!-- Row 2: End Info -->
          <div class="col-md-3">
            <label class="form-label small fw-bold">Tanggal Kembali</label>
            <input id="dateEnd" type="date" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Jam Kembali</label>
            <input id="timeEnd" type="time" class="form-control">
          </div>
          <div class="col-12 d-flex align-items-end mt-3 gap-2">
            <div class="text-muted small js-daterange me-auto">Silakan tentukan waktu peminjaman dulu.</div>
            <button id="btnShowCalendar" class="btn btn-outline-secondary" type="button" title="Lihat Jadwal Harian">
              <i class="bi bi-calendar3"></i>
            </button>
            <button id="btnSetDates" class="btn btn-success">
              <i class="bi bi-search me-1"></i> Cek Ketersediaan
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- PINJAM BARANG -->
  <section id="pinjam-barang" class="py-4">
    <div class="container">
      <h3 class="section-title mb-3">Pinjam Barang</h3>

      <div class="row g-4 align-items-stretch items-grid">
        <!-- Dynamic Items Loop -->
        @foreach($items as $item)
        <div class="col-6 col-md-4 col-lg-2-4">
          <article class="item-card tap-anim reveal-up h-100" data-type="barang" data-max="{{ $item->stock }}">
            <div class="item-thumb">
              <img src="{{ asset('fe-guest/' . $item->image_path) }}" alt="{{ $item->name }}" class="img-fluid" onerror="this.onerror=null; this.src='{{ asset('fe-guest/' . $item->image_path) }}'; this.onerror=function(){this.src='https://placehold.co/400';}">
              <span class="badge-status">Active</span>
              <div class="qty-actions">
                <button type="button" class="qty-btn" data-action="inc">−</button>
                <button type="button" class="qty-btn" data-action="dec">＋</button>
              </div>
            </div>
            <div class="item-body">
              <div class="item-title fw-bold fs-5">{{ $item->name }}</div>
              <div class="item-desc text-muted small mb-2" style="font-size: 0.8rem;">{{ $item->description }}</div>
              <div class="item-meta fw-bold">Sisa : <span class="sisa">{{ $item->stock }}</span></div>
            </div>
          </article>
        </div>
        @endforeach

        <!-- CTA Card -->
        <div class="col-12 col-md-4 col-lg-2-4">
          <a href="{{ route('guest.catalogue.barang') }}" class="cta-card tap-anim reveal-up h-100 text-decoration-none">
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
      <div class="row g-4 align-items-stretch items-grid">
        <!-- Dynamic Items Loop -->
        @foreach($facilities as $item)
        <div class="col-6 col-md-4 col-lg-2-4">
          <article class="item-card tap-anim reveal-up h-100" data-type="ruang" data-max="1">
            <div class="item-thumb">
              <img src="{{ asset('fe-guest/' . $item->image_path) }}" alt="{{ $item->name }}" class="img-fluid" onerror="this.onerror=null; this.src='{{ asset('fe-guest/' . $item->image_path) }}'; this.onerror=function(){this.src='https://placehold.co/400';}">
              <span class="badge-status">Active</span>
              <div class="qty-actions">
                <button type="button" class="qty-btn" data-action="inc">−</button>
                <button type="button" class="qty-btn" data-action="dec">＋</button>
              </div>
            </div>
            <div class="item-body">
              <div class="item-title fw-bold fs-5">{{ $item->name }}</div>
              <div class="item-desc text-muted small mb-1" style="font-size: 0.75rem;">{{ $item->description }}</div>
              <div class="item-capacity text-muted small mb-2" style="font-size: 0.85rem;">Kapasitas : {{ $item->capacity ?? '-' }}</div>
              <div class="item-meta fw-bold">Sisa : <span class="sisa">1</span></div>
            </div>
          </article>
        </div>
        @endforeach

        <!-- CTA Card -->
        <div class="col-6 col-md-4 col-lg-2-4">
          <a href="{{ route('guest.catalogue.ruangan') }}" class="cta-card tap-anim reveal-up h-100 text-decoration-none">
            <div class="cta-plus">+</div>
            <div>Cari Ruang</div>
          </a>
        </div>
      </div>
    </div>
  </section>
</div>
