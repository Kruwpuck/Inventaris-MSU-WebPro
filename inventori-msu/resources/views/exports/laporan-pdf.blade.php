<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2,h4 { margin: 0 0 8px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 6px 5px; }
        th { background: #eee; }
        .meta { margin-bottom: 6px; }
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
                <th style="width:30px">No</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Peminjam</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Tgl Kembali</th>
                <th style="width:60px">Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $r)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $r['nama_item'] }}</td>
                    <td>{{ $r['kategori'] }}</td>
                    <td>{{ $r['peminjam'] }}</td>
                    <td>{{ $r['tgl_pinjam']->format('m/d/Y') }}</td>
                    <td>{{ $r['jatuh_tempo']->format('m/d/Y') }}</td>
                    <td>{{ $r['tgl_kembali'] }}</td>
                    <td style="text-align:center">{{ $r['jumlah'] }}</td>
                    <td>{{ $r['status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
