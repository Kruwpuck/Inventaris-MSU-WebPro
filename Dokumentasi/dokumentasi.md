# Pre-Requisites
1. Install Composer 
2. Install XAMPP 8.2.*
3. Install PHP 8.4.*
4. VS Code / IDE lainya
5. Docker Desktop

# Initial Laravel
1. Buka terminal di folder project
2. jalankan perintah berikut di terminal
```
composer global require laravel/installer
```
lalu ikuti ini
![alt text](image.png)
![alt text](image-1.png)
# LANGSUNG JALANIN DARI SINI
7. jalankan perintah berikut di terminal yang sama dengan working directory project
```
mvn spring-boot:run
```
8. buka browser dan akses http://localhost:8080/
9. Login menggunakan username: `user` dan password: yang tersedia di terminal seperti berikut
![alt text](image-1.png)

# JIKA ADA YG DI UBAH IKUTIN INI
1. Build image baru (--build) → kalau kamu ubah Dockerfile atau dependensi (Composer/NPM).
2. Restart container → kalau kamu ubah konfigurasi docker-compose.yml atau environment .env.docker.
3. Auto-reload kode → kalau cuma ubah file Laravel (PHP, Blade, Controller, dsb), Docker bisa langsung nyadar karena kita pakai bind mount (- ./:/var/www).
| Perubahan yang Kamu Lakukan                            | Harus Jalankan Apa                                                                  |
| ------------------------------------------------------ | ----------------------------------------------------------------------------------- |
| 🧑‍💻 Ubah file Laravel (controller, view, route, dll) | **Cukup reload browser saja** 🚀                                                    |
| 🎨 Ubah CSS/JS (Vite)                                  | Jalankan `npm run dev` di container `vite` (atau biarin service `vite` tetap nyala) |
| ⚙️ Ubah file `.env.docker`                             | `docker compose restart app`                                                        |
| 🧩 Ubah `Dockerfile`                                   | `docker compose up -d --build`                                                      |
| 🧱 Ubah `docker-compose.yml`                           | `docker compose up -d`                                                              |
| 🗄️ Tambah migration baru                              | `docker exec -it inventori-msu-app php artisan migrate`                             |
| 🧰 Ubah dependency Composer                            | `docker exec -it inventori-msu-app composer install`                                |
| 🪄 Ubah dependency NPM                                 | `docker exec -it inventori-msu-vite npm install`                                    |
| 🪣 Reset database                                      | `docker compose down -v && docker compose up -d`                                    |
