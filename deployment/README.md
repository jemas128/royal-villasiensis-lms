# Villasis State Polytechnic University LMS — Deployment Guide for Byet Host

This document explains step-by-step how to deploy the Villasis State Polytechnic University LMS to **Byet Host shared hosting** (or any cPanel / VistaPanel LAMP shared host).

---

## 1. Compatibility Overview

| Requirement | Byet Host | RVU LMS Configured | Status |
|:---|:---|:---|:---|
| **PHP Version** | PHP 8.2 / 8.3 | PHP 8.2+ (Laravel 12) | Compatible |
| **Database** | MySQL 8.x / MariaDB | MySQL driver (`utf8mb4`) | Compatible |
| **Web Server** | Apache (`mod_rewrite`) | Standard Apache Rewrite | Compatible |
| **Node.js / NPM** | Not available on server | Pre-compiled locally (`npm run build`) | Compatible |
| **Composer** | Not available on Free Tier | Vendor folder uploaded via FTP | Compatible |
| **Storage Driver** | Local Disk / Shared | File sessions & cache | Compatible |

---

## 2. Pre-Deployment (Run on Local Machine)

Before uploading files to Byet Host, prepare your production bundle locally:

```bash
# 1. Ensure production dependencies are installed
composer install --optimize-autoloader --no-dev

# 2. Compile CSS and JavaScript assets
npm run build

# 3. Export your local database schema & seed data
# In XAMPP phpMyAdmin, export `royal_villasiensis_lms` as SQL (e.g., `rvu_lms.sql`)
```

---

## 3. Directory Structure Strategy for Shared Hosting

On shared hosts like Byet Host, the public web root is usually `/htdocs` or `/public_html`.

### Recommended Structure (High Security):
Place Laravel application files **outside** the web root, and only place the contents of `public/` inside the web root.

```
/home/your_account/
├── rvu_core/               <-- Entire Laravel project EXCEPT public/
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── artisan
│
└── htdocs/                 <-- Only the contents of public/
    ├── build/              <-- Pre-compiled CSS & JS
    ├── index.php           <-- Updated path to ../rvu_core/
    ├── .htaccess
    └── favicon.ico
```

#### Adjusting `htdocs/index.php`:
If using this structure, edit `htdocs/index.php` lines 12 & 19:
```php
// Change:
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// To:
require __DIR__.'/../rvu_core/vendor/autoload.php';
$app = require_once __DIR__.'/../rvu_core/bootstrap/app.php';
```

---

## 4. Alternative Single-Folder Structure (`.htaccess` Redirect)

If your Byet Host plan restricts you to a single root directory inside `htdocs/`, create a root `.htaccess` inside `htdocs/` that rewrites traffic to `/public`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^$ public/ [L]
    RewriteRule (.*) public/$1 [L]
</IfModule>
```

---

## 5. Setting Up the Database on Byet Host

1. Log into your **VistaPanel / cPanel**.
2. Navigate to **MySQL Databases**.
3. Create a new database (e.g., `b8_12345678_rvu_lms`).
4. Note your database host (Byet Host often uses `sqlXXX.byethost.com`, **not** `localhost`).
5. Open **phpMyAdmin** from the panel, select the newly created database, and import your exported `rvu_lms.sql` file.

---

## 6. Configuring `.env` for Production

Create or edit `.env` in your Laravel root on the server:

```ini
APP_NAME="Villasis State Polytechnic University LMS"
APP_ENV=production
APP_KEY=base64:RLk1oAdlArMJxqe+LSo3rk+YxVy0a2PZPQoqGs3xrgw=
APP_DEBUG=false
APP_URL=http://your-subdomain.byethost.com

DB_CONNECTION=mysql
DB_HOST=sqlXXX.byethost.com
DB_PORT=3306
DB_DATABASE=b8_12345678_rvu_lms
DB_USERNAME=b8_12345678
DB_PASSWORD=your_db_password

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
```

> **Important**: Keep `APP_DEBUG=false` in production. Always use `SESSION_DRIVER=file` and `CACHE_STORE=file` for Byet Host compatibility.

---

## 7. Storage Permissions

Ensure the following folders have write permissions (`755` or `775` via FTP):
- `storage/`
- `storage/framework/`
- `storage/framework/cache/`
- `storage/framework/sessions/`
- `storage/framework/views/`
- `storage/logs/`
- `bootstrap/cache/`

---

## 8. Verification & Default Credentials

After deployment, test access at your Byet Host URL:
- **Admin**: `admin@rvu.edu` / `password`
- **Teacher**: `teacher@rvu.edu` / `password`
- **Student**: `student@rvu.edu` / `password`
