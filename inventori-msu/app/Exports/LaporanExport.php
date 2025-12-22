<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(public Collection $rows) {}

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Kategori',
            'Peminjam',
            'Waktu Pinjam',
            'Jatuh Tempo',
            'Waktu Kembali',
            'Jumlah',
            'Status',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row['nama_item'],
            $row['kategori'],
            $row['peminjam'],
            $row['waktu_pinjam'],   // Pre-formatted string
            $row['jatuh_tempo'],    // Pre-formatted string
            $row['waktu_kembali'],  // Pre-formatted string
            $row['jumlah'],
            $row['status'],
            $row['keterangan'] ?? '-',
        ];
    }
}
