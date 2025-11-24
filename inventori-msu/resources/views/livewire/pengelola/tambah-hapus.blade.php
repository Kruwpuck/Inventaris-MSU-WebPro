{{-- resources/views/livewire/pengelola/tambah-hapus.blade.php --}}

@push('head')
<style>
  body {
    background: #ffffff;
    font-family: "Poppins", sans-serif;
  }
  .shadow-soft {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08),
      0 2px 4px rgba(0, 0, 0, 0.04);
  }
  .shadow-strong {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12),
      0 4px 8px rgba(0, 0, 0, 0.08);
  }
  .title-bar {
    letter-spacing: 0.06em;
    color: #2a6a55;
  }
  .form-card {
    border-radius: 22px;
  }
  .pill {
    border-radius: 9999px;
  }
  .btn-msu {
    background: #0b492c;
    border-color: #0b492c;
  }
  .btn-msu:hover {
    filter: brightness(0.95);
  }
  .label-muted {
    font-size: 0.95rem;
    color: #56616b;
  }
  .help {
    font-size: 0.8rem;
    color: #6c757d;
  }
  .preview {
    height: 360px;
    object-fit: cover;
    border-radius: 18px;
  }
  @media (max-width: 991.98px) {
    .preview { height: 240px; }
  }

  .dropdown-toggle.pill {
    border: 1px solid #0b492c;
    color: #0b492c;
    background: #fff;
  }
  .dropdown-toggle.pill:hover {
    background: #f5fbf7;
    color: #0b492c;
  }
  .dropdown-menu {
    border-radius: 14px;
    border: 1px solid #0b492c;
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.1);
  }
  .dropdown-item {
    border-radius: 10px;
  }
  .dropdown-item.active,
  .dropdown-item:hover {
    background: #e9f6ef;
    color: #0b492c;
  }

  input.form-control:focus,
  textarea.form-control:focus,
  select.form-select:focus,
  .dropdown-toggle:focus {
    border-color: #000;
    box-shadow: none;
    outline: 0;
  }
</style>
@endpush


<div class="container pt-5">
  <div style="height: 84px"></div>

  {{-- HEADER --}}
  <section class="container text-center mb-4">
    <p class="mb-1 title-bar fw-semibold">PENAMBAHAN BARANG/RUANGAN</p>
    <h2 class="fw-bolder text-uppercase" style="color: #32435a">
      Masjid Syamsul Ulum
    </h2>
    <div class="mx-auto" style="width:120px;height:3px;background:#2a6a55;border-radius:2px;"></div>
  </section>

  {{-- FORM SECTION --}}
  <section class="container pb-5">
    <div class="row g-4 align-items-stretch">

      {{-- LEFT: FOTO --}}
      <div class="col-12 col-lg-5">
        <div class="card form-card shadow-strong h-100">
          <div class="card-body">
            <h5 class="mb-3">Foto Barang/Ruangan</h5>

            {{-- Preview image --}}
            <img
              id="imgPreview"
              class="w-100 preview mb-3"
              src="https://images.unsplash.com/photo-1520975922219-830a99aa20a6?q=80&w=1600&auto=format&fit=crop"
              alt="Preview"
            />

            <div class="d-grid gap-2">
              <label class="btn btn-outline-secondary pill">
                <i class="bi bi-upload me-2"></i>Pilih Foto
                <input
                  id="fileInput"
                  type="file"
                  class="d-none"
                  accept="image/*"
                />
              </label>
              <small class="help">
                Format JPG/PNG, maksimal ±5MB. Gunakan foto terang agar jelas di katalog.
              </small>
            </div>
          </div>
        </div>
      </div>

      {{-- RIGHT: FORM --}}
      <div class="col-12 col-lg-7">
        <div class="card form-card shadow-strong h-100">
          <div class="card-body">
            <h5 class="text-center fw-bold mb-4">Form Pendaftaran</h5>

            <form id="formTambah" class="row g-3 needs-validation" novalidate>

              {{-- Kategori --}}
              <div class="col-12">
                <label class="form-label label-muted">Kategori*</label>
                <div class="dropdown">
                  <button
                    class="btn dropdown-toggle pill w-100 text-start"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    id="btnKategori"
                  >
                    Pilih kategori
                  </button>
                  <ul class="dropdown-menu w-100" aria-labelledby="btnKategori">
                    <li><a class="dropdown-item" href="#" data-value="Barang">Barang</a></li>
                    <li><a class="dropdown-item" href="#" data-value="Ruangan">Ruangan</a></li>
                  </ul>
                </div>

                <input type="hidden" id="inpKategori" required />
                <div class="invalid-feedback">Pilih kategori terlebih dahulu.</div>
              </div>

              {{-- Nama --}}
              <div class="col-12">
                <label class="form-label label-muted">Nama*</label>
                <input
                  id="inpNama"
                  type="text"
                  class="form-control pill"
                  placeholder="Contoh: Proyektor / Ruang Tamu VIP"
                  required
                />
                <div class="invalid-feedback">Nama wajib diisi.</div>
              </div>

              {{-- Deskripsi --}}
              <div class="col-12">
                <label class="form-label label-muted">Deskripsi</label>
                <textarea
                  id="inpDesk"
                  class="form-control"
                  rows="3"
                  style="border-radius:18px"
                  placeholder="Spesifikasi singkat / aturan penggunaan (opsional)"
                ></textarea>
                <small class="help">Disarankan 15–200 karakter.</small>
              </div>

              {{-- Status --}}
              <div class="col-12 col-md-6">
                <label class="form-label label-muted">Status</label>
                <select id="inpStatus" class="form-select pill">
                  <option value="Tersedia" selected>Tersedia</option>
                  <option value="Tidak Tersedia">Tidak Tersedia</option>
                  <option value="Perawatan">Perawatan</option>
                </select>
              </div>

              {{-- Stok (khusus Barang) --}}
              <div class="col-12 col-md-6" id="wrapStok" style="display:none">
                <label class="form-label label-muted">Stok*</label>
                <input
                  id="inpStok"
                  type="number"
                  min="0"
                  class="form-control pill"
                  placeholder="Contoh: 10"
                />
                <div class="invalid-feedback">
                  Stok wajib diisi untuk kategori Barang.
                </div>
              </div>

              {{-- Kapasitas (khusus Ruangan) --}}
              <div class="col-12 col-md-6" id="wrapKapasitas" style="display:none">
                <label class="form-label label-muted">Kapasitas*</label>
                <div class="input-group">
                  <input
                    id="inpKapasitas"
                    type="number"
                    min="1"
                    class="form-control pill"
                    placeholder="Contoh: 100"
                  />
                  <span class="input-group-text pill">orang</span>
                </div>
                <div class="invalid-feedback">
                  Kapasitas wajib diisi untuk kategori Ruangan.
                </div>
              </div>

              {{-- Aksi --}}
              <div class="col-12 d-flex justify-content-end pt-2">
                <button type="reset" class="btn btn-outline-secondary pill px-4 me-2">
                  Bersihkan
                </button>
                <button type="submit" class="btn btn-msu text-white pill px-4">
                  <i class="bi bi-check2-circle me-1"></i>Daftar
                </button>
              </div>

              {{-- Alert sukses --}}
              <div id="alertSukses" class="alert alert-success d-none mt-2 mb-0" role="alert">
                Data berhasil disiapkan. (Simulasi front-end) — lanjut proses simpan ke backend.
              </div>

            </form>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>


