<div>
    <!-- Styles (Directly Included to ensure loading) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="{{ asset('css/pengurus.css') }}">
    
    <style>
        /* Exact Match Styling based on Screenshot */
        body { 
            background-color: #f8f9fa; 
            font-family: "Inter", sans-serif; 
            padding-top: 80px; 
            color: #333;
        }
        
        /* Navbar */
        .navbar { 
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); 
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
        }
        .navbar-brand img {
            height: 50px; /* Slightly larger as per screenshot */
        }
        .nav-link {
            color: #198754; /* Green text */
            font-weight: 600;
            margin: 0 12px;
            font-size: 0.95rem;
        }
        .nav-link:hover {
            color: #0f5132;
        }
        .nav-link.active {
            color: #0f5132 !important;
            font-weight: 700;
        }
        
        /* Hero */
        .hero-section { 
            background: white; 
            border-radius: 20px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.03); 
            padding: 3.5rem 3rem;
        }
        .hero-title {
            font-weight: 800;
            color: #212529;
            font-size: 2.8rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }
        .hero-subtitle {
            font-size: 1.15rem;
            color: #495057;
            max-width: 90%;
        }
        .highlight {
            color: #198754;
            font-weight: 700;
        }
        
        /* Section Title */
        .section-title {
            font-weight: 700;
            color:#064e3b;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 3px solid #198754;
            display: inline-block;
            padding-bottom: 0.5rem;
        }

        /* Empty State Card */
        .empty-state-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 4rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        
        /* User Dropdown Profile */
        .user-profile-btn {
            border: none;
            background: transparent;
            padding: 6px 16px; /* Increased padding for pill shape */
            border-radius: 50px; /* Pill shape */
            transition: all 0.2s;
        }
        .user-profile-btn:hover {
            background-color: #6c757d; /* Gray background on hover */
            color: white !important;
        }
        .user-profile-btn:hover .text-dark {
            color: white !important;
        }
        .user-profile-btn:hover .text-muted {
            color: rgba(255,255,255, 0.8) !important;
        }
        .user-profile-btn:hover i {
            color: white !important;
        }
    </style>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('pengurus.dashboard') }}">
                <img src="{{ asset('aset/logo.png') }}" alt="Logo">
            </a>

            <!-- Toggle for Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navmenu">
                <!-- Center/Right Links -->
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('pengurus.dashboard') ? 'active' : '' }}" href="{{ route('pengurus.dashboard') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('pengurus.fasilitas') ? 'active' : '' }}" href="{{ route('pengurus.fasilitas') }}">Peminjaman Fasilitas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('pengurus.riwayat') ? 'active' : '' }}" href="{{ route('pengurus.riwayat') }}">Riwayat Peminjaman</a>
                    </li>
                
                    <!-- Separation for User Config -->
                    <li class="nav-item ms-lg-4 d-flex align-items-center">
                        <div class="dropdown">
                            <!-- User Button with Full Image Fix -->
                            <button class="user-profile-btn d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ asset('aset/logo.png') }}" alt="User" style="max-height: 40px; width: auto;">
                                <div class="text-start lh-1">
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">Pengurus</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Pengurus Side</small>
                                </div>
                                <i class="bi bi-chevron-down ms-1 text-muted small"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 mt-2 p-2" style="min-width: 220px;">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger fw-bold py-2 fs-6 rounded-3 d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-box-arrow-right fs-5"></i>
                                            Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- CONTENT CONTAINER -->
    <div class="container pb-5">
        <!-- HERO SECTION -->
        <style>
            .hero-section-bg {
                /* Gradient from top (transparent) to bottom (white) to reveal image top and make text readable at bottom */
                background-image: url("{{ asset('aset/gedung.png') }}");
                background-size: cover;
                background-position: center -80px; /* Shifts image up to move 'Syamsul Ulum' higher */
                border-radius: 20px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.03);
                padding: 4rem 3rem;
                min-height: 380px; 
                display: flex;
                align-items: flex-end; /* Align text to bottom */
                justify-content: center; /* Center text horizontally */
                margin-bottom: 2rem;
                text-align: center;
            }
        </style>

        <section class="hero-section-bg mb-5 mt-4">
            <div class="col-lg-8">
                <p class="hero-subtitle mb-0 fw-medium mx-auto" style="font-size: 1.25rem; color: #333;">
                    Satu langkah menuju <span class="highlight">kemudahan beraktivitas</span> di MSU.<br>
                    Semua urusan peminjaman dan perizinan kini bisa dilakukan secara online.
                </p>
            </div>
        </section>

        <!-- DATA SECTION -->
        <section>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="section-title mb-0">Peminjaman Hari Ini</h3>
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                        <i class="bi bi-search text-secondary"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 rounded-end-pill ps-0" 
                           placeholder="Cari..." 
                           wire:model.live.debounce.300ms="search">
                </div>
            </div>

            @if($data->isEmpty())
                <!-- EMPTY STATE (Exact Match) -->
                <div class="empty-state-card text-center">
                    <div class="mb-3">
                        <i class="bi bi-calendar-event text-success" style="font-size: 4rem; opacity: 0.6;"></i>
                    </div>
                    <h5 class="text-secondary fw-normal mb-1">Data peminjaman hari ini akan muncul di sini.</h5>
                    <p class="text-muted small mb-4">Silakan cek menu Peminjaman Fasilitas untuk detail lengkap.</p>
                    
                    <a href="{{ route('pengurus.fasilitas') }}" class="btn btn-success rounded-pill px-4 py-2 fw-semibold shadow-sm">
                        Lihat Semua Peminjaman
                    </a>
                </div>
            @else
                <!-- TABLE WITH DATA (Preserved) -->
                <div class="table-scroll-container">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="text-center">
                        <tr>
                            <th>NO</th>
                            <th class="text-center">NAMA PEMINJAM</th>
                            <th>WAKTU AMBIL</th>
                            <th>WAKTU KEMBALI</th>
                            <th>FASILITAS</th>
                            <th>SUDAH AMBIL</th>
                            <th>SUDAH KEMBALI</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $d)
                            <tr wire:key="{{ $d->id }}">
                                <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                <td class="fw-semibold text-center">{{ $d->borrower_name }}</td>
                                <td class="text-center text-secondary">
                                    {{ $d->loan_date_start ? $d->loan_date_start->format('d M Y | H:i') : '-' }}
                                </td>
                                <td class="text-center text-secondary">
                                    {{ $d->loan_date_end ? $d->loan_date_end->format('d M Y | H:i') : '-' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-normal">
                                        {{ $d->items->pluck('name')->join(', ') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" style="width: 1.2em; height: 1.2em;"
                                               onclick="confirmPickup(event, {{ $d->id }})"
                                            {{ optional($d->loanRecord)->picked_up_at ? 'checked disabled' : '' }}>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" style="width: 1.2em; height: 1.2em;"
                                               onclick="confirmReturn(event, {{ $d->id }})"
                                            {{ optional($d->loanRecord)->returned_at ? 'checked disabled' : '' }}>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('pengurus.fasilitas') }}" class="btn btn-outline-success rounded-pill px-4">
                        Lihat Selengkapnya <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            @endif
        </section>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script>
            function confirmPickup(event, id) {
                event.preventDefault();
                
                Swal.fire({
                    title: 'Konfirmasi Pengambilan',
                    text: "Apakah fasilitas sudah diambil?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Sudah Diambil',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('toggleStatus', id, 'ambil');
                    }
                });
            }

            function confirmReturn(event, id) {
                event.preventDefault(); // Prevent default checkbox change
                
                // If it's already checked (returned), maybe we don't want to uncheck it or different logic? 
                // Assuming this is only for MARKING as returned.
                // If user unchecks, we might need different logic. For now, strictly for Returning.
                
                Swal.fire({
                    title: 'Konfirmasi Pengembalian',
                    text: "Apakah fasilitas ini sudah dikembalikan?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Sudah Kembali',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('toggleStatus', id, 'kembali');
                    }
                });
            }
        </script>
    @endpush
</div>
