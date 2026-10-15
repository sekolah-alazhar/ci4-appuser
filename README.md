# Sistem Manajemen User (CodeIgniter 4)

Aplikasi CRUD User dengan role-based access control (Admin & Guest) menggunakan CodeIgniter 4.

## 📋 Fitur

- **Authentication**: Login dengan username & password
- **Authorization**: 
  - Admin: Full CRUD (Create, Read, Update, Delete) user
  - Guest: Read-only (hanya bisa melihat user dengan role Guest)
- **Password Hashing**: Menggunakan `password_hash()` PHP (bcrypt)
- **Database**: MariaDB dengan migrations
- **Frontend**: Tailwind CSS via CDN + Font Awesome

## 🔐 Default Akun

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |
| Guest | `tamu01` | `tamu123` |

## 🌐 URL Routes

```
https://users.pudin.eu.org/          → Login page
https://users.pudin.eu.org/login     → Login page
https://users.pudin.eu.org/logout    → Logout
https://users.pudin.eu.org/admin/dashboard   → Admin: Dashboard (grafik + jumlah user)
https://users.pudin.eu.org/admin/users      → Admin: Daftar semua user
https://users.pudin.eu.org/admin/users/create → Admin: Tambah user
https://users.pudin.eu.org/admin/users/{id}/edit → Admin: Edit user
https://users.pudin.eu.org/guest/dashboard  → Guest: Dashboard (read-only)
https://users.pudin.eu.org/guest/users       → Guest: Daftar user (read-only)
```

## 🎨 UI / Tampilan

- **Footer hitam dihapus** — halaman login kini full center tanpa footer gelap;
  halaman login setelah layout baru memakai footer tipis abu-abu (bukan hitam).
- **Sidebar kiri** (setelah login): brand, menu Dashboard & Manajemen User/Daftar User,
  info user + tombol logout di bawah sidebar.
- **Menu aktif**: item menu dapat highlight `bg-indigo-600` + indikator batang di kiri
  + ikon panah, item non-aktif hover `bg-slate-800` (efek hover grup).
- **Responsive**: sidebar jadi off-canvas di layar < 768px dengan tombol hamburger
  dan overlay gelap.
- **Topbar**: judul halaman, tanggal, badge role, ikon logout.
- **Dashboard**: banner sapaan, 4 kartu statistik (Total User, Admin, Guest,
  Baru Bulan Ini), grafik batang user baru 6 bulan (Chart.js), donat distribusi role,
  tabel 5 user terbaru.
- Grafik memakai **Chart.js 4** (CDN) via section `scripts` di layout.

## 🏗️ Struktur File

```
/home/upudin/ci4-appuser/
├── ci4-appuser/                    # Aplikasi CodeIgniter 4
│   ├── app/
│   │   ├── Config/
│   │   │   ├── Routes.php          # Routing aplikasi
│   │   │   └── Filters.php         # Filter auth (auth, admin, guest)
│   │   ├── Controllers/
│   │   │   ├── BaseController.php  # Base controller
│   │   │   └── Users.php           # Controller utama
│   │   ├── Database/
│   │   │   └── Migrations/
│   │   │       └── 2026-10-15-105216_CreateUserTable.php
│   │   ├── Filters/
│   │   │   ├── AuthFilter.php      # Filter autentikasi
│   │   │   ├── AdminFilter.php     # Filter admin only
│   │   │   └── GuestFilter.php     # Filter guest only
│   │   ├── Models/
│   │   │   └── UserModel.php       # Model user
│   │   ├── Services/
│   │   │   └── UserService.php     # Service layer
│   │   └── Views/
│   │       ├── layouts/
│   │       │   └── main.php        # Layout: sidebar + topbar (footer hitam dihapus)
│   │       ├── auth/
│   │       │   └── login.php       # Halaman login (center, tanpa footer hitam)
│   │       ├── dashboard/
│   │       │   └── index.php       # Dashboard (kartu statistik + grafik Chart.js)
│   │       ├── admin/
│   │       │   └── users/
│   │       │       ├── index.php   # Daftar user (admin)
│   │       │       └── form.php    # Form tambah/edit
│   │       └── guest/
│   │           └── users/
│   │               └── index.php   # Daftar user (guest)
│   ├── public/
│   │   └── index.php               # Entry point
│   ├── .env                        # Konfigurasi environment
│   └── spark                       # CLI tool CodeIgniter
├── nginx-users.conf                # Konfigurasi nginx virtual host
├── app-user.service                # Systemd service
└── seed_admin.sh                   # Script seeding admin user
```

