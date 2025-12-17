<!DOCTYPE html>
<html>
<head>
    <title>Peminjaman Disetujui</title>
</head>
<body>
    <h1>Kabar Baik, {{ $loan->borrower_name }}!</h1>
    <p>Permohonan peminjaman Anda telah <strong>DISETUJUI</strong> oleh Admin.</p>
    
    <p>Silakan ambil barang/gunakan fasilitas sesuai jadwal berikut:</p>
    <ul>
        <li>Keperluan: {{ $loan->borrower_reason }}</li>
        <li>Waktu Mulai: {{ $loan->loan_date_start->format('d M Y H:i') }}</li>
    </ul>

    <p>Harap menjaga barang/fasilitas dengan baik. Terima kasih!</p>
</body>
</html>
