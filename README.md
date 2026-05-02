# 📋 AbsensiGPS — Dokumentasi Proyek

> **Sistem Absensi Karyawan Berbasis GPS + Foto Selfie**
> Dibangun dengan Laravel 13 + MySQL + Blade + Tailwind CSS

---

## ⚠️ PERINGATAN PENTING UNTUK AI YANG MELANJUTKAN PROYEK INI

Sebelum menambahkan atau mengubah kode apapun, **WAJIB** ikuti aturan berikut:

1. **Minta file terlebih dahulu** sebelum memberikan solusi. Jangan asumsikan isi file karena bisa berbeda dengan template default Laravel.
2. **Tunjukkan lokasi perubahan secara tepat** — jangan tulis ulang seluruh file. Cukup tampilkan potongan kode yang cukup panjang dan unik, lalu instruksikan untuk menambahkan/mengubah di atas atau bawahnya.
3. **Satu langkah = satu masalah.** Jangan gabung banyak perubahan sekaligus.
4. **Gunakan bahasa Indonesia** yang sederhana dan jelas.
5. **Pelan-pelan dan sabar.** Tunggu konfirmasi user sebelum lanjut ke step berikutnya.

---

## 🗂️ Ringkasan Proyek

| Item | Detail |
|---|---|
| Nama Proyek | AbsensiGPS |
| Framework | Laravel 13.7 |
| Database | MySQL |
| Frontend | Blade Template + Tailwind CSS (via Vite) |
| Icon | Font Awesome 6.5 |
| Interaktif | Alpine.js |
| Notifikasi | SweetAlert2 |
| Grafik | Chart.js |
| Export | Maatwebsite Excel 3.1 |
| Lokasi | Browser Geolocation API (Haversine Formula) |
| Kamera | Native Browser API |
| Peta Interaktif | Leaflet.js (OpenStreetMap, via CDN) |

---

## 📁 Struktur Folder Penting

```
absensi-gps/
├── app/
│   ├── Exports/
│   │   └── AttendanceExport.php         # Export rekap absensi ke Excel
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php       # Login & logout
│   │   │   ├── AdminController.php      # Semua fitur admin
│   │   │   ├── KaryawanController.php   # Absen, dashboard, riwayat karyawan
│   │   │   └── ProfileController.php   # Edit profil & ganti password
│   │   └── Middleware/
│   │       └── RoleMiddleware.php       # Proteksi route berdasarkan role
│   └── Models/
│       ├── User.php                     # Model user (admin & karyawan)
│       ├── Office.php                   # Model kantor
│       └── Attendance.php              # Model absensi
├── database/
│   ├── migrations/                      # Semua migration tabel
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php               # Data awal admin & karyawan demo
│       └── OfficeSeeder.php             # Data awal kantor
├── resources/
│   ├── css/
│   │   └── app.css                      # Hanya berisi: @import "tailwindcss";
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php            # Layout untuk halaman karyawan
│       │   └── admin.blade.php          # Layout sidebar untuk admin
│       ├── auth/
│       │   └── login.blade.php          # Halaman login
│       ├── admin/
│       │   ├── dashboard.blade.php      # Dashboard admin + grafik
│       │   ├── karyawan.blade.php       # Kelola karyawan (CRUD)
│       │   ├── rekap.blade.php          # Rekap absensi + export Excel
│       │   └── settings.blade.php       # Setting koordinat kantor + peta interaktif
│       ├── karyawan/
│       │   ├── dashboard.blade.php      # Dashboard absen (kamera + GPS)
│       │   └── riwayat.blade.php        # Riwayat absensi pribadi
│       └── profile/
│           └── index.blade.php          # Halaman profil (edit + ganti password)
├── routes/
│   └── web.php                          # Semua route aplikasi
├── bootstrap/
│   └── app.php                          # Registrasi middleware RoleMiddleware
└── vite.config.js                       # Konfigurasi Vite + Tailwind
```

