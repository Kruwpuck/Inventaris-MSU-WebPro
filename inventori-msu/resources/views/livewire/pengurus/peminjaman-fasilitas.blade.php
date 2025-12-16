<div>
    @push('styles')
      <link rel="stylesheet" href="{{ asset('css/pengurus.css') }}">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endpush

    <!-- HEADER FULLSCREEN -->
    <header class="d-flex justify-content-between align-items-center fixed-top px-4 py-2" 
            style="background-color: #d8f2d0; border-bottom: 2px solid #a8d5a2; z-index: 1000;">
      <div class="logo d-flex align-items-center gap-2" style="cursor: pointer;">
        <img src="{{ asset('aset/logo.png') }}" alt="Logo" style="width: 120px;">
      </div>

      <!-- NAV DESKTOP -->
      <nav class="nav-desktop d-none d-lg-flex">
        <a href="{{ route('pengurus.dashboard') }}">Beranda</a>
        <a href="{{ route('pengurus.fasilitas') }}" class="active">Peminjaman Fasilitas</a>
        <a href="{{ route('pengurus.riwayat') }}">Riwayat Peminjaman</a>
      </nav>

      <!-- USER DROPDOWN DESKTOP (BOOTSTRAP) -->
      <div class="user-desktop d-none d-lg-flex align-items-center dropdown ms-3">
        <div class="user-info-box d-flex align-items-center gap-2 px-3 py-2 rounded-pill text-white dropdown-toggle" 
             role="button" data-bs-toggle="dropdown" aria-expanded="false" 
             style="background: #5e6a72; cursor: pointer;">
          <img src="{{ asset('aset/logo.png') }}" class="rounded-circle" style="width: 40px; height: 40px;">
          <div class="lh-1 text-start me-2">
             <strong style="font-size: 0.9rem;">Pengurus</strong><br>
             <span style="font-size: 0.75rem;">Pengurus Side</span>
          </div>
        </div>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="width: 180px;">
           <li>
             <form method="POST" action="{{ route('logout') }}">
               @csrf
               <button type="submit" class="dropdown-item text-danger fw-bold py-2">Keluar</button>
             </form>
           </li>
        </ul>
      </div>

      <!-- HAMBURGER BUTTON (OFFCANVAS TRIGGER) -->
      <button class="d-lg-none border-0 bg-transparent fs-2 cursor-pointer" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
        ☰
      </button>
    </header>

    <!-- MOBILE SIDEBAR (BOOTSTRAP OFFCANVAS) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel" style="width: 280px;">
      <div class="offcanvas-header">
        <div class="offcanvas-title" id="mobileMenuLabel">
          <img src="{{ asset('aset/logo.png') }}" alt="logo" style="width: 120px;">
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body d-flex flex-column">
        
       <div class="d-flex flex-column gap-3 mb-4">
         <a href="{{ route('pengurus.dashboard') }}" 
            class="text-decoration-none fs-5 {{ Route::is('pengurus.dashboard') ? 'fw-bold text-success' : 'text-dark' }}"
            style="border-bottom: 1px solid #f0f0f0; padding-bottom: 10px;">Beranda</a>
            
         <a href="{{ route('pengurus.fasilitas') }}" 
            class="text-decoration-none fs-5 {{ Route::is('pengurus.fasilitas') ? 'fw-bold text-success' : 'text-dark' }}"
            style="border-bottom: 1px solid #f0f0f0; padding-bottom: 10px;">Peminjaman Fasilitas</a>
            
         <a href="{{ route('pengurus.riwayat') }}" 
            class="text-decoration-none fs-5 {{ Route::is('pengurus.riwayat') ? 'fw-bold text-success' : 'text-dark' }}"
            style="border-bottom: 1px solid #f0f0f0; padding-bottom: 10px;">Riwayat Peminjaman</a>
       </div>

       <form method="POST" action="{{ route('logout') }}">
           @csrf
           <button type="submit" class="btn btn-link text-decoration-none fw-bold text-danger p-0 text-start w-100 fs-5">Keluar</button>
       </form>

       <div class="mt-auto p-3 rounded d-flex align-items-center gap-3" style="background-color: #f0f0f0;">
           <img src="{{ asset('aset/logo.png') }}" alt="User" class="rounded-circle" style="width: 40px; height: 40px;">
           <div class="lh-1">
               <strong style="font-size: 0.9rem;">Pengurus</strong><br>
               <span class="text-muted" style="font-size: 0.8rem;">Pengurus Side</span>
           </div>
       </div>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="container-fluid px-4">
      <section class="judul-bawah my-4">
        <h1>Peminjaman Fasilitas</h1>
      </section>

      <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered">
          <thead class="table-success">
            <tr>
              <th>No</th>
              <th>Nama Peminjam</th>
              <th>Waktu Pengambilan</th>
              <th>Waktu Pengembalian</th>
              <th>Fasilitas</th>
              <th>Sudah Ambil</th>
              <th>Sudah Kembali</th>
            </tr>
          </thead>
          <tbody>
            @forelse($data as $d)
              <tr wire:key="{{ $d->id }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->borrower_name }}</td>
                <td>
                   {{ $d->loan_date_start ? $d->loan_date_start->format('d M Y | H:i') : '-' }}
                </td>
                <td>
                   {{ $d->loan_date_end ? $d->loan_date_end->format('d M Y | H:i') : '-' }}
                </td>
                <td>
                   {{ $d->items->pluck('name')->join(', ') }}
                </td>
                <td>
                   <input type="checkbox" 
                          wire:click="toggleStatus({{ $d->id }}, 'ambil')"
                          {{ optional($d->loanRecord)->picked_up_at ? 'checked' : '' }}>
                </td>
                <td>
                   <input type="checkbox" 
                          wire:click="toggleStatus({{ $d->id }}, 'kembali')"
                          wire:confirm="Apakah fasilitasnya sudah kembali?"
                          {{ optional($d->loanRecord)->returned_at ? 'checked' : '' }}>
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center">Tidak ada peminjaman fasilitas saat ini.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @endpush
</div>