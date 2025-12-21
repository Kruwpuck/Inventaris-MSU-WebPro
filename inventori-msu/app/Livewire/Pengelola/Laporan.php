<?php

namespace App\Livewire\Pengelola;

use Livewire\Component;
use App\Models\LoanRequest;
use Carbon\Carbon;

class Laporan extends Component
{
    use Livewire\WithPagination;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Support\Collection;

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
    public function updatingDateFrom() { $this->resetPage(); }
    public function updatingDateTo() { $this->resetPage(); }

    public function render()
    {
        // 1. Base Query
        $query = LoanRequest::with(['items', 'loanRecord'])->orderByDesc('id');

        // Filter Date Range at Query Level (Optimization)
        if ($this->dateFrom) {
            $query->whereDate('loan_date_start', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('loan_date_start', '<=', $this->dateTo);
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

                switch ($lr->status) {
                    case 'returned':
                        $waktuKembaliStr = '-';
                        $actualReturn = null;
                        if ($lr->loanRecord && $lr->loanRecord->returned_at) {
                            $actualReturn = Carbon::parse($lr->loanRecord->returned_at);
                            $waktuKembaliStr = $actualReturn->format('d/m/Y | H:i');
                        } else {
                            $waktuKembaliStr = $jatuhTempo->format('d/m/Y | H:i'); 
                        }
                        
                        if ($lr->loanRecord && $lr->loanRecord->is_submitted) {
                            if ($actualReturn && $actualReturn->gt($jatuhTempo)) {
                                $statusUi = 'Terlambat';
                            } else {
                                $statusUi = 'Selesai';
                            }
                        } else {
                             $statusUi = 'Sudah Kembali';
                        }
                        break;
                    
                    case 'handed_over':
                        $statusUi = $today->gt($jatuhTempo) ? 'Terlambat' : 'Sedang Dipinjam';
                        break;

                    case 'approved':
                        $statusUi = 'Siap Diambil';
                        break;

                    case 'pending':
                        $statusUi = 'Menunggu Approve';
                        break;

                    case 'rejected':
                        $statusUi = 'Ditolak';
                        break;

                    default:
                        $statusUi = $today->gt($jatuhTempo) ? 'Terlambat' : 'Sedang Dipinjam';
                        break;
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
                'labels' => ["Proyektor", "Meja", "Speaker", "Terpal", "Sofa", "Hijab", "Ruang Utama", "Selasar", "Zoom", "Ruang VIP"],
                'values' => [12, 10, 9, 8, 7, 6, 5, 4, 3, 2],
                'isDummy' => true
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
