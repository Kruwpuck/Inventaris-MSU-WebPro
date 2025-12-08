<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BorrowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists to prevent wiping data on every deployment/restart
        if (DB::table('inventories')->count() > 0) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('inventories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Barang
        $items = [
            [
                'name' => 'Proyektor',
                'description' => 'Proyektor berkualitas tinggi untuk presentasi.',
                'category' => 'barang',
                'stock' => 7,
                'image_path' => 'proyektor.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Sound System',
                'description' => 'Sound system lengkap dengan speaker dan mixer.',
                'category' => 'barang',
                'stock' => 5,
                'image_path' => 'sound.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Terpal',
                'description' => 'Terpal ukuran besar untuk alas duduk.',
                'category' => 'barang',
                'stock' => 12,
                'image_path' => 'terpal.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Karpet',
                'description' => 'Karpet merah tebal dan nyaman.',
                'category' => 'barang',
                'stock' => 8,
                'image_path' => 'karpet.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Kursi Lipat',
                'description' => 'Kursi lipat besi yang kokoh.',
                'category' => 'barang',
                'stock' => 20,
                'image_path' => 'kursi.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Meja Lipat',
                'description' => 'Meja lipat serbaguna.',
                'category' => 'barang',
                'stock' => 10,
                'image_path' => 'meja.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Mic Wireless',
                'description' => 'Microphone tanpa kabel dengan jangkauan luas.',
                'category' => 'barang',
                'stock' => 6,
                'image_path' => 'mic.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Kabel Roll',
                'description' => 'Kabel roll panjang 15 meter.',
                'category' => 'barang',
                'stock' => 9,
                'image_path' => 'kabel.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Tikar',
                'description' => 'Tikar anyaman tradisional.',
                'category' => 'barang',
                'stock' => 15,
                'image_path' => 'tikar.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Speaker Portable',
                'description' => 'Speaker portable dengan baterai tahan lama.',
                'category' => 'barang',
                'stock' => 4,
                'image_path' => 'speaker.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Ruangan
        $facilities = [
            [
                'name' => 'Aula Utama',
                'description' => 'Aula utama masjid dengan kapasitas besar.',
                'category' => 'fasilitas',
                'capacity' => 200,
                'image_path' => 'plaza.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Ruang Rapat A',
                'description' => 'Ruang rapat kecil ber-AC.',
                'category' => 'fasilitas',
                'capacity' => 15,
                'image_path' => 'plaza.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Ruang Rapat B',
                'description' => 'Ruang rapat sedang dengan proyektor.',
                'category' => 'fasilitas',
                'capacity' => 25,
                'image_path' => 'plaza.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Ruang Kajian',
                'description' => 'Ruang khusus untuk kajian intensif.',
                'category' => 'fasilitas',
                'capacity' => 50,
                'image_path' => 'plaza.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Ruang Tamu',
                'description' => 'Ruang tunggu tamu VIP.',
                'category' => 'fasilitas',
                'capacity' => 10,
                'image_path' => 'plaza.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Kelas 1',
                'description' => 'Ruang kelas untuk TPA/Madrasah.',
                'category' => 'fasilitas',
                'capacity' => 30,
                'image_path' => 'plaza.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Kelas 2',
                'description' => 'Ruang kelas untuk TPA/Madrasah.',
                'category' => 'fasilitas',
                'capacity' => 30,
                'image_path' => 'plaza.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Kelas 3',
                'description' => 'Ruang kelas untuk TPA/Madrasah.',
                'category' => 'fasilitas',
                'capacity' => 30,
                'image_path' => 'plaza.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Kelas 4',
                'description' => 'Ruang kelas untuk TPA/Madrasah.',
                'category' => 'fasilitas',
                'capacity' => 30,
                'image_path' => 'plaza.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Perpustakaan',
                'description' => 'Perpustakaan masjid dengan koleksi buku lengkap.',
                'category' => 'fasilitas',
                'capacity' => 20,
                'image_path' => 'plaza.jpeg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        DB::table('inventories')->insert($items);
        DB::table('inventories')->insert($facilities);
    }
}
