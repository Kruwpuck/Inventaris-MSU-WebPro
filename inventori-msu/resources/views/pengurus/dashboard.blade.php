<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Pengurus MSU</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

<header class="header">
    <div class="logo">
        <img src="{{ asset('Assets/logo.png') }}" alt="Logo">
    </div>

    <nav class="nav-desktop">
        <a href="{{ route('dashboard') }}">Beranda</a>
        <a href="{{ route('fasilitas') }}">Peminjaman Fasilitas</a>
        <a href="{{ route('barang') }}">Peminjaman Barang</a>
        <a href="{{ route('riwayat') }}">Riwayat Peminjaman</a>
    </nav>

    <div class="user-desktop" onclick="toggleUserDropdown()">
        <div class="user-info-box">
            <div class="user-photo" style="background-image: url('{{ asset('Assets/gedung.png') }}'); background-size: cover; background-position: center;"></div>
            <div>
                <strong>pengurus</strong><br>
                Pengurus Side
            </div>
            <span class="arrow">▼</span>
        </div>

        <div class="user-dropdown" id="user-dropdown">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Keluar</button>
            </form>
        </div>
    </div>

    <div class="hamburger" onclick="toggleMenu()">☰</div>
</header>

<div id="mobileMenu" class="mobile-menu">
    <div class="mobile-logo">
        <img src="{{ asset('Assets/logo.png') }}">
    </div>
    <a href="{{ route('dashboard') }}">Beranda</a>
    <a href="{{ route('fasilitas') }}">Peminjaman Fasilitas</a>
    <a href="{{ route('barang') }}">Peminjaman Barang</a>
    <a href="{{ route('riwayat') }}">Riwayat Peminjaman</a>

    <a href="#" class="logout">Keluar</a>
</div>

<div id="overlay" class="overlay" onclick="toggleMenu()"></div>

<main>
    @yield('content')
</main>

<script src="{{ asset('js/script.js') }}"></script>

</body>
</html>
