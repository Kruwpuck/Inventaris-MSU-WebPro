<div>
  <section class="py-5">
    <div class="container">
      <h2 class="section-title mb-4">Keranjang Peminjaman</h2>

      @if(session()->has('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <div class="row g-5">
        <!-- List Item -->
        <div class="col-lg-7">
          @if(empty($cart))
              <div class="text-center py-5">
                  <p class="text-muted">Keranjang Anda kosong.</p>
                  <a href="{{ route('home') }}" class="btn btn-primary">Mulai Pinjam</a>
              </div>
          @else
              @foreach($cart as $id => $item)
              <div class="card mb-3 border-0 shadow-sm">
                <div class="row g-0 align-items-center">
                  <div class="col-3 col-md-2">
                    <img src="{{ asset('aset/peminjam/' . $item['image_path']) }}" class="img-fluid rounded-start" alt="{{ $item['name'] }}" style="height: 80px; object-fit: cover; width: 100%;">
                  </div>
                  <div class="col-9 col-md-10">
                    <div class="card-body d-flex justify-content-between align-items-center">
                      <div>
                        <h5 class="card-title mb-1">{{ $item['name'] }}</h5>
                        <p class="card-text text-muted small mb-0">{{ ucfirst($item['category']) }}</p>
                      </div>
                      <div class="d-flex align-items-center gap-3">
                        <div class="btn-group btn-group-sm" role="group">
                          <button type="button" class="btn btn-outline-secondary" wire:click="updateQuantity({{ $id }}, 'dec')">-</button>
                          <span class="btn btn-outline-secondary disabled text-dark" style="min-width: 40px;">{{ $item['quantity'] }}</span>
                          <button type="button" class="btn btn-outline-secondary" wire:click="updateQuantity({{ $id }}, 'inc')">+</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" wire:click="removeItem({{ $id }})">
                            <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
          @endif
        </div>

        <!-- Form Peminjam -->
        <div class="col-lg-5">
          <div class="card border-0 shadow-sm p-4">
            <h4 class="mb-3">Data Peminjam</h4>
            <form wire:submit.prevent="submit">
              <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control @error('borrower_name') is-invalid @enderror" id="name" wire:model="borrower_name">
                @error('borrower_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('borrower_email') is-invalid @enderror" id="email" wire:model="borrower_email">
                @error('borrower_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="mb-3">
                <label for="phone" class="form-label">No. Telepon / WA</label>
                <input type="text" class="form-control @error('borrower_phone') is-invalid @enderror" id="phone" wire:model="borrower_phone">
                @error('borrower_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="mb-3">
                <label for="reason" class="form-label">Keperluan Peminjaman</label>
                <textarea class="form-control @error('borrower_reason') is-invalid @enderror" id="reason" rows="3" wire:model="borrower_reason"></textarea>
                @error('borrower_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
              <div class="row">
                <div class="col-6 mb-3">
                  <label for="start_date" class="form-label">Tanggal Mulai</label>
                  <input type="date" class="form-control @error('loan_date_start') is-invalid @enderror" id="start_date" wire:model="loan_date_start">
                  @error('loan_date_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-6 mb-3">
                  <label for="end_date" class="form-label">Tanggal Selesai</label>
                  <input type="date" class="form-control @error('loan_date_end') is-invalid @enderror" id="end_date" wire:model="loan_date_end">
                  @error('loan_date_end') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
              </div>

              <button type="submit" class="btn btn-success w-100 mt-3" {{ empty($cart) ? 'disabled' : '' }}>
                Ajukan Peminjaman
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
