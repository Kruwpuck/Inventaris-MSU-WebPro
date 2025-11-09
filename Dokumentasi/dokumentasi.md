# 🧭 Inventory MSU — Laravel Setup Guide (Docker & XAMPP)

Panduan lengkap untuk menjalankan proyek **Inventory MSU** berbasis **Laravel 12.x**
baik menggunakan **Docker Desktop** maupun **XAMPP (tanpa Docker)** di Windows.

---

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-ff2d20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12.x Badge"/>
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.4 Badge"/>
  <img src="https://img.shields.io/badge/Docker-Desktop-blue?style=for-the-badge&logo=docker&logoColor=white" alt="Docker Desktop Badge"/>
  <img src="https://img-shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL Badge"/>
</p>

---

## 🧩 Prasyarat Instalasi

Pastikan software berikut sudah terpasang di sistem kamu:

| Komponen | Keterangan |
|-----------|------------|
| 🧰 **Composer** | Dependency manager untuk PHP |
| 🐘 **PHP 8.4.\*** | Bisa dari XAMPP atau standalone |
| 🐬 **XAMPP 8.2.\*** | Untuk mode tanpa Docker |
| 🐳 **Docker Desktop** | Untuk mode containerized |
| 🖥️ **VS Code / IDE** | Untuk development |

---

## ⚙️ 1️⃣ Setup Awal Laravel

Langkah-langkah ini wajib dilakukan terlepas dari metode deployment (Docker atau XAMPP) yang kamu pilih.

1.  Buka **terminal** di root folder project.
2.  Jalankan perintah berikut untuk menginstal *laravel installer global*:

    ```bash
    composer global require laravel/installer
    ```

3.  Setelah proyek di-clone/didapatkan, jalankan:

    ```bash
    composer install
    ```

    > **Catatan:** Kamu mungkin perlu menginstruksi Laravel untuk memilih starter kit (seperti Breeze atau Jetstream) dan testing framework (seperti Pest atau PHPUnit) jika ini adalah proyek baru.

Setelah instalasi *dependency* selesai, Laravel siap digunakan 🎉

---

## 🐳 2️⃣ Menjalankan Menggunakan Docker

Mode ini menggunakan **Docker Desktop** untuk menjalankan aplikasi Laravel (PHP), Database (MySQL), dan server aset (Vite) dalam container terpisah.

### 🔧 Persiapan Awal

Pastikan file konfigurasi Docker berikut sudah ada di root project:

* **`Dockerfile`**
* **`docker-compose.yml`**
* **`nginx.conf`**
* **`.env.docker`** (File environment khusus Docker)

### 🚀 Langkah Menjalankan

1.  Jalankan **Docker Desktop** terlebih dahulu.
2.  **Build** dan jalankan semua container (perintah ini hanya perlu dijalankan pertama kali atau jika ada perubahan pada Dockerfile/compose):

    ```bash
    docker compose up -d --build
    ```

3.  Generate **APP\_KEY** menggunakan environment file `.env.docker`:

    ```bash
    docker exec -it inventori-msu-app php artisan key:generate --env=.env.docker
    ```

4.  Jalankan **migrasi database** ke dalam container MySQL:

    ```bash
    docker exec -it inventori-msu-app php artisan migrate --env=.env.docker
    ```

5.  Jalankan **Vite** untuk mode pengembangan (agar asset CSS/JS bisa di-reload otomatis):

    ```bash
    docker exec -it inventori-msu-vite npm run dev
    ```

### 🌐 Akses Aplikasi

Akses melalui browser:

👉 **`http://localhost:8080`**

### ⚙️ Workflow Cepat (Perintah Harian)

| Tujuan | Perintah |
| :--- | :--- |
| 🔥 Menyalakan semua container | `docker compose up -d` |
| 🧱 Migrasi database | `docker exec -it inventori-msu-app php artisan migrate` |
| 🧩 Jalankan Vite Dev Server | `docker exec -it inventori-msu-vite npm run dev` |
| 🧯 Hentikan container | `docker compose down` |
| 🧹 Reset database (dari nol) | `docker compose down -v && docker compose up -d` |

### 🧠 Jika Ada Perubahan