@push('scripts')
<script>
  // Preview upload foto
  const fileInput = document.getElementById("fileInput");
  const imgPreview = document.getElementById("imgPreview");
  if (fileInput && imgPreview) {
    fileInput.addEventListener("change", (e) => {
      const f = e.target.files && e.target.files[0];
      if (!f) return;
      const reader = new FileReader();
      reader.onload = (ev) => (imgPreview.src = ev.target.result);
      reader.readAsDataURL(f);
    });
  }

  // Dropdown kategori -> hidden input + bidang dinamis
  const btnKategori = document.getElementById("btnKategori");
  const menuKategori = btnKategori ? btnKategori.nextElementSibling : null;
  const inpKategori = document.getElementById("inpKategori");

  const wrapStok = document.getElementById("wrapStok");
  const wrapKapasitas = document.getElementById("wrapKapasitas");
  const inpStok = document.getElementById("inpStok");
  const inpKapasitas = document.getElementById("inpKapasitas");

  function applyKategoriUI(v) {
    if (v === "Barang") {
      wrapStok.style.display = "";
      wrapKapasitas.style.display = "none";
      inpStok.required = true;
      inpKapasitas.required = false;
      inpKapasitas.value = "";
    } else if (v === "Ruangan") {
      wrapStok.style.display = "none";
      wrapKapasitas.style.display = "";
      inpStok.required = false;
      inpKapasitas.required = true;
      inpStok.value = "";
    } else {
      wrapStok.style.display = "none";
      wrapKapasitas.style.display = "none";
      inpStok.required = false;
      inpKapasitas.required = false;
      inpStok.value = "";
      inpKapasitas.value = "";
    }
  }

  if (menuKategori && inpKategori) {
    menuKategori.querySelectorAll(".dropdown-item").forEach((item) => {
      item.addEventListener("click", (e) => {
        e.preventDefault();
        menuKategori.querySelectorAll(".dropdown-item")
          .forEach((i) => i.classList.remove("active"));

        item.classList.add("active");
        const v = item.getAttribute("data-value") || "";
        btnKategori.textContent = item.textContent.trim();
        inpKategori.value = v;
        applyKategoriUI(v);
      });
    });
  }

  // Validasi & submit demo
  (function () {
    const form = document.getElementById("formTambah");
    if (!form) return;

    form.addEventListener("submit", function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      } else {
        event.preventDefault();
        document.getElementById("alertSukses").classList.remove("d-none");
        window.scrollTo({ top: 0, behavior: "smooth" });
      }
      form.classList.add("was-validated");
    }, false);
  })();
</script>
@endpush
