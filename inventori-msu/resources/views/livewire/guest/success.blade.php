@push('styles')
<link rel="stylesheet" href="{{ asset('fe-guest/success.css') }}">
@endpush

<div>
  <main class="container py-5 d-flex flex-column align-items-center justify-content-center" style="min-height: 85vh;">
    
    <div class="card border-0 shadow-lg p-4 p-md-5 reveal-up" style="max-width: 650px; border-radius: 24px; width: 100%;">
      
      <!-- Success Icon -->
      <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success p-3 animate-bounce" style="width: 80px; height: 80px;">
          <i class="bi bi-check-lg" style="font-size: 2.5rem;"></i>
        </div>
      </div>

      <!-- Title & Desc -->
      <div class="text-center mb-4">
        <h2 class="fw-bold text-dark mb-2">Peminjaman Berhasil Diajukan!</h2>
        <p class="text-muted">
           Detail konfirmasi peminjaman telah kami kirimkan ke email: <br/>
           <span class="fw-bold text-dark">{{ session('email') ?? 'alamat email Anda' }}</span>
        </p>
      </div>

      <!-- Simple Status Timeline -->
      <div class="position-relative d-flex justify-content-between text-center mb-5 px-4">
         <div class="position-absolute top-50 start-0 translate-middle-y w-100 bg-light" style="height: 4px; z-index: 0;"></div>
         <div class="position-absolute top-50 start-0 translate-middle-y w-50 bg-success bg-opacity-25" style="height: 4px; z-index: 0;"></div>

         <div class="position-relative z-1">
             <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm" style="width: 32px; height: 32px;">
                 <i class="bi bi-check"></i>
             </div>
             <div class="small fw-bold text-success" style="font-size: 0.75rem;">Diajukan</div>
         </div>
         <div class="position-relative z-1">
             <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center mx-auto mb-2 shadow-sm" style="width: 32px; height: 32px;">
                 <i class="bi bi-hourglass-split"></i>
             </div>
             <div class="small fw-bold text-warning" style="font-size: 0.75rem;">Menunggu Review</div>
         </div>
         <div class="position-relative z-1">
             <div class="rounded-circle bg-light text-muted d-flex align-items-center justify-content-center mx-auto mb-2 border" style="width: 32px; height: 32px;">
                 <i class="bi bi-check-all"></i>
             </div>
             <div class="small text-muted" style="font-size: 0.75rem; opacity:0.7">Disetujui</div>
         </div>
      </div>

      <!-- Admin Contact -->
      <div class="bg-light rounded-4 p-3 border mb-4">
         <div class="d-flex align-items-center gap-3">
             <div class="flex-shrink-0">
                 <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                     <i class="bi bi-whatsapp text-success fs-3"></i>
                 </div>
             </div>
             <div class="flex-grow-1">
                 <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Butuh Bantuan? Hubungi Admin</div>
                 <div class="fw-bold text-dark">+62 882-7982-9071</div>
             </div>
             <div>
                <a href="https://wa.me/6288279829071" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                    <i class="bi bi-chat-dots me-1"></i> Chat
                </a>
             </div>
         </div>
      </div>

      <!-- Buttons -->
      <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center w-100">
        <a class="btn btn-outline-secondary py-2 px-4 rounded-pill fw-bold" href="{{ route('guest.home') }}">
          Kembali ke Beranda
        </a>
        <a class="btn btn-success py-2 px-4 rounded-pill fw-bold shadow-sm" href="{{ route('guest.catalogue.barang') }}">
          Pinjam Barang Lagi
        </a>
      </div>

    </div>
  </main>
</div>

@push('scripts')
<script src="{{ asset('fe-guest/success.js') }}"></script>
@endpush
