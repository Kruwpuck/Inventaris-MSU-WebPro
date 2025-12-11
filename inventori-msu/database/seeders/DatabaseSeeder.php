<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'pengelola@inventori.com'],
            [
                'name' => 'Pengelola',
                'password' => bcrypt('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'pengurus@inventori.com'],
            [
                'name' => 'Pengurus',
                'password' => bcrypt('password'),
            ]
        );

        $this->call([
            BorrowerSeeder::class,
        ]);
    }
}
