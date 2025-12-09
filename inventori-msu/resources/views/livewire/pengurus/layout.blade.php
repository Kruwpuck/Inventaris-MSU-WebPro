<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengurus – Inventaris MSU</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/aset/pengurus-assets/style.css">
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
        <a class="navbar-brand fw-bold text-white" href="#">Pengurus</a>

        <!-- Hamburger -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pengurus.dashboard') }}">Dashboard</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pengurus.fasilitas') }}">Peminjaman Fasilitas</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pengurus.barang') }}">Peminjaman Barang</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pengurus.riwayat') }}">Riwayat</a>
                </li>
            </ul>

            <!-- USER DROPDOWN -->
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle fw-semibold" data-bs-toggle="dropdown">
                    Pengurus
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item text-danger fw-semibold" href="/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="container py-4">
        @yield('content')
    </div>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="/aset/pengurus-assets/script.js"></script>
</body>
</html>
