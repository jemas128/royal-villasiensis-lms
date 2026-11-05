# Villasis State Polytechnic University LMS
## Tailored Hosting Guide for `uni-vspu-lms.byethost6.com`

> **Status Update**:
> - ✅ Database created: `b6_43183498_lms`
> - ✅ Database SQL file imported into phpMyAdmin
> - ✅ Production `.env` pre-filled and generated at [`deployment/.env.production`](file:///c:/xampp/htdocs/royal-villasiensis-lms/deployment/.env.production)
> - ✅ Production Vite assets compiled in [`public/build/`](file:///c:/xampp/htdocs/royal-villasiensis-lms/public/build)

---

## 📌 Your Exact Byet.host Account Summary

| Setting | Your Exact Value |
|:---|:---|
| **Website URL** | `http://uni-vspu-lms.byethost6.com` |
| **VistaPanel URL** | `cpanel.byethost6.com` |
| **Username** | `b6_43183498` |
| **Password** | `qwertyjrn123` |
| **FTP Host** | `ftp.byethost6.com` |
| **FTP Port** | `21` |
| **MySQL Host** | `sql205.byethost6.com` |
| **MySQL Database** | `b6_43183498_lms` |
| **MySQL User** | `b6_43183498` |

---

## 🚀 Remaining Steps to Go Live

Since your database is already imported, you only need to complete the following **4 quick steps**:

```
[ Step 1: Connect FileZilla ] ➔ [ Step 2: Upload Files ] ➔ [ Step 3: Set Permissions ] ➔ [ Step 4: Verify PHP 8.2 ]
```

---

### Step 1: Connect via FileZilla (FTP)

1. Open **FileZilla** on your computer.
2. At the top Quickconnect bar, enter:
   - **Host:** `ftp.byethost6.com`
   - **Username:** `b6_43183498`
   - **Password:** `qwertyjrn123`
   - **Port:** `21`
3. Click **Quickconnect**.
4. In the right pane (**Remote site**), you will see your server files including the folder **`htdocs`**.

---

### Step 2: Upload Files (Choose Option A or Option B)

Choose whichever method you prefer:

#### 🌟 Option A: Single-Folder Method (Easiest & Fastest)
*Uploads everything directly inside `htdocs/`.*

1. In FileZilla, double-click to open the **`htdocs/`** directory on the server.
   - Delete any default `index2.html` or placeholder files if present.
2. In the left pane (**Local site**), navigate to:
   `c:\xampp\htdocs\royal-villasiensis-lms`
3. Select **all files and folders** in your project and drag them into the server's **`htdocs/`** folder.  
   *(Wait for FileZilla to finish transferring all files).*
4. **Upload the configuration files:**
   - In your local project, go to `deployment/`
   - Upload [`deployment/.env.production`](file:///c:/xampp/htdocs/royal-villasiensis-lms/deployment/.env.production) into `htdocs/` on the server and rename it to **`.env`**.
   - Upload [`deployment/htaccess-single-folder.txt`](file:///c:/xampp/htdocs/royal-villasiensis-lms/deployment/htaccess-single-folder.txt) into `htdocs/` on the server and rename it to **`.htaccess`** (overwriting the root `.htaccess`).

---

#### 🛡️ Option B: Split-Folder Method (Recommended for Maximum Security)
*Keeps all core code and `.env` credentials outside the public web root.*

1. In FileZilla's right pane (**Remote site** root, next to `htdocs/`):
   - Right-click and choose **Create directory** -> name it **`rvu_core`**.
2. Open **`rvu_core/`** on the server.
3. Upload **everything** from `c:\xampp\htdocs\royal-villasiensis-lms` into `rvu_core/` **EXCEPT** the `public/` folder.
4. Upload [`deployment/.env.production`](file:///c:/xampp/htdocs/royal-villasiensis-lms/deployment/.env.production) into `rvu_core/` on the server and rename it to **`.env`**.
5. Now open **`htdocs/`** on the server:
   - Upload the **entire contents of your local `public/` folder** (`build/`, `.htaccess`, `favicon.ico`, etc.) directly into `htdocs/`.
   - Upload [`deployment/index-split-folder.php`](file:///c:/xampp/htdocs/royal-villasiensis-lms/deployment/index-split-folder.php) into `htdocs/` on the server and rename it to **`index.php`** (replacing the default `index.php`).

---

### Step 3: Set Permissions for Storage and Cache

Laravel needs write permissions to store sessions, blade templates, and cache.

In FileZilla:
1. Locate the **`storage/`** folder (inside `htdocs/storage` for Option A, or `rvu_core/storage` for Option B).
2. Right-click **`storage`** -> **File permissions...**:
   - Numeric value: **`775`** (or `755`)
   - Check **Recurse into subdirectories** -> **Apply to all files and directories**.
   - Click **OK**.
3. Do the same for **`bootstrap/cache`** folder (set to **`775`** or `755`).

---

### Step 4: Ensure PHP Version is 8.2 or 8.3

Laravel 12 requires PHP 8.2+.

1. Log into your **VistaPanel**: [cpanel.byethost6.com](http://cpanel.byethost6.com)
2. Scroll to the **Software** section and click **Select PHP Version** (or **PHP Configurations**).
3. Select your domain: `uni-vspu-lms.byethost6.com`.
4. Ensure the PHP version is set to **PHP 8.2** or **PHP 8.3**.
5. Click **Set** / **Save**.

---

## 🎯 Step 5: Visit Your Live Site!

Open your browser and navigate to:
👉 **[http://uni-vspu-lms.byethost6.com](http://uni-vspu-lms.byethost6.com)**

### Default Login Accounts:
- **System Administrator:** `admin@rvu.edu` / `password`
- **Teacher:** `teacher@rvu.edu` / `password`
- **Student:** `student@rvu.edu` / `password`

---

## 🔍 Troubleshooting Checklist

| Issue | Cause | Fix |
|:---|:---|:---|
| **500 Internal Server Error** | Permissions or PHP version | 1. Check PHP version in VistaPanel (must be 8.2+).<br>2. Set `storage/` and `bootstrap/cache/` to permissions 775.<br>3. Verify `.env` exists and contains `APP_KEY`. |
| **SQL Connection Error** | Wrong DB Host | Ensure `DB_HOST=sql205.byethost6.com` in `.env` (never `localhost`). |
| **Styles / JS broken** | Missing build folder | Verify that `build/` folder is uploaded inside `public/` (or inside `htdocs/build/`). |
| **Blank White Screen** | Incomplete upload | Check FileZilla's "Failed transfers" tab at the bottom and re-queue any failed files. |
