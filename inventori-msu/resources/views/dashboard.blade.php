<x-layouts.auth :title="__('Dashboard')">
    <style>
        body {
            background-color: #f8f9fa;
        }
    </style>

    <div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="card shadow-sm border-0 rounded-4 p-5 text-center mx-auto" style="max-width: 600px; width: 100%;">
            <div class="mb-4">
                <img src="{{ asset('aset/MSU.png') }}" alt="Logo" style="height: 80px;" class="mb-3">
                <h2 class="fw-bold">Selamat Datang</h2>
                <p class="text-muted">Anda berada di Dashboard Umum.</p>
            </div>

            <div class="alert alert-info d-inline-block text-start w-100">
                <strong class="d-block mb-2">Status Akun Anda:</strong>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <span>Nama:</span>
                    <span class="fw-semibold">{{ Auth::user()->name }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <span>Email:</span>
                    <span class="fw-semibold">{{ Auth::user()->email }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Role:</span>
                    <span class="badge bg-primary rounded-pill px-3">{{ Auth::user()->role ?? 'User' }}</span>
                </div>
            </div>

            <div class="mt-4">
                <p class="mb-4 small text-muted">Akses ini terbatas. Jika Anda pengelola/pengurus, silakan login ulang.
                </p>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-lg px-5 rounded-pill shadow-sm w-100">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.auth>