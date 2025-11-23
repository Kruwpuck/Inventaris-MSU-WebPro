{{-- resources/views/livewire/pengelola/beranda.blade.php --}}

<div>
    {{-- HERO / HEADER GAMBAR --}}
    <section
        class="d-flex justify-content-center align-items-start position-relative w-100"
        style="
            padding-top: 55px;
            height: 70vh;
            background-image: url('{{ asset('aset/ramadhan.png') }}');
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
        "
    >
        <img
            src="{{ asset('aset/MSU.png') }}"
            alt="Syamsul Ulum"
            class="img-fluid shadow-strong rounded-3 mt-5"
            style="height: 230px; width: 1100px; z-index: 2"
        />
    </section>


    <div class="container">

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif

        {{-- SEARCH --}}
        <form
            wire:submit.prevent
            id="searchForm"
            class="row align-items-center justify-content-center gx-2 gy-2"
        >
            <div class="col-12 col-md-6 col-lg-5">
                <div class="input-group">
                    <input
                        wire:model.live="q"
                        id="quickSearch"
                        type="text"
                        class="form-control rounded-start-pill py-2"
                        placeholder="Cari barang atau fasilitas..."
                    />
                    <button class="btn btn-success rounded-end-pill px-4" type="button">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>
                </div>
            </div>
        </form>


        {{-- TEXT HEADER + DROPDOWN KATEGORI --}}
        <section class="text-center mt-5">
            <h4 class="fw-light mb-2">
                Satu langkah menuju
                <b class="text-success">kemudahan beraktivitas</b> di MSU
            </h4>
            <p class="text-muted mb-3">
                Semua urusan peminjaman dan perizinan kini bisa dilakukan secara online.
            </p>

            <div class="text-start">
                <div class="dropdown d-inline-block">
                    <button
                        id="kategoriBtn"
                        class="btn btn-success dropdown-toggle rounded-pill px-4 py-2"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        style="background-color: #0b492c; border-color: #0b492c"
                    >
                        Barang
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#" data-switch="barang">Barang</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" data-switch="ruangan">Ruangan</a>
                        </li>
                    </ul>
                </div>
            </div>
        </section>


        {{-- GRID BARANG --}}
        <section id="gridBarang" class="mt-4 pb-5">
            <div class="row g-4 justify-content-center">

                {{-- DINAMIS: loop dari DB --}}
                @forelse($barangs as $b)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div
                            class="card h-100 border-0 rounded-4 shadow-strong mx-auto {{ !$b->is_active ? 'item-disabled' : '' }}"
                            style="max-width: 16rem"
                        >
                            <img
                                src="{{ $b->gambar ? asset($b->gambar) : asset('aset/default.png') }}"
                                class="card-img-top"
                                alt="{{ $b->nama }}"
                                style="height: 160px; object-fit: cover"
                            />
                            <div class="card-body py-3 px-3">
                                <h6 class="card-title fw-semibold mb-1">{{ $b->nama }}</h6>
                                <p class="card-text text-muted small mb-2">
                                    {{ $b->deskripsi }}
                                </p>
                                <p class="item-stok text-secondary small mb-3">
                                    Stok: <b>{{ $b->stok }} unit</b>
                                </p>

                                <button
                                    class="btn btn-success btn-sm rounded-pill px-3 py-1 btn-edit"
                                    style="background-color: #0b492c"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-item-tipe="barang"
                                    data-item-id="{{ $b->id }}"
                                    data-item-nama="{{ $b->nama }}"
                                    data-item-deskripsi="{{ $b->deskripsi }}"
                                    data-item-stok="{{ $b->stok }}"
                                >
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted mt-4">Belum ada data barang.</p>
                @endforelse

            </div>
        </section>


        {{-- GRID FASILITAS --}}
        <section id="gridRuangan" class="mt-4 pb-5 d-none">
            <div class="row g-4 justify-content-center">

                @forelse($fasilitas as $f)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div
                            class="card h-100 border-0 rounded-4 shadow-strong mx-auto {{ !$f->is_active ? 'item-disabled' : '' }}"
                            style="max-width: 16rem"
                        >
                            <img
                                src="{{ $f->gambar ? asset($f->gambar) : asset('aset/default.png') }}"
                                class="card-img-top"
                                alt="{{ $f->nama }}"
                                style="height: 160px; object-fit: cover"
                            />
                            <div class="card-body py-3 px-3">
                                <h6 class="card-title fw-semibold mb-1">{{ $f->nama }}</h6>
                                <p class="card-text text-muted small mb-2">
                                    {{ $f->deskripsi }}
                                </p>
                                <p class="item-stok text-secondary small mb-3">
                                    Ketersediaan: <b>{{ $f->status }}</b>
                                </p>

                                <button
                                    class="btn btn-success btn-sm rounded-pill px-3 py-1 btn-edit"
                                    style="background: #0b492c"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-item-tipe="fasilitas"
                                    data-item-id="{{ $f->id }}"
                                    data-item-nama="{{ $f->nama }}"
                                    data-item-deskripsi="{{ $f->deskripsi }}"
                                    data-item-status="{{ $f->status }}"
                                >
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted mt-4">Belum ada data fasilitas/ruangan.</p>
                @endforelse

            </div>
        </section>
    </div>


    {{-- MODAL EDIT (tetap 1 modal untuk dua tipe) --}}
    <div
        class="modal fade"
        id="editModal"
        tabindex="-1"
        aria-labelledby="editModalLabel"
        aria-hidden="true"
        wire:ignore
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- 
                  Form ini lebih gampang kalau submitnya diarahkan ke Livewire modal khusus (InventoryEditModal).
                  Tapi kalau sementara mau plain HTML dulu, biarkan seperti ini + handle di JS/route. 
                --}}
                <form id="editForm">
                    <div class="modal-body">
                        <input type="hidden" id="editItemId" />

                        <div class="mb-3">
                            <label class="form-label">Nama Item</label>
                            <input type="text" class="form-control" id="editNamaItem" readonly disabled />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="editDeskripsiItem" rows="3" required></textarea>
                        </div>

                        <div class="mb-3" id="editFormGroupBarang">
                            <label class="form-label">Stok (unit/akun)</label>
                            <input type="number" class="form-control" id="editStokInput" min="0" required />
                        </div>

                        <div class="mb-3 d-none" id="editFormGroupFasilitas">
                            <label class="form-label">Status Ketersediaan</label>
                            <select class="form-select" id="editStatusSelect" required>
                                <option value="Tersedia">Tersedia</option>
                                <option value="Tidak Tersedia">Tidak Tersedia</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

  document.body.addEventListener('click', function(e){
    const item = e.target.closest('[data-switch]');
    if(!item) return;

    e.preventDefault();

    const tipe = item.getAttribute('data-switch'); // "barang" / "ruangan"

    const gridBarang = document.getElementById('gridBarang');
    const gridRuangan = document.getElementById('gridRuangan'); 
    // id sectionnya masih gridFasilitas (boleh tetap), tapi kita anggap itu grid ruangan

    const kategoriBtn = document.getElementById('kategoriBtn');

    if(tipe === 'barang'){
      gridBarang.classList.remove('d-none');
      gridRuangan.classList.add('d-none');
      kategoriBtn.textContent = 'Barang';
    } else if(tipe === 'ruangan'){
      gridBarang.classList.add('d-none');
      gridRuangan.classList.remove('d-none');
      kategoriBtn.textContent = 'Ruangan';
    }
  });

});
</script>
@endpush
