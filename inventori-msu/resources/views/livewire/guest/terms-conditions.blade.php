@push('styles')
    <link rel="stylesheet" href="{{ asset('fe-guest/ruangan.css') }}">
@endpush

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <h1 class="fw-bold mb-4 text-center">Syarat dan Ketentuan & Kebijakan Privasi</h1>
                    <p class="lead text-muted text-center mb-5">Peminjaman Inventaris Masjid Syamsul Ulum (MSU)</p>

                    <div class="mb-4">
                        <p>Selamat datang di sistem Inventaris Masjid Syamsul Ulum (MSU). Sebelum menggunakan layanan kami, mohon baca dokumen ini dengan saksama. Dengan melakukan peminjaman, Anda dianggap telah menyetujui poin-poin di bawah ini.</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold">1. Pengumpulan Data Pribadi</h5>
                        <p>Pihak MSU mengumpulkan data pribadi Anda untuk keperluan administrasi peminjaman barang, yang meliputi namun tidak terbatas pada:</p>
                        <ul>
                            <li><strong>Identitas Diri:</strong> Nama Lengkap dan NIM (Nomor Induk Mahasiswa).</li>
                            <li><strong>Kontak:</strong> Nomor WhatsApp/Telepon dan Email.</li>
                            <li><strong>Data Peminjaman:</strong> Jenis barang, durasi peminjaman, dan tujuan penggunaan.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold">2. Penggunaan Data (Purpose of Use)</h5>
                        <p>Data yang Anda berikan akan digunakan oleh pengurus MSU secara bertanggung jawab untuk:</p>
                        <ul>
                            <li><strong>Pelacakan (Tracking):</strong> Memantau keberadaan aset masjid yang sedang dipinjam.</li>
                            <li><strong>Komunikasi:</strong> Menghubungi peminjam jika terjadi keterlambatan pengembalian atau masalah pada barang.</li>
                            <li><strong>Audit Internal:</strong> Sebagai laporan berkala mengenai utilitas barang inventaris MSU.</li>
                            <li><strong>Verifikasi:</strong> Memastikan peminjam adalah civitas akademika Telkom University yang sah.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold">3. Keamanan dan Penyimpanan Data</h5>
                        <p>Data Anda disimpan secara digital dalam database sistem Inventaris MSU.</p>
                        <p>Pihak MSU berkomitmen untuk menjaga kerahasiaan data tersebut dan tidak akan memberikan, menjual, atau menyebarluaskan data Anda kepada pihak ketiga di luar kepentingan internal MSU dan Telkom University.</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold">4. Tanggung Jawab Peminjam</h5>
                        <p>Dengan menyetujui ketentuan ini, Anda menyatakan bahwa:</p>
                        <ul>
                            <li>Data yang diberikan adalah benar dan akurat.</li>
                            <li>Bersedia dihubungi melalui media komunikasi yang didaftarkan terkait urusan peminjaman.</li>
                            <li>Bertanggung jawab penuh atas kondisi barang yang dipinjam hingga kembali ke pihak MSU.</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold">5. Persetujuan (Consent)</h5>
                        <p>Dengan melanjutkan proses peminjaman pada sistem ini, Anda memberikan persetujuan eksplisit kepada pihak pengurus MSU untuk menyimpan dan mengolah data pribadi Anda sesuai dengan tujuan yang telah disebutkan di atas.</p>
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ route('guest.cart') }}" class="btn btn-primary px-4 rounded-pill fw-bold">
                            <i class="bi bi-arrow-left me-2"></i>Kembali ke Form Booking
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
