<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\LoanRequest;
use App\Models\LoanRequestItem;
use Carbon\Carbon;

class PengurusDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Inventories
        $items = [
            'Ruangan VIP',
            'Aula Syamsul Ulum',
            'Proyektor',
            'Kabel HDMI',
            'Pointer',
            'Hijab'
        ];

        $inventoryIds = [];
        foreach ($items as $name) {
            $inv = Inventory::firstOrCreate(
                ['name' => $name],
                ['amount' => 10, 'condition' => 'Baik', 'description' => 'Fasilitas umum']
            );
            $inventoryIds[$name] = $inv->id;
        }

        // 2. Create Loan Requests (Data from shared-data.js)

        // P1: UKM Al-Fath
        $p1 = LoanRequest::create([
            'user_id' => 1, // Assuming admin/existing user logic, or just a placeholder
            'borrower_name' => 'UKM Al-Fath',
            'loan_date_start' => Carbon::create(2025, 10, 28, 18, 0, 0),
            'loan_date_end' => Carbon::create(2025, 10, 28, 20, 0, 0),
            'status' => 'approved',
            'reason' => 'Kegiatan Rutin'
        ]);
        // Attach items
        $p1->items()->attach([
            $inventoryIds['Ruangan VIP'],
            $inventoryIds['Aula Syamsul Ulum']
        ]);
        // Init loan record
        $p1->loanRecord()->create([]);


        // P2: HIPMI
        $p2 = LoanRequest::create([
            'user_id' => 1,
            'borrower_name' => 'HIPMI',
            'loan_date_start' => Carbon::create(2025, 10, 28, 17, 0, 0),
            'loan_date_end' => Carbon::create(2025, 10, 30, 8, 0, 0),
            'status' => 'approved',
            'reason' => 'Seminar Wirausaha'
        ]);
        $p2->items()->attach([
            $inventoryIds['Proyektor'],
            $inventoryIds['Kabel HDMI'],
            $inventoryIds['Pointer']
        ]);
        $p2->loanRecord()->create([]);

        // P3: HMIT
        $p3 = LoanRequest::create([
            'user_id' => 1,
            'borrower_name' => 'HMIT',
            'loan_date_start' => Carbon::create(2025, 10, 29, 20, 0, 0),
            'loan_date_end' => Carbon::create(2025, 10, 30, 17, 0, 0),
            'status' => 'approved',
            'reason' => 'Workshop IT'
        ]);
        $p3->items()->attach([
            $inventoryIds['Hijab']
        ]);
        $p3->loanRecord()->create([]);
    }
}
