<x-layouts.app :title="__('Dashboard')">
    <div class="container py-5">
        <div class="card shadow-sm border-0 rounded-4 p-5 text-center mx-auto" style="max-width: 600px;">
            <div class="mb-4">
                <img src="{{ asset('aset/MSU.png') }}" alt="Logo" style="height: 80px;" class="mb-3">
                <h2 class="fw-bold">Selamat Datang</h2>
                <p class="text-muted">Anda berada di Dashboard Umum.</p>
            </div>

            <div class="alert alert-info d-inline-block text-start">
                <strong>Status Akun Anda:</strong><br>
                Nama: {{ Auth::user()->name }}<br>
                Email: {{ Auth::user()->email }}<br>
                Role: <span class="badge bg-primary">{{ Auth::user()->role ?? 'User' }}</span>
            </div>

            <div class="mt-5">
                <p class="mb-3">Jika Anda seharusnya berada di halaman Pengelola atau Pengurus, silakan Logout dan Login
                    kembali.</p>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-lg px-5 rounded-pill">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>