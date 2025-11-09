/* File: login.js */

document.addEventListener("DOMContentLoaded", () => {
	const loginForm = document.getElementById("loginForm");
	const loginRoleInput = document.getElementById("loginRole");
	const loginSubtitle = document.getElementById("login-subtitle");

	// 1. Baca 'role' dari URL
	const params = new URLSearchParams(window.location.search);
	const role = params.get("role");

	if (role) {
		// 2. Set role di input tersembunyi
		loginRoleInput.value = role;
		
		// 3. Ubah judul agar lebih jelas
		const roleName = role.charAt(0).toUpperCase() + role.slice(1);
		loginSubtitle.textContent = `Masuk sebagai ${roleName} untuk melanjutkan.`;
		
		// Jika link "Daftar" ada, tambahkan role ke sana juga
		const registerLink = document.querySelector('a[href="register.html"]');
		if(registerLink) {
			registerLink.href = `register.html?role=${role}`;
		}
		
	} else {
		// Jika tidak ada role, paksa kembali ke halaman pilih role
		alert("Anda harus memilih role terlebih dahulu.");
		window.location.href = "index.html";
	}

	// 4. Tangani submit form
	loginForm.addEventListener("submit", (e) => {
		e.preventDefault(); // Mencegah reload halaman

		const username = document.getElementById("username").value;
		const password = document.getElementById("password").value;
		const loginRole = loginRoleInput.value;

		// --- SIMULASI LOGIN ---
		
		if (loginRole === "pengelola") {
			if (username === "pengelola" && password === "admin123") {
				alert("Login Pengelola berhasil!");
				// Arahkan ke beranda pengelola
				window.location.href = "beranda.html"; 
			} else {
				alert("Username atau password Pengelola salah!");
			}
		} else if (loginRole === "pengurus") {
			if (username === "pengurus" && password === "admin123") {
				alert("Login Pengurus berhasil!");
				// Ganti ini ke halaman pengurus jika sudah ada
				// window.location.href = "pengurus.html"; 
				alert("Halaman pengurus belum dibuat.");
			} else {
				alert("Username atau password Pengurus salah!");
			}
		} else {
			alert("Role tidak valid.");
		}
	});
});