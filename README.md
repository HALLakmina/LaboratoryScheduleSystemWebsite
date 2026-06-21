# Laboratory Schedule System

> A PHP and JavaScript web application for managing laboratory schedules, lecturer requests, timetable changes, news, and administration for a university faculty or department.

---

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Tech Stack](#tech-stack)
4. [Architecture](#architecture)
5. [Project Structure](#project-structure)
6. [Quick Start](#quick-start)
7. [Detailed Setup](#detailed-setup)
8. [XAMPP Configuration](#xampp-configuration)
9. [Docker Setup](#docker-setup)
10. [Environment Variables](#environment-variables)
11. [Seed Accounts](#seed-accounts)
12. [API Reference](#api-reference)
13. [Database Schema](#database-schema)
14. [Database Migrations](#database-migrations)
15. [Backend Libraries](#backend-libraries)
16. [Contributing](#contributing)
17. [Troubleshooting](#troubleshooting)
18. [Security](#security)
19. [Logging System](#logging-system)
20. [Author](#author)

---

## Overview

The Laboratory Schedule System helps lecturers and administrators coordinate laboratory usage in a clear, structured way.

```
Lecturer                          Admin
────────                          ─────
View timetable       ──────►     Manage timetable structure
Filter by week                   Assign lecturers to subjects
Submit slot request  ──────►     Review & confirm/cancel requests
Receive email                    Send email notifications
                                 Publish news
                                 Manage users
```

**Who is this for?**

| Audience | Use case |
|----------|----------|
| **Lecturers** | View schedules, request extra slots, track request status |
| **Administrators** | Manage the full system from one admin panel |
| **Developers** | Extend or integrate with the REST API |
| **Researchers** | Reference implementation of a PHP layered architecture with JWT auth |

---

## Features

### Public Timetable
- Dynamic grid built entirely from database settings (rows, columns, break row — all configurable)
- Week-by-week navigation — current week and up to 3 weeks ahead
- Permanent timetable overlaid with confirmed temporary changes for the selected week
- Lab allocation modal showing which labs are in use for each time slot
- Lab detail modal showing the full lecture schedule for a selected lab
- Time-slot detail panel with two distinct lecturer fields:
  - **Lecturer In-Charge** — the lecturer assigned with `responsible_level = 1`
  - **Lecturers** — all other assigned lecturers (comma-separated)

### Lecturer Request Flow
- Submit a slot request (subject, year, group, day, time slot, date, description)
- Availability check before submission to prevent double-booking
- Submit-locking prevents duplicate requests
- Email notification to all admins on new request
- Admin confirms (assigns a lab) or cancels (mandatory cancel reason)
- Email notification sent to the requesting lecturer with outcome

### Lecturer Assignments
- Define reusable **responsibility types** (e.g. Lab In-Charge, Demonstrator, Assistant) with an optional unique numeric level
- `responsible_level = 1` marks the **Lecturer In-Charge** — enforced as unique per subject
- Assign lecturers to subjects once, with or without a responsibility level
- Frontend prevents duplicate assignments and two level-1 holders per subject before any API call

### Admin Panel
- Overview dashboard with live system statistics
- Timetable settings (grid dimensions, break row)
- Full CRUD for: timetable records, years, groups, labs, subjects, column headings, time slots
- Incoming lecturer request workflow (confirm with lab / cancel with reason)
- News management with optional image uploads
- User management (create, update, delete, reset passwords)
- Responsibilities management (create, update, delete responsibility type definitions)
- Lecturer Assignments management (assign lecturers to subjects)
- Action Logs viewer — paginated table of every INSERT / UPDATE / DELETE across the system, with before/after data diff in a detail modal and a record-count selector (10 / 20 / 50 / 100 / All)

### Logging System
- **File-based logs** — system events written to one file per level **per day**: `Backend/logs/error YYYY-MM-DD.log`, `warning YYYY-MM-DD.log`, `info YYYY-MM-DD.log`; the directory is blocked from browser access via `.htaccess`
- **Database audit log** — every INSERT, UPDATE, and DELETE across all five resource controllers is captured in `database_modification_logs` with the `old_data` snapshot (for UPDATE and DELETE) and `new_data` payload, the acting user's ID, and a timestamp; password hashes are stripped before any user-table record is written to the log
- **Non-disruptive** — a DB-log failure falls back silently to that day's `error YYYY-MM-DD.log` and never interrupts the main request

### Security
- JWT stored in **HttpOnly** cookie — not accessible to JavaScript
- `validateToken` middleware on every protected route
- `requireRole('admin')` middleware on all admin-only state-changing routes
- CORS restricted to configured allowed origins
- File uploads validated by extension allowlist **and** MIME-type inspection
- Audit fields (`created_by`, `updated_by`, `assigned_by`) injected server-side from JWT — never trusted from the client

---

## Tech Stack

| Layer    | Technology                                               | Notes |
|----------|----------------------------------------------------------|-------|
| Server   | XAMPP (Apache + MySQL / MariaDB)                         | Any LAMP stack works |
| Backend  | PHP 8.1+, Composer                                       | Custom router, no framework |
| Frontend | PHP templates, Vanilla JS, Tailwind CSS (CDN)            | No build step required |
| Database | MySQL / MariaDB                                          | PDO with prepared statements |
| Auth     | `firebase/php-jwt` (HS256), HttpOnly cookie              | JWT pinned to HS256 |
| Email    | `phpmailer/phpmailer`                                    | Graceful fallback when unconfigured |
| Validation | `respect/validation`                                   | Centralized in `validation.php` |

---

## Architecture

The backend follows a strict four-layer architecture. Every API request travels the same path:

```
HTTP Request
    │
    ▼
server.php          ← CORS headers, URL prefix routing
    │
    ▼
Router              ← registers routes, applies middleware chain
    │
    ├─► Middleware  ← validateToken → requireRole → validation
    │
    ▼
Controller          ← thin: unpacks request, injects audit values, calls service
    │
    ▼
Service             ← all business logic and SQL queries (PDO prepared statements)
    │
    ▼
DbConnection        ← PDO wrapper
```

**Request lifecycle example — admin confirms a lecturer request:**

1. `POST /api/v1/lecturer-request/update` arrives at `server.php`
2. Router matches path prefix, loads `lecturer_requests_router.php`
3. Middleware chain: `validateToken` (attaches user from JWT to request) → `requireRole('admin')` (403 if not admin) → `lecturerRequestUpdate` validation (400 if invalid body)
4. `LecturerRequestsController::update()` extracts payload, injects `updated_by` from JWT
5. `LecturerRequestsService::update()` updates the DB, syncs `temporary_timetable`, sends email notification
6. JSON response returned

---

## Project Structure

```text
LaboratoryScheduleSystemWebsite/
├── Backend/
│   ├── controllers/                        # Thin handlers — unpack, inject audit, call service
│   │   ├── lecturer_assignments_controller.php
│   │   ├── lecturer_requests_controller.php
│   │   ├── logs_controller.php             # Action-logs read endpoint
│   │   ├── news_controller.php
│   │   ├── timetable_controller.php
│   │   └── users_controller.php
│   ├── middleware/
│   │   ├── jwtToken.php                    # validateToken, requireRole('admin')
│   │   └── validation.php                  # All Respect\Validation rules
│   ├── routers/
│   │   ├── lecturer_assignments_router.php
│   │   ├── lecturer_requests_router.php
│   │   ├── logs_router.php                 # GET /action-logs (admin only)
│   │   ├── news_router.php
│   │   ├── timetable_router.php
│   │   └── users_router.php
│   ├── services/                           # Business logic + all SQL queries
│   │   ├── email_notification_service.php
│   │   ├── lecturer_assignments_service.php
│   │   ├── lecturer_requests_service.php
│   │   ├── logs_service.php                # logAction, fetchRowById, getActionLogs
│   │   ├── news_service.php
│   │   ├── timetable_service.php
│   │   └── users_service.php
│   ├── templates/                          # HTML email templates
│   │   ├── admin_lecturer_request_email_template.php
│   │   └── lecturer_request_status_email_template.php
│   ├── seeds/
│   │   ├── laboratory_schedule_system.sql  # Full database schema
│   │   └── users_seed.php                  # Seed user definitions
│   ├── scripts/
│   │   └── run_seed.php                    # One-time database setup CLI script
│   ├── utils/
│   │   ├── database_seed.php               # Seed orchestration
│   │   ├── httpOnlyCookie.php              # Cookie helper
│   │   ├── logger.php                      # Static Logger — writes one file per level per day
│   │   └── route.php                       # Custom singleton router
│   ├── logs/                               # Runtime log files (auto-created, not in repo)
│   │   ├── error YYYY-MM-DD.log
│   │   ├── warning YYYY-MM-DD.log
│   │   ├── info YYYY-MM-DD.log
│   │   └── .htaccess                       # Deny from all — blocks direct browser access
│   ├── DB/
│   │   └── dbConnection.php                # PDO wrapper
│   ├── server.php                          # API entry point, CORS headers
│   ├── .htaccess                           # URL rewriting to server.php
│   ├── .env                                # Local config (not in repo)
│   ├── .env-example                        # Template for .env
│   ├── .env.docker                         # Dev Docker config — DB_HOST=db (not in repo)
│   └── .env.production                     # Prod Docker config (not in repo)
│
├── Frontend/
│   ├── API/                                # JS fetch wrappers — one file per backend resource
│   │   ├── lecturerAssignmentsApi.js
│   │   ├── lecturerRequestApi.js
│   │   ├── logsApi.js                      # getActionLogs(page, perPage)
│   │   ├── newsApi.js
│   │   ├── timetableApi.js
│   │   └── userApi.js
│   ├── Components/
│   │   ├── NavigationBar.php
│   │   └── FooterBar.php
│   ├── JS/
│   │   ├── admin.js                        # Admin panel logic and rendering
│   │   ├── login.js                        # Login form handler
│   │   ├── loginUser.js                    # Session state helpers
│   │   ├── main.js                         # Entry point — initialises all page modules
│   │   ├── news.js                         # Public news page
│   │   ├── timetable.js                    # Timetable rendering and scheduling form
│   │   └── utils.js                        # Shared helpers (escapeHtml, bindAsyncFormSubmit)
│   ├── Pages/
│   │   ├── AdminPanel/admin.php
│   │   ├── login.php
│   │   ├── news.php
│   │   └── timetable.php
│   ├── resources/img/
│   ├── config.php                          # Base URL constant
│   └── index.php                           # Home page
│
├── storage/
│   └── images/                             # Uploaded news images (auto-created on first upload)
├── Dockerfile                              # php:8.2-apache image, builds Backend/vendor via Composer
├── docker-compose.yaml                     # Dev stack — live bind-mounted source, phpMyAdmin included
├── docker-compose.prod.yaml                # Prod stack — baked image, no live mount, no exposed DB port
├── docker-root-index.php                   # Redirect at the Apache DocumentRoot → Frontend/
├── .dockerignore
└── README.md
```

---

## Quick Start

> For a fresh XAMPP installation with default settings.

```bash
# 1 — Clone or place the project
cd C:\xampp\htdocs
# project folder: LaboratoryScheduleSystemWebsite

# 2 — Install PHP dependencies
cd LaboratoryScheduleSystemWebsite\Backend
composer install

# 3 — Create environment file
copy .env-example .env
# Edit .env with your database credentials and a strong JWT_KEY

# 4 — Seed the database (creates DB, imports schema, inserts admin + lecturer)
php scripts/run_seed.php
# *** Save the printed credentials — they are shown only once ***

# 5 — Open the app
start http://localhost/LaboratoryScheduleSystemWebsite/Frontend/
```

---

## Detailed Setup

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) with Apache and MySQL running
- PHP 8.1 or later (included in XAMPP)
- [Composer](https://getcomposer.org/)

### Step 1 — Place the project

Put the project folder inside your XAMPP `htdocs` directory:

```
D:\Software\RunApps\xampp\htdocs\LaboratoryScheduleSystemWebsite\
```

The URL rewriting in `Backend/.htaccess` is written relative to this path. If you use a different folder name, update `RewriteBase` in that file and `BASE_URL` in `Frontend/config.php`.

### Step 2 — Start XAMPP services

Open the XAMPP Control Panel and start **Apache** and **MySQL**.

### Step 3 — Install backend dependencies

```bash
cd Backend
composer install
```

This installs all packages listed in `composer.json` into `Backend/vendor/`.

### Step 4 — Configure the environment

```bash
copy Backend\.env-example Backend\.env
```

Open `Backend/.env` and fill in at minimum the four required database variables and `JWT_KEY`. See [Environment Variables](#environment-variables) for the full reference.

### Step 5 — Run the seed script

```bash
php Backend/scripts/run_seed.php
```

The script:
- Creates the database if it does not exist
- Imports the full schema from `Backend/seeds/laboratory_schedule_system.sql`
- Creates the initial admin and lecturer accounts
- Prints the generated credentials to the terminal

**Save the printed credentials before closing the window.** After a successful run, the script creates `Backend/seeds/.seed.lock` and will refuse to run again (delete the lock file to re-seed a clean database).

To pre-set passwords instead of using randomly generated ones, add `SEED_ADMIN_PASSWORD` and `SEED_LECTURER_PASSWORD` to `.env` before running the script.

### Step 6 — Open the application

```
http://localhost/LaboratoryScheduleSystemWebsite/Frontend/
```

| Page        | URL path                                              |
|-------------|-------------------------------------------------------|
| Home        | `/Frontend/`                                          |
| Timetable   | `/Frontend/Pages/timetable.php`                       |
| News        | `/Frontend/Pages/news.php`                            |
| Login       | `/Frontend/Pages/login.php`                           |
| Admin Panel | `/Frontend/Pages/AdminPanel/admin.php`                |

---

## XAMPP Configuration

These one-time changes to your XAMPP installation are required before the application will run natively (skip this section entirely if you're using [Docker Setup](#docker-setup) instead — `mod_rewrite` and the PHP extensions below are already baked into the container image). Apply them once, restart Apache, and you will not need to touch them again.

### Apache — enable mod_rewrite and AllowOverride

The backend routes every request through `Backend/.htaccess` using Apache's `mod_rewrite`. Two settings must be active.

**1. Enable mod_rewrite**

Open `C:\xampp\apache\conf\httpd.conf` and find the rewrite module line. Remove the leading `#` so it reads:

```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

**2. Allow .htaccess overrides in htdocs**

In the same file, find the `<Directory>` block for `htdocs` and change `AllowOverride None` to `AllowOverride All`:

```apache
<Directory "C:/xampp/htdocs">
    Options Indexes FollowSymLinks Includes ExecCGI
    AllowOverride All
    Require all granted
</Directory>
```

**3. Restart Apache** from the XAMPP Control Panel after saving the file.

> **Verify:** Navigate to `http://localhost/LaboratoryScheduleSystemWebsite/Backend/api/v1/user/` in a browser. You should get a JSON response (likely `{"status":"401",...}`), not a 404 or blank page. A 404 means mod_rewrite is still off or AllowOverride is still None.

---

### PHP — verify required extensions

Two extensions must be enabled in `php.ini`. XAMPP includes them but they are sometimes commented out.

Open `C:\xampp\php\php.ini` and confirm these lines are present and **not** prefixed with a semicolon:

```ini
extension=pdo_mysql    ; PDO MySQL driver — all database connections
extension=fileinfo     ; MIME-type inspection — file upload validation
```

If either line starts with `;extension=...`, remove the semicolon and restart Apache.

> **Verify:** Open `http://localhost/dashboard/phpinfo.php`, use Ctrl+F to search for `pdo_mysql` and `fileinfo`. Each should appear in its own section listing its version and status.

---

### MySQL / MariaDB — default credentials

XAMPP ships MySQL/MariaDB with a `root` account and an **empty password**. The seed script and `.env` use these defaults unless you change them.

| Setting  | XAMPP default |
|----------|---------------|
| Host     | `localhost`   |
| Port     | `3306`        |
| User     | `root`        |
| Password | _(empty — leave `DB_PASSWORD=` blank in `.env`)_ |

**To change the root password after installation:**

1. Open `http://localhost/phpmyadmin`
2. Go to **User Accounts** → click **Edit Privileges** next to `root@localhost`
3. Click the **Change password** tab, set a new password, click **Go**
4. Update `DB_PASSWORD` in `Backend/.env` to match

---

### phpMyAdmin — database management

phpMyAdmin is bundled with XAMPP and provides a full graphical interface for the MySQL database. Access it at:

```
http://localhost/phpmyadmin
```

Common tasks:

| Task | Steps in phpMyAdmin |
|------|---------------------|
| Browse the database | Left panel → click the `DB_NAME` database → click any table |
| Run a raw SQL query | Select the database → **SQL** tab → paste query → **Go** |
| Export a full backup | Select the database → **Export** → Quick → Format: SQL → **Go** |
| Import a SQL file | Select the database → **Import** → choose file → **Go** |
| Drop and recreate | Select the database → **Operations** → **Drop the database** → create a new empty one with the same name (charset `utf8mb4`, collation `utf8mb4_unicode_ci`) |
| Check table structure | Select a table → **Structure** tab |
| Inspect audit logs | Select database → table `database_modification_logs` → **Browse** |

---

## Docker Setup

An alternative to native XAMPP setup — runs the whole stack (Apache + PHP 8.2, MySQL 8.0, phpMyAdmin) in containers, with `mod_rewrite` and the PHP extensions from [XAMPP Configuration](#xampp-configuration) already baked into the image. Two Compose files are provided: a dev stack with live-reloading source, and a production stack with a baked image.

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac) or Docker Engine + the Compose plugin (Linux)
- **Port 80 free** — stop XAMPP's Apache (or anything else bound to port 80) first. `Frontend/config.php` and every `Frontend/API/*.js` file hardcode `http://localhost/...` with no port, so the app must stay on host port 80.
- **Port 3306 free** — stop any local MySQL/XAMPP MySQL instance, since the dev compose file also publishes the database on the host.

### Services (dev stack)

| Service      | Image                              | Container                 | Host port | Purpose |
|--------------|-------------------------------------|----------------------------|-----------|---------|
| `web`        | built from `Dockerfile` (`php:8.2-apache`) | `lab_schedule_web`         | 80        | Apache + PHP serving the whole app |
| `db`         | `mysql:8.0`                         | `lab_schedule_db`          | 3306      | Database |
| `phpmyadmin` | `phpmyadmin/phpmyadmin`             | `lab_schedule_phpmyadmin`  | 8081      | Browser-based DB management |

### Dev setup — `docker-compose.yaml`

```bash
# 1 — Create the Docker env file (based on .env-example, but DB_HOST must be "db")
copy Backend\.env-example Backend\.env.docker
# Edit Backend/.env.docker: set DB_HOST=db, DB_USER=labapp, DB_PASSWORD=labapppassword,
# DB_NAME=timetable_system (or match whatever you set in docker-compose.yaml's db
# service), plus a real JWT_KEY and optional SMTP_* values.

# 2 — Build and start the stack
docker compose up -d --build

# 3 — One-time database seed (creates tables + admin/lecturer accounts)
docker compose exec web php Backend/scripts/run_seed.php
# *** Save the printed credentials — they are shown only once ***
```

| Page         | URL |
|--------------|-----|
| App          | `http://localhost/LaboratoryScheduleSystemWebsite/Frontend/` |
| Bare root    | `http://localhost/` — redirects to the line above via `docker-root-index.php` |
| phpMyAdmin   | `http://localhost:8081` (login with the `db` service's `MYSQL_USER`/`MYSQL_PASSWORD`, or `root`/`MYSQL_ROOT_PASSWORD`) |

**How the dev stack is wired:**
- The entire repo is bind-mounted into the `web` container at `/var/www/html/LaboratoryScheduleSystemWebsite` — edits on the host take effect immediately, no rebuild needed for PHP/JS/CSS changes.
- Only `Backend/.env` is overlaid from `Backend/.env.docker` (read-only), so your real `Backend/.env` used by native XAMPP is never touched and the two setups can coexist on the same checkout.
- A rebuild (`docker compose up -d --build`) is only needed after changing the `Dockerfile` itself or `Backend/composer.json`.
- `docker-root-index.php` is copied to the Apache `DocumentRoot` (`/var/www/html/index.php`) separately from the app folder, because `DocumentRoot` has no index of its own — only the `LaboratoryScheduleSystemWebsite/` subfolder. Without it, visiting `http://localhost/` produces Apache's "No matching DirectoryIndex found" error.

### Production stack — `docker-compose.prod.yaml`

Unlike the dev stack, this bakes the source into the image at build time (no bind mount) and keeps the database off the host network entirely.

```bash
# 1 — Create Backend/.env.production: DB_HOST=db, DB_USER/DB_PASSWORD/DB_NAME
#     matching step 2 below, plus real JWT_KEY/SMTP secrets. Never commit it.

# 2 — Create a ".env" file next to docker-compose.prod.yaml:
#       MYSQL_ROOT_PASSWORD=...
#       MYSQL_DATABASE=...
#       MYSQL_USER=...
#       MYSQL_PASSWORD=...

# 3 — Build and start
docker compose -f docker-compose.prod.yaml up -d --build

# 4 — One-time database seed
docker compose -f docker-compose.prod.yaml exec web php Backend/scripts/run_seed.php
```

| Aspect | Dev (`docker-compose.yaml`) | Prod (`docker-compose.prod.yaml`) |
|--------|------------------------------|-------------------------------------|
| Source code | Live bind-mounted from host | Baked into the image at build time |
| Env file | `Backend/.env.docker` | `Backend/.env.production` |
| Database port on host | Yes — `3306:3306` | No — reachable only from `web` over the internal Compose network |
| `storage/` and `Backend/logs/` | Bind-mounted from host | Named volumes (`storage_data`, `logs_data`) |
| Restart policy | `unless-stopped` | `always` |
| phpMyAdmin | Included | Not included — use a tunnel or a one-off `mysql` client if needed |

> **Domain caveat:** `Frontend/config.php` and every `Frontend/API/*.js` file hardcode `http://localhost/...`. The production stack works as-is when accessed as `localhost` on the server itself, but deploying behind a real domain requires updating those hardcoded URLs first.

---

## Environment Variables

All runtime configuration is in `Backend/.env`. Use `Backend/.env-example` as the starting template.

### Application

| Variable          | Required | Default  | Description |
|-------------------|----------|----------|-------------|
| `APP_ENV`         | No       | `local`  | Set to `production` on an HTTPS server to enable the `Secure` flag on the auth cookie. |
| `ALLOWED_ORIGINS` | No       | _(none)_ | Comma-separated list of origins the API will reflect in `Access-Control-Allow-Origin`. Leave empty to block all cross-origin requests. |

### Database

| Variable      | Required | Description |
|---------------|----------|-------------|
| `DB_HOST`     | Yes      | MySQL server host. Usually `localhost` in XAMPP. |
| `DB_USER`     | Yes      | MySQL username. Default XAMPP value is `root`. |
| `DB_PASSWORD` | Yes      | MySQL password. Leave empty if your local MySQL user has no password. |
| `DB_NAME`     | Yes      | Name of the database the application will create and use. |

### Authentication

| Variable  | Required | Description |
|-----------|----------|-------------|
| `JWT_KEY` | Yes      | Secret key used to sign and verify JWT tokens. Use at least 32 random characters. |
| `DOMAIN`  | Yes      | Domain embedded in JWT issuer / audience claims. Usually `localhost` in development. |

### Email (SMTP) — all optional

When `SMTP_HOST` or `SMTP_FROM_EMAIL` is not configured, email notifications are silently skipped. Every other feature works normally.

| Variable          | Description | Example |
|-------------------|-------------|---------|
| `SMTP_HOST`       | SMTP server hostname. | `smtp.gmail.com` |
| `SMTP_PORT`       | SMTP port. | `587` |
| `SMTP_USERNAME`   | SMTP login username. | `you@example.com` |
| `SMTP_PASSWORD`   | SMTP password or app-specific password. | `your-smtp-password` |
| `SMTP_ENCRYPTION` | Encryption type: `tls` or `ssl`. | `tls` |
| `SMTP_AUTH`       | Enable SMTP authentication. | `true` |
| `SMTP_FROM_EMAIL` | Sender address on outgoing emails. | `no-reply@example.com` |
| `SMTP_FROM_NAME`  | Sender display name on outgoing emails. | `Laboratory Schedule System` |

**Gmail tip:** Enable 2-Step Verification and create an [App Password](https://myaccount.google.com/apppasswords). Use the App Password as `SMTP_PASSWORD`.

### Seed Passwords — optional

| Variable                 | Description |
|--------------------------|-------------|
| `SEED_ADMIN_PASSWORD`    | Password for the initial admin account. If not set, a random password is generated and printed to the terminal. |
| `SEED_LECTURER_PASSWORD` | Password for the initial lecturer account. Same behaviour as above. |

---

## Seed Accounts

The seed script creates two accounts:

| Role     | Email                       | Password |
|----------|-----------------------------|----------|
| Admin    | `admin@laboratory.local`    | Printed to terminal on first run, or set via `SEED_ADMIN_PASSWORD` |
| Lecturer | `lecturer@laboratory.local` | Printed to terminal on first run, or set via `SEED_LECTURER_PASSWORD` |

No default passwords are committed to the repository.

---

## API Reference

Base URL: `http://localhost/LaboratoryScheduleSystemWebsite/Backend/api/v1`

All endpoints return JSON with a `status` field (`"200"`, `"400"`, `"401"`, `"403"`, `"500"`).

**Auth levels:**
- 🔓 **Public** — no authentication needed
- 🔑 **Authenticated** — valid JWT cookie required
- 🛡️ **Admin** — valid JWT cookie + `role = admin` required

---

### Users — `/user`

| Method | Path              | Auth | Description |
|--------|-------------------|------|-------------|
| GET    | `/`               | 🛡️  | List all users (password hash excluded) |
| POST   | `/`               | 🛡️  | Create a user |
| POST   | `/update`         | 🛡️  | Update a user |
| POST   | `/delete`         | 🛡️  | Delete a user |
| POST   | `/reset-password` | 🛡️  | Reset a user's password |
| POST   | `/login`          | 🔓  | Login — sets JWT in HttpOnly cookie |
| POST   | `/logout`         | 🔓  | Logout — clears the JWT cookie |

---

### Timetable — `/timetable`

| Method | Path                       | Auth | Description |
|--------|----------------------------|------|-------------|
| GET    | `/`                        | 🔓  | Get full permanent timetable |
| GET    | `/temporary`               | 🔓  | Get temporary timetable; supports `?date_from=YYYY-MM-DD&date_to=YYYY-MM-DD` |
| GET    | `/years`                   | 🔓  | List academic years |
| GET    | `/timeSlots`               | 🔓  | List time slot definitions |
| GET    | `/columnHeadings`          | 🔓  | List column (day) headings |
| GET    | `/lectureGroups`           | 🔓  | List lecture groups |
| GET    | `/labs`                    | 🔓  | List labs |
| GET    | `/cells`                   | 🔓  | List timetable grid cells |
| GET    | `/settings`                | 🔓  | Get current timetable settings |
| GET    | `/subjectCodes`            | 🔓  | List all subject codes with year |
| GET    | `/getByYear`               | 🔓  | Get timetable filtered by year: `?year=1st+Year` |
| POST   | `/`                        | 🛡️  | Create a timetable record |
| POST   | `/update`                  | 🛡️  | Update a timetable record |
| POST   | `/delete`                  | 🛡️  | Delete a timetable record |
| POST   | `/settings/update`         | 🛡️  | Update timetable grid settings |
| POST   | `/settings/reset`          | 🛡️  | Reset timetable grid to zero |
| POST   | `/years`                   | 🛡️  | Create a year |
| POST   | `/years/update`            | 🛡️  | Update a year |
| POST   | `/years/delete`            | 🛡️  | Delete a year |
| POST   | `/lectureGroups`           | 🛡️  | Create a lecture group |
| POST   | `/lectureGroups/update`    | 🛡️  | Update a lecture group |
| POST   | `/lectureGroups/delete`    | 🛡️  | Delete a lecture group |
| POST   | `/labs`                    | 🛡️  | Create a lab |
| POST   | `/labs/update`             | 🛡️  | Update a lab |
| POST   | `/labs/delete`             | 🛡️  | Delete a lab |
| POST   | `/columnHeadings`          | 🛡️  | Create a column heading |
| POST   | `/columnHeadings/update`   | 🛡️  | Update a column heading |
| POST   | `/columnHeadings/delete`   | 🛡️  | Delete a column heading |
| POST   | `/timeSlots`               | 🛡️  | Create a time slot |
| POST   | `/timeSlots/update`        | 🛡️  | Update a time slot |
| POST   | `/timeSlots/delete`        | 🛡️  | Delete a time slot |
| POST   | `/subjects`                | 🛡️  | Create a subject |
| POST   | `/subjects/update`         | 🛡️  | Update a subject |
| POST   | `/subjects/delete`         | 🛡️  | Delete a subject |

**Timetable response fields (per record):**

| Field              | Description |
|--------------------|-------------|
| `lecturer_name`    | Full name of the Lecturer In-Charge (`responsible_level = 1`). `null` if none assigned. |
| `other_lecturers`  | Comma-separated names of all other assigned lecturers. `null` if none. |
| `group_name`       | Lecture group name |
| `lab`              | Lab name |
| `subject`          | Subject full name |
| `subject_cord`     | Subject code |
| `year`             | Academic year |
| `action`           | `active`, `free`, `cancel`, or `temporary_lecture` |

---

### Lecturer Requests — `/lecturer-request`

| Method | Path                   | Auth | Description |
|--------|------------------------|------|-------------|
| GET    | `/`                    | 🔑  | List all lecturer requests |
| POST   | `/`                    | 🔑  | Submit a new lecturer request |
| POST   | `/check-availability`  | 🔑  | Check if a slot is available for a given date |
| POST   | `/update`              | 🛡️  | Confirm (assign lab) or cancel (provide reason) a request |
| POST   | `/delete`              | 🛡️  | Delete a request (allowed only after date passes or request is cancelled) |

---

### News — `/news`

| Method | Path       | Auth | Description |
|--------|------------|------|-------------|
| GET    | `/`        | 🔓  | List all published news items |
| GET    | `/byId`    | 🔓  | Get a single news item: `?id=<id>` |
| POST   | `/`        | 🛡️  | Create a news item (supports `multipart/form-data` for image upload) |
| POST   | `/update`  | 🛡️  | Update a news item |
| POST   | `/delete`  | 🛡️  | Delete a news item |

Uploaded images are stored in `storage/images/` and validated by both extension allowlist (`jpg`, `jpeg`, `png`, `gif`, `webp`) and MIME-type inspection.

---

### Logs — `/logs`

| Method | Path             | Auth | Description |
|--------|------------------|------|-------------|
| GET    | `/action-logs`   | 🛡️  | Paginated list of all database modification logs. Supports `?page=N&per_page=N`. Pass `per_page=0` or `per_page=all` to retrieve all records in one response. |

**Query parameters:**

| Parameter  | Default | Notes |
|------------|---------|-------|
| `page`     | `1`     | Page number (1-based) |
| `per_page` | `20`    | Records per page. Max `500`. Use `0` or `all` for unlimited. |

**Response fields:**

| Field         | Description |
|---------------|-------------|
| `logs`        | Array of log records (see below) |
| `total`       | Total number of log entries |
| `page`        | Current page |
| `per_page`    | Records per page returned |
| `total_pages` | Total pages (always `1` when `per_page=all`) |

**Log record fields:**

| Field          | Description |
|----------------|-------------|
| `log_id`       | Auto-increment primary key |
| `action_type`  | `INSERT`, `UPDATE`, or `DELETE` |
| `table_name`   | Database table that was modified |
| `old_data`     | JSON snapshot of the row **before** mutation (`null` for INSERT) |
| `new_data`     | JSON payload sent to the mutation (`null` for DELETE) |
| `changed_at`   | UTC timestamp of the action |
| `user_id`      | ID of the acting user |
| `first_name`   | Acting user's first name |
| `last_name`    | Acting user's last name |
| `email`        | Acting user's email |
| `role`         | Acting user's role (`admin` / `lecturer`) |

> Password hashes are always stripped from `old_data` and `new_data` before a user-table record is written.

---

### Lecturer Assignments — `/lecturer-assignment`

| Method | Path                        | Auth | Description |
|--------|-----------------------------|------|-------------|
| GET    | `/responsibilities`         | 🔓  | List all responsibility types |
| POST   | `/responsibilities`         | 🛡️  | Create a responsibility type |
| POST   | `/responsibilities/update`  | 🛡️  | Update a responsibility type |
| POST   | `/responsibilities/delete`  | 🛡️  | Delete a responsibility type |
| GET    | `/assignments`              | 🔓  | List all subject–lecturer assignments (with subject name, year, lecturer name, responsibility) |
| POST   | `/assignments`              | 🛡️  | Assign a lecturer to a subject |
| POST   | `/assignments/update`       | 🛡️  | Update an assignment |
| POST   | `/assignments/delete`       | 🛡️  | Remove an assignment |

**Responsibility type fields:**

| Field               | Type      | Description |
|---------------------|-----------|-------------|
| `responsibility`    | string    | Display name (e.g. "Lab In-Charge") |
| `responsible_level` | int\|null | Optional unique level number. Level 1 = Lecturer In-Charge. Only leveled responsibilities appear in the assignment form. |

---

## Database Schema

Full schema: `Backend/seeds/laboratory_schedule_system.sql`

```
users ◄──── subject_lecture_relations ────► practical_subjects
  │           │        │                          │
  │           ▼        ▼                          ▼
  │  lecturer_responsibility              subject_group_relations
  │                                                │
  │                                                ▼
  │  timetable_settings                    lecture_groups
  │  timetable_column_headings
  │  timetable_time_slots
  │  timetable_cells
  │  timetable ──────────────────────────► temporary_timetable
  │                                        lecturer_requests
  │  news ──► images
  │  years
  │  labs
  │
  └──► database_modification_logs   ← audit log for every INSERT / UPDATE / DELETE
```

| Table                       | Purpose |
|-----------------------------|---------|
| `users`                     | Admin and lecturer accounts. Password stored as bcrypt hash. |
| `timetable_settings`        | Single-row grid configuration (rows, columns, break row position). |
| `timetable_time_slots`      | Time slot definitions — slot number, start time, end time. |
| `timetable_column_headings` | Column (day) headings with display order. |
| `timetable`                 | Permanent scheduled lectures. |
| `timetable_cells`           | Grid reference points linking time slots to column headings. |
| `temporary_timetable`       | Confirmed lecturer requests; overlaid on the weekly timetable view. |
| `years`                     | Academic year labels. |
| `lecture_groups`            | Student group definitions. |
| `labs`                      | Laboratory room records. |
| `practical_subjects`        | Subjects with academic year association. |
| `subject_group_relations`   | Many-to-many: subject ↔ lecture group. |
| `lecturer_responsibility`   | Reusable responsibility types. `responsible_level` is unique; level 1 = Lecturer In-Charge. |
| `subject_lecture_relations` | Many-to-many: subject ↔ lecturer with optional responsibility. One level-1 holder per subject enforced in the frontend. |
| `lecturer_requests`         | Incoming slot requests from lecturers. |
| `news`                      | News articles. |
| `images`                    | Uploaded image metadata linked to news. |
| `database_modification_logs`| Audit log of every INSERT / UPDATE / DELETE. Stores `old_data` and `new_data` as JSON, plus the acting user ID and timestamp. Password hashes are never written here. |

---

## Database Migrations

### Initial setup — PHP seed script

The project includes a one-time setup script (`Backend/scripts/run_seed.php`) that creates the database entirely through PHP and PDO — no manual SQL import needed.

**What the script does, in order:**

1. Reads `DB_HOST`, `DB_USER`, `DB_PASSWORD`, and `DB_NAME` from `Backend/.env`
2. Opens a PDO connection to MySQL **without** a database selected
3. Executes `CREATE DATABASE IF NOT EXISTS` with charset `utf8mb4` and collation `utf8mb4_unicode_ci`
4. Imports the full schema from `Backend/seeds/laboratory_schedule_system.sql` — all tables, indexes, foreign keys, and default rows
5. Inserts the initial `admin` and `lecturer` accounts with bcrypt-hashed passwords
6. Creates `Backend/seeds/.seed.lock` to prevent accidental re-runs
7. Prints the generated credentials to the terminal (shown only once)

**Run:**

```bash
cd LaboratoryScheduleSystemWebsite
php Backend/scripts/run_seed.php
```

---

### Re-seeding a clean database

To wipe all data and start fresh with new seed credentials:

```bash
# Step 1 — Remove the lock file
del Backend\seeds\.seed.lock          # Windows CMD
# rm Backend/seeds/.seed.lock         # macOS / Linux / Git Bash

# Step 2 — Drop the database (choose one method)

# Option A — phpMyAdmin (recommended)
# Open http://localhost/phpmyadmin → select the database →
# Operations → Drop the database → confirm → create a new
# empty database with the same name (utf8mb4 / utf8mb4_unicode_ci)

# Option B — MySQL CLI
mysql -u root -e "DROP DATABASE IF EXISTS your_db_name;"
mysql -u root -e "CREATE DATABASE your_db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Step 3 — Re-run the seed script
php Backend/scripts/run_seed.php
```

> **Warning:** Dropping the database deletes all records permanently — timetable data, news articles, users, requests, and audit logs.

---

### Schema-only import via phpMyAdmin

To import only the schema (no seed users) on a server where you manage accounts manually:

1. Open `http://localhost/phpmyadmin` and create a new empty database
   - Charset: `utf8mb4`
   - Collation: `utf8mb4_unicode_ci`
2. Click the new database in the left panel
3. Go to the **Import** tab
4. Choose `Backend/seeds/laboratory_schedule_system.sql`
5. Click **Go**
6. Update `DB_NAME` in `Backend/.env` to match the new database name

---

### PDO connection details

All database access goes through `Backend/DB/dbConnection.php` using PHP's PDO layer.

| Setting | Value |
|---------|-------|
| Driver | `mysql` |
| Charset | `utf8mb4` |
| Collation | `utf8mb4_unicode_ci` |
| Emulated prepares | Disabled — real server-side prepared statements |
| Error mode | `ERRMODE_EXCEPTION` — all errors throw `PDOException` |
| LIMIT / OFFSET | Integer-interpolated directly (not bound as parameters — PDO binds them as strings which breaks MySQL with emulation disabled) |

No manual DSN configuration is required beyond the four `DB_*` variables in `Backend/.env`.

---

## Backend Libraries

| Package                   | Version | Purpose |
|---------------------------|---------|---------|
| `vlucas/phpdotenv`        | ^5.6    | Loads `.env` file into `$_ENV` |
| `firebase/php-jwt`        | ^6.11   | JWT signing (HS256) and verification |
| `respect/validation`      | ^2.4    | Centralized request-body validation rules |
| `phpmailer/phpmailer`     | ^7.0    | SMTP email delivery with HTML template support |
| `paragonie/sodium_compat` | ^2.4    | Cryptographic compatibility layer |

---

## Contributing

Contributions, bug reports, and feature suggestions are welcome.

### Getting started

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Follow the [Detailed Setup](#detailed-setup) steps on your fork
4. Make your changes and test them locally
5. Commit with a clear message describing the *why*, not just the *what*
6. Push your branch and open a Pull Request against `dev`

### Branch naming

| Type | Pattern | Example |
|------|---------|---------|
| Feature | `feature/<id>-short-description` | `feature/ft-010-lab-export` |
| Bug fix | `bug/<id>-short-description` | `bug/bt-012-timetable-overflow` |
| Documentation | `docs/<description>` | `docs/api-cleanup` |

### Code conventions

- **Backend**: one class per file, namespace `Backend\<Layer>`, PDO prepared statements only — no raw string concatenation in queries
- **Frontend**: `escapeHtml()` from `utils.js` must wrap every database-sourced value rendered into `innerHTML`
- **Audit fields**: `created_by`, `updated_by`, `assigned_by` are always injected in the controller from the JWT — never accepted from the client
- Validation rules go in `Backend/middleware/validation.php`; no validation logic belongs in controllers or services

### Running a quick sanity check

```bash
# Verify PHP syntax across the Backend
find Backend -name "*.php" -exec php -l {} \;
```

---

## Troubleshooting

### Blank page or 404 on API calls

- Confirm Apache is running in XAMPP
- Verify `mod_rewrite` is enabled in `httpd.conf` (`LoadModule rewrite_module`)
- Confirm `AllowOverride All` is set for the `htdocs` directory in Apache config

### "No token found" on every request

- The frontend expects the JWT in an HttpOnly cookie named `token`
- Cookies set with `SameSite=Lax` require the frontend and backend to share the same origin
- If you changed the project folder name, update `ALLOWED_ORIGINS` in `.env` and `BASE_URL` in each `Frontend/API/*.js` file

### Seed script fails with "Access denied"

- Confirm `DB_USER` and `DB_PASSWORD` in `.env` match your MySQL credentials
- Default XAMPP MySQL has user `root` with an empty password — leave `DB_PASSWORD=` blank

### Seed script says "already executed"

- The lock file `Backend/seeds/.seed.lock` exists from a previous run
- Delete it to allow re-seeding: `del Backend\seeds\.seed.lock`
- **Warning:** re-seeding on an existing database will try to re-insert the seed users

### Email notifications not arriving

- Check that `SMTP_HOST` and `SMTP_FROM_EMAIL` are both set in `.env`
- Test SMTP credentials with a tool like [Mailtrap](https://mailtrap.io) before pointing at a live mail server
- For Gmail, use an [App Password](https://myaccount.google.com/apppasswords) (not your account password)
- Check your spam folder

### Image upload fails

- The `storage/images/` directory is created automatically on first upload, but Apache must have write permission to the project root
- On Windows XAMPP, permissions are generally open; on Linux, run `chmod -R 775 storage/`

### Docker: `docker compose up` fails with a port already in use

- Port 80: stop XAMPP's Apache (or any other service bound to 80) — the app requires host port 80 because Frontend URLs are hardcoded without a port
- Port 3306: stop any local MySQL/XAMPP MySQL instance — the dev compose file also publishes the database on the host

### Docker: "localhost sent an invalid response" when opening `http://localhost:3306/`

- This is expected — port 3306 speaks the raw MySQL protocol, not HTTP, so no browser can open it directly
- Use phpMyAdmin instead: `http://localhost:8081`

### Docker: blank page or "No matching DirectoryIndex found" at `http://localhost/`

- Confirm `docker-root-index.php` was copied to the image (`docker compose up -d --build` after any `Dockerfile` change) — it redirects the bare Apache `DocumentRoot` to `Frontend/`
- Navigate directly to `http://localhost/LaboratoryScheduleSystemWebsite/Frontend/` if the redirect isn't picked up

---

## Security

The following controls are in place:

| Concern | Implementation |
|---------|----------------|
| SQL injection | PDO prepared statements with named parameters throughout all services |
| XSS | `escapeHtml()` applied to all database-sourced values rendered into `innerHTML` |
| CSRF | HttpOnly + SameSite=Lax cookie prevents cross-site request forgery for same-origin deployments |
| Authentication bypass | `validateToken` verifies JWT signature on every protected route |
| Privilege escalation | `requireRole('admin')` middleware runs after token verification |
| File upload | Extension allowlist + MIME-type inspection (`finfo`) before `move_uploaded_file` |
| Password storage | bcrypt via PHP `password_hash(PASSWORD_DEFAULT)` |
| Audit trail | `created_by` / `updated_by` / `assigned_by` injected server-side from JWT; full before/after snapshots recorded in `database_modification_logs` for every mutation |
| CORS | Reflected only when request `Origin` matches `ALLOWED_ORIGINS` allowlist |
| Sensitive data | `password` column excluded from all public-facing user list queries; password hashes stripped before any user-table row is written to `database_modification_logs` |
| Log file access | `Backend/logs/.htaccess` denies all direct browser access to every `*.log` file in the directory |

To report a security vulnerability, please email [lahirulakmina1999@gmail.com](mailto:lahirulakmina1999@gmail.com) directly rather than opening a public issue.

---

## Logging System

The system has two complementary logging layers that operate independently.

### File-based logs

Runtime events (errors, warnings, informational messages) are written to flat log files by the `Backend\Utils\Logger` static class. A **new file is created per day, per level** — logs naturally rotate at midnight with no cleanup job required.

| File pattern                          | Written when |
|----------------------------------------|--------------|
| `Backend/logs/error YYYY-MM-DD.log`   | Unhandled exceptions, DB failures, auth errors |
| `Backend/logs/warning YYYY-MM-DD.log` | Non-critical anomalies (e.g. missing optional config) |
| `Backend/logs/info YYYY-MM-DD.log`    | Informational lifecycle events |

Example: an error logged on 2026-06-21 is written to `Backend/logs/error 2026-06-21.log`; the next day's errors go to a new `error 2026-06-22.log`.

Each line follows the format:

```
[YYYY-MM-DD HH:MM:SS] [level] message | {"context":"key"}
```

The `Backend/logs/` directory is created automatically on first write. A `.htaccess` file inside it denies all direct HTTP access.

### Database audit log

Every state-changing operation (INSERT, UPDATE, DELETE) across all five resource controllers is recorded to `database_modification_logs`.

**How it works — fetch-before-modify pattern:**

```
1. Controller fetches the current row from the DB (old_data)
2. Controller calls the service to mutate the row
3. Controller calls LogsService::logAction() with old_data and new_data
```

This guarantees `old_data` always reflects the actual pre-mutation state, not just the values that happened to be in the incoming request.

**Tables covered:**

| Controller | Tables logged |
|------------|---------------|
| `timetable_controller.php` | `timetable`, `years`, `labs`, `lecture_groups`, `timetable_column_headings`, `timetable_time_slots`, `practical_subjects` |
| `news_controller.php` | `news` |
| `lecturer_requests_controller.php` | `lecturer_requests` |
| `users_controller.php` | `users` |
| `lecturer_assignments_controller.php` | `lecturer_responsibility`, `subject_lecture_relations` |

**Password safety:** For the `users` table, the `password` field is removed from both `old_data` and `new_data` before they are passed to `logAction()`. Password hashes never appear in `database_modification_logs`.

**Failure isolation:** `logAction()` wraps its DB insert in a try/catch. If the audit log write fails it falls back to `Logger::error()` and returns silently — a logging failure never breaks the main request.

**Admin UI:** The Action Logs section in the admin panel fetches records from `GET /api/v1/logs/action-logs` (admin-only endpoint). Records are displayed in a paginated table with a record-count selector (10 / 20 / 50 / 100 / All). Clicking a row opens a detail modal showing the full `old_data` and `new_data` JSON side-by-side.

---

## Author

**Lahiru Lakmina**

- Email: [lahirulakmina1999@gmail.com](mailto:lahirulakmina1999@gmail.com)
- GitHub: [HALLakmina](https://github.com/HALLakmina)
