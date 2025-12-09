<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\LoanRecord;
use App\Models\LoanRequest;
use App\Models\Pengurus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengurusDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_toggle_status_and_create_loan_record()
    {
        // 1. Setup Data
        $user = User::factory()->create();
        $inventory = Inventory::create([
            'name' => 'Proyektor',
            'category' => 'fasilitas',
            'stock' => 10,
            'capacity' => 0,
            'is_active' => true
        ]);

        $request = LoanRequest::create([
            'borrower_name' => 'John Doe',
            'borrower_email' => 'john@example.com',
            'borrower_phone' => '08123456789',
            'borrower_reason' => 'Rapat',
            'loan_date_start' => now(),
            'loan_date_end' => now()->addDay(),
            'status' => 'approved' 
        ]);
        
        $request->items()->attach($inventory->id, ['quantity' => 1]);

        // 2. Action: Toggle Ambil
        $result = Pengurus::toggleStatus($request->id, 'ambil');
        $this->assertTrue($result);

        // Assert LoanRecord created and picked_up_at set
        $this->assertDatabaseHas('loan_records', [
            'loan_request_id' => $request->id,
        ]);
        
        $record = LoanRecord::where('loan_request_id', $request->id)->first();
        $this->assertNotNull($record->picked_up_at);
        $this->assertNull($record->returned_at);

        // 3. Action: Toggle Kembali
        Pengurus::toggleStatus($request->id, 'kembali');
        
        $record->refresh();
        $this->assertNotNull($record->returned_at);
        
        // Assert Riwayat getter
        $riwayat = Pengurus::getAllRiwayat();
        $this->assertCount(1, $riwayat);
        $this->assertEquals('John Doe', $riwayat->first()->borrower_name);
        $this->assertEquals('Proyektor', $riwayat->first()->item_details);
    }
}
