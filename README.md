# 🎓 Sistem PMB (Penerimaan Mahasiswa Baru)

Aplikasi web untuk mengelola proses **Penerimaan Mahasiswa Baru** di perguruan tinggi, dibangun dengan **Mazu Framework**.

## 📦 Instalasi

### 1. Clone & Setup

```bash
# Clone repository
git clone <repository-url>
cd <project-folder>

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php mazu key:generate
```

### 2. Konfigurasi Database

Edit file `.env`:

```env
APP_ENV=development
APP_DEBUG=true
APP_NAME=Sistem PMB

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=pmb_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Jalankan Migration

```bash
php mazu migrate
```

### 4. Start Development Server

```bash
php mazu serve
```

Akses aplikasi di `http://localhost:8000`

## 🛠️ CLI Commands

```bash
# Generate code
php mazu make:controller NamaController    # Buat controller baru
php mazu make:model NamaModel              # Buat model baru
php mazu make:middleware Auth              # Buat middleware auth

# Database
php mazu migrate                           # Jalankan migration

# Development
php mazu serve                             # Start dev server
php mazu build                             # Build assets
php mazu route:cache                       # Cache routes

# Info
php mazu about                             # Info framework
```

## 📁 Struktur Folder

```
project-root/
├── app/                    # Mazu Framework Core (JANGAN MODIFIKASI)
│   ├── Console/            # CLI Commands
│   ├── Core/               # Framework Core
│   ├── Helpers/            # Helper Functions
│   └── Services/           # Core Services
├── addon/                  # Application Code
│   ├── Controllers/        # Controllers
│   ├── Middleware/         # Middleware (Auth, Role, CSRF)
│   ├── Models/             # Models
│   ├── Providers/          # Service Providers
│   ├── Router/
│   │   └── index.php       # Route Definitions
│   └── Views/              # View Templates
│       ├── layout.php      # Root layout
│       └── (group)/        # Grouped layouts
├── config/                 # Configuration Files
├── public/                 # Public Assets
└── storage/                # Cache, Logs, Uploaded Files
```

## 👥 User Roles

Aplikasi ini memiliki 2 jenis user:

| Role  | Deskripsi                           |
| ----- | ----------------------------------- |
| Admin | Mengelola data, verifikasi, laporan |
| User  | Pendaftar/peserta PMB               |

**Admin Access Control:**
Admin memiliki field array untuk mengatur akses per modul/fitur secara granular.

## 🎯 Quick Start

### 1. Buat Model

```bash
php mazu make:model NamaModel
```

### 2. Buat Controller

```bash
php mazu make:controller NamaController
```

### 3. Definisikan Route

Edit `addon/Router/index.php`:

```php
<?php

use App\Core\Http\Request;
use App\Core\Http\Response;

// Public routes
$router->get('/', fn(Request $r, Response $res) => $res->renderPage([]));

// Protected routes (dengan middleware)
$router->get('/admin', [AdminController::class, 'index'], ['auth', 'role:admin']);
```

## 🔧 Helper Functions

```php
// Environment
env('APP_NAME', 'Default')

// URL
getBaseUrl('/path')
asset('css/style.css')
currentUrl()

// Security
csrf_token()
csrf_field()
e($value)  // Escape HTML

// Debug
dump($variable)
logger()->info('Message', ['context' => $data])

// Utils
ulid()      // Generate ULID
uuidv4()    // Generate UUID
```

## 🚀 Deployment

### Production Setup

```bash
# Install production dependencies
composer install --no-dev --optimize-autoloader

# Cache routes
php mazu route:cache

# Build assets
php mazu build

# Set environment
APP_ENV=production
APP_DEBUG=false
```

### Server Requirements

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- Extension: pdo_mysql, json, mbstring, fileinfo

## 📚 Dokumentasi

Untuk dokumentasi lengkap tentang Mazu Framework, lihat skill files di `.roo/skills/`:

- [`mazu-core`](.roo/skills/mazu-core/SKILL.md) - Struktur folder, CLI, rules
- [`mazu-controller`](.roo/skills/mazu-controller/SKILL.md) - Controller patterns & routing
- [`mazu-model`](.roo/skills/mazu-model/SKILL.md) - Model schema & migration
- [`mazu-views`](.roo/skills/mazu-views/SKILL.md) - View system & layouts
- [`mazu-middleware`](.roo/skills/mazu-middleware/SKILL.md) - Auth, Role, CSRF

## 📄 License

Sistem PMB - Personal/Commercial Use

---

**Sistem PMB** - _Build Faster. Scale Better._
