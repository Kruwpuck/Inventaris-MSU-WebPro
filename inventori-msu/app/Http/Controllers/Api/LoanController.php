<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoanRequest;
use App\Models\Inventory;
use App\Models\LoanItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanController extends Controller
{
    /**
     * List bookings for public calendar (calendar view).
     */
    public function index(Request $request)
    {
        $date = $request->input('date');
        if (!$date) return response()->json([], 400);

        // Fetch bookings for the date
        $bookings = LoanRequest::with('items')
            ->whereDate('loan_date_start', $date)
            ->whereIn('status', ['PENDING', 'APPROVED', 'ON_LOAN', 'COMPLETED'])
            ->get();

        // Transform to format expected by booking-barang.js
        $data = $bookings->map(function ($b) {
            // Get items with quantities: "Karpet (2)", "Speaker (1)"
            $itemStrings = $b->loanItems->map(function ($li) {
                return $li->inventory ? "{$li->inventory->name} ({$li->quantity})" : "Unknown";
            })->toArray();
            
            // Extract department/peminjam name for display
            // Extract department/unit
            $dept = $b->department; 
            if (!$dept && preg_match('/Dept:\s*(.*?)\./', $b->borrower_reason, $matches)) {
                $dept = trim($matches[1]);
            }

            // Mask borrower name: "Gilang" -> "G*****"
            $borrowerName = $b->borrower_name;
            if (strlen($borrowerName) > 1) {
                $borrowerName = substr($borrowerName, 0, 1) . str_repeat('*', strlen($borrowerName) - 1);
            }

            return [
                'status' => $b->status,
                'description' => $b->borrower_reason ?? '',
                'items' => $itemStrings,
                'department' => $dept,
                'borrowerName' => $borrowerName,
                'startTime' => $b->start_time,
                'endTime' => $b->end_time,
            ];
        });

        return response()->json($data);
    }

    /**
     * Store new booking.
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'borrowerName' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'startTime' => 'required',
            'endTime' => 'required',
            'items' => 'required', // JSON string
            'file' => 'required|file|mimes:pdf|max:10240',
            'ktp' => 'required|file|max:10240',
        ]);

        // Server-side Logic Check: Past Time
        $startStr = $request->startDate . ' ' . $request->startTime;
        $endStr = $request->endDate . ' ' . $request->endTime;
        
        $startDT = \Carbon\Carbon::parse($startStr);
        $endDT = \Carbon\Carbon::parse($endStr);

        if ($startDT->isPast()) {
             return response()->json(['message' => 'ERROR: Waktu tidak valid. Tanggal/Jam sudah terlewat.'], 422);
        }
        
        if ($startDT->greaterThanOrEqualTo($endDT)) {
             return response()->json(['message' => 'ERROR: Waktu tidak valid. Jam Berakhir harus lebih lambat dari Jam Mulai.'], 422);
        }

        DB::beginTransaction();
        try {
            // Handle Proposal File
            $path = null;
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('proposals', 'public');
            }
            
            // Handle KTP File
            $ktpPath = null;
            if ($request->hasFile('ktp')) {
                $ktpPath = $request->file('ktp')->store('ktp', 'public');
            }

            // Parse Items
            $items = json_decode($request->items, true);
            if (!is_array($items) || empty($items)) {
                throw new \Exception("Keranjang kosong atau invalid.");
            }
            
            $loanRequest = LoanRequest::create([
                'borrower_name' => $request->borrowerName,
                'borrower_email' => $request->email,
                'borrower_phone' => $request->phone,
                
                // New Columns
                'department' => $request->department,
                'nim_nip' => $request->nimNip,
                
                'borrower_reason' => $request->reason, // Kegiatan
                'activity_description' => $request->activity_description, // Deskripsi Kegiatan
                'activity_location' => $request->activity_location, // Lokasi
                
                'proposal_path' => $path,
                'ktp_path' => $ktpPath,
                
                'loan_date_start' => $request->startDate, 
                'loan_date_end' => $request->endDate, 
                'start_time' => $request->startTime, 
                'start_time' => $request->startTime, 
                'end_time' => $request->endTime, 
                
                'status' => 'PENDING',
                'donation_amount' => $request->donation ?? 0,
            ]);

            // Fallback: If for some reason start_time is missing from request but present in description (legacy)


            // Attach Items
            foreach ($items as $itm) {
                $inventory = Inventory::where('name', $itm['name'])->first();
                if ($inventory) {
                    LoanItem::create([
                        'loan_request_id' => $loanRequest->id,
                        'inventory_id' => $inventory->id,
                        'quantity' => $itm['quantity']
                    ]);
                }
            }

            DB::commit();
            
            // CLEAR THE SESSION CART IMMEDIATELY
            session()->forget('cart');
            
            session()->flash('email', $request->email);
            session()->flash('success', 'Permintaan peminjaman berhasil dikirim. Admin akan segera memprosesnya.');
            
            return response()->json(['message' => 'Success', 'id' => $loanRequest->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking Error: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Check availability.
     */
    public function check(Request $request)
    {
        $startDate = $request->input('startDate'); // YYYY-MM-DD
        $endDate = $request->input('endDate');     // YYYY-MM-DD
        $startTime = $request->input('startTime'); // HH:mm:ss
        $endTime = $request->input('endTime');     // HH:mm:ss

        if (!$startDate) return response()->json([], 400);
        // Fallback defaults if missing (though frontend should enforce)
        if (!$endDate) $endDate = $startDate;
        if (!$startTime) $startTime = '00:00:00';
        if (!$endTime) $endTime = '23:59:59';

        // Construct Request DateTime objects for comparison
        $reqStart = \Carbon\Carbon::parse("$startDate $startTime");
        $reqEnd = \Carbon\Carbon::parse("$endDate $endTime");

        // Check active bookings that overlap
        // Overlap Logic: (StartA < EndB) and (EndA > StartB)
        $bookings = LoanRequest::with(['loanItems.inventory'])
            ->whereIn('status', ['PENDING', 'APPROVED', 'ON_LOAN'])
            ->get()
            ->filter(function ($booking) use ($reqStart, $reqEnd) {
                // Parse Booking DateTimes
                $bDateStart = $booking->loan_date_start ? $booking->loan_date_start->format('Y-m-d') : null;
                $bDateEnd = $booking->loan_date_end ? $booking->loan_date_end->format('Y-m-d') : $bDateStart;
                
                $bTimeStart = $booking->start_time ?: '00:00:00';
                $bTimeEnd = $booking->end_time ?: '23:59:59';

                if (!$bDateStart) return false; // Invalid data

                $bookStart = \Carbon\Carbon::parse("$bDateStart $bTimeStart");
                $bookEnd = \Carbon\Carbon::parse("$bDateEnd $bTimeEnd");

                // Check overlap
                return $reqStart->lt($bookEnd) && $reqEnd->gt($bookStart);
            });


        // Calculate Used Stock
        $usedStock = [];
        foreach ($bookings as $booking) {
            foreach ($booking->loanItems as $li) {
                if ($li->inventory) {
                    $pid = $li->inventory->id;
                    if (!isset($usedStock[$pid])) $usedStock[$pid] = 0;
                    $usedStock[$pid] += $li->quantity;
                }
            }
        }

        // Get Stock
        $inventory = Inventory::where('is_active', true)->get();
        
        $result = $inventory->map(function ($inv) use ($usedStock) {
            $used = $usedStock[$inv->id] ?? 0;
            $available = max(0, $inv->stock - $used);
            
            return [
                'itemId' => $inv->id,
                'itemName' => $inv->name,
                'available' => $available
            ];
        });

        return response()->json($result);
    }
}
