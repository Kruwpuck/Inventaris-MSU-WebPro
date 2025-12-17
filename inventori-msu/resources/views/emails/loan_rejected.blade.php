<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; background-color: #fff; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #E60012; padding-bottom: 15px; display: flex; align-items: center; gap: 15px; }
        .header img { height: 60px; width: auto; }
        .header-text { font-size: 12px; color: #666; }
        .header-text strong { font-size: 16px; color: #000; display: block; margin-bottom: 4px; }
        .content { margin-bottom: 30px; }
        .greeting { font-weight: bold; font-size: 16px; margin-bottom: 10px; }
        .detail-box { background-color: #fff5f5; padding: 15px; border-left: 4px solid #cc0000; margin: 20px 0; }
        .footer { font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 15px; margin-top: 30px; }
        ul.details { list-style: none; padding: 0; margin: 0; }
        ul.details li { margin-bottom: 8px; }
        ul.details li span { font-weight: bold; width: 140px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <img src="{{ $message->embed(public_path('aset/loogoo.png')) }}" alt="Logo MSU">
            <div class="header-text">
                <strong>MASJID SYAMSUL ULUM</strong>
                Jl. Telekomunikasi No.1, Bandung • Jawa Barat, Indonesia<br>
                Telp: +62 882-7982-9071 • Email: msu.telyu@gmail.com
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">
            <div class="greeting">Halo, {{ $loan->borrower_name }}</div>
            
            <p>Terima kasih telah mengajukan peminjaman di Masjid Syamsul Ulum.</p>
            <p>Mohon maaf, kami harus menolak permohonan peminjaman Anda dengan rincian sebagai berikut:</p>
            
            <div class="detail-box">
                <ul class="details">
                    <li><span>Tanggal Pengajuan</span> : {{ $loan->created_at->format('d F Y') }}</li>
                    <li><span>Keperluan</span> : {{ $loan->borrower_reason }}</li>
                    <li><span>Status</span> : <strong style="color: #cc0000;">DITOLAK</strong></li>
                    <li style="margin-top: 10px; border-top: 1px dashed #ccc; padding-top: 10px;">
                        <span>Alasan Penolakan</span> :<br>
                        <em>"{{ $loan->rejection_reason }}"</em>
                    </li>
                </ul>
            </div>

            <p>Jika Anda memiliki pertanyaan lebih lanjut atau ingin memperbaiki data pengajuan, silakan hubungi kontak pengelola terlampir atau balas email ini.</p>
            
            <p>Terima kasih atas pengertiannya.</p>
            
            <p>Salam hangat,<br>
            <strong>Pengelola MSU</strong>
            </p>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            &copy; {{ date('Y') }} Masjid Syamsul Ulum Telkom University. All rights reserved.<br>
            Email ini dibuat secara otomatis.
        </div>
    </div>
</body>
</html>
