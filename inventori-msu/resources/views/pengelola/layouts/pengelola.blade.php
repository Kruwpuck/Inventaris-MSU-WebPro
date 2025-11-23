<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title','Pengelola MSU')</title>

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
  />

  <style>
    body {
      background-color: #ffffff;
      font-family: "Poppins", sans-serif;
    }
    .shadow-soft {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08),
        0 2px 4px rgba(0, 0, 0, 0.04);
    }
    .shadow-strong {
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12),
        0 4px 6px rgba(0, 0, 0, 0.08);
    }
    .card:hover {
      transform: translateY(-4px);
      transition: all 0.25s ease;
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }
    .navbar-nav .nav-link {
      color: #2fa16c;
      font-weight: 600;
      margin: 0 10px;
      transition: color 0.2s ease;
    }
    .navbar-nav .nav-link:hover {
      color: #0b492c;
    }
    .navbar-nav .nav-link.active {
      color: #0b492c !important;
    }
    input[type="search"]:focus,
    input[type="text"]:focus,
    .form-control:focus {
      box-shadow: none !important;
      border-color: #000 !important;
    }
    .card.item-disabled {
      opacity: 0.6;
      background-color: #f8f9fa;
    }
    .card .btn.btn-light.rounded-circle {
      backdrop-filter: blur(4px);
    }
  </style>

  @livewireStyles
  @stack('head')
</head>

<body>
  {{-- NAVBAR --}}
  <nav class="navbar navbar-expand-lg fixed-top shadow-soft bg-white">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img
          src="{{ asset('aset/logo.png') }}"
          alt="Logo MSU"
          style="height: 45px"
        />
      </a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navmenu"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navmenu">
        <ul class="navbar-nav mb-2 mb-lg-0">
          <li class="nav-item">
            <a
              class="nav-link {{ request()->routeIs('pengelola.beranda') ? 'active' : '' }}"
              href="{{ route('pengelola.beranda') }}"
            >Beranda</a>
          </li>

          <li class="nav-item">
            <a
              class="nav-link {{ request()->routeIs('pengelola.laporan') ? 'active' : '' }}"
              href="{{ route('pengelola.laporan') }}"
            >Laporan</a>
          </li>

          <li class="nav-item">
            <a
              class="nav-link {{ request()->routeIs('pengelola.tambah') ? 'active' : '' }}"
              href="{{ route('pengelola.tambah') }}"
            >Tambah Barang</a>
          </li>

          <li class="nav-item">
            <a
              class="nav-link {{ request()->routeIs('pengelola.approval') ? 'active' : '' }}"
              href="{{ route('pengelola.approval') }}"
            >Approval</a>
          </li>
        </ul>

        {{-- USER DROPDOWN --}}
        <div class="ms-3 dropdown">
          <button
            class="btn btn-outline-secondary d-flex align-items-center rounded-pill px-3 py-1 border-0"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            <img
              src="{{ asset('aset/MSU.png') }}"
              alt="User"
              class="rounded-circle me-2"
              width="32"
              height="32"
            />
            <div class="text-start">
              <div class="fw-semibold text-dark small">Pengelola</div>
              <div class="text-muted" style="font-size: 0.75rem">
                Pengelola Side
              </div>
            </div>
            <i class="bi bi-chevron-down ms-2 small"></i>
          </button>

          <ul class="dropdown-menu dropdown-menu-end mt-2">
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="dropdown-item text-danger" type="submit">
                  <i class="bi bi-box-arrow-right me-2"></i>Keluar
                </button>
              </form>
            </li>
          </ul>
        </div>

      </div>
    </div>
  </nav>

  {{-- SLOT KONTEN DARI LIVEWIRE --}}
  <main>
    @hasSection('content')
      @yield('content')
    @else
      {{ $slot }}
    @endif
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  @livewireScripts
  @stack('scripts')
</body>
</html>
