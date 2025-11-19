/* ================================
    FILE : login.js (FINAL VERSION)
   ================================ */

document.addEventListener("DOMContentLoaded", () => {

    const loginForm = document.getElementById("loginForm");
    const loginRoleInput = document.getElementById("loginRole");
    const loginSubtitle = document.getElementById("login-subtitle");

    /* ---------------------------------
       1. AMBIL ROLE DARI URL
       --------------------------------- */
    const params = new URLSearchParams(window.location.search);
    const role = params.get("role");

    // Jika role tidak diberikan, kembali ke halaman utama
    if (!role) {
        alert("Anda harus memilih role terlebih dahulu.");
        window.location.href = "index.html";
        return;
    }

    /* ---------------------------------
       2. SET SUBTITLE LOGIN
       --------------------------------- */
    if (role === "pengelola") {
        loginSubtitle.textContent = "Masuk sebagai Pengelola untuk melanjutkan.";
    } else if (role === "pengurus") {
        loginSubtitle.textContent = "Masuk sebagai Pengurus untuk mengelola peminjaman fasilitas.";
    } else {
        loginSubtitle.textContent = "Role tidak dikenal.";
    }

    loginRoleInput.value = role;


    /* ---------------------------------
       3. FORM LOGIN
       --------------------------------- */
    loginForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value;

        /* ===================================
           ======== LOGIN PENGELOLA ==========
           =================================== */
        if (role === "pengelola") {

            if (username === "pengelola" && password === "admin123") {

                // Simpan session ke localStorage
                localStorage.setItem("msuUser", JSON.stringify({
                    username: "pengelola",
                    role: "pengelola"
                }));

                alert("Login berhasil!");
                window.location.href = "beranda.html";
                return;
            }

            alert("Username atau password salah!");
            return;
        }


        /* ===================================
           ========= LOGIN PENGURUS ===========
           =================================== */
        if (role === "pengurus") {

            if (username === "pengurus" && password === "pengurus123") {

                // Simpan session login
                localStorage.setItem("msuUser", JSON.stringify({
                    username: "pengurus",
                    role: "pengurus"
                }));

                alert("Login berhasil!");
                window.location.href = "dashboard.html";
                return;
            }

            alert("Username atau password salah!");
            return;
        }


        /* ---------------------------------
           4. ROLE INVALID
           --------------------------------- */
        alert("Role tidak valid.");
    });

});
