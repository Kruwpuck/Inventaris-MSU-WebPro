<div>
    <!-- Styles (Directly Included to ensure loading, matching Dashboard) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="{{ asset('css/pengurus.css') }}">
    
    <style>
        /* Exact Match Styling based on Screenshot & Dashboard Consistency */
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
            height: 50px; 
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

        /* Table Area */
        .page-title {
            font-weight: 700;
            color: #064e3b; /* Visible Dark Green */
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
        .page-subtitle {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }
        
        .custom-table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            overflow: hidden; /* For rounded corners on table */
            padding-bottom: 1rem;
        }

        .table thead th {
             font-size: 0.8rem;
             font-weight: 700;
             color: #0d5e42; /* Darker green for header text */
             text-transform: uppercase;
             letter-spacing: 0.5px;
             background-color: #f0f7f4; /* Very light green background */
             border-bottom: none;
             padding: 1rem 1.5rem;
        }
        .table tbody td {
             padding: 1rem 1.5rem;
             vertical-align: middle;
             font-size: 0.95rem;
        }

        /* Empty State */
        .empty-state-container {
            padding: 4rem 2rem;
            text-align: center;
        }
        .empty-icon {
            font-size: 4rem;
            color: #ced4da; /* Light gray for icon */
            margin-bottom: 1rem;
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

    <!-- CONTENT -->
    <!-- CONTENT -->
    <div class="container pb-5">
        <div class="mt-4 d-flex justify-content-between align-items-end">
            <div>
                <h1 class="page-title">Peminjaman Fasilitas</h1>
                <p class="page-subtitle mb-0">Kelola status pengambilan dan pengembalian fasilitas.</p>
            </div>
            
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" class="form-control border-start-0 rounded-end-pill ps-0" 
                       placeholder="Cari..." 
                       wire:model.live.debounce.300ms="search">
            </div>
        </div>

        <div class="custom-table-container mt-4">
            @if($data->isEmpty())
                <!-- HEADER ONLY for context, then empty state -->
                 <div class="table-responsive">
                     <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA PEMINJAM</th>
                                <th>WAKTU AMBIL</th>
                                <th>WAKTU KEMBALI</th>
                                <th>FASILITAS</th>
                                <th>SUDAH AMBIL</th>
                                <th>SUDAH KEMBALI</th>
                            </tr>
                        </thead>
                     </table>
                 </div>
                 <div class="empty-state-container">
                     <!-- Using a generic box/inbox icon to match screenshot -->
                     <i class="bi bi-inbox empty-icon"></i> 
                     <p class="text-muted mb-0">Tidak ada peminjaman aktif saat ini.</p>
                 </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA PEMINJAM</th>
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
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $d->borrower_name }}</td>
                                    <td class="text-secondary">
                                        {{ $d->loan_date_start ? $d->loan_date_start->format('d M Y | H:i') : '-' }}
                                    </td>
                                    <td class="text-secondary">
                                        {{ $d->loan_date_end ? $d->loan_date_end->format('d M Y | H:i') : '-' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fw-normal">
                                            {{ $d->items->pluck('name')->join(', ') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <input class="form-check-input" type="checkbox" style="width: 1.2em; height: 1.2em; border-color: #dee2e6;"
                                            onclick="confirmPickup(event, {{ $d->id }})"
                                            {{ optional($d->loanRecord)->picked_up_at ? 'checked disabled' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input class="form-check-input" type="checkbox" style="width: 1.2em; height: 1.2em; border-color: #dee2e6;"
                                            onclick="confirmReturn(event, {{ $d->id }})"
                                            {{ optional($d->loanRecord)->returned_at ? 'checked disabled' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
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
                event.preventDefault(); 
                
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