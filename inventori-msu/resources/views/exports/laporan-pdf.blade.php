<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        h2,h4 { margin: 0 0 8px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 4px 4px; }
        th { background: #eee; }
        .meta { margin-bottom: 6px; font-size: 12px; }
    </style>
</head>
<body>
    <h2>Laporan Riwayat Peminjaman</h2>
    <div class="meta">
        Periode: <b>{{ $periode_label }}</b> |
        Kategori: <b>{{ $kategori }}</b> |
        Status: <b>{{ $status }}</b>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:25px">No</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Peminjam</th>
                <th>Waktu Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Waktu Kembali</th>
                <th style="width:40px">Jml</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $r)
                <tr>
                    <td style="text-align:center">{{ $i+1 }}</td>
                    <td>{{ $r['nama_item'] }}</td>
                    <td>{{ $r['kategori'] }}</td>
                    <td>{{ $r['peminjam'] }}</td>
                    <td>{{ $r['waktu_pinjam'] }}</td>
                    <td>{{ $r['jatuh_tempo'] }}</td>
                    <td>{{ $r['waktu_kembali'] }}</td>
                    <td style="text-align:center">{{ $r['jumlah'] }}</td>
                    <td>{{ $r['status'] }}</td>
                    <td>{{ $r['keterangan'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
