{{-- resources/views/livewire/pengelola/tambah-hapus.blade.php --}}
@push('head')
<style>
  body{background:#fff;font-family:"Poppins",sans-serif}
  .form-card{border-radius:22px}
  .pill{border-radius:9999px}
  .btn-msu{background:#0b492c;border-color:#0b492c}
  .btn-msu:hover{filter:brightness(.95)}
  .preview{height:360px;object-fit:cover;border-radius:18px}
  @media (max-width:991.98px){.preview{height:240px}}
</style>
@endpush

<div class="container pt-5">
  <div style="height:84px"></div>

  <section class="text-center mb-4">
    <p class="mb-1 fw-semibold" style="letter-spacing:.06em;color:#2a6a55">PENAMBAHAN BARANG/RUANGAN</p>
    <h2 class="fw-bolder text-uppercase" style="color:#32435a">Masjid Syamsul Ulum</h2>
    <div class="mx-auto" style="width:120px;height:3px;background:#2a6a55;border-radius:2px"></div>
  </section>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <section class="pb-5">
    <div class="row g-4 align-items-stretch">

      {{-- LEFT: FOTO --}}
      <div class="col-12 col-lg-5">
        <div class="card form-card shadow h-100">
          <div class="card-body">
            <h5 class="mb-3">Foto Barang/Ruangan</h5>

            {{-- Preview: jika user pilih file -> temporaryUrl() --}}
            @if ($image)
              <img class="w-100 preview mb-3" src="{{ $image->temporaryUrl() }}" alt="Preview">
            @else
              <img class="w-100 preview mb-3"
                   src="https://images.unsplash.com/photo-1520975922219-830a99aa20a6?q=80&w=1600&auto=format&fit=crop"
                   alt="Preview">
            @endif

            <input type="file" class="form-control" accept="image/*" wire:model="image">
            <small class="text-muted d-block mt-2">Format JPG/PNG, maksimal 5MB.</small>
            @error('image') <small class="text-danger">{{ $message }}</small> @enderror
            <div wire:loading wire:target="image" class="small text-muted mt-1">Mengunggah...</div>
          </div>
        </div>
      </div>

      {{-- RIGHT: FORM --}}
      <div class="col-12 col-lg-7">
        <div class="card form-card shadow h-100">
          <div class="card-body">
            <h5 class="text-center fw-bold mb-4">Form Pendaftaran</h5>

            <form wire:submit.prevent="save" class="row g-3">

              {{-- Kategori --}}
              <div class="col-12">
                <label class="form-label">Kategori*</label>
                <select class="form-select pill" wire:model="category" required>
                  <option value="">Pilih kategori</option>
                  <option value="Barang">Barang</option>
                  <option value="Ruangan">Ruangan</option>
                </select>
                @error('category') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              {{-- Nama --}}
              <div class="col-12">
                <label class="form-label">Nama*</label>
                <input type="text" class="form-control pill"
                       wire:model.defer="name"
                       placeholder="Contoh: Proyektor / Ruang Tamu VIP" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              {{-- Deskripsi --}}
              <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" rows="3" style="border-radius:18px"
                          wire:model.defer="description"
                          placeholder="Spesifikasi singkat / aturan penggunaan (opsional)"></textarea>
                @error('description') <small class="text-danger">{{ $message }}</small> @enderror
              </div>

              {{-- Status (belum ke DB, disimpan di properti saja) --}}
              <div class="col-12 col-md-6">
                <label class="form-label">Status</label>
                <select class="form-select pill" wire:model.defer="status">
                  <option value="Tersedia">Tersedia</option>
                  <option value="Tidak Tersedia">Tidak Tersedia</option>
                  <option value="Perawatan">Perawatan</option>
                </select>
              </div>

              {{-- Stok / Kapasitas (muncul sesuai kategori) --}}
              @if ($category === 'Barang')
                <div class="col-12 col-md-6">
                  <label class="form-label">Stok*</label>
                  <input type="number" min="0" class="form-control pill"
                         wire:model.defer="stock" placeholder="Contoh: 10">
                  @error('stock') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
              @elseif ($category === 'Ruangan')
                <div class="col-12 col-md-6">
                  <label class="form-label">Kapasitas*</label>
                  <div class="input-group">
                    <input type="number" min="1" class="form-control pill"
                           wire:model.defer="capacity" placeholder="Contoh: 100">
                    <span class="input-group-text pill">orang</span>
                  </div>
                  @error('capacity') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
              @endif

              {{-- Aksi --}}
              <div class="col-12 d-flex justify-content-end pt-2">
                <button type="reset" class="btn btn-outline-secondary pill px-4 me-2">Bersihkan</button>
                <button type="submit" class="btn btn-msu text-white pill px-4">
                  <i class="bi bi-check2-circle me-1"></i>Daftar
                </button>
              </div>

            </form>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>
