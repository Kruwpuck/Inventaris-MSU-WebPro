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

        // Create Admin / Pengelola
        User::firstOrCreate(
            ['email' => 'pengelola@msu.com'],
            [
                'name' => 'Pengelola',
                'password' => bcrypt('password'),
                'role' => 'pengelola',
            ]
        );

        // Create Pengurus
        User::firstOrCreate(
            ['email' => 'pengurus@msu.com'],
            [
                'name' => 'Pengurus',
                'password' => bcrypt('password'),
                'role' => 'pengurus',
            ]
        );

        // Default Test User (No Role)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        $this->call([
            BorrowerSeeder::class,
        ]);
    }
}
