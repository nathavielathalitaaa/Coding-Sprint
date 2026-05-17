# 🏨 HRIS Sinergi Hotel & Villa
> Human Resource Information System — Built with Laravel 12

---

## 📋 Deskripsi

Sistem HRIS berbasis web untuk pengelolaan SDM Hotel & Villa, mencakup:
- Manajemen Karyawan (import Excel, profil, TTD digital)
- Sistem Pengajuan & Approval Surat (multi-level approver)
- Manajemen Absensi dengan AI Name-Matching
- Dashboard Analytics per Role (HR / Supervisor / HOD / Staff)
- System Health Monitor & Document Archive Manager
- Audit Trail Activity Log

---

## ⚙️ Tech Stack

| Layer | Tech |
|-------|------|
| Backend | Laravel 12, PHP 8.2 |
| Frontend | Blade, Tailwind CSS (CDN), Lucide Icons |
| Database | MySQL |
| Auth | Laravel Auth + Spatie Permission |
| PDF | FPDI, FPDF |
| Excel | PhpSpreadsheet |

---

## 🚀 Panduan Deploy (untuk Teknisi Server)

### Prasyarat
- PHP >= 8.2 dengan ekstensi: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `fileinfo`, `zip`
- Composer
- MySQL >= 5.7
- Node.js & NPM (untuk build assets)
- Web server: Apache / Nginx

---

### Step 1 — Clone Repository

```bash
git clone https://github.com/nathavielathalitaaa/HR-DTP-Project-Final.git
cd HR-DTP-Project-Final
```

---

### Step 2 — Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

---

### Step 3 — Konfigurasi Environment

Minta file `.env` dari pengembang, lalu taruh di root project. Sesuaikan bagian berikut:

```env
APP_NAME="HRIS Sinergi"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://[domain-atau-ip-server]

DB_HOST=127.0.0.1
DB_DATABASE=[nama_database]
DB_USERNAME=[user_database]
DB_PASSWORD=[password_database]

SESSION_DOMAIN=[domain-atau-ip-server]
```

---

### Step 4 — Generate App Key

```bash
php artisan key:generate
```

---

### Step 5 — Migrasi Database

```bash
php artisan migrate
```

---

### Step 6 — Isi Data Awal (Seeder Production)

```bash
php artisan db:seed --class=ProductionSeeder --force
```

Setelah selesai, akan muncul:
```
Admin Email : admin@sinergihotel.com
Password    : Sinergi@2026
```

> ⚠️ **Segera ganti password HR setelah login pertama!**

---

### Step 7 — Setup Storage

```bash
php artisan storage:link
```

---

### Step 8 — Optimasi Cache

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### Step 9 — Permission Folder (Linux only)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

### Step 10 — Konfigurasi Web Server

**Nginx** — arahkan `root` ke folder `public/`:
```nginx
root /var/www/HR-DTP-Project-Final/public;
index index.php;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
}
```

**Apache** — pastikan `mod_rewrite` aktif, file `.htaccess` sudah ada di folder `public/`.

---

## 🔐 Akun Default Setelah Deploy

| Role | Email | Password |
|------|-------|----------|
| HR Admin | `admin@sinergihotel.com` | `Sinergi@2026` |

> Karyawan lain di-import oleh HR melalui fitur Import Excel di dalam sistem.

---

## 📂 Struktur Penting

```
├── app/
│   ├── Http/Controllers/   # Logic controller
│   ├── Http/Middleware/    # Auth, session, force password
│   ├── Imports/            # Import Excel karyawan
│   ├── Models/             # Eloquent models
│   └── Services/           # PDF stamping, merge, dll
├── database/
│   ├── migrations/         # Struktur tabel
│   └── seeders/
│       ├── ProductionSeeder.php   # ← Gunakan ini saat deploy
│       └── SuratTypeSeeder.php
├── resources/views/        # Blade templates
├── routes/web.php          # Semua route
└── storage/                # File upload (PDF, TTD, foto)
```

---

## 🆘 Troubleshooting

| Error | Solusi |
|-------|--------|
| `500 Internal Server Error` | Cek `storage/logs/laravel.log`, pastikan `APP_DEBUG=true` sementara |
| `Permission denied` | Jalankan `chmod -R 775 storage bootstrap/cache` |
| `Class not found` | Jalankan `composer dump-autoload` |
| File upload tidak bisa | Pastikan `php artisan storage:link` sudah dijalankan |
| Route not found | Jalankan `php artisan route:clear` |

---

*Developed for PT Sinergi Hotel & Villa — 2026*
