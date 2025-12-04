<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $title ?? 'Masjid Syamsul Ulum' }}</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Inter font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('styles.css') }}">
  @livewireStyles
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-masjid sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
        <img src="{{ asset('aset/peminjam/loogoo.png') }}" alt="Logo" class="logo">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navMain">
        <ul class="navbar-nav align-items-lg-center gap-lg-4">
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('catalogue.barang') ? 'active' : '' }}" href="{{ route('catalogue.barang') }}">Pinjam Barang</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('catalogue.ruangan') ? 'active' : '' }}" href="{{ route('catalogue.ruangan') }}">Pinjam Fasilitas</a></li>
          <li class="nav-item">
              <a class="nav-link position-relative {{ request()->routeIs('cart') ? 'active' : '' }}" href="{{ route('cart') }}">
                  <i class="bi bi-cart"></i>
                  <livewire:borrower.cart-counter />
              </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  {{ $slot }}

  <!-- Toast Container -->
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 2000;">
      @if (session()->has('success'))
          <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="d-flex">
                  <div class="toast-body">
                      <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                  </div>
                  <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
              </div>
          </div>
      @endif
  </div>

  <!-- FOOTER -->
  <footer class="site-footer mt-5">
    <div class="container py-4">
      <div class="row g-4 align-items-start">
        <div class="col-md-6">
          <div class="d-flex align-items-center gap-2 mb-2">
            <img src="{{ asset('aset/peminjam/loogoo.png') }}" alt="Logo MSU" class="logo footer-logo">
            <strong>Masjid Syamsul Ulum</strong>
          </div>
          <div class="text-muted small">
            Jl. Telekomunikasi No.1, Bandung • Jawa Barat, Indonesia<br/>
            Telp: 08xx-xxxx-xxxx • Email: msu@example.ac.id
          </div>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="small text-muted">© <span id="yearNow">{{ date('Y') }}</span> MSU — All Rights Reserved</div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('main.js') }}"></script>
  @livewireScripts
</body>
</html>
