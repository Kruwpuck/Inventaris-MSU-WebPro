<div>
  @push('styles')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap">
    <!-- Fix animations -->
    <style>
        .reveal-up, .drop-in { opacity: 1 !important; transform: none !important; }
    </style>
  @endpush

  <!-- HERO -->
  <header id="beranda" class="hero">
    <div class="hero-bg" style="background-image:url('{{ asset('assets/plaza1.png') }}');"></div>
    <div class="hero-overlay"></div>
    <div class="container text-center">
      <h1 class="hero-title">
        SELAMAT DATANG DI MASJID <br /> SYAMSUL ULUM
      </h1>
    </div>
  </header>

  <!-- PROMO -->
  <section class="promo">
    <div class="container text-center position-relative">
      <h2 class="promo-title">
        Satu langkah menuju <span class="text-highlight">kemudahan beraktivitas</span> di MSU
      </h2>
      <p class="promo-subtitle mt-2">
        Semua urusan peminjaman dan perizinan kini bisa dilakukan secara online.
      </p>
      <a href="#pinjam-barang" class="btn btn-panduan mt-3">
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
          <div class="flow-card tap-anim">
            <span class="flow-step-badge">1</span>
            <div class="flow-thumb">
              <img src="{{ asset('assets/peminjam.jpeg') }}" alt="Mengisi Form Peminjaman" class="img-fluid">
            </div>
            <div class="flow-caption">Mengisi Form Peminjaman</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim">
            <span class="flow-step-badge">2</span>
            <div class="flow-thumb">
              <img src="{{ asset('assets/setuju.jpeg') }}" alt="Pengelola Menyetujui" class="img-fluid">
            </div>
            <div class="flow-caption">Pengelola Menyetujui</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim">
            <span class="flow-step-badge">3</span>
            <div class="flow-thumb">
              <img src="{{ asset('assets/jabat.jpg') }}" alt="Pengurus Melakukan COD" class="img-fluid">
            </div>
            <div class="flow-caption">Pengurus Melakukan COD</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim">
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
    <section class="datebar mb-4" id="bookingMetaBar">
        <div class="container p-0">
            <h3 class="section-title mb-3 d-flex align-items-center gap-2" style="font-size:1.25rem;">
                <i class="bi bi-clock-history"></i> Waktu Peminjaman
            </h3>

            <div class="datebar-wrap">
                <!-- Exact HTML structure from index.html -->
                <div class="datebar-field datebar-field--start">
                    <label for="dateStart" class="form-label mb-1"><i class="bi bi-calendar-event me-1"></i>Tanggal Pakai</label>
                    <input id="dateStart" type="date" class="form-control form-control-sm">
                </div>
                <div class="datebar-field datebar-field--end">
                    <label for="dateEnd" class="form-label mb-1"><i class="bi bi-calendar-check me-1"></i>Tanggal Kembali</label>
                    <input id="dateEnd" type="date" class="form-control form-control-sm">
                </div>
                <div class="datebar-field datebar-field--time">
                    <label for="timeStart" class="form-label mb-1"><i class="bi bi-alarm me-1"></i>Jam Mulai</label>
                    <input id="timeStart" type="time" class="form-control form-control-sm">
                </div>
                <div class="datebar-field datebar-field--dur">
                    <label for="durationSel" class="form-label mb-1"><i class="bi bi-hourglass-split me-1"></i>Durasi</label>
                    <select id="durationSel" class="form-select form-select-sm">
                        <option value="1">1 jam</option>
                        <option value="2">2 jam</option>
                        <option value="3">3 jam</option>
                        <option value="4">4 jam</option>
                        <option value="8">Seharian</option>
                    </select>
                </div>
                <div class="datebar-actions">
                    <button id="btnSetDates" class="btn btn-success btn-sm w-100"><i class="bi bi-search me-1"></i>Cek Ketersediaan</button>
                    <div class="small text-muted mt-2 js-daterange" aria-live="polite"></div>
                </div>
            </div>
        </div>
    </section>
  </div>

  <!-- PINJAM BARANG -->
  <section id="pinjam-barang" class="py-4">
    <div class="container">
      <h3 class="section-title mb-3">Pinjam Barang</h3>
      <div class="row g-4 align-items-stretch items-grid">
        @forelse($items as $item)
        @php
            $cart = session('cart', []);
            $inCart = $cart[$item->id]['quantity'] ?? 0;
            $sisa = max(0, $item->stock - $inCart);
        @endphp
        <div class="col-6 col-md-4 col-lg-2-4">
            <article class="item-card tap-anim h-100" data-type="barang">
                <div class="item-thumb">
                    <img src="{{ asset('assets/' . $item->image_path) }}" 
                         alt="{{ $item->name }}" 
                         class="img-fluid"
                         onerror="this.src='{{ asset('assets/placeholder.jpg') }}'">
                    
                    @if($sisa == 0)
                        <span class="badge-status" style="background: #a94442;">Habis</span>
                    @else
                        <span class="badge-status">Active</span>
                    @endif

                    <div class="qty-actions">
                         <button class="qty-btn" wire:click="decrement({{ $item->id }})" 
                                 @if($inCart <= 0) disabled @endif>−</button>
                         <button class="qty-btn" wire:click="addToCart({{ $item->id }})" 
                                 @if($sisa <= 0) disabled @endif
                                 wire:loading.attr="disabled">＋</button>
                    </div>
                </div>
                <div class="item-body">
                    <div class="item-title">{{ $item->name }}</div>
                    <div class="item-meta">Sisa : <span class="sisa">{{ $sisa }}</span></div>
                </div>
            </article>
        </div>
        @empty
           <div class="col-12"><p class="text-muted text-center w-100">Tidak ada barang.</p></div>
        @endforelse

        <div class="col-12 col-md-4 col-lg-2-4">
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
      <div class="row g-4 align-items-stretch items-grid">
        @forelse($facilities as $room)
        @php
            $cart = session('cart', []);
            $inCart = $cart[$room->id]['quantity'] ?? 0;
            $max = 1; 
            $sisa = max(0, $max - $inCart);
        @endphp
        <div class="col-6 col-md-4 col-lg-2-4">
            <article class="item-card tap-anim h-100" data-type="ruang">
                <div class="item-thumb">
                    <img src="{{ asset('assets/' . $room->image_path) }}" 
                         alt="{{ $room->name }}" 
                         class="img-fluid" 
                         onerror="this.src='{{ asset('assets/placeholder.jpg') }}'">
                    
                    @if($sisa == 0)
                         <span class="badge-status" style="background: #a94442;">Habis</span>
                    @else
                         <span class="badge-status">Active</span>
                    @endif

                    <div class="qty-actions">
                         <button class="qty-btn" wire:click="decrement({{ $room->id }})" 
                                 @if($inCart <= 0) disabled @endif>−</button>
                         <button class="qty-btn" wire:click="addToCart({{ $room->id }})" 
                                 @if($sisa <= 0) disabled @endif
                                 wire:loading.attr="disabled">＋</button>
                    </div>
                </div>
                <div class="item-body">
                    <div class="item-title">{{ $room->name }}</div>
                    <div class="item-meta">Tersedia : <span class="sisa">{{ $sisa }}</span></div>
                </div>
            </article>
        </div>
        @empty
        @endforelse
        
        <div class="col-12 col-md-4 col-lg-2-4">
          <a href="{{ route('guest.catalogue.ruangan') }}" class="cta-card tap-anim h-100 text-decoration-none">
            <div class="cta-plus">+</div>
            <div>Cari Ruang</div>
          </a>
        </div>
      </div>
    </div>
  </section>
</div>
