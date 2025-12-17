<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Peminjaman</title>
</head>
<body>
    <h1>Halo, {{ $loan->borrower_name }}</h1>
    <p>Terima kasih. Permohonan peminjaman Anda telah kami terima.</p>
    
    <h3>Detail Peminjaman:</h3>
    <ul>
        <li>Keperluan: {{ $loan->borrower_reason }}</li>
        <li>Waktu: {{ $loan->loan_date_start->format('d M Y H:i') }} s/d {{ $loan->loan_date_end->format('d M Y H:i') }}</li>
        <li>Status: <strong>Menunggu Persetujuan</strong></li>
    </ul>

    <p>Kami akan mengirimkan email lagi jika status berubah.</p>
</body>
</html>
