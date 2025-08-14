# 📊 DOKUMENTASI DATABASE - SISTEM PANEN SAWIT DIGITAL

## 🗃️ Struktur Database

### 1. Tabel `users`
**Fungsi**: Menyimpan data pengguna sistem
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### 2. Tabel `master_data` ⭐ **BARU**
**Fungsi**: Menyimpan data master kebun, divisi, SPH, luas TM, dan budget per periode
```sql
CREATE TABLE master_data (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kebun VARCHAR(64) NOT NULL,
    divisi VARCHAR(64) NOT NULL,
    sph_panen FLOAT DEFAULT 136,
    luas_tm FLOAT DEFAULT 0,
    budget_alokasi FLOAT DEFAULT 0,
    pkk INT DEFAULT 0,
    bulan VARCHAR(16) NOT NULL,
    tahun INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_kebun_divisi_periode (kebun, divisi, tahun, bulan),
    UNIQUE KEY unique_master_data (kebun, divisi, tahun, bulan)
);
```

**Keterangan Kolom**:
- `kebun`: Nama kebun (max 64 karakter)
- `divisi`: Nama divisi (max 64 karakter)
- `sph_panen`: Standar Pokok per Hektar untuk panen
- `luas_tm`: Luas Tanaman Menghasilkan dalam hektar
- `budget_alokasi`: Budget yang dialokasikan untuk periode tersebut
- `pkk`: Pokok Kelapa Sawit (jumlah pohon)
- `bulan`: Nama bulan (January, February, dst)
- `tahun`: Tahun (2020-2050)

### 3. Tabel `panen_harians` ⭐ **STRUKTUR BARU LENGKAP**
**Fungsi**: Menyimpan data panen harian dengan struktur lengkap sesuai spesifikasi
```sql
CREATE TABLE panen_harians (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tanggal_panen DATE NOT NULL,
    bulan VARCHAR(16) NOT NULL,
    tahun INT NOT NULL,
    kebun VARCHAR(64) NOT NULL,
    divisi VARCHAR(64) NOT NULL,
    akp_panen VARCHAR(8) NULL,
    jumlah_tk_panen INT DEFAULT 0,
    luas_panen_ha FLOAT DEFAULT 0,
    jjg_panen_jjg INT DEFAULT 0,
    jjg_kirim_jjg INT DEFAULT 0,
    ketrek VARCHAR(64) NULL,
    total_jjg_kirim_jjg INT DEFAULT 0,
    tonase_panen_kg FLOAT DEFAULT 0,
    refraksi_kg FLOAT DEFAULT 0,
    refraksi_persen FLOAT DEFAULT 0,
    restant_jjg INT DEFAULT 0,
    bjr_hari_ini FLOAT DEFAULT 0,
    output_kg_hk FLOAT DEFAULT 0,
    output_ha_hk FLOAT DEFAULT 0,
    budget_harian FLOAT DEFAULT 0,
    timbang_kebun_harian FLOAT DEFAULT 0,
    timbang_pks_harian FLOAT DEFAULT 0,
    rotasi_panen FLOAT DEFAULT 0,
    input_by VARCHAR(64) NULL,
    additional_data JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_tanggal_kebun_divisi (tanggal_panen, kebun, divisi),
    INDEX idx_tahun_bulan (tahun, bulan)
);
```

**Keterangan Kolom**:
- `tanggal_panen`: Tanggal panen
- `bulan`: Nama bulan (auto-filled dari tanggal)
- `tahun`: Tahun (auto-filled dari tanggal)
- `kebun`: Nama kebun
- `divisi`: Nama divisi
- `akp_panen`: AKP dalam format persentase (misal: "2.5%")
- `jumlah_tk_panen`: Jumlah tenaga kerja panen
- `luas_panen_ha`: Luas panen dalam hektar
- `jjg_panen_jjg`: Jumlah JJG yang dipanen
- `jjg_kirim_jjg`: Jumlah JJG yang dikirim
- `ketrek`: Field opsional untuk ketrek
- `total_jjg_kirim_jjg`: Total JJG yang dikirim
- `tonase_panen_kg`: Total tonase panen dalam kg
- `refraksi_kg`: Refraksi dalam kg
- `refraksi_persen`: Refraksi dalam persentase
- `restant_jjg`: JJG yang tersisa
- `bjr_hari_ini`: Berat Janjang Rata-rata hari ini
- `output_kg_hk`: Output per HK dalam kg
- `output_ha_hk`: Output per HK dalam hektar
- `budget_harian`: Budget harian
- `timbang_kebun_harian`: Timbangan di kebun
- `timbang_pks_harian`: Timbangan di PKS
- `rotasi_panen`: Rotasi panen dalam hari
- `input_by`: Nama user yang input data

### 4. Tabel `panen_bulanans`
**Fungsi**: Menyimpan agregasi data panen bulanan
```sql
CREATE TABLE panen_bulanans (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tahun INT NOT NULL,
    bulan INT NOT NULL,
    kebun_id BIGINT NOT NULL,
    divisi_id BIGINT NOT NULL,
    total_luas_panen DECIMAL(10,2) DEFAULT 0,
    total_jjg_panen INT DEFAULT 0,
    total_timbang_kebun DECIMAL(10,2) DEFAULT 0,
    total_timbang_pks DECIMAL(10,2) DEFAULT 0,
    total_jumlah_tk INT DEFAULT 0,
    total_refraksi_kg DECIMAL(10,2) DEFAULT 0,
    total_alokasi_budget DECIMAL(12,2) DEFAULT 0,
    bjr_bulanan DECIMAL(10,2) DEFAULT 0,
    akp_bulanan DECIMAL(10,4) DEFAULT 0,
    acv_prod_bulanan DECIMAL(10,2) DEFAULT 0,
    refraksi_persen DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_bulanan (tahun, bulan, kebun_id, divisi_id)
);
```

### 5. Tabel `kebuns` (Legacy - untuk kompatibilitas)
**Fungsi**: Data master kebun (legacy)
```sql
CREATE TABLE kebuns (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    nama_kebun VARCHAR(255) NOT NULL,
    kode_kebun VARCHAR(50) UNIQUE NOT NULL,
    alamat TEXT NULL,
    luas_total DECIMAL(10,2) NULL,
    sph_panen INT DEFAULT 136,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### 6. Tabel `divisis` (Legacy - untuk kompatibilitas)
**Fungsi**: Data master divisi (legacy)
```sql
CREATE TABLE divisis (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    nama_divisi VARCHAR(255) NOT NULL,
    kode_divisi VARCHAR(50) UNIQUE NOT NULL,
    kebun_id BIGINT NOT NULL,
    luas_divisi DECIMAL(10,2) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (kebun_id) REFERENCES kebuns(id) ON DELETE CASCADE
);
```

### 7. Tabel `table_columns`
**Fungsi**: Konfigurasi kolom dinamis untuk tabel
```sql
CREATE TABLE table_columns (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    table_name VARCHAR(255) NOT NULL,
    column_name VARCHAR(255) NOT NULL,
    column_label VARCHAR(255) NOT NULL,
    column_type VARCHAR(255) DEFAULT 'text',
    is_visible BOOLEAN DEFAULT TRUE,
    is_required BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    validation_rules JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY unique_table_column (table_name, column_name)
);
```

## 🔧 Operasi Database

### Menambah Data Master
```sql
INSERT INTO master_data (kebun, divisi, sph_panen, luas_tm, budget_alokasi, pkk, bulan, tahun) 
VALUES ('Kebun Baru', 'Divisi A', 140, 50.5, 75000000, 3500, 'January', 2025);
```

### Menambah Data Panen Harian
```sql
INSERT INTO panen_harians (
    tanggal_panen, kebun, divisi, jumlah_tk_panen, luas_panen_ha, 
    jjg_panen_jjg, tonase_panen_kg, timbang_kebun_harian, timbang_pks_harian
) VALUES (
    '2025-01-15', 'Kebun Sawit Utama', 'Divisi A', 25, 15.5, 
    1200, 24000, 23800, 23900
);
```

### Query Perhitungan Metrik
```sql
-- BJR (Berat Janjang Rata-rata)
SELECT 
    kebun, divisi, tanggal_panen,
    CASE 
        WHEN jjg_panen_jjg > 0 THEN timbang_kebun_harian / jjg_panen_jjg 
        ELSE 0 
    END as bjr_calculated
FROM panen_harians;

-- AKP (Angka Kerapatan Panen)
SELECT 
    p.kebun, p.divisi, p.tanggal_panen,
    CASE 
        WHEN (p.luas_panen_ha * COALESCE(m.sph_panen, 136)) > 0 
        THEN p.jjg_panen_jjg / (p.luas_panen_ha * COALESCE(m.sph_panen, 136))
        ELSE 0 
    END as akp_calculated
FROM panen_harians p
LEFT JOIN master_data m ON p.kebun = m.kebun 
    AND p.divisi = m.divisi 
    AND p.tahun = m.tahun 
    AND p.bulan = m.bulan;

-- ACV Prod (Achievement vs Budget)
SELECT 
    kebun, divisi, tanggal_panen,
    CASE 
        WHEN budget_harian > 0 THEN (timbang_pks_harian / budget_harian) * 100
        ELSE 0 
    END as acv_prod_calculated
FROM panen_harians;
```

### Menghapus Data
```sql
-- Hapus data panen harian berdasarkan tanggal
DELETE FROM panen_harians 
WHERE tanggal_panen BETWEEN '2025-01-01' AND '2025-01-31';