| Perubahan yang Kamu Lakukan | Jalankan Perintah |
| :--- | :--- |
| 🧑‍💻 Ubah file Laravel (controller, view, route) | Cukup **reload browser** saja 🚀 |
| 🎨 Ubah CSS/JS (Vite) | Pastikan `npm run dev` berjalan |
| ⚙️ Ubah file `.env.docker` | `docker compose restart app` |
| 🧩 Ubah `Dockerfile` | `docker compose up -d --build` |
| 🧱 Ubah `docker-compose.yml` | `docker compose up -d` |
| 🗄️ Tambah migration baru | `docker exec -it inventori-msu-app php artisan migrate` |
| 🧰 Ubah dependency Composer | `docker exec -it inventori-msu-app composer install` |
| 🪄 Ubah dependency NPM | `docker exec -it inventori-msu-vite npm install` |

### 💡 Tips Docker

Gunakan `docker exec` untuk menjalankan Artisan Command atau Composer/NPM langsung di dalam container:

* **Jalankan Artisan Command:**
    ```bash
    docker exec -it inventori-msu-app php artisan route:list
    docker exec -it inventori-msu-app php artisan make:model Item -mcr
    ```
* **Install/update dependency Composer:**
    ```bash
    docker exec -it inventori-msu-app composer install
    ```
* **Build asset untuk production:**
    ```bash
    docker exec -it inventori-msu-vite npm run build
    ```

---

## ⚙️ 3️⃣ Menjalankan Tanpa Docker (XAMPP)

Mode ini menggunakan lingkungan lokal (PHP, MySQL, Apache) yang disediakan oleh **XAMPP**.

1.  **Jalankan XAMPP**
    Buka XAMPP Control Panel dan klik **Start** pada:
    * ✅ **Apache**
    * ✅ **MySQL**

2.  **Buat Database**
    * Buka **`http://localhost/phpmyadmin`**
    * Klik **New** → buat database baru bernama **`inventori_msu`**

3.  **Konfigurasi `.env`**
    Edit file **`.env`** di root project, pastikan bagian database-nya seperti berikut:

    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=inventori_msu
    DB_USERNAME=root
    DB_PASSWORD=
    SESSION_DRIVER=file
    ```

    > ⚠️ **Penting:** Default XAMPP user untuk MySQL adalah **`root`** tanpa password.

4.  **Jalankan Migration**

    ```bash
    php artisan migrate
    ```

5.  **Jalankan Server Laravel (Development)**

    ```bash
    php artisan serve
    ```

### 🌐 Akses Aplikasi

Akses di browser:

👉 **`http://127.0.0.1:8000`**

### 🧰 Tips Tambahan (Lokal)

* **Generate key** (jika belum):
    ```bash
    php artisan key:generate
    ```
* **Bersihkan cache:**
    ```bash
    php artisan optimize:clear
    ```
* **Jalankan ulang migrasi dari nol:**
    ```bash
    php artisan migrate:fresh --seed
    ```

---

## ⚡ Appendix: Shortcut Commands

Untuk memudahkan penggunaan perintah Docker, Anda dapat menambahkan script berikut ke dalam file **`package.json`** pada bagian `"scripts"`:

```json
"scripts": {
  "up": "docker compose up -d",
  "down": "docker compose down",
  "migrate": "docker exec -it inventori-msu-app php artisan migrate",
  "vite": "docker exec -it inventori-msu-vite npm run dev",
  "build": "docker exec -it inventori-msu-vite npm run build"
}
```

## 🧭 Workflow Cepat (Development)

| Langkah Cepat | Perintah |
|----------------|-----------|
| Nyalakan semua container | `docker compose up -d` |
| Jalankan migrasi | `docker exec -it inventori-msu-app php artisan migrate` |
| Jalankan Vite dev server | `docker exec -it inventori-msu-vite npm run dev` |
| Hentikan semua container | `docker compose down` |
| Reset database | `docker compose down -v && docker compose up -d` |

---

## 🧰 Tips Tambahan

- Jalankan perintah **Artisan** langsung di container:
    
    ```bash
    docker exec -it inventori-msu-app php artisan route:list
    docker exec -it inventori-msu-app php artisan make:model Item -mcr
    ```

- Install atau update dependency **Composer**:
    
    ```bash
    docker exec -it inventori-msu-app composer install
    ```

- Build asset untuk **production**:
    
    ```bash
    docker exec -it inventori-msu-vite npm run build
    ```

---

## 🧾 Credit
Disusun oleh **Habb**  
> Panduan resmi pengembangan dan deployment proyek **Inventory MSU**  
> Laravel + Docker Development Workflow 💚
