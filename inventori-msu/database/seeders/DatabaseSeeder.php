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
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Pengelola',
                'role' => 'pengelola',
                'password' => bcrypt('password')
            ]
        );

        User::firstOrCreate(
            ['email' => 'pengurus@gmail.com'],
            [
                'name' => 'Staf Pengurus',
                'role' => 'pengurus',
                'password' => bcrypt('password')
            ]
        );

        $this->call([
            BorrowerSeeder::class,
        ]);
    }
}
