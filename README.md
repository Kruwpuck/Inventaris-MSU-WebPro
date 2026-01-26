# Sistem Informasi Inventaris & Peminjaman MSU

![Preview Aplikasi](inventory%20msu.png)

## 📖 Deskripsi

**Sistem Informasi Inventaris & Peminjaman MSU** adalah aplikasi berbasis web yang dikembangkan untuk mendigitalisasi proses manajemen aset dan fasilitas di lingkungan **Masjid Syamsul 'Ulum**. 

Aplikasi ini dirancang untuk menggantikan pencatatan manual, mempermudah pelacakan kondisi inventaris, serta mengelola jadwal peminjaman ruangan dan barang secara efisien, transparan, dan terstruktur.

## 🚀 Fitur Utama

* **Manajemen Inventaris (Asset Management):** * Mencatat data barang masuk dan kondisi barang.
    * Pembaruan status ketersediaan aset secara *real-time*.
* **Sistem Peminjaman (Booking & Loan):** * Pengajuan peminjaman barang atau ruangan oleh pengguna.
    * Validasi tanggal agar tidak bentrok.
    * Persetujuan (Approval) oleh Admin.
* **Manajemen Pengguna (User Roles):** * **Admin:** Mengelola seluruh data aset dan persetujuan.
    * **User:** Melihat ketersediaan dan mengajukan peminjaman.
* **Riwayat & Laporan:** Memantau sejarah peminjaman dan pengembalian aset.

## 🏗️ Arsitektur & Teknologi

Aplikasi ini dibangun menggunakan pola arsitektur **MVC (Model-View-Controller)** untuk memisahkan logika bisnis, antarmuka pengguna, dan data, sehingga kode lebih rapi dan mudah dikembangkan.

### Tech Stack:
| Komponen | Teknologi |
| :--- | :--- |
| **Framework Backend** | [Laravel](https://laravel.com/) (PHP) |
| **Database** | MySQL / MariaDB |
| **Frontend** | Blade Templates, Bootstrap / Tailwind CSS |
| **Server Environment** | XAMPP / Laragon (Apache/Nginx) |
