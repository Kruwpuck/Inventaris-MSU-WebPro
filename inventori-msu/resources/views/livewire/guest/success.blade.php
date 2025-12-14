<div>
  @push('styles')
  <style>
    body {
      background-color: #f8f9fa;
      display: flex; 
      align-items: center; 
      justify-content: center; 
      min-height: 100vh;
      font-family: 'Inter', sans-serif;
    }
    .success-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.06);
      padding: 3rem 2rem;
      text-align: center;
      max-width: 480px;
      width: 100%;
      animation: popUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes popUp {
      0% { transform: scale(0.8); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }
    .icon-wrapper {
      width: 80px; height: 80px;
      background: #e0f5f1;
      color: #167c73;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 2.5rem;
      margin: 0 auto 1.5rem auto;
    }
    .btn-home {
      background-color: #167c73; 
      color: #fff; 
      border-radius: 50px; 
      padding: 0.75rem 2rem; 
      font-weight: 600;
    }
    .btn-home:hover { background-color: #12635c; color: #fff; }
  </style>
  @endpush

  <div class="success-card">
    <div class="icon-wrapper">
      <i class="bi bi-check-lg"></i>
    </div>
    <h2 class="fw-bold mb-3">Booking Berhasil!</h2>
    <p class="text-muted mb-4">
      Permintaan peminjaman kamu sudah kami terima dan sedang diproses.<br>
      Silakan cek status secara berkala atau hubungi pengurus.
    </p>
    
    <div class="d-grid gap-2">
      <a href="{{ route('guest.home') }}" class="btn btn-home">
        Kembali ke Beranda
      </a>
      <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-outline-success rounded-pill fw-semibold">
        <i class="bi bi-whatsapp me-1"></i> Konfirmasi via WA
      </a>
    </div>
  </div>
</div>