## 📊 Database Schema

```sql
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'guest') DEFAULT 'guest',
    created_at DATETIME,
    updated_at DATETIME
);
```

## 🔧 Setup & Deployment

### 1. Install Dependencies
```bash
cd /home/upudin/ci4-appuser/ci4-appuser
composer install
```

### 2. Database Migration
```bash
php spark migrate
```

### 3. Seed Admin User
```bash
bash /home/upudin/ci4-appuser/seed_admin.sh
```

### 4. Configure Nginx
```bash
ln -sf /home/upudin/ci4-appuser/nginx-users.conf /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
```

### 5. Create Systemd Service
```bash
cp /home/upudin/ci4-appuser/app-user.service /etc/systemd/system/
systemctl daemon-reload
systemctl enable app-user.service
systemctl restart app-user.service
```

### 6. Verify
```bash
systemctl status app-user.service
curl -I http://127.0.0.1:8080/login
```

## 🔒 Security Features

1. **Password Hashing**: bcrypt via `password_hash()`
2. **CSRF Protection**: Enabled di `Filters.php`
3. **Role-based Access Control**: Filter middleware
4. **SQL Injection Prevention**: Query builder CI4
5. **XSS Prevention**: Auto-escaping di views

## 🔄 Flow Kerja

```
1. User buka https://users.pudin.eu.org/
   ↓
2. Redirect ke /login
   ↓
3. Input username & password
   ↓
4. Verifikasi di UserService::verifyPassword()
   ↓
5. Session dibuat (logged_in, user_id, role, name)
   ↓
6. Redirect berdasarkan role:
   - Admin → /admin/users
   - Guest → /guest/users
   ↓
7. Filter memeriksa role sebelum akses controller
```

## 🐛 Troubleshooting

### Perbaikan Penting (15 Okt 2026)
1. **Routes double-slash**: route di dalam `group()` ditulis `'/users'` sehingga
   jadi `/admin//users` → tidak ketemu. Diperbaiki jadi `'users'` tanpa leading slash.
2. **Guest view 500**: view pakai `$pagination` yang tidak dikirim controller,
   plus pakai `$pager` yang tidak ada. View ditulis ulang tanpa pagination
   (list sederhana + Total count).
3. **Model insert 500**: `allowedFields` tidak memuat `id` sehingga UUID ditolak;
   validation model pakai placeholder `{id}` yang tidak terisi saat insert
   (`No validation rules for the placeholder: "id"`). Solusi: validasi model
   dimatikan (`skipValidation=true`), validasi dilakukan di Controller;
   callback hash diperbaiki memakai `$data['data']` (format event CI4, bukan
   `$data['params']`) + cek `password_get_info()` agar tidak hash ganda.
4. **DB user tanpa hak DELETE**: `appuser` hanya punya SELECT/INSERT/UPDATE.
   Ditambah via root: `GRANT SELECT, INSERT, UPDATE, DELETE ON db_appuser.users`.
5. **Login diam-diam gagal**: hash password lama salah → hash ulang dengan
   `password_hash(..., PASSWORD_DEFAULT)` dan verifikasi `password_verify`.
6. **Akses dari internet 403 challenge**: itu Cloudflare "Under Attack Mode"
   (bot challenge), BUKAN error nginx/aplikasi. Browser asli lolos challenge,
   curl mentah diblok. Verifikasi dari server pakai `--resolve ...:127.0.0.1`.

### Debug Mode
```bash
# Cek error log
tail -f /home/upudin/ci4-appuser/ci4-appuser/writable/logs/log-*.log

# Test koneksi database
mysql -u appuser -p@C0b4d1b4c4 -h localhost db_appuser
```

### Service Status
```bash
systemctl status app-user.service
systemctl restart app-user.service
```

### Nginx Status
```bash
nginx -t
systemctl status nginx
```

## 📝 Notes

- Database: `db_appuser` dengan user `appuser`
- Port internal: `8080` (dikelola systemd)
- SSL: Cloudflare (Automatic HTTPS Rewrites)
- Environment: production mode
