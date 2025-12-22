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

        // Barang (Data from DataSeeder/Stock Barang filenames)
        $items = [
            [ 'name' => 'Alas Karpet', 'stock' => 12, 'image_path' => 'inventories/alas-karpet.jpeg' ],
            [ 'name' => 'HT', 'stock' => 10, 'image_path' => 'inventories/ht.jpeg' ],
            [ 'name' => 'Hijab', 'stock' => 11, 'image_path' => 'inventories/hijab.jpeg' ],
            [ 'name' => 'Kursi Lipat', 'stock' => 7, 'image_path' => 'inventories/kursi-lipat.jpeg' ],
            [ 'name' => 'Meja Lipat', 'stock' => 5, 'image_path' => 'inventories/meja-lipat.jpeg' ],
            [ 'name' => 'Mic Wireless', 'stock' => 13, 'image_path' => 'inventories/mic-wireless.jpeg' ],
            [ 'name' => 'Papan Tulis Berkaki', 'stock' => 1, 'image_path' => 'inventories/papan-tulis-berkaki.jpg' ],
            [ 'name' => 'Papan Tulis', 'stock' => 11, 'image_path' => 'inventories/papan-tulis.webp' ],
            [ 'name' => 'Sofa Besar', 'stock' => 1, 'image_path' => 'inventories/sofa-besar.jpeg' ],
            [ 'name' => 'Sofa Kecil', 'stock' => 1, 'image_path' => 'inventories/sofa-kecil.jpeg' ],
            [ 'name' => 'Speaker', 'stock' => 2, 'image_path' => 'inventories/speaker.jpg' ],
            [ 'name' => 'TV LED', 'stock' => 1, 'image_path' => 'inventories/tv-led.jpeg' ],
            [ 'name' => 'Tikar Karpet', 'stock' => 12, 'image_path' => 'inventories/tikar-karpet.jpeg' ],
        ];

        // Process items addition
        foreach ($items as $item) {
            DB::table('inventories')->insert([
                'name' => $item['name'],
                'description' => $item['name'] . ' berkualitas baik.', // Default desc
                'category' => 'barang',
                'stock' => $item['stock'],
                'image_path' => $item['image_path'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ruangan (Data from User Request)
        $facilities = [
            [
                'name' => 'Ruang Utama',
                'description' => 'Ruangan Utama MSU',
                'capacity' => 500,
                'image_path' => 'inventories/ruang-utama.jpeg'
            ],
            [
                'name' => 'Ruang VIP',
                'description' => 'Ruang tamu yang biasa digunakan untuk rapat',
                'capacity' => 20,
                'image_path' => 'inventories/vip.jpeg'
            ],
            [
                'name' => 'Ruang Diskusi Selasar Utara',
                'description' => 'Ruangan Diskusi dengan karpet',
                'capacity' => 20,
                'image_path' => 'inventories/ruang-diskusi-selasar-utara.jpeg'
            ],
            [
                'name' => 'Ruang Rapat Lt 2',
                'description' => 'Lorong Lantai 2 MSU',
                'capacity' => 50,
                'image_path' => 'inventories/ruang-rapat-lt2.jpeg'
            ],
            [
                'name' => 'Plaza',
                'description' => 'Plaza Rindang MSU',
                'capacity' => 800,
                'image_path' => 'inventories/plaza.jpeg'
            ],
        ];

        // Process facilities addition
        foreach ($facilities as $room) {
            DB::table('inventories')->insert([
                'name' => $room['name'],
                'description' => $room['description'],
                'category' => 'ruangan',
                'capacity' => $room['capacity'],
                'stock' => 1, // Logic: Rooms always stock 1
                'image_path' => $room['image_path'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