---

## 🗄️ Struktur Database

### Tabel `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | Auto increment |
| name | varchar | Nama lengkap |
| email | varchar unique | Email login |
| role | enum | `admin` atau `karyawan` |
| password | varchar | Bcrypt hash |
| photo | varchar nullable | Path foto profil |
| phone | varchar nullable | Nomor HP |
| department | varchar nullable | Departemen/divisi |
| email_verified_at | timestamp nullable | Verifikasi email |
| remember_token | varchar nullable | Remember me token |
| created_at / updated_at | timestamp | Timestamps |

### Tabel `offices`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | Auto increment |
| name | varchar | Nama kantor |
| latitude | decimal(10,8) | Koordinat latitude kantor |
| longitude | decimal(11,8) | Koordinat longitude kantor |
| radius | integer | Radius absensi dalam meter (default: 100) |
| created_at / updated_at | timestamp | Timestamps |

### Tabel `attendances`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | Auto increment |
| user_id | FK → users | Karyawan yang absen |
| office_id | FK → offices | Kantor referensi |
| type | enum | `masuk` atau `pulang` |
| latitude | decimal(10,8) | Koordinat GPS saat absen |
| longitude | decimal(11,8) | Koordinat GPS saat absen |
| photo | varchar | Path foto selfie (storage/public) |
| status | enum | `tepat_waktu` atau `terlambat` |
| checked_at | timestamp | Waktu absen dilakukan |
| created_at / updated_at | timestamp | Timestamps |

---

## 👥 Akun Default (Seeder)

| Role | Email | Password |
|---|---|---|
| Admin | admin@absensi.com | password |
| Karyawan Demo | karyawan@absensi.com | password |

---

## 🔐 Sistem Route & Middleware

```
/                          → redirect ke /login
/login                     → AuthController@showLogin
POST /login                → AuthController@login
POST /logout               → AuthController@logout

/profile                   → ProfileController@index          [auth]
POST /profile/update       → ProfileController@update         [auth]
POST /profile/password     → ProfileController@updatePassword [auth]

/admin/dashboard           → AdminController@dashboard        [auth, role:admin]
/admin/karyawan            → AdminController@karyawan         [auth, role:admin]
POST /admin/karyawan       → AdminController@karyawanStore    [auth, role:admin]
PUT /admin/karyawan/{id}   → AdminController@karyawanUpdate   [auth, role:admin]
DELETE /admin/karyawan/{id}→ AdminController@karyawanDestroy  [auth, role:admin]
/admin/rekap               → AdminController@rekap            [auth, role:admin]
/admin/rekap/export        → AdminController@export           [auth, role:admin]
/admin/settings            → AdminController@settings         [auth, role:admin]
PUT /admin/settings        → AdminController@settingsUpdate   [auth, role:admin]

/karyawan/dashboard        → KaryawanController@dashboard     [auth, role:karyawan]
POST /karyawan/absen       → KaryawanController@absen         [auth, role:karyawan]
/karyawan/riwayat          → KaryawanController@riwayat       [auth, role:karyawan]
```

---

## ✅ Fitur yang Sudah Selesai

### Autentikasi
- [x] Login dengan email & password
- [x] Logout
- [x] Middleware proteksi role (admin/karyawan)
- [x] Redirect otomatis ke dashboard sesuai role

### Fitur Karyawan
- [x] Dashboard absensi hari ini
- [x] Absen masuk dengan GPS + foto selfie
- [x] Absen pulang dengan GPS + foto selfie
- [x] Validasi lokasi (harus dalam radius kantor)
- [x] Validasi jam masuk (07.00–08.00 = tepat waktu, lewat = terlambat, sebelum 07.00 = ditolak)
- [x] Riwayat absensi pribadi (pagination)
- [x] Edit profil (nama, email, no HP, departemen, foto profil)
- [x] Ganti password

