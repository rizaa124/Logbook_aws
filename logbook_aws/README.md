# LOGBOOK AWS — Dashboard QC

Dashboard monitoring kualitas data Automatic Weather Station (AWS) berbasis PHP + MySQL.

## Cara Menjalankan di XAMPP

1. **Ekstrak** folder `logbook-aws` ke direktori `htdocs` XAMPP.
   ```
   C:\xampp\htdocs\logbook-aws\
   ```

2. **Import database** menggunakan phpMyAdmin:
   - Buka `http://localhost/phpmyadmin`
   - Buat database baru bernama `db_alertdata`
   - Import file SQL `station_flags.sql` ke database tersebut

3. **Konfigurasi database** di `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');         // kosongkan jika tidak ada password
   define('DB_NAME', 'db_alertdata');
   ```

4. **Akses dashboard** di browser:
   ```
   http://localhost/logbook-aws/
   ```

## Struktur Folder

```
logbook-aws/
├── index.php               # Halaman utama dashboard
├── config/
│   └── database.php        # Konfigurasi koneksi database
├── api/
│   ├── dashboard-summary.php  # Endpoint: statistik ringkasan
│   ├── chart-quality.php      # Endpoint: data grafik kualitas
│   ├── station-table.php      # Endpoint: data tabel station
│   ├── map-data.php           # Endpoint: koordinat peta
│   └── filters.php            # Endpoint: opsi filter
└── README.md
```

## Fitur

- 📊 **6 Stat Card** — Total AWS, Aktif, Bermasalah, Valid%, Invalid%, Missing%
- 📈 **Horizontal Stacked Bar Chart** — Kualitas per parameter sensor (Chart.js)
- 🗺️ **Peta Interaktif** — Lokasi AWS dengan status warna (Leaflet.js)
- 📋 **Tabel DataTables** — Search, pagination, sortable
- 📤 **Export Excel & PDF** — Dengan XLSX.js dan jsPDF
- 🔍 **Filter** — Tanggal, Kota, Tipe Station
- 🌙 **Dark Mode** — Toggle light/dark
- 🔄 **Auto Refresh** — Setiap 30 detik
- 🟢🟡🔴 **Status Otomatis** — Hijau >80%, Kuning 50-80%, Merah <50%

## Teknologi

- PHP (native)
- MySQL / MariaDB
- Bootstrap 5
- Chart.js 4
- DataTables 1.10
- Leaflet.js 1.9
- XLSX.js (export Excel)
- jsPDF + AutoTable (export PDF)
