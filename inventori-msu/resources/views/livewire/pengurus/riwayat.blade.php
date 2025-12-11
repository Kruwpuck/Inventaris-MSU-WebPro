<div>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/pengurus.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endpush

    <!-- HEADER FULLSCREEN -->
    <header class="header">
        <div class="logo">
            <img src="{{ asset('aset/logo.png') }}" alt="Logo">
        </div>

        <nav class="nav-desktop">
            <a href="{{ route('pengurus.dashboard') }}">Beranda</a>
            <a href="{{ route('pengurus.fasilitas') }}">Peminjaman Fasilitas</a>
            <a href="{{ route('pengurus.riwayat') }}" class="active">Riwayat Peminjaman</a>
        </nav>

        <div class="user-desktop" onclick="toggleUserDropdown()">
            <div class="user-info-box">
                <img src="{{ asset('aset/logo.png') }}" class="user-photo">
                <div>
                    <strong>{{ Auth::user()->name }}</strong><br>
                    Pengurus Side
                </div>
                <span class="arrow">▼</span>
            </div>
            <div class="user-dropdown" id="user-dropdown">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">Keluar</a>
                </form>
            </div>
        </div>

        <div class="hamburger" onclick="toggleMenu()">☰</div>
    </header>

    <!-- MOBILE SIDEBAR -->
    <div id="mobileMenu" class="mobile-menu">
        <div class="mobile-logo"><img src="{{ asset('aset/logo.png') }}" alt="logo"></div>
        <a href="{{ route('pengurus.dashboard') }}">Beranda</a>
        <a href="{{ route('pengurus.fasilitas') }}">Peminjaman Fasilitas</a>
        <a href="{{ route('pengurus.riwayat') }}" class="active">Riwayat Peminjaman</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-decoration-none fw-bold"
                style="color: #c4262e; padding: 12px 0;">Keluar</button>
        </form>
    </div>
    <div id="overlay" class="overlay" onclick="toggleMenu()"></div>

    <!-- CONTENT -->
    <div class="container-fluid px-4">
        <section class="judul-bawah my-4">
            <h1>Riwayat Peminjaman</h1>
        </section>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Nama Peminjam</th>
                        <th>Waktu Ambil</th>
                        <th>Waktu Kembali</th>
                        <th>Fasilitas</th>
                        <th>Cancel</th>
                        <th>Submit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $d)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $d->borrower_name }}</td>
                            <td>
                                {{ optional($d->loanRecord)->picked_up_at ? $d->loanRecord->picked_up_at->format('d M Y | H:i') : '-' }}
                            </td>
                            <td>
                                {{ optional($d->loanRecord)->returned_at ? $d->loanRecord->returned_at->format('d M Y | H:i') : '-' }}
                            </td>
                            <td>
                                {{ $d->items->pluck('name')->join(', ') }}
                            </td>
                            <!-- Placeholder Buttons for 'Cancel' and 'Submit' based on screenshot/html -->
                            <td>
                                <button class="cancel-btn">Cancel</button>
                            </td>
                            <td>
                                <button class="submit-btn">Submit</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada riwayat peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
                function toggleUserDropdown() {
                    document.getElementById("user-dropdown").classList.toggle("open");
                }
                function toggleMenu() {
                    document.getElementById("mobileMenu").classList.toggle("open");
                    document.getElementById("overlay").classList.toggle("show");
                }
                 window.onclick = function (event) {
                    if (!event.target.closest('.user-desktop')) {
                        var dropdowns = document.getElementsByClassName("user-dropdown");
                        for (var i = 0; i < dropdowns.length; i++) {
                            var openDropdown = dropdowns[i];
                            if (openDropdown.classList.contains('open')) {
                                openDropdown.classList.remove('open');
                     }
                        }
                    }
                }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @endpush
</div>