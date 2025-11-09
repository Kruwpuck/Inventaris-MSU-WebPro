# KASUS 1
1. 
Sensing Layer
Sensor kelembapan tanah dan suhu udara (misalnya DHT11 atau LM35) mendeteksi kondisi lingkungan secara real-time. Data yang diperoleh berupa nilai kelembapan (%) dan suhu (°C).
2. 
Network Layer
Data dari sensor dikirim ke gateway (misalnya NodeMCU atau Raspberry Pi) dan diteruskan ke cloud server melalui jaringan internet menggunakan protokol seperti MQTT atau HTTP.
3. 
Service Layer
Server menyimpan dan memproses data. Sistem menggunakan algoritma analisis atau machine learning sederhana untuk menentukan kondisi lahan dan memberikan rekomendasi waktu penyiraman yang optimal.
4. 
Application Layer
Petani mengakses hasil analisis melalui aplikasi di ponsel. Aplikasi menampilkan data kelembapan dan suhu secara real-time serta memberikan notifikasi kapan waktu terbaik untuk menyiram tanaman.

# KASUS 2
1. 
- Sensing layer = sensor detak jantung dan kadar oksigen pada gelang pintar. Teknologi: sensor optik, microcontroller.
- Network layer = mengirim data via Bluetooth/Wi-Fi ke smartphone, lalu ke cloud server menggunakan MQTT protocol.
- Service layer = cloud platform untuk penyimpanan dan analisis data dengan machine learning.
- Application layer = aplikasi mobile/web untuk dokter dan keluarga yang menampilkan hasil monitoring dan notifikasi.
2. 
- Nilai inovasi = monitoring real-time, deteksi dini penyakit, dan akses layanan medis jarak jauh.
- Digital transformation = pasien menjadi lebih mandiri mengontrol kesehatan, dokter mendapat data lebih akurat dan cepat untuk diagnosis.
3. 
- Risiko: kebocoran data medis, manipulasi data, gangguan layanan cloud.
- Mitigasi
> Confidentiality: enkripsi data.
> Integrity: validasi data & digital signature.
> Availability: backup server & sistem redundansi.
> Authenticity: otentikasi dua faktor.
> Authority: pengaturan hak akses pengguna.
4. 
- Positif: Akses layanan kesehatan meningkat, terutama di daerah terpencil.
- Negatif: Potensi kesenjangan digital (tidak semua orang punya akses), ketergantungan pada sistem otomatis, dan risiko penyalahgunaan data pribadi.
5. 
- Proses bisnis/layanan berubah: Dari pemeriksaan manual ke monitoring digital real-time.
- Nilai tambah bagi pengguna: Pemantauan kesehatan berkelanjutan dan deteksi dini penyakit.
- Peran data dan teknologi: Data real-time dan machine learning menciptakan solusi prediktif untuk pencegahan penyakit dan efisiensi pelayanan medis.

# BAYANGKAN KAMU
Sensing Layer:
Sensor LM35 mendeteksi suhu ruangan dan mengubahnya menjadi data analog (nilai tegangan) yang merepresentasikan suhu dalam derajat Celcius.

Network Layer:
Modul ESP8266 mengonversi data analog dari LM35 menjadi digital, lalu mengirimkannya ke cloud server melalui koneksi Wi-Fi menggunakan protokol HTTP atau MQTT.

Service Layer:
Server menerima dan menyimpan data suhu dari sensor. Sistem kemudian mengolah data untuk menampilkan tren suhu atau memberikan peringatan jika suhu melebihi batas tertentu.

Application Layer:
Aplikasi pada smartphone menampilkan hasil pengukuran suhu secara real-time dalam bentuk grafik atau angka, sehingga pengguna dapat memantau kondisi ruangan kapan pun.

# URAIKAN 4 LAPISAN
Sensing Layer
Lapisan ini berfungsi untuk mengumpulkan data dari lingkungan menggunakan sensor atau aktuator. Misalnya sensor LM35 untuk suhu, sensor PIR untuk gerakan, atau sensor kelembapan tanah. Data analog dari sensor diubah menjadi data digital untuk diproses lebih lanjut.

Network Layer
Berperan dalam mengirimkan data dari perangkat sensor ke server atau cloud melalui jaringan komunikasi. Dapat menggunakan koneksi kabel (LAN) maupun nirkabel (Wi-Fi, Bluetooth, MQTT, HTTP).

Service Layer
Lapisan ini mengelola dan memproses data yang diterima dari jaringan. Data disimpan, dianalisis, dan diolah untuk menghasilkan informasi yang berguna, seperti notifikasi atau laporan kondisi tertentu.

Application Layer
Lapisan paling atas yang berinteraksi langsung dengan pengguna. Data hasil analisis ditampilkan melalui aplikasi web atau mobile, sehingga pengguna dapat memantau, mengontrol, atau mengambil keputusan berdasarkan informasi dari sistem IoT.

# JELASKAN APA YG DIMAKSUD IOT
Pengertian IoT (Internet of Things):
Internet of Things adalah konsep di mana berbagai perangkat fisik saling terhubung melalui internet untuk mengumpulkan, mengirim, dan bertukar data secara otomatis tanpa memerlukan interaksi manusia secara langsung.

Tiga karakteristik utama IoT yang membedakannya dari sistem komputer konvensional:

Konektivitas: Perangkat saling terhubung melalui jaringan internet untuk berkomunikasi dan bertukar data secara real-time.

Sensor dan Otomatisasi: IoT menggunakan sensor untuk mendeteksi kondisi lingkungan dan dapat melakukan tindakan otomatis berdasarkan data yang dikumpulkan.

Analisis Data dan Kecerdasan: Data dari berbagai sumber dianalisis menggunakan teknologi seperti AI dan machine learning untuk menghasilkan informasi dan keputusan yang lebih cerdas.

# PERBEDAAN M2M & IOT
M2M
- Autonomous device
- Communicating with other autonomous device
- May communicate over non-IP based channel
IoT
- May incorporate some M2M nodes
- Aggregates data ta an edge gateway
- Serve the entry point onto the Internet

Konektivitas:

M2M menggunakan koneksi langsung antar perangkat dengan protokol non-IP (misalnya SMS, radio, atau jaringan seluler tertutup).

IoT menggunakan koneksi berbasis IP dan internet, memungkinkan komunikasi melalui jaringan global dan cloud.

Peran manusia:

M2M lebih banyak bersifat otomatis dan terbatas pada interaksi antar mesin tanpa campur tangan manusia.

IoT melibatkan manusia sebagai pengguna yang dapat memantau, mengontrol, dan menganalisis data melalui aplikasi atau dashboard.

Arsitektur sistem:

M2M bersifat sistem tertutup dan berdiri sendiri (lokal).

IoT memiliki arsitektur terbuka dan terhubung melalui gateway ke internet serta dapat diintegrasikan dengan layanan lain di cloud.

UART parameter
1. Number of data bit
2. parity bit
3. stop bit
4. baud rate = tergantung panjang kabel, klo pendek br tinggi dan sebaliknya, biasanya panjang 8-10 bit
i2c half, lines 
blutut pake uart