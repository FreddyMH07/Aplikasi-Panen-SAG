# Sistem Report Panen Sawit Digital

Aplikasi web untuk mengelola dan melaporkan data panen kelapa sawit dengan fitur lengkap dan modern.

## 🌟 Fitur Utama

### 🔐 Sistem Autentikasi
- Login dengan email dan password terenkripsi (hash)
- Session management yang aman
- Multi-user support dengan role berbeda

### 📊 Dashboard Modern
- Sidebar responsif dengan auto-expand on hover
- Dark mode toggle
- Summary metrics real-time
- Chart visualisasi data produksi
- Quick actions untuk akses cepat

### 📈 Report Panen Harian
- Input data panen satu per satu melalui form
- Upload massal via Excel/CSV dengan validasi otomatis
- Filter data berdasarkan tanggal, kebun, dan divisi
- Export ke Excel/CSV atau copy ke clipboard
- Kolom dinamis yang dapat dikustomisasi admin

### 📅 Report Panen Bulanan
- Agregasi otomatis dari data harian
- Summary bulanan dengan metrik lengkap
- Visualisasi tren produksi

### 🧮 Perhitungan Otomatis
- **BJR (Berat Janjang Rata-rata)**: `IF(JJG Panen > 0, Timbang Kebun / JJG Panen, 0)`
- **AKP (Angka Kerapatan Panen)**: `IF(Luas Panen * SPH > 0, JJG Panen / (Luas Panen * SPH), 0)`
- **ACV Prod**: `IF(Alokasi Budget > 0, 100 * Timbang PKS / Alokasi Budget, 0)`
- **Selisih**: `Timbang PKS - Timbang Kebun`
- **Refraksi (%)**: `IF(Timbang Kebun > 0, 100 * Refraksi Kg / Timbang Kebun, 0)`

### 🎨 Conditional Formatting
- **Hijau**: Nilai di atas median atau target (ACV ≥ 100%, BJR > median)
- **Merah**: Nilai di bawah target (ACV < 80%, BJR < 75% median)
- **Kuning/Oranye**: Nilai mendekati threshold waspada
- Pewarnaan dinamis berdasarkan median 30 hari terakhir

### 🗄️ Master Data
- Manajemen data kebun (nama, kode, alamat, luas, SPH)
- Manajemen data divisi per kebun
- Kolom tabel yang dapat dikustomisasi

## 🛠️ Teknologi

- **Backend**: Laravel 12 (PHP 8.3)
- **Frontend**: Tailwind CSS, Alpine.js
- **Database**: SQLite (dapat diganti MySQL/PostgreSQL)
- **Charts**: Chart.js
- **Tables**: DataTables
- **Excel**: Maatwebsite/Laravel-Excel
- **Icons**: Font Awesome

## 📋 Persyaratan Sistem

- PHP 8.3 atau lebih tinggi
- Composer
- SQLite/MySQL/PostgreSQL
- Web server (Apache/Nginx)

## 🚀 Instalasi

1. **Clone atau extract aplikasi**
   ```bash
   cd sistem-panen-sawit
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Setup database**
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan aplikasi**
   ```bash
   php artisan serve
   ```

6. **Akses aplikasi**
   - URL: http://localhost:8000
   - Login dengan akun demo (lihat di halaman login)

## 👥 Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@panensawit.com | admin123 |
| Manager | manager@panensawit.com | manager123 |
| Operator | operator@panensawit.com | operator123 |

## 📁 Struktur Aplikasi

```
sistem-panen-sawit/
├── app/
│   ├── Http/Controllers/     # Controllers
│   ├── Models/              # Models (Kebun, Divisi, PanenHarian, dll)
│   ├── Exports/             # Excel export classes
│   └── Imports/             # Excel import classes
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   └── views/              # Blade templates
│       ├── layouts/        # Layout templates
│       ├── auth/           # Authentication views
│       ├── dashboard/      # Dashboard views
│       ├── panen-harian/   # Daily harvest views
│       └── master/         # Master data views
└── routes/
    └── web.php             # Web routes
