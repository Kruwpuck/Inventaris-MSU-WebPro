<div>
    {{-- Main content --}}
    <main class="container py-5">
        <section class="success-wrap d-grid text-center gap-3">
          <div class="success-illustration drop-in">
            <!-- Amplop + centang -->
            <svg viewBox="0 0 256 256" width="220" height="220" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <rect x="16" y="40" width="224" height="160" rx="16" fill="#5aa29a" opacity=".35"/>
              <rect x="32" y="56" width="192" height="128" rx="12" fill="#fff"/>
              <path d="M40 64l88 72L216 64" fill="none" stroke="#2c5552" stroke-width="12" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="176" cy="84" r="42" fill="#2e8b57"/>
              <path d="M158 84l12 12 22-22" fill="none" stroke="#fff" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
    
          <div class="reveal-up">
            <h1 class="fw-extrabold mb-1">Notifikasi Sukses</h1>
            <p class="text-muted m-0" id="descText">
              Notifikasi sukses dikirim ke email.
            </p>
          </div>
    
          <div class="d-flex gap-2 justify-content-center mt-2 reveal-up">
            <a class="btn btn-primary-soft" href="{{ route('home') }}">
              <i class="bi bi-house-door me-1"></i> Beranda
            </a>
            <a class="btn btn-outline-success" href="{{ route('catalogue.barang') }}">
              <i class="bi bi-bag-check me-1"></i> Pinjam Barang Lagi
            </a>
          </div>
        </section>
      </main>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/success.css') }}">
@endpush

@script
<script src="{{ asset('js/success.js') }}"></script>
@endscript
