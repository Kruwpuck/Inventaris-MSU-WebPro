<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris MSU - Pengurus</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('aset/pengurus-assets/style.css') }}">
</head>

<body>

    {{-- ======================================
         HEADER / NAVBAR 
    ======================================= --}}
    <header class="header">

        {{-- LOGO KIRI --}}
        <div class="logo">
            <img src="{{ asset('aset/pengurus-assets/logo.png') }}" alt="Logo MSU">
            <span style="font-weight:700; color:#004d00;">Pengurus MSU</span>
        </div>

        {{-- NAV DESKTOP --}}
        <nav class="nav-desktop">
            <a href="{{ route('pengurus.dashboard') }}" class="{{ request()->routeIs('pengurus.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
            
            <a href="{{ route('pengurus.fasilitas') }}" class="{{ request()->routeIs('pengurus.fasilitas') ? 'active' : '' }}">
                Peminjaman Fasilitas
            </a>

            <a href="{{ route('pengurus.barang') }}" class="{{ request()->routeIs('pengurus.barang') ? 'active' : '' }}">
                Peminjaman Barang
            </a>

            <a href="{{ route('pengurus.riwayat') }}" class="{{ request()->routeIs('pengurus.riwayat') ? 'active' : '' }}">
                Riwayat
            </a>
        </nav>

        {{-- USER DESKTOP --}}
        <div class="user-desktop" id="userMenu">
            <div class="user-info-box" id="userDropdownToggle">
                <img src="{{ asset('aset/pengurus-assets/user.png') }}" class="user-photo">
                <span>Pengurus</span>
                <span class="arrow">▼</span>
            </div>

            {{-- DROPDOWN --}}
            <div class="user-dropdown" id="userDropdown">
                <a href="#">Logout</a>
            </div>
        </div>

        {{-- HAMBURGER (MOBILE) --}}
        <div class="hamburger" id="hamburgerMenu">☰</div>
    </header>

    {{-- ======================================
         MOBILE SIDEBAR 
    ======================================= --}}
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-logo">
            <img src="{{ asset('aset/pengurus-assets/logo.png') }}">
        </div>

        <a href="{{ route('pengurus.dashboard') }}">Dashboard</a>
        <a href="{{ route('pengurus.fasilitas') }}">Peminjaman Fasilitas</a>
        <a href="{{ route('pengurus.barang') }}">Peminjaman Barang</a>
        <a href="{{ route('pengurus.riwayat') }}">Riwayat</a>

        <a href="#" class="logout">Logout</a>
    </div>

    <div class="overlay" id="overlay"></div>


    {{-- ======================================
         KONTEN HALAMAN 
    ======================================= --}}
    <main style="padding: 30px 20px;">
        {{ $slot }}
    </main>

    {{-- JS --}}
    <script src="{{ asset('aset/pengurus-assets/script.js') }}"></script>

</body>
</html>
