{{-- resources/views/livewire/pengelola/approval.blade.php --}}

@push('head')
<style>
  body {
    background-color: #ffffff;
    font-family: "Poppins", sans-serif;
  }
  .shadow-soft {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08),
      0 2px 4px rgba(0, 0, 0, 0.04);
  }
  .navbar-nav .nav-link {
    color: #2fa16c;
    font-weight: 600;
    margin: 0 10px;
    transition: color 0.2s ease;
  }
  .navbar-nav .nav-link:hover {
    color: #0b492c;
  }
  .navbar-nav .nav-link.active {
    color: #0b492c !important;
  }
  input[type="search"]:focus,
  input[type="text"]:focus,
  .form-control:focus {
    box-shadow: none !important;
    border-color: #000 !important;
  }
  .action-buttons .btn {
    min-width: 90px;
  }
</style>
@endpush

<div>
  <div class="container pt-5 pb-5" style="padding-top: 100px">
    <div class="row">
      <div class="col-12">
        
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <h2 class="mb-4">Daftar Pengajuan (Pending)</h2>
        <p class="text-muted mb-4">
          Tinjau pengajuan peminjaman barang dan fasilitas yang masih tertunda.
        </p>

        <div class="table-responsive shadow-soft rounded-3">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Peminjam</th>
                <th scope="col">Barang/Ruangan</th>
                <th scope="col">Tgl Pinjam</th>
                <th scope="col">Status</th>
                <th scope="col" style="min-width: 190px">Aksi</th>
              </tr>
            </thead>
            <tbody>
                @forelse($pendingRequests as $req)
                    <tr wire:key="pending-{{ $req->id }}">
                        <td><strong>{{ $req->id }}</strong></td>
                        <td>{{ $req->borrower_name }}</td>
                        <td>
                            <ul class="list-unstyled mb-0">
                                @foreach($req->items as $item)
                                    <li>{{ $item->name }} (x{{ $item->pivot->quantity }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            {{ $req->loan_date_start->format('d M Y') }} - 
                            {{ $req->loan_date_end->format('d M Y') }}
                        </td>
                        <td><span class="badge bg-warning text-dark">{{ ucfirst($req->status) }}</span></td>
                        <td>
                            <div class="d-flex align-items-center" style="gap: .25rem;">
                                <button class="btn btn-success btn-sm" 
                                        wire:click="approve({{ $req->id }})"
                                        wire:confirm="Yakin ingin menyetujui pengajuan ini?">
                                    <i class="bi bi-check-lg"></i> Setuju
                                </button>
                                <button class="btn btn-danger btn-sm" 
                                        wire:click="prepareReject({{ $req->id }})">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-5 text-muted text-center">
                            Tidak ada pengajuan pending saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="row mt-5 pt-3">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="mb-0">Riwayat Keputusan</h2>
          {{-- Export button could be re-implemented to point to a route --}}
        </div>

        <p class="text-muted mb-4">
          Daftar pengajuan yang telah disetujui atau ditolak.
        </p>

        <div class="table-responsive shadow-soft rounded-3">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Peminjam</th>
                <th scope="col">Item</th>
                <th scope="col">Status</th>
                <th scope="col">Catatan</th>
              </tr>
            </thead>
            <tbody>
                @forelse($historyRequests as $hist)
                    <tr wire:key="hist-{{ $hist->id }}">
                        <td><strong>{{ $hist->id }}</strong></td>
                        <td>{{ $hist->borrower_name }}</td>
                        <td>
                             <ul class="list-unstyled mb-0">
                                @foreach($hist->items as $item)
                                    <li>{{ $item->name }} (x{{ $item->pivot->quantity }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            @if($hist->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $hist->rejection_reason ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-5 text-muted text-center">
                            Belum ada riwayat keputusan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- MODAL PENOLAKAN --}}
  <div class="modal fade" id="rejectionModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form wire:submit.prevent="reject">
          <div class="modal-header">
            <h5 class="modal-title">Alasan Penolakan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p>Anda wajib memberikan alasan mengapa pengajuan ini ditolak.</p>
            
            <div class="mb-3">
              <label class="form-label">Catatan Alasan (Wajib diisi)</label>
              <textarea
                class="form-control"
                wire:model="rejectReason"
                rows="4"
                required
              ></textarea>
              @error('rejectReason') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Kirim Penolakan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        const rejectionModal = new bootstrap.Modal(document.getElementById('rejectionModal'));

        Livewire.on('open-reject-modal', () => {
            rejectionModal.show();
        });

        Livewire.on('close-reject-modal', () => {
            rejectionModal.hide();
        });
    });
</script>
@endpush