### Fitur Admin
- [x] Dashboard dengan statistik (total karyawan, hadir hari ini, terlambat)
- [x] Grafik absensi bulanan (Chart.js)
- [x] Kelola karyawan (tambah, edit, hapus) via SweetAlert2
- [x] Tambah karyawan langsung dari halaman admin (nama, email, password) — karyawan bisa langsung login
- [x] Rekap absensi semua karyawan (filter bulan & tahun)
- [x] Lihat foto selfie absensi
- [x] Export rekap absensi ke Excel
- [x] Setting koordinat & radius kantor
- [x] Deteksi lokasi otomatis untuk setting kantor
- [x] Atur lokasi & radius kantor lewat peta interaktif (Leaflet.js + OpenStreetMap) — geser pin, lihat lingkaran radius, simpan ke form

---

## 🚧 Fitur yang Belum Dibuat (Rencana Selanjutnya)

- [ ] Pengajuan izin/sakit oleh karyawan
- [ ] Approve/reject izin oleh admin
- [ ] Notifikasi in-app (badge izin masuk)
- [ ] Rekap per karyawan di admin (klik nama → histori lengkap)
- [ ] Manajemen hari libur nasional
- [ ] Dashboard karyawan — statistik absensi pribadi per bulan

---

## ⚙️ Cara Menjalankan Proyek

```bash
# 1. Install dependencies
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Konfigurasi database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_gps
DB_USERNAME=root
DB_PASSWORD=

# 4. Jalankan migration + seeder
php artisan migrate:fresh --seed

# 5. Buat storage link
php artisan storage:link

# 6. Jalankan server (2 terminal terpisah)
npm run dev
php artisan serve
```

Buka browser: `http://localhost:8000`

---

## 📦 Library yang Digunakan

| Library | Versi | Kegunaan |
|---|---|---|
| laravel/framework | 13.7 | Framework utama |
| maatwebsite/excel | 3.1 | Export Excel |
| tailwindcss | latest | CSS framework |
| @tailwindcss/vite | latest | Integrasi Tailwind + Vite |
| Font Awesome | 6.5 (CDN) | Icon |
| Alpine.js | 3.x (CDN) | Interaktivitas frontend |
| SweetAlert2 | 11 (CDN) | Popup notifikasi |
| Chart.js | latest (CDN) | Grafik dashboard admin |
| Leaflet.js | 1.9.4 (CDN) | Peta interaktif pengaturan kantor |

---

## 📌 Catatan Teknis

- **Foto selfie** disimpan di `storage/app/public/absen/` dan diakses via `storage/absen/`
- **Foto profil** disimpan di `storage/app/public/profiles/`
- **Kalkulasi jarak** menggunakan **Haversine Formula** di `KaryawanController@hitungJarak`
- **Validasi jam** menggunakan `now()->hour` (pastikan timezone di `.env` sudah benar: `APP_TIMEZONE=Asia/Jakarta`)
- **Role** disimpan di kolom `role` tabel `users` dengan nilai `admin` atau `karyawan`
- Semua foto dikirim via **base64** dari browser ke server, lalu di-decode dan disimpan
- **Form tambah karyawan** di SweetAlert2 menggunakan form hidden terpisah (`#formTambahHidden`) karena SweetAlert2 mengkloning HTML sehingga `getElementById` pada form di dalam popup tidak bekerja reliabel
- **Peta pengaturan kantor** menggunakan Leaflet.js (tanpa API key) dengan tile dari OpenStreetMap. Marker bisa digeser atau klik lokasi di peta. Radius diatur via slider dan divisualisasikan sebagai lingkaran biru. Klik "Simpan dari Peta" untuk mengisi otomatis field latitude, longitude, dan radius di form — lalu tetap perlu klik "Simpan Pengaturan" untuk menyimpan ke database.

---

*Dokumentasi ini dibuat pada: 2 Mei 2026*
*Step terakhir yang dikerjakan: Peta interaktif pengaturan kantor (Leaflet.js)*