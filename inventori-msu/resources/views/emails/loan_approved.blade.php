<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            line-height: 1.4;
            font-size: 14px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 40px;
            border: 1px solid #ccc;
            background-color: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
        }

        .header img {
            width: 80px;
            position: absolute;
            top: 0;
            left: 10px;
        }

        .header-title {
            font-weight: bold;
            font-size: 18px;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .header-subtitle {
            font-size: 14px;
        }

        .doc-title {
            text-align: center;
            margin-top: 30px;
            font-weight: bold;
            text-decoration: underline;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .doc-number {
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            vertical-align: top;
            padding: 3px 0;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }

        .item-table th,
        .item-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .item-table th {
            background-color: #f0f0f0;
        }

        .item-table td.left {
            text-align: left;
        }

        .signature-section {
            margin-top: 50px;
            text-align: right;
        }

        .signature-name {
            margin-top: 70px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- KOP SURAT -->
        <div class="header">
            <img src="{{ $message->embed(public_path('aset/loogoo.png')) }}" alt="Logo">
            <div class="header-title">MASJID SYAMSUL ULUM</div>
            <div class="header-subtitle">Telkom University, Bandung, Jawa Barat</div>
            <div class="header-subtitle" style="font-size: 12px; margin-top: 5px;">
                Email: msu.telyu@gmail.com | Telp: +62 882-7982-9071
            </div>
        </div>

        <div class="doc-title">BUKTI PERSETUJUAN PEMINJAMAN</div>
        <div class="doc-number">Nomor:
            MSU/LOAN/{{ $loan->created_at->format('Y') }}/{{ str_pad($loan->id, 4, '0', STR_PAD_LEFT) }}</div>

        <p>Dengan ini menerangkan bahwa permohonan peminjaman fasilitas/barang yang diajukan oleh:</p>

        <table class="info-table">
            <tr>
                <td style="width: 150px; font-weight: bold;">Nama Peminjam</td>
                <td style="width: 15px;">:</td>
                <td>{{ $loan->borrower_name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Instansi/Unit</td>
                <td>:</td>
                <td>{{ $loan->department ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tanggal Pengajuan</td>
                <td>:</td>
                <td>{{ $loan->created_at->format('d F Y') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Status</td>
                <td>:</td>
                <td style="font-weight: bold; border: 2px solid #000; padding: 2px 5px; display:inline-block;">DISETUJUI
                </td>
            </tr>
        </table>

        <p>Detail barang atau fasilitas yang diizinkan untuk dipinjam adalah sebagai berikut:</p>

        <table class="item-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Barang / Fasilitas</th>
                    <th style="width: 100px;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loan->items as $index => $item)
                    @php
                        // Handle items relation through pivot
                        $itemName = $item->name ?? ($item->inventory->name ?? 'Item');
                        $qty = $item->quantity ?? $item->pivot->quantity ?? 1;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="left">{{ $itemName }}</td>
                        <td>{{ $qty }} unit</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p><strong>Catatan Tambahan:</strong></p>
        <ul style="margin-top: 0;">
            <li>Tanggal Peminjaman: {{ $loan->loan_date_start?->format('d-m-Y') ?? '-' }}</li>
            <li>Jam Pakai: {{ $loan->start_time }} WIB (Durasi: {{ $loan->duration ?? 0 }} Jam)</li>
            <li>Keperluan: {{ $loan->borrower_reason }}</li>
        </ul>

        <p>Demikian bukti ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>

        <div class="signature-section">
            <p>Bandung, {{ now()->format('d F Y') }}</p>
            <p>Pengelola MSU,</p>
            <div style="margin-top: 10px; margin-bottom: 5px;">
                <img src="{{ $message->embed(public_path('aset/ttd farhan.png')) }}" alt="Tanda Tangan"
                    style="height: 70px; width: auto;">
            </div>
            <div class="signature-name" style="margin-top: 5px;">Pengelola Operasional</div>
        </div>
    </div>
</body>

</html>