```

## 🔧 Konfigurasi

### Database
Edit file `.env` untuk konfigurasi database:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite
```

### Email (Opsional)
Untuk fitur reset password:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

## 📊 Import/Export Data

### Format Excel Import
Kolom yang diperlukan untuk import data panen harian:
- Tanggal Panen (format: dd/mm/yyyy)
- Kebun (nama kebun)
- Divisi (nama divisi)
- Luas Panen (Ha)
- JJG Panen
- Timbang Kebun (Kg)
- Timbang PKS (Kg)
- HK (Tenaga Kerja)
- Refraksi (Kg) - opsional
- Alokasi Budget - opsional

### Export Features
- Export ke Excel (.xlsx)
- Export ke CSV
- Copy data ke clipboard
- Filter data sebelum export

## 🎯 Fitur Lanjutan

### Kolom Dinamis
Admin dapat menambah/mengurangi kolom tabel melalui UI tanpa coding:
1. Masuk ke menu Master Data
2. Pilih "Kelola Kolom Tabel"
3. Tambah/edit/hapus kolom sesuai kebutuhan

### Conditional Formatting
Sistem otomatis memberikan warna pada data berdasarkan:
- Perbandingan dengan median
- Target produksi
- Threshold yang ditentukan

### Responsive Design
- Mobile-friendly interface
- Sidebar yang adaptif
- Tabel responsif dengan scroll horizontal

## 🔒 Keamanan

- Password di-hash menggunakan bcrypt
- CSRF protection
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templating
- Session security

## 🐛 Troubleshooting

### Error "could not find driver"
Install SQLite extension:
```bash
# Ubuntu/Debian
sudo apt install php-sqlite3

# CentOS/RHEL
sudo yum install php-sqlite3
```

### Permission Error
Set permission untuk storage dan cache:
```bash
chmod -R 775 storage bootstrap/cache
```

### Composer Error
Update composer:
```bash
composer self-update
composer update
```

## � Deployment (Railway + SQLite)

### Build & Deploy
Railway menggunakan `railway.json` untuk menjalankan build:
1. Install composer (tanpa dev) & dump autoload
2. Install npm dependencies dan build aset Vite
3. Copy `.env.railway` menjadi `.env`
4. Jalankan migrasi dan seeder (`php artisan migrate --force && php artisan db:seed --force`)

### Seeder CSV Otomatis
Dua file CSV berikut dipakai otomatis saat seeding:
- `Template Import Master Data Panen PT SAG.csv`
- `Template Import Panen Harian PT SAG.csv`

Pastikan file tersebut ikut dipush agar data awal ter-inject. Seeder terkait:
- `MasterDataCsvSeeder`
- `PanenHarianCsvSeeder`

Jika Anda ingin skip injeksi CSV (misal staging kosong), hapus/komentari seeder tersebut dari `DatabaseSeeder`.

### Variabel Environment Minimum (SQLite)
```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:isi_key_generate
APP_URL=https://your-app.railway.app
LOG_CHANNEL=stack

DB_CONNECTION=sqlite
DB_DATABASE=/app/database/database.sqlite

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### Tips Produksi
- Gunakan `php artisan config:cache route:cache view:cache` untuk optimasi.
- Jangan commit file `database.sqlite` di produksi; gunakan migrasi + seeder.
- Perbaharui CSV lalu redeploy untuk refresh data awal.

## 🧪 Test Data Refresh Cepat (Lokal)
```bash
rm -f database/database.sqlite
touch database/database.sqlite
php artisan migrate --seed
```


## �📞 Support

Untuk bantuan teknis atau pertanyaan:
- Email: support@panensawit.com
- Developer: freddymazmur

## 📄 Lisensi

© 2025 freddymazmur - Sistem Report Panen Sawit Digital

---

**Catatan**: Aplikasi ini dirancang khusus untuk industri kelapa sawit dengan perhitungan dan metrik yang sesuai standar industri.
