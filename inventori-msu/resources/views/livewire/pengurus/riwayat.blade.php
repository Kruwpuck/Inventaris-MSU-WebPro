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
        
        /* Status Badges */
        .badge-status-selesai {
            background-color: #d1e7dd;
            color: #0f5132;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35em 0.8em;
            border-radius: 50px;
        }
        .badge-status-terlambat {
            background-color: #f8d7da;
            color: #842029;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35em 0.8em;
            border-radius: 50px;
        }

        /* Empty State */
        .empty-state-container {
            padding: 4rem 2rem;
            text-align: center;
        }
        .empty-icon {
            color: #ced4da; /* Light gray for icon */
            margin-bottom: 1rem;
        }

        /* Pagination Customization to Match Green Theme */
        .pagination {
            --bs-pagination-color: #198754;
            --bs-pagination-hover-color: #0f5132;
            --bs-pagination-active-bg: #198754;
            --bs-pagination-active-border-color: #198754;
            --bs-pagination-focus-box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        }
        .page-link {
            color: #198754;
        }
        .page-link:hover {
            color: #0f5132;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        .page-item.active .page-link {
            background-color: #198754;
            border-color: #198754;
            color: white;
        }
    </style>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg fixed-top" wire:ignore>
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
    <div class="container pb-5">
        <div class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-end gap-3 gap-md-0 text-center text-md-start">
            <div>
                <h1 class="page-title">Riwayat Peminjaman</h1>
                <p class="page-subtitle mb-0">Arsip semua aktivitas peminjaman fasilitas.</p>
            </div>
            
            <div class="d-flex flex-column flex-sm-row gap-3 align-items-center">
                <div class="d-flex align-items-center gap-2 text-secondary">
                    <span>Show</span>
                    <select class="form-select form-select-sm border-secondary-subtle rounded-3" style="min-width: 70px;" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>data</span>
                </div>

                <div class="input-group" style="width: 100%; max-width: 300px;">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                        <i class="bi bi-search text-secondary"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 rounded-end-pill ps-0" 
                           placeholder="Cari..." 
                           wire:model.live.debounce.300ms="search">
                </div>
            </div>
        </div>

        <div class="custom-table-container mt-4 position-relative" wire:loading.class="table-loading">
            <!-- Loading Overlay -->
            <div wire:loading.flex class="position-absolute top-0 start-0 w-100 h-100 align-items-center justify-content-center bg-white bg-opacity-50" style="z-index: 10;">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            @if($data->isEmpty())
                <!-- HEADER ONLY for context (consistent with previous request) -->
                 <div class="table-responsive">
                     <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA PEMINJAM</th>
                                <th>KONTAK</th>
                                <th>MULAI PEMINJAMAN</th>
                                <th>SELESAI PEMINJAMAN</th>
                                <th>FASILITAS</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                     </table>
                 </div>
                 <div class="empty-state-container">
                     <i class="bi bi-inbox empty-icon"></i> 
                     <p class="text-muted mb-0">Belum ada riwayat peminjaman.</p>
                 </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <th>NO</th>
                                <th>NAMA PEMINJAM</th>
                                <th>KONTAK</th>
                                <th>MULAI PEMINJAMAN</th>
                                <th>SELESAI PEMINJAMAN</th>
                                <th>FASILITAS</th>
                                <th class="text-center">BATAL</th>
                                <th class="text-center">KIRIM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $d)
                                <tr wire:key="{{ $d->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $d->borrower_name }}</td>
                                    <td>{{ $d->borrower_phone }}</td>
                                    <td class="text-secondary">
                                        <i class="bi bi-calendar4 me-2"></i> 
                                        {{ optional($d->loanRecord)->picked_up_at ? $d->loanRecord->picked_up_at->format('d M Y | H:i') : '-' }}
                                    </td>
                                    <td class="text-secondary">
                                        <i class="bi bi-calendar4 me-2"></i> 
                                        {{ optional($d->loanRecord)->returned_at ? $d->loanRecord->returned_at->format('d M Y | H:i') : '-' }}
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($d->items as $item)
                                                <span class="text-secondary small fw-medium text-nowrap">
                                                    &bull; {{ $item->name }} <span class="text-muted">(x{{ $item->pivot->quantity ?? 1 }})</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm rounded-pill px-3 {{ optional($d->loanRecord)->is_submitted ? 'btn-secondary' : 'btn-outline-danger' }}" 
                                                onclick="confirmCancel({{ $d->id }})"
                                                {{ optional($d->loanRecord)->is_submitted ? 'disabled' : '' }}>
                                            <i class="bi bi-x-circle me-1"></i> Batal
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm rounded-pill px-3 {{ optional($d->loanRecord)->is_submitted ? 'btn-secondary' : 'btn-outline-success' }}" 
                                                onclick="confirmSubmit({{ $d->id }})"
                                                {{ optional($d->loanRecord)->is_submitted ? 'disabled' : '' }}>
                                            <i class="bi bi-check-circle me-1"></i> Kirim
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-top">
                    {{ $data->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script>
            // Toast Configuration
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Listen for Livewire event
            document.addEventListener('livewire:init', () => {
                Livewire.on('show-toast', (event) => {
                    Toast.fire({
                        icon: event.type,
                        title: event.message
                    });
                });
            });

            function confirmCancel(id) {
                Swal.fire({
                    title: 'Batalkan Peminjaman?',
                    text: "Data akan dikembalikan ke Dashboard.",
                    icon: 'warning', // changed from question for stronger warning
                    showCancelButton: true,
                    confirmButtonColor: '#d33', // Red
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Kembali'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('cancel', id);
                    }
                });
            }

            async function confirmSubmit(id) {
                // 1. First Confirmation
                const result = await Swal.fire({
                    title: 'Selesaikan Peminjaman?',
                    text: "Data akan disimpan permanen sebagai selesai.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Selesaikan',
                    cancelButtonText: 'Batal'
                });

                if (!result.isConfirmed) return;

                // 2. Check if Late (Server-side check)
                // Show loading state
                Swal.fire({
                    title: 'Memeriksa...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const isLate = await @this.call('checkIsLate', id);
                    
                    if (isLate) {
                        // 3. Late Flow: Input Notes
                        const noteResult = await Swal.fire({
                            title: 'Terlambat Mengembalikan',
                            input: 'textarea',
                            inputLabel: 'Mohon isi keterangan keterlambatan',
                            inputPlaceholder: 'Tuliskan alasan keterlambatan...',
                            inputAttributes: {
                                'aria-label': 'Tuliskan alasan keterlambatan'
                            },
                            showCancelButton: true,
                            confirmButtonColor: '#d33', // Red for emphasis on issue
                            confirmButtonText: 'Lanjut',
                            cancelButtonText: 'Batal',
                            inputValidator: (value) => {
                                if (!value) {
                                    return 'Keterangan wajib diisi!'
                                }
                            }
                        });

                        if (!noteResult.isConfirmed) return; // Cancelled at note input

                        const notes = noteResult.value;

                        // 4. Confirm Notes
                        const finalConfirm = await Swal.fire({
                            title: 'Konfirmasi Keterangan',
                            text: "Apakah keterangan sudah benar?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#198754',
                            cancelButtonText: 'Edit Kembali',
                            confirmButtonText: 'Ya, Simpan'
                        });

                        if (finalConfirm.isConfirmed) {
                             @this.call('submit', id, notes);
                        } else {
                            // Loop back or just exit? User asked "modal lagi konfirmasi apakah keterangan sudah benar"
                            // If they cancel here, maybe they want to edit? 
                            // For simplicity based on request "jika ya maka ... modal konfirmasi ... data masuk"
                            // If cancelled here, we can just stop or re-prompt. Stopping is safer.
                            // Or better: Re-open input? Let's just stop for now to match strict flow request.
                        }

                    } else {
                        // 4. Not Late Flow
                        @this.call('submit', id, '-');
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire('Error', 'Gagal memproses permintaan.', 'error');
                }
            }
        </script>
    @endpush
</div>