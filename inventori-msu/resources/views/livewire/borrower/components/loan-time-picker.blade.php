<section class="datebar reveal-up" id="loan-time-picker">
    <div class="container">
      <h3 class="section-title mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-clock-history"></i> Waktu Peminjaman
      </h3>

      <div class="datebar-wrap">
        <!-- Tanggal Pakai -->
        <div class="datebar-field datebar-field--start">
          <label class="form-label mb-1">
            <i class="bi bi-calendar-event me-1"></i>Tanggal Pakai
          </label>
          <input type="date" class="form-control form-control-sm" wire:model="startDate">
           @error('startDate') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <!-- Tanggal Kembali -->
        <div class="datebar-field datebar-field--end">
          <label class="form-label mb-1">
            <i class="bi bi-calendar-check me-1"></i>Tanggal Kembali
          </label>
          <input type="date" class="form-control form-control-sm" wire:model="endDate">
          @error('endDate') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <!-- Jam Mulai -->
        <div class="datebar-field datebar-field--time">
          <label class="form-label mb-1">
            <i class="bi bi-alarm me-1"></i>Jam Mulai
          </label>
          <input type="time" class="form-control form-control-sm" wire:model="startTime">
          @error('startTime') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <!-- Durasi -->
        <div class="datebar-field datebar-field--dur">
          <label class="form-label mb-1">
            <i class="bi bi-hourglass-split me-1"></i>Durasi
          </label>
          <select class="form-select form-select-sm" wire:model="duration">
            <option value="1">1 jam</option>
            <option value="2">2 jam</option>
            <option value="3">3 jam</option>
            <option value="4">4 jam</option>
            <option value="5">5 jam</option>
            <option value="8">Seharian (8 jam)</option>
            <option value="24">Seharian (24 jam)</option>
          </select>
           @error('duration') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <!-- Tombol + info tanggal -->
        <div class="datebar-actions">
          <button class="btn btn-success btn-sm w-100" wire:click="checkAvailability">
            <i class="bi bi-search me-1"></i>Cek Ketersediaan
          </button>
           @if(session()->has('loan_session'))
          <div class="small text-muted mt-2 js-daterange" aria-live="polite">
             ✅ Diset: {{ session('loan_session')['start_date'] }} ({{ session('loan_session')['start_time'] }}, {{ session('loan_session')['duration'] }} jam)
          </div>
          @endif
        </div>
      </div>
    </div>
</section>
