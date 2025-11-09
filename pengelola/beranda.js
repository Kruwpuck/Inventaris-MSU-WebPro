// =============== UTIL ===============
function getEls() {
	const btnKategori = document.getElementById("kategoriBtn");
	const gridBarang = document.getElementById("gridBarang");
	const gridFasil = document.getElementById("gridFasilitas");
	const input = document.getElementById("quickSearch");
	const form = input ? input.closest("form") : null;
	return { btnKategori, gridBarang, gridFasil, input, form };
}

function getActiveGrid(grids) {
	if (grids.gridFasil && !grids.gridFasil.classList.contains("d-none"))
		return grids.gridFasil;
	return grids.gridBarang;
}

function normalize(str) {
	return (str || "").toLowerCase().trim();
}

// Sembunyikan/lihat kolom card
function setCardColumnDisplay(card, show) {
	const col =
		card.closest(".col-12, .col-sm-6, .col-md-4, .col-lg-3") ||
		card.parentElement;
	if (col) col.style.display = show ? "" : "none";
}

// =============== GRID SWITCH ===============
function setActiveGrid(type, grids, keepQuery = true) {
	const isBarang = type === "barang";
	if (grids.gridBarang) grids.gridBarang.classList.toggle("d-none", !isBarang);
	if (grids.gridFasil) grids.gridFasil.classList.toggle("d-none", isBarang);

	if (grids.btnKategori) {
		grids.btnKategori.textContent = isBarang ? "Barang" : "Fasilitas";
	}

	if (keepQuery && grids.input) {
		filterCards(grids.input.value, grids);
	}
}

// =============== FILTER ===============
function filterCards(query, grids) {
	const grid = getActiveGrid(grids);
	if (!grid) return;

	const q = normalize(query);
	const cards = grid.querySelectorAll(".card");

	cards.forEach((card) => {
		const title = card.querySelector(".card-title")?.innerText || "";
		const desc = card.querySelector(".card-text")?.innerText || "";
		const blob = title + " " + desc + " " + card.innerText;
		const match = normalize(blob).includes(q);
		setCardColumnDisplay(card, q === "" ? true : match);
	});
}

// =============== BOOT ===============
(function initBeranda() {
	const els = getEls();
	if (!els.input) return;

	// Submit search
	if (els.form) {
		els.form.addEventListener("submit", (e) => {
			e.preventDefault();
			filterCards(els.input.value, els);
		});
	}

	// Live search saat mengetik
	els.input.addEventListener("input", () => filterCards(els.input.value, els));

	// Dropdown kategori: ganti grid
	if (els.btnKategori) {
		const menu = els.btnKategori.parentElement?.querySelector(".dropdown-menu");
		if (menu) {
			menu.querySelectorAll("[data-switch]").forEach((item) => {
				item.addEventListener("click", (e) => {
					e.preventDefault();
					const type = item.getAttribute("data-switch");
					setActiveGrid(type, els, true);
				});
			});
		}
	}

	filterCards("", els);

	/*
	 * ---
	 * BARU: LOGIKA UNTUK MODAL EDIT STOK/STATUS
	 * ---
	 */

	// 1. Ambil elemen-elemen modal
	const editModalEl = document.getElementById("editModal");
	if (editModalEl) {
		const editModal = new bootstrap.Modal(editModalEl);
		const editForm = document.getElementById("editForm");
		const editItemId = document.getElementById("editItemId");
		const editNamaItem = document.getElementById("editNamaItem");
		const editDeskripsiItem = document.getElementById("editDeskripsiItem");
		const editFormGroupBarang = document.getElementById("editFormGroupBarang");
		const editStokInput = document.getElementById("editStokInput");
		const editFormGroupFasilitas = document.getElementById(
			"editFormGroupFasilitas"
		);
		const editStatusSelect = document.getElementById("editStatusSelect");

		// 2. Saat modal akan ditampilkan (event 'show.bs.modal')
		editModalEl.addEventListener("show.bs.modal", (event) => {
			// Dapatkan tombol 'Edit' yang di-klik
			const button = event.relatedTarget;
			
			// Ambil semua data 'data-item-*' dari tombol
			const itemId = button.getAttribute("data-item-id");
			const itemTipe = button.getAttribute("data-item-tipe");
			const itemNama = button.getAttribute("data-item-nama");
			const itemDeskripsi = button.getAttribute("data-item-deskripsi");

			// Masukkan data ke form modal
			editItemId.value = itemId;
			editNamaItem.value = itemNama;
			editDeskripsiItem.value = itemDeskripsi;

			// Logika untuk menampilkan form yang sesuai (Barang atau Fasilitas)
			if (itemTipe === "barang") {
				// Tampilkan form stok barang, sembunyikan form status fasilitas
				editFormGroupBarang.style.display = "block";
				editFormGroupFasilitas.style.display = "none";
				
				// Isi nilai stok saat ini
				const itemStok = button.getAttribute("data-item-stok");
				editStokInput.value = itemStok;

			} else if (itemTipe === "fasilitas") {
				// Sembunyikan form stok barang, tampilkan form status fasilitas
				editFormGroupBarang.style.display = "none";
				editFormGroupFasilitas.style.display = "block";

				// Isi nilai status saat ini
				const itemStatus = button.getAttribute("data-item-status");
				editStatusSelect.value = itemStatus;
			}
		});

		// 3. Saat form di modal di-submit
		editForm.addEventListener("submit", (event) => {
			event.preventDefault(); // Mencegah halaman reload

			// Ambil data dari form
			const itemId = editItemId.value;
			const newDeskripsi = editDeskripsiItem.value;

			// Temukan card di halaman utama berdasarkan ID
			const cardToUpdate = document.getElementById(itemId);
			if (!cardToUpdate) return;

			// Perbarui deskripsi di card
			cardToUpdate.querySelector(".card-text").innerText = newDeskripsi;
			
			// Ambil tombol edit
			const editButton = cardToUpdate.querySelector(".btn-edit");

			// Cek apakah ini barang atau fasilitas dari form group yang terlihat
			if (editFormGroupBarang.style.display === "block") {
				// Ini BARANG, update stok
				const newStok = editStokInput.value;
				
				// Tentukan label unit (akun atau unit)
				const unitLabel = (editNamaItem.value.includes("Zoom")) ? "akun" : "unit";
				
				cardToUpdate.querySelector(".item-stok b").innerText = `${newStok} ${unitLabel}`;
				
				// Update juga data-attribute di tombol 'Edit'
				editButton.setAttribute("data-item-stok", newStok);

			} else {
				// Ini FASILITAS, update status
				const newStatus = editStatusSelect.value;
				cardToUpdate.querySelector(".item-stok b").innerText = newStatus;
				
				// Update juga data-attribute di tombol 'Edit'
				editButton.setAttribute("data-item-status", newStatus);
				
				// Tambah/Hapus class 'item-disabled'
				cardToUpdate.classList.toggle("item-disabled", newStatus === "Tidak Tersedia");
			}
			
			// Update data-attribute deskripsi di tombol 'Edit'
			editButton.setAttribute("data-item-deskripsi", newDeskripsi);

			// Tutup modal
			editModal.hide();
		});
	}
})();
