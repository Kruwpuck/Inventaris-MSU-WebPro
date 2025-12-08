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
            'Tgl Pinjam',
            'Jatuh Tempo',
            'Tgl Kembali',
            'Jumlah',
            'Status',
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
            $row['tgl_pinjam']->format('m/d/Y'),
            $row['jatuh_tempo']->format('m/d/Y'),
            $row['tgl_kembali'],
            $row['jumlah'],
            $row['status'],
        ];
    }
}
