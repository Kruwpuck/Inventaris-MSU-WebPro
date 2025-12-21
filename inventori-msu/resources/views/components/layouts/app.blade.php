<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Masjid Syamsul Ulum' }}</title>
  <link rel="icon" href="{{ asset('fe-guest/loogoo.png') }}" type="image/x-icon">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap">
  @stack('styles')
  @livewireStyles
  <style>
    /* Default Navbar Style (Fallback for pages without specific CSS) */

    .navbar-masjid .logo {
      height: 56px !important;
      width: auto;
      object-fit: contain;
      display: block;
    }

    #btnShowCalendar {
      height: 48px !important;
      width: 48px !important;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0;
    }
  </style>
</head>

<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-masjid sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('guest.home') }}">
        <img src="{{ asset('fe-guest/loogoo.png') }}" alt="Logo" class="logo">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navMain">
        <ul class="navbar-nav align-items-lg-center gap-lg-4">
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('guest.home') ? 'active' : '' }}" href="{{ route('guest.home') }}">Beranda</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('guest.catalogue.barang') ? 'active' : '' }}" href="{{ route('guest.catalogue.barang') }}">Pinjam Barang</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('guest.catalogue.ruangan') ? 'active' : '' }}" href="{{ route('guest.catalogue.ruangan') }}">Pinjam Ruangan</a></li>
          <li class="nav-item d-flex align-items-center msu-cart-entry">
            <a class="nav-link position-relative" href="{{ route('guest.cart') }}" aria-label="Buka keranjang">
              <i class="bi bi-bag-check"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger msu-cart-badge">0</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  {{ $slot }}

  <!-- FAB Checkout -->
  @unless(request()->routeIs('guest.cart') || request()->routeIs('guest.success') || request()->routeIs('guest.terms'))
  <a href="{{ route('guest.cart') }}" id="fabCheckout" class="fab-checkout" aria-label="Checkout" style="text-decoration:none">
    <i class="bi bi-bag-check"></i>
    <span id="fabCount" class="fab-count">0</span>
  </a>
  @endunless

  <!-- Modal Konfirmasi -->
  <div class="modal fade" id="confirmAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold">Konfirmasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body pt-0">
          <div class="d-flex align-items-center gap-3">
            <img id="confirmThumb" src="" alt="" style="width:72px;height:72px;object-fit:cover;border-radius:12px;">
            <div>
              <div class="small text-muted mb-1">Tambah ke keranjang</div>
              <div id="confirmName" class="fw-semibold">Nama Item</div>
              <div id="confirmType" class="small text-muted"></div>
            </div>
          </div>
          <div class="alert alert-info d-flex align-items-center gap-2 py-2 px-3 mt-3 mb-0">
            <i class="bi bi-info-circle"></i>
            <div class="small">Setelah dikonfirmasi, item masuk ke keranjang dan kamu tetap di halaman ini.</div>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-success" id="confirmAddBtn">
            <i class="bi bi-bag-plus me-1"></i> Tambah
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- FLOATING CONTACT (WA) -->
  @unless(request()->routeIs('guest.success'))
  <a href="https://wa.me/6288279829071" target="_blank" id="fabContact" class="fab-contact" aria-label="Hubungi Admin">
    <i class="bi bi-whatsapp"></i>
  </a>
  @endunless

  <!-- Toast -->
  <div id="toastStack" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:2000"></div>

  <!-- FOOTER -->
  <footer class="site-footer mt-5">
    <div class="container py-4">
      <div class="row g-4 align-items-start">
        <div class="col-md-6">
          <div class="d-flex align-items-center gap-2 mb-2">
            <img src="{{ asset('fe-guest/loogoo.png') }}" alt="Logo MSU" class="logo footer-logo">
            <strong>Masjid Syamsul Ulum</strong>
          </div>
          <div class="text-muted small">
            Jl. Telekomunikasi No.1, Bandung • Jawa Barat, Indonesia<br />
            Telp: +62 882-7982-9071 • Email: msu.telyu@gmail.com
          </div>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="small text-muted">© <span id="yearNow">{{ date('Y') }}</span> MSU — All Rights Reserved</div>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('fe-guest/cart.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('fe-guest/main.js') }}?v={{ time() }}"></script>
  @stack('scripts')
  @livewireScripts
</body>

</html>