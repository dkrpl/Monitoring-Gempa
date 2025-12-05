# 🌋 Earthquake Monitoring System - IoT Based

Sistem monitoring gempa berbasis IoT menggunakan sensor SW-420 dengan web dashboard real-time untuk monitoring sederhana.

## 📋 Daftar Isi
- [Fitur Utama](#-fitur-utama)
- [Teknologi](#️-teknologi)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Struktur Database](#-struktur-database)
- [Fitur Detail](#-fitur-detail)
- [API Endpoints](#-api-endpoints)
- [Cara Penggunaan](#-cara-penggunaan)
- [Testing](#-testing)
- [Troubleshooting](#-troubleshooting)
- [Kontribusi](#-kontribusi)
- [Lisensi](#-lisensi)

## 🚀 Fitur Utama

### 📊 Dashboard & Monitoring
- **Dashboard Real-time** dengan statistik live
- **Chart interaktif** untuk data gempa
- **Device status monitoring** (online/offline)
- **Alert system** otomatis berdasarkan threshold

### 👥 User Management
- **Role-based access control** (Admin/User)
- **CRUD lengkap** untuk manajemen user
- **Profile picture upload**
- **Status management** (active/inactive)

### 📡 Device Management
- **IoT Device management** untuk sensor SW-420
- **QR Code generator** untuk setiap device
- **Heartbeat monitoring** real-time
- **Location tracking** dan status device

### 🌍 Earthquake Events
- **Automatic event classification** (Warning/Danger)
- **Magnitude-based alerts**
- **Location mapping** dengan koordinat
- **Export data** ke CSV
- **Event simulation** untuk testing

### ⚙️ System Features
- **Custom error pages** dengan animasi seismik
- **SweetAlert notifications** tanpa refresh
- **Responsive design** dengan SB Admin template
- **Real-time updates** dengan AJAX
- **Export/Import functionality**

## 🛠️ Teknologi

### Backend
- **Laravel 12** - PHP Framework
- **MySQL** - Database
- **Breeze** - Authentication

### Frontend
- **SB Admin 2** - Admin Template
- **Bootstrap 5** - CSS Framework
- **Chart.js** - Data Visualization
- **SweetAlert2** - Notification System
- **Font Awesome** - Icons

### Development Tools
- **Composer** - PHP Dependency Manager
- **Blade Templating** - Laravel Template Engine
- **Eloquent ORM** - Database Management

## 📥 Instalasi

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 5.7+
- Web Server (Apache/Nginx)

### Langkah 1: Clone Repository
```bash
git clone https://github.com/yourusername/earthquake-monitoring.git
cd earthquake-monitoring
```

### Langkah 2: Install Dependencies
```bash
composer install
```

### Langkah 3: Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env`:
```env
APP_NAME="Earthquake Monitoring System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=earthquake_monitoring
DB_USERNAME=root
DB_PASSWORD=

LOG_CHANNEL=stack
LOG_LEVEL=debug
```

### Langkah 4: Database Setup
```bash
php artisan migrate --seed
```

### Langkah 5: Storage Link
```bash
php artisan storage:link
```

### Langkah 6: Serve Application
```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

## ⚙️ Konfigurasi

### Default Login Credentials
**Admin:**
- Email: `admin@eqmonitor.com`
- Password: `password123`

**User:**
- Email: `user@eqmonitor.com`
- Password: `password123`

### Threshold Settings
Default thresholds untuk classification:
- **Warning**: ≥ 3.0 magnitude
- **Danger**: ≥ 5.0 magnitude

### Environment Variables Penting
```env
# Session Lifetime (minutes)
SESSION_LIFETIME=120

# Queue Connection
QUEUE_CONNECTION=database

# Broadcast Driver
BROADCAST_DRIVER=log

# Cache Driver
CACHE_DRIVER=file

# File System
FILESYSTEM_DISK=local
```

## 🗃️ Struktur Database

### 📊 Tabel Users
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    image TEXT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### 📡 Tabel Devices
```sql
CREATE TABLE devices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) UNIQUE NOT NULL,
    nama_device VARCHAR(255) NOT NULL,
    lokasi VARCHAR(255) NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    last_seen DATETIME NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### 📈 Tabel Thresholds
```sql
CREATE TABLE thresholds (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    min_value FLOAT NOT NULL,
    description VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### 🌋 Tabel Earthquake Events
```sql
CREATE TABLE earthquake_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_id BIGINT UNSIGNED NOT NULL,
    magnitude FLOAT NOT NULL,
    status ENUM('warning', 'danger') NOT NULL,
    occurred_at DATETIME NOT NULL,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    depth DECIMAL(8, 2) NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE
);
```

### 📝 Tabel Device Logs
```sql
CREATE TABLE device_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    device_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(255) NULL,
    magnitude FLOAT NULL,
    logged_at DATETIME NOT NULL,
    FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE CASCADE
);
```

## 🔧 Fitur Detail

### 1. Landing Page
- **Hero section** dengan animasi sensor
- **Features showcase** dengan card interaktif
- **How it works** dengan visualisasi 3 langkah
- **Live demo alert** dengan simulasi
- **Responsive design** untuk semua device

### 2. User Management
- **Create/Edit/Delete users**
- **Role assignment** (Admin/User)
- **Profile picture upload**
- **Password strength indicator**
- **Bulk actions** (future)

### 3. Device Management
- **Device registration** dengan UUID otomatis
- **QR Code generator** untuk device identification
- **Real-time status monitoring**
- **Heartbeat simulation**
- **Offline device detection**

### 4. Earthquake Events
- **Automatic classification** berdasarkan threshold
- **Location tracking** dengan koordinat
- **Export functionality** ke CSV
- **Event simulation** untuk testing
- **Chart visualization** untuk trend analysis

### 5. Error Pages
- **Custom error pages** untuk semua HTTP error codes
- **Seismic wave animations**
- **Helpful error messages**
- **Recovery actions**
- **Debug information** (development only)

## 📡 API Endpoints

### Public API (v1)
```
GET    /api/v1/health           # Health check
POST   /api/v1/devices/{uuid}/data  # IoT data reception
```

### Authentication Required
```
GET    /api/devices             # List devices
POST   /api/devices             # Create device
GET    /api/devices/{id}        # Device details
PUT    /api/devices/{id}        # Update device
DELETE /api/devices/{id}        # Delete device

GET    /api/events              # List earthquake events
POST   /api/events              # Create event
GET    /api/events/{id}         # Event details
PUT    /api/events/{id}         # Update event
DELETE /api/events/{id}         # Delete event
```

### IoT Device API Example
```json
POST /api/v1/devices/{UUID}/data
{
    "magnitude": 4.5,
    "status": "online",
    "timestamp": "2024-01-15T10:30:00Z",
    "latitude": -6.200000,
    "longitude": 106.816666,
    "depth": 10.5
}
```

## 🎮 Cara Penggunaan

### 1. Login sebagai Admin
1. Akses `http://localhost:8000/login`
2. Gunakan credentials admin
3. Anda akan diarahkan ke dashboard

### 2. Menambahkan Device Sensor
1. Navigasi ke **Devices** → **Add New Device**
2. Isi informasi device:
   - Nama Device (contoh: SW-420 Sensor #001)
   - Lokasi (contoh: Building A, 3rd Floor)
   - Status: Active
3. Device akan otomatis mendapatkan UUID

### 3. Monitoring Earthquake Events
1. Navigasi ke **Earthquake Events**
2. Sistem akan menampilkan:
   - Recent events dalam 24 jam
   - Chart aktivitas 30 hari terakhir
   - Statistik real-time

### 4. Record Manual Event
1. Klik **Record Event** di halaman Earthquake Events
2. Pilih device yang mendeteksi
3. Masukkan magnitude dan waktu kejadian
4. Sistem akan otomatis klasifikasikan (Warning/Danger)

### 5. Export Data
1. Navigasi ke **Earthquake Events**
2. Klik tombol **Export**
3. Pilih date range dan status
4. Download file CSV

### 6. Simulasi Event
1. Di halaman Earthquake Events
2. Klik **Simulate Event**
3. Sistem akan membuat event simulasi untuk testing

## 🧪 Testing

### Error Pages Testing (Development Only)
Akses URL berikut untuk test error pages:
```
/_test/error/404      # Page Not Found
/_test/error/500      # Server Error
/_test/error/403      # Forbidden
/_test/error/429      # Too Many Requests
/_test/error/503      # Service Unavailable
```

### Seeded Data
Sistem sudah include sample data:
- 5 devices dengan status aktif/nonaktif
- 30 hari log data untuk setiap device
- Random earthquake events
- 2 user accounts (admin/user)

## 🔍 Troubleshooting

### Common Issues

#### 1. Database Connection Error
```bash
# Check database credentials
php artisan config:clear
php artisan cache:clear
```

#### 2. Migration Error
```bash
# Fresh migration
php artisan migrate:fresh --seed
```

#### 3. Storage Link Error
```bash
# Remove and recreate
rm public/storage
php artisan storage:link
```

#### 4. Permission Issues
```bash
# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

#### 5. Session/Cache Issues
```bash
# Clear all caches
php artisan optimize:clear
```

### Log Files
- **Laravel logs**: `storage/logs/laravel.log`
- **Error logs**: Check web server logs

## 🤝 Kontribusi

### Development Setup
1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

### Coding Standards
- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add comments for complex logic
- Update documentation accordingly

### Testing Requirements
- Test all new features
- Ensure no breaking changes
- Update error handling
- Verify responsive design

## 📄 Lisensi

Copyright © 2024 Earthquake Monitoring System

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

## 📞 Kontak & Support

**Email Support:** support@eqmonitor.com  
**Documentation:** [docs.eqmonitor.com](https://docs.eqmonitor.com)  
**Status Page:** [status.eqmonitor.com](https://status.eqmonitor.com)

---

**Earthquake Monitoring System** - Sistem monitoring gempa real-time berbasis IoT dengan sensor SW-420. Memberikan peringatan dini dan monitoring komprehensif untuk keselamatan masyarakat.

**Versi:** 1.0.0  
**Terakhir Diupdate:** 15 Januari 2024  
**Kompatibilitas:** Laravel 12, PHP 8.2+, MySQL 5.7+