-- Hapus data master untuk periode tertentu
DELETE FROM master_data 
WHERE tahun = 2024 AND bulan = 'December';
```

### Update Data
```sql
-- Update budget alokasi
UPDATE master_data 
SET budget_alokasi = 100000000 
WHERE kebun = 'Kebun Sawit Utama' AND tahun = 2025;

-- Update data panen
UPDATE panen_harians 
SET timbang_pks_harian = 25000 
WHERE id = 123;
```

## 📈 Query Analisis

### Summary Harian
```sql
SELECT 
    tanggal_panen,
    COUNT(*) as total_records,
    SUM(luas_panen_ha) as total_luas,
    SUM(jjg_panen_jjg) as total_jjg,
    SUM(tonase_panen_kg) as total_tonase,
    SUM(timbang_pks_harian) as total_produksi,
    AVG(CASE WHEN jjg_panen_jjg > 0 THEN timbang_kebun_harian / jjg_panen_jjg ELSE 0 END) as avg_bjr
FROM panen_harians 
WHERE tanggal_panen >= CURDATE() - INTERVAL 30 DAY
GROUP BY tanggal_panen
ORDER BY tanggal_panen DESC;
```

### Summary per Kebun
```sql
SELECT 
    kebun,
    COUNT(*) as total_hari_panen,
    SUM(luas_panen_ha) as total_luas,
    SUM(jjg_panen_jjg) as total_jjg,
    SUM(timbang_pks_harian) as total_produksi,
    AVG(CASE WHEN jjg_panen_jjg > 0 THEN timbang_kebun_harian / jjg_panen_jjg ELSE 0 END) as avg_bjr
FROM panen_harians 
WHERE YEAR(tanggal_panen) = YEAR(CURDATE())
GROUP BY kebun
ORDER BY total_produksi DESC;
```

### Top Performing Divisi
```sql
SELECT 
    kebun, divisi,
    AVG(CASE WHEN budget_harian > 0 THEN (timbang_pks_harian / budget_harian) * 100 ELSE 0 END) as avg_acv_prod,
    SUM(timbang_pks_harian) as total_produksi
FROM panen_harians 
WHERE tanggal_panen >= CURDATE() - INTERVAL 30 DAY
GROUP BY kebun, divisi
HAVING avg_acv_prod > 0
ORDER BY avg_acv_prod DESC, total_produksi DESC
LIMIT 10;
```

## 🔒 Backup dan Restore

### Backup Database
```bash
# SQLite
cp database/database.sqlite backup/database_$(date +%Y%m%d_%H%M%S).sqlite

# MySQL
mysqldump -u username -p database_name > backup/backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restore Database
```bash
# SQLite
cp backup/database_20250126_120000.sqlite database/database.sqlite

# MySQL
mysql -u username -p database_name < backup/backup_20250126_120000.sql
```

## 🚀 Optimasi Performance

### Index yang Direkomendasikan
```sql
-- Untuk query berdasarkan tanggal dan lokasi
CREATE INDEX idx_panen_tanggal_lokasi ON panen_harians(tanggal_panen, kebun, divisi);

-- Untuk query berdasarkan periode
CREATE INDEX idx_panen_periode ON panen_harians(tahun, bulan);

-- Untuk master data
CREATE INDEX idx_master_periode ON master_data(tahun, bulan, kebun, divisi);
```

### Query Optimization Tips
1. Selalu gunakan WHERE clause dengan index
2. Hindari SELECT * untuk tabel besar
3. Gunakan LIMIT untuk pagination
4. Gunakan agregasi di database level, bukan aplikasi

## 📊 Data Validation Rules

### Master Data
- `kebun`: Required, max 64 karakter
- `divisi`: Required, max 64 karakter
- `sph_panen`: Numeric, min 50, max 200
- `luas_tm`: Numeric, min 0
- `budget_alokasi`: Numeric, min 0
- `tahun`: Integer, 2020-2050

### Panen Harian
- `tanggal_panen`: Required, valid date
- `kebun`: Required, max 64 karakter
- `divisi`: Required, max 64 karakter
- `jumlah_tk_panen`: Integer, min 0
- `luas_panen_ha`: Numeric, min 0
- `jjg_panen_jjg`: Integer, min 0
- Semua field numeric: min 0

## 🔧 Maintenance

### Pembersihan Data Lama
```sql
-- Hapus data panen lebih dari 2 tahun
DELETE FROM panen_harians 
WHERE tanggal_panen < DATE_SUB(CURDATE(), INTERVAL 2 YEAR);

-- Hapus master data tidak aktif lebih dari 1 tahun
DELETE FROM master_data 
WHERE is_active = FALSE 
AND updated_at < DATE_SUB(CURDATE(), INTERVAL 1 YEAR);
```

### Vacuum Database (SQLite)
```sql
VACUUM;
ANALYZE;
```

---

**Catatan**: Database ini dirancang untuk mendukung sistem panen sawit dengan struktur yang fleksibel dan dapat dikembangkan sesuai kebutuhan bisnis.
