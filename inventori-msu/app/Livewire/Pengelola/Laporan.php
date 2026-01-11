<?php

namespace App\Livewire\Pengelola;

use Livewire\Component;
use App\Models\LoanRequest;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class Laporan extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $q = '';
    public $perPage = 10;

    // Filters
    public $vKategori = 'all';
    public $vStatus = 'all';
    
    // Custom Date Range
    public $dateFrom = null;
    public $dateTo = null;

    public function updatingQ() { $this->resetPage(); }
    public function updatingVKategori() { $this->resetPage(); }
    public function updatingVStatus() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function applyDateFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        // 1. Base Query
        $query = LoanRequest::with(['items', 'loanRecord'])->orderByDesc('id');

        // Filter Date Range (Only if BOTH are set)
        if ($this->dateFrom && $this->dateTo) {
            $query->whereDate('loan_date_start', '>=', $this->dateFrom)
                  ->whereDate('loan_date_start', '<=', $this->dateTo);
        }

        $requests = $query->get();
        $today = Carbon::today();

        // 2. Transform & Map Logic (Existing Logic)
        /** @var Collection $mappedCollection */
        $mappedCollection = $requests->flatMap(function ($lr) use ($today) {
            return $lr->items->map(function ($inv) use ($lr, $today) {
                // ... (Logic pemetaan status UI, sama seperti sebelumnya) ...
                $tglPinjam = Carbon::parse($lr->loan_date_start);
                $jatuhTempo = Carbon::parse($lr->loan_date_end);

                $startTime = $lr->start_time ?? '00:00:00';
                $endTime   = $lr->end_time ?? '00:00:00';

                $waktuPinjamStr = $tglPinjam->format('d/m/Y') . ' | ' . \Carbon\Carbon::parse($startTime)->format('H:i');
                $jatuhTempoStr = $jatuhTempo->format('d/m/Y') . ' | ' . \Carbon\Carbon::parse($endTime)->format('H:i');

                $statusUi = 'Unknown';
                $waktuKembaliStr = '-';

                // --- [FIX BEGIN] Custom Late Logic (Same as Riwayat.php) ---
                $statusUi = $lr->status_ui; // Default fallback

                // 1. Construct Precise Due Date Time
                $dueDateTime = $jatuhTempo->copy(); // Date part
                if (!empty($lr->end_time)) {
                    $parts = explode(':', $lr->end_time);
                    if (count($parts) >= 2) {
                        $dueDateTime->setTime($parts[0], $parts[1], 0);
                    } else {
                        $dueDateTime->setTime(23, 59, 59);
                    }
                } else {
                    $dueDateTime->setTime(23, 59, 59);
                }

                // 2. Override Status based on Precise Comparison
                if ($lr->status === 'returned' && $lr->loanRecord && $lr->loanRecord->is_submitted) {
                    $returnedAt = $lr->loanRecord->returned_at 
                        ? Carbon::parse($lr->loanRecord->returned_at) 
                        : null;

                    if ($returnedAt) {
                        if ($returnedAt->gt($dueDateTime)) {
                            $statusUi = 'Terlambat';
                        } else {
                            $statusUi = 'Selesai';
                        }
                    } else {
                         // Fallback if no returned_at but status is returned
                         $statusUi = 'Sudah Kembali'; 
                    }
                } 
                elseif (in_array($lr->status, ['handed_over', 'approved'])) {
                    // For active items, check against NOW
                    if (now()->gt($dueDateTime)) {
                        $statusUi = 'Terlambat';
                    }
                }
                // --- [FIX END] ---

                $waktuKembaliStr = '-';
                if ($lr->status === 'returned' && $lr->loanRecord && $lr->loanRecord->returned_at) {
                     $waktuKembaliStr = \Carbon\Carbon::parse($lr->loanRecord->returned_at)->format('d/m/Y | H:i');
                } elseif ($lr->status === 'returned') {
                     $waktuKembaliStr = $jatuhTempo->format('d/m/Y | H:i');
                }

                $kategoriUi = $inv->category === 'ruangan' ? 'Ruangan' : 'Barang';

                return (object) [
                    'nama_item'    => $inv->name,
                    'kategori'     => $kategoriUi,
                    'peminjam'     => $lr->borrower_name,
                    'waktu_pinjam' => $waktuPinjamStr,
                    'jatuh_tempo'  => $jatuhTempoStr,
                    'waktu_kembali'=> $waktuKembaliStr,
                    'jumlah'       => (int) ($inv->pivot->quantity ?? 1),
                    'status'       => $statusUi,
                    'keterangan'   => optional($lr->loanRecord)->notes ?? '-',
                    
                    // Fields for filtering
                    'raw_date'     => $tglPinjam
                ];
            });
        });

        // 3. Filter Collection
        // Strict Allowed Statuses
        $allowedStatuses = ['Sedang Dipinjam', 'Sudah Kembali', 'Terlambat', 'Siap Diambil', 'Selesai'];
        $mappedCollection = $mappedCollection->filter(fn ($r) => in_array($r->status, $allowedStatuses, true));

        // Filter Category
        if ($this->vKategori !== 'all') {
            $mappedCollection = $mappedCollection->filter(fn ($r) => $r->kategori === $this->vKategori);
        }

        // Filter Status
        if ($this->vStatus !== 'all') {
            $mappedCollection = $mappedCollection->filter(fn ($r) => $r->status === $this->vStatus);
        }

        // Filter Search
        if ($this->q) {
            $q = strtolower($this->q);
            $mappedCollection = $mappedCollection->filter(function ($r) use ($q) {
                return str_contains(strtolower($r->nama_item), $q)
                    || str_contains(strtolower($r->kategori), $q)
                    || str_contains(strtolower($r->peminjam), $q)
                    || str_contains(strtolower($r->status), $q);
            });
        }

        // 4. Calculate Chart Data (from filtered results, BEFORE pagination)
        $chartData = $this->calculateChartData($mappedCollection);

        // 5. Paginate Collection
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $slice = $mappedCollection->slice(($currentPage - 1) * $this->perPage, $this->perPage)->values();
        
        $laporans = new LengthAwarePaginator(
            $slice, 
            $mappedCollection->count(), 
            $this->perPage, 
            $currentPage, 
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        $this->dispatch('chartUpdate', [
            'labels' => $chartData['labels'],
            'values' => $chartData['values'],
            'isDummy' => $chartData['isDummy']
        ]);

        return view('livewire.pengelola.laporan', [
            'laporans' => $laporans,
            'chartLabels' => $chartData['labels'],
            'chartValues' => $chartData['values'],
            'isDummyChart' => $chartData['isDummy']
        ])->layout('pengelola.layouts.pengelola');
    }

    private function calculateChartData($collection)
    {
        if ($collection->isEmpty()) {
            return [
                'labels' => [],
                'values' => [],
                'isDummy' => false
            ];
        }

        $counts = $collection->groupBy('nama_item')->map->sum('jumlah')->sortDesc()->take(10);
        
        return [
            'labels' => $counts->keys()->toArray(),
            'values' => $counts->values()->toArray(),
            'isDummy' => false
        ];
    }
}
