<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; background-color: #fff; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #E60012; padding-bottom: 15px; }
        .header strong { font-size: 16px; color: #000; display: block; margin-bottom: 4px; }
        .header-text { font-size: 12px; color: #666; }
        .content { margin-bottom: 30px; }
        .greeting { font-weight: bold; font-size: 16px; margin-bottom: 10px; }
        .detail-box { background-color: #f9f9f9; padding: 15px; border-left: 4px solid #E60012; margin: 20px 0; }
        ul.details { list-style: none; padding: 0; margin: 0; }
        ul.details li { margin-bottom: 8px; }
        ul.details li span { font-weight: bold; width: 150px; display: inline-block; vertical-align: top; }
        table.items { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 14px; }
        table.items th, table.items td { border: 1px solid #e0e0e0; padding: 8px; text-align: left; }
        table.items th { background-color: #f4f4f4; }
        .btn { display: inline-block; background-color: #E60012; color: #ffffff !important; text-decoration: none;
               padding: 12px 24px; border-radius: 6px; font-weight: bold; margin-top: 10px; }
        .footer { font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 15px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <strong>MASJID SYAMSUL ULUM</strong>
            <div class="header-text">Notifikasi Pengelola &bull; Sistem Inventaris MSU</div>
        </div>

        <div class="content">
            <div class="greeting">Ada pengajuan peminjaman baru</div>

            <p>Pengajuan berikut masuk pada
                {{ ($loan->created_at ?? now())->translatedFormat('d F Y, H:i') }} WIB
                dan menunggu peninjauan.</p>

            <div class="detail-box">
                <ul class="details">
                    <li><span>Nama</span> : {{ $loan->borrower_name }}</li>
                    <li><span>Email</span> : {{ $loan->borrower_email }}</li>
                    <li><span>Telepon</span> : {{ $loan->borrower_phone }}</li>
                    <li><span>NIM / NIP</span> : {{ $loan->nim_nip ?: '-' }}</li>
                    <li><span>Prodi / Unit</span> : {{ $loan->department ?: '-' }}</li>
                    <li><span>Keperluan</span> : {{ $loan->borrower_reason }}</li>
                    <li><span>Lokasi Kegiatan</span> : {{ $loan->activity_location ?: '-' }}</li>
                    <li><span>Mulai</span> :
                        {{ $loan->loan_date_start?->translatedFormat('d F Y') }} pukul
                        {{ \Illuminate\Support\Str::substr((string) $loan->start_time, 0, 5) }} WIB</li>
                    <li><span>Selesai</span> :
                        {{ $loan->loan_date_end?->translatedFormat('d F Y') }} pukul
                        {{ \Illuminate\Support\Str::substr((string) $loan->end_time, 0, 5) }} WIB</li>
                </ul>
            </div>

            @if ($loan->activity_description)
                <p><strong>Deskripsi kegiatan:</strong><br>{{ $loan->activity_description }}</p>
            @endif

            @if ($loan->items->isNotEmpty())
                <p><strong>Barang / fasilitas yang diajukan:</strong></p>
                <table class="items">
                    <tr><th>Item</th><th>Jumlah</th></tr>
                    @foreach ($loan->items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->pivot->quantity }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if ($loan->proposal_path || $loan->ktp_path)
                <p><strong>Berkas terlampir di sistem:</strong>
                    {{ $loan->proposal_path ? 'Proposal' : '' }}{{ $loan->proposal_path && $loan->ktp_path ? ', ' : '' }}{{ $loan->ktp_path ? 'Identitas peminjam' : '' }}
                </p>
            @endif

            <p>Setujui atau tolak pengajuan ini di halaman pengelola:</p>
            <a class="btn" href="{{ route('pengelola.approval') }}">Buka Halaman Persetujuan</a>
        </div>

        <div class="footer">
            Email ini dikirim otomatis oleh Sistem Inventaris Masjid Syamsul Ulum.
            Balasan atas email ini diteruskan ke {{ $loan->borrower_email }}.
        </div>
    </div>
</body>
</html>
