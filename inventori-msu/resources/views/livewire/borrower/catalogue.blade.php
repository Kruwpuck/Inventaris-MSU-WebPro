<div>
  <section class="py-5">
    <div class="container">
      <h2 class="section-title mb-4">
        {{ $category == 'barang' ? 'Katalog Barang' : 'Katalog Ruangan' }}
      </h2>

      <!-- Search Bar -->
      <div class="mb-4">
        <input type="text" class="form-control" placeholder="Cari {{ $category == 'barang' ? 'barang' : 'ruangan' }}..." wire:model.live="search">
      </div>

      <div class="row g-4 align-items-stretch items-grid">
        @forelse($items as $item)
        <div class="col-6 col-md-4 col-lg-2-4">
          <article class="item-card tap-anim reveal-up h-100" data-max="{{ $category == 'barang' ? $item->stock : 1 }}">
            <div class="item-thumb">
              <img src="{{ asset('aset/peminjam/' . $item->image_path) }}" alt="{{ $item->name }}" class="img-fluid">
              <span class="badge-status">Active</span>
              <div class="qty-actions">
                <button type="button" class="qty-btn" wire:click="addToCart({{ $item->id }})" aria-label="Tambahkan">＋</button>
              </div>
            </div>
            <div class="item-body">
              <div class="item-title">{{ $item->name }}</div>
              <div class="item-meta">
                  @if($category == 'barang')
                    Sisa : <span class="sisa">{{ $item->stock }}</span>
                  @else
                    Kapasitas : <span class="sisa">{{ $item->capacity }}</span>
                  @endif
              </div>
            </div>
          </article>
        </div>
        @empty
        <div class="col-12 text-center">
            <p>Tidak ada item ditemukan.</p>
        </div>
        @endforelse
      </div>
    </div>
  </section>
</div>
