<div>
  <!-- HERO -->
  <header id="beranda" class="hero">
    <div class="hero-bg" style="background-image:url('{{ asset('aset/peminjam/plaza1.png') }}');"></div>
    <div class="hero-overlay"></div>
    <div class="container text-center">
      <h1 class="hero-title drop-in">
        SELAMAT DATANG DI MASJID <br /> SYAMSUL ULUM
      </h1>
    </div>
  </header>

  <!-- PROMO -->
  <section class="promo">
    <div class="promo-mosque" aria-hidden="true"></div>
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
       Aplikasi Inventory MSU adalah sistem peminjaman barang dan fasilitas Masjid Syamsul Ulum yang memudahkan pengguna untuk mengecek ketersediaan, melakukan peminjaman, hingga pengembalian secara teratur dan tercatat. Dengan aplikasi ini, proses administrasi menjadi lebih cepat, transparan, dan efisien.
      </p>
    </div>
  </section>

  <!-- FLOW PEMINJAMAN -->
  <section class="pb-5">
    <div class="container">
      <h3 class="section-title mb-4">Flow Peminjaman</h3>

      <div class="row g-4">
        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim reveal-up">
            <div class="flow-thumb">
              <img src="{{ asset('aset/peminjam/peminjam.jpeg') }}" alt="Mengisi Form Peminjaman" class="img-fluid">
            </div>
            <div class="flow-caption">Mengisi Form Peminjaman</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim reveal-up" style="transition-delay:.05s">
            <div class="flow-thumb">
              <img src="{{ asset('aset/peminjam/setuju.jpeg') }}" alt="Pengelola Menyetujui" class="img-fluid">
            </div>
            <div class="flow-caption">Pengelola Menyetujui</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim reveal-up" style="transition-delay:.1s">
            <div class="flow-thumb">
              <img src="{{ asset('aset/peminjam/jabat.jpg') }}" alt="Pengurus Melakukan COD" class="img-fluid">
            </div>
            <div class="flow-caption">Pengurus Melakukan COD</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="flow-card tap-anim reveal-up" style="transition-delay:.15s">
            <div class="flow-thumb">
              <img src="{{ asset('aset/peminjam/balik.jpg') }}" alt="Pengembalian & Selesai" class="img-fluid">
            </div>
            <div class="flow-caption">Pengembalian & Selesai</div>
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

        @foreach($items as $item)
        <div class="col-6 col-md-4 col-lg-2-4">
          <article class="item-card tap-anim reveal-up h-100" data-max="{{ $item->stock }}">
            <div class="item-thumb">
              <img src="{{ asset('aset/peminjam/' . $item->image_path) }}" alt="{{ $item->name }}" class="img-fluid">
              <span class="badge-status">Active</span>
              <div class="qty-actions">
                <!-- Logic add to cart will be handled by Livewire later or JS for now -->
                <button type="button" class="qty-btn" wire:click="addToCart({{ $item->id }})" aria-label="Tambahkan ke keranjang">＋</button>
              </div>
            </div>
            <div class="item-body">
              <div class="item-title">{{ $item->name }}</div>
              <div class="item-meta">Sisa : <span class="sisa">{{ $item->stock }}</span></div>
            </div>
          </article>
        </div>
        @endforeach

        <!-- CTA Cari Barang -->
        <div class="col-12 col-md-4 col-lg-2-4 order-last">
          <a href="{{ route('catalogue.barang') }}" class="cta-card tap-anim reveal-up h-100 text-decoration-none">
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

        @foreach($facilities as $facility)
        <div class="col-6 col-md-4 col-lg-2-4">
          <article class="item-card tap-anim reveal-up h-100" data-type="ruang" data-max="1">
            <div class="item-thumb">
              <img src="{{ asset('aset/peminjam/' . $facility->image_path) }}" alt="{{ $facility->name }}" class="img-fluid">
              <span class="badge-status">Active</span>
              <div class="qty-actions">
                 <button type="button" class="qty-btn" wire:click="$dispatch('add-to-cart', { id: {{ $facility->id }} })" aria-label="Pilih ruangan">＋</button>
              </div>
            </div>
            <div class="item-body">
              <div class="item-title">{{ $facility->name }}</div>
              <div class="item-meta">Kapasitas : <span class="sisa">{{ $facility->capacity }}</span></div>
            </div>
          </article>
        </div>
        @endforeach

        <!-- CTA Cari Ruang -->
        <div class="col-12 col-md-4 col-lg-2-4 order-last">
          <a href="{{ route('catalogue.ruangan') }}" class="cta-card tap-anim reveal-up h-100 text-decoration-none">
            <div class="cta-plus">+</div>
            <div>Cari Ruang</div>
          </a>
        </div>

      </div>
    </div>
  </section>
</div>
