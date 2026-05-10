# Laboratory Schedule System Website

A PHP and JavaScript web application for managing laboratory schedules, lecturer requests, temporary timetable changes, news updates, and administration tasks for a faculty or department.

## Stack

| Layer     | Technology                                             |
|-----------|--------------------------------------------------------|
| Server    | XAMPP (Apache + MySQL)                                 |
| Backend   | PHP 8+, Composer                                       |
| Frontend  | PHP templates, Vanilla JavaScript, Tailwind CSS (CDN)  |
| Database  | MySQL / MariaDB                                        |

---

## Overview

The system helps lecturers and administrators coordinate laboratory usage.

**Lecturers** can:
- view the permanent weekly timetable and filter it by week (current week + up to 3 weeks ahead)
- view temporary timetable changes for any visible week
- submit requests for extra lecture slots
- check slot availability before submitting a request
- receive email notifications when a request is confirmed or cancelled

**Admins** can:
- review incoming lecturer requests and check slot availability
- confirm a request (assign a lab) or cancel it (with a mandatory cancel reason)
- manage the full timetable structure (settings, column headings, time slots, timetable cells)
- manage reference data (years, groups, labs, subjects)
- manage users (create, update, delete, reset passwords)
- publish and manage news items with optional image uploads

---

## Features

### Public Timetable
- dynamic timetable built entirely from database configuration
- week-by-week filter — view the current week and up to 3 future weeks
- temporary timetable overlay showing confirmed lecturer requests for the selected week
- timetable slot summary with lecture codes
- lab allocation modal for each time slot
- lab list modal showing the full lecture schedule for a selected lab
- lecturer request form for logged-in users (only shown when a slot is available)

### Lecturer Request Flow
- lecturers submit requests with: subject, year, group, day, time slot, date, description
- availability check prevents double-booking before submission
- loading indicator and submit-lock prevent duplicate submissions
- admins review all incoming requests from the admin panel
- admins assign a lab and confirm, or provide a cancel reason and cancel
- **email notifications** sent to the lecturer on confirmation or cancellation
- **email notifications** sent to all admins when a new request arrives
- confirmed requests are stored in `temporary_timetable` and appear in the timetable view

### Admin Panel
- overview dashboard with system statistics
- timetable settings (rows, columns, break row)
- timetable CRUD — manage the permanent lecture schedule
- incoming lecturer requests with confirm / cancel workflow
- news management with image upload support
- years, groups, labs, subjects CRUD
- user management with role assignment and password reset

### Email Notifications
- powered by PHPMailer (`phpmailer/phpmailer`)
- configured via SMTP environment variables (see below)
- email templates for:
  - new lecturer request → sent to all admins
  - request confirmed / cancelled → sent to the requesting lecturer
- graceful fallback: notifications are skipped silently when SMTP is not configured

### Authentication & Authorization
- login issues a JWT stored in an **HttpOnly** cookie (not accessible to JavaScript)
- `validateToken` middleware verifies the JWT on every protected route
- `requireRole('admin')` middleware guards all admin-only state-changing routes
- public GET routes (timetable view, news list) require no authentication
- lecturer routes (submit request, check availability) require authentication
- admin routes (confirm/cancel request, all CRUD) require both authentication and admin role

---

## Project Structure

```text
LaboratoryScheduleSystemWebsite/
├── Backend/
│   ├── controllers/            # Request handlers (thin — delegate to services)
│   ├── middleware/             # JWT validation, role check, request validation
│   │   ├── jwtToken.php        # createJwtToken, validateToken, requireRole
│   │   └── validation.php      # Respect\Validation rules for every endpoint
│   ├── routers/                # Route registration and middleware chain wiring
│   ├── services/               # Business logic and all database queries
│   ├── templates/              # HTML email templates (PHPMailer)
│   ├── seeds/
│   │   ├── laboratory_schedule_system.sql   # Full DB schema
│   │   └── users_seed.php                   # Seed user definitions
│   ├── scripts/
│   │   └── run_seed.php        # One-time database setup CLI script
│   ├── utils/
│   │   ├── route.php           # Custom router (singleton)
│   │   ├── httpOnlyCookie.php  # Cookie helper
│   │   └── database_seed.php   # Seed orchestration logic
│   ├── DB/
│   │   └── dbConnection.php    # PDO wrapper
│   ├── server.php              # API entry point, CORS headers
│   ├── .htaccess               # URL rewriting to server.php
│   ├── .env                    # Local environment config (not in repo)
│   └── .env-example            # Template for .env
├── Frontend/
│   ├── API/                    # JS functions for every backend endpoint
│   ├── Components/             # NavigationBar, FooterBar PHP partials
│   ├── JS/                     # Page scripts (admin.js, timetable.js, etc.)
│   ├── Pages/
│   │   ├── AdminPanel/admin.php
│   │   ├── login.php
│   │   ├── news.php
│   │   └── timetable.php
│   ├── resources/img/          # Static images
│   ├── config.php              # Frontend base URL constant
│   └── index.php               # Home page
├── storage/
│   └── images/                 # Uploaded news images (created on first upload)
├── database_migration_lecturer_requests.sql   # Migration for lecturer_requests table
└── README.md
```

---

## Requirements

- XAMPP (Apache + MySQL)
- PHP 8.1 or later
- Composer

---

## Setup

### 1. Place the project in `htdocs`

```text
D:\Software\RunApps\xampp\htdocs\LaboratoryScheduleSystemWebsite
```

### 2. Start Apache and MySQL in XAMPP

### 3. Install backend dependencies

```bash
cd Backend
composer install
```

### 4. Create the environment file

```bash
copy Backend\.env-example Backend\.env
```

Edit `Backend/.env` with your local values. See the full reference below.

### 5. Run the one-time seed script

```bash
php Backend/scripts/run_seed.php
```

This script:
- creates the database if it does not exist
- imports the schema from `Backend/seeds/laboratory_schedule_system.sql`
- inserts the initial admin and lecturer accounts

**The seed prints the generated credentials to the terminal — save them before closing the window.**

After a successful run it creates `Backend/seeds/.seed.lock`, preventing the script from running again by accident.

### 6. Open the application

```
http://localhost/LaboratoryScheduleSystemWebsite/Frontend/
```

---

## Environment Variables

All runtime configuration lives in `Backend/.env`. Use `Backend/.env-example` as the template.

### Application

| Variable           | Required | Description                                              | Example            |
|--------------------|----------|----------------------------------------------------------|--------------------|
| `APP_ENV`          | No       | Runtime environment. Set to `production` on a live server to enable secure cookies. | `local`            |
| `ALLOWED_ORIGINS`  | No       | Comma-separated list of allowed CORS origins. The API reflects the request `Origin` header only when it appears in this list. | `http://localhost` |

### Database

| Variable      | Required | Description                              | Example                   |
|---------------|----------|------------------------------------------|---------------------------|
| `DB_HOST`     | Yes      | MySQL host. Usually `localhost` in XAMPP.| `localhost`               |
| `DB_USER`     | Yes      | MySQL username.                          | `root`                    |
| `DB_PASSWORD` | Yes      | MySQL password. Leave empty if none.     | `your-database-password`  |
| `DB_NAME`     | Yes      | Database name the application will use.  | `laboratory_schedule_system` |

### Authentication

| Variable  | Required | Description                                                     | Example               |
|-----------|----------|-----------------------------------------------------------------|-----------------------|
| `JWT_KEY` | Yes      | Secret key used to sign and verify JWT tokens. Use a long random string. | `your-secret-key`     |
| `DOMAIN`  | Yes      | Domain used in JWT issuer / audience claims.                    | `localhost`           |

### Email (SMTP)

All SMTP variables are optional. If `SMTP_HOST` or `SMTP_FROM_EMAIL` is empty, email notifications are silently skipped — the rest of the system works normally.

| Variable          | Description                                      | Example                        |
|-------------------|--------------------------------------------------|--------------------------------|
| `SMTP_HOST`       | SMTP server hostname.                            | `smtp.gmail.com`               |
| `SMTP_PORT`       | SMTP port.                                       | `587`                          |
| `SMTP_USERNAME`   | SMTP login username.                             | `you@example.com`              |
| `SMTP_PASSWORD`   | SMTP login password or app password.             | `your-smtp-password`           |
| `SMTP_ENCRYPTION` | Encryption type: `tls` or `ssl`.                | `tls`                          |
| `SMTP_AUTH`       | Whether SMTP authentication is required.         | `true`                         |
| `SMTP_FROM_EMAIL` | The sender address shown in outgoing emails.     | `no-reply@example.com`         |
| `SMTP_FROM_NAME`  | The sender display name.                         | `Laboratory Schedule System`   |

### Seed Passwords (optional)

If these variables are set when the seed script runs, they are used as the initial account passwords. If they are not set, the seed generates random passwords and prints them to the terminal.

| Variable               | Description                               |
|------------------------|-------------------------------------------|
| `SEED_ADMIN_PASSWORD`  | Password for the seed admin account.      |
| `SEED_LECTURER_PASSWORD` | Password for the seed lecturer account. |

---

## Seed Accounts

The seed creates two accounts defined in `Backend/seeds/users_seed.php`:

| Role     | Email                       | Password                                                   |
|----------|-----------------------------|------------------------------------------------------------|
| Admin    | `admin@laboratory.local`    | Generated on first run (printed to terminal) or `SEED_ADMIN_PASSWORD` from `.env` |
| Lecturer | `lecturer@laboratory.local` | Generated on first run (printed to terminal) or `SEED_LECTURER_PASSWORD` from `.env` |

> No default passwords are shipped in the source code. Credentials are always generated fresh or provided via environment variables.

---

## API Endpoints

All API routes are served from `Backend/server.php` via `Backend/.htaccess`.

Base path: `/LaboratoryScheduleSystemWebsite/Backend/api/v1`

### Auth requirements key

| Symbol | Meaning                        |
|--------|--------------------------------|
| 🔓     | Public — no authentication needed |
| 🔑     | Requires valid JWT token       |
| 🛡️    | Requires valid JWT + admin role |

### Users `/user`

| Method | Path              | Auth | Description                       |
|--------|-------------------|------|-----------------------------------|
| GET    | `/`               | 🔑  | List all users                    |
| POST   | `/`               | 🛡️  | Create a user                     |
| POST   | `/update`         | 🛡️  | Update a user                     |
| POST   | `/delete`         | 🛡️  | Delete a user                     |
| POST   | `/reset-password` | 🛡️  | Reset a user's password           |
| POST   | `/login`          | 🔓  | Login — returns JWT cookie        |
| POST   | `/logout`         | 🔓  | Logout — clears JWT cookie        |

### Timetable `/timetable`

| Method | Path                       | Auth | Description                            |
|--------|----------------------------|------|----------------------------------------|
| GET    | `/`                        | 🔓  | Get full timetable                     |
| GET    | `/temporary`               | 🔓  | Get temporary timetable (filterable by date range) |
| GET    | `/years`                   | 🔓  | List all years                         |
| GET    | `/timeSlots`               | 🔓  | List all time slots                    |
| GET    | `/columnHeadings`          | 🔓  | List all column headings               |
| GET    | `/lectureGroups`           | 🔓  | List all lecture groups                |
| GET    | `/labs`                    | 🔓  | List all labs                          |
| GET    | `/cells`                   | 🔓  | List timetable cells                   |
| GET    | `/settings`                | 🔓  | Get timetable settings                 |
| GET    | `/subjectCodes`            | 🔓  | List subject codes                     |
| POST   | `/`                        | 🛡️  | Create a timetable record              |
| POST   | `/update`                  | 🛡️  | Update a timetable record              |
| POST   | `/delete`                  | 🛡️  | Delete a timetable record              |
| POST   | `/settings/update`         | 🛡️  | Update timetable settings              |
| POST   | `/settings/reset`          | 🛡️  | Reset timetable settings               |
| POST   | `/years`                   | 🛡️  | Create a year                          |
| POST   | `/years/update`            | 🛡️  | Update a year                          |
| POST   | `/years/delete`            | 🛡️  | Delete a year                          |
| POST   | `/lectureGroups`           | 🛡️  | Create a lecture group                 |
| POST   | `/lectureGroups/update`    | 🛡️  | Update a lecture group                 |
| POST   | `/lectureGroups/delete`    | 🛡️  | Delete a lecture group                 |
| POST   | `/labs`                    | 🛡️  | Create a lab                           |
| POST   | `/labs/update`             | 🛡️  | Update a lab                           |
| POST   | `/labs/delete`             | 🛡️  | Delete a lab                           |
| POST   | `/columnHeadings`          | 🛡️  | Create a column heading                |
| POST   | `/columnHeadings/update`   | 🛡️  | Update a column heading                |
| POST   | `/columnHeadings/delete`   | 🛡️  | Delete a column heading                |
| POST   | `/timeSlots`               | 🛡️  | Create a time slot                     |
| POST   | `/timeSlots/update`        | 🛡️  | Update a time slot                     |
| POST   | `/timeSlots/delete`        | 🛡️  | Delete a time slot                     |
| POST   | `/subjects`                | 🛡️  | Create a subject                       |
| POST   | `/subjects/update`         | 🛡️  | Update a subject                       |
| POST   | `/subjects/delete`         | 🛡️  | Delete a subject                       |

### Lecturer Requests `/lecturer-request`

| Method | Path                   | Auth | Description                                   |
|--------|------------------------|------|-----------------------------------------------|
| GET    | `/`                    | 🔑  | List all lecturer requests                    |
| POST   | `/`                    | 🔑  | Submit a new lecturer request                 |
| POST   | `/check-availability`  | 🔑  | Check if a slot is available for a given date |
| POST   | `/update`              | 🛡️  | Confirm or cancel a request (admin)           |
| POST   | `/delete`              | 🛡️  | Delete a request (admin)                      |

### News `/news`

| Method | Path        | Auth | Description          |
|--------|-------------|------|----------------------|
| GET    | `/`         | 🔓  | List all news items  |
| GET    | `/byId`     | 🔓  | Get a single news item |
| POST   | `/`         | 🛡️  | Create a news item   |
| POST   | `/update`   | 🛡️  | Update a news item   |
| POST   | `/delete`   | 🛡️  | Delete a news item   |

---

## Database Schema

The full schema is in `Backend/seeds/laboratory_schedule_system.sql`.

Key tables:

| Table                       | Purpose                                              |
|-----------------------------|------------------------------------------------------|
| `users`                     | Registered users (admins and lecturers)              |
| `timetable_settings`        | Grid dimensions (rows, columns, break row)           |
| `timetable_time_slots`      | Time slot definitions (slot number, start, end time) |
| `timetable_column_headings` | Column (day) headings with display order             |
| `timetable`                 | Permanent timetable records                          |
| `timetable_cells`           | Grid cell reference points                           |
| `temporary_timetable`       | Confirmed lecturer requests shown in the weekly view |
| `years`                     | Academic years                                       |
| `lecture_groups`            | Student groups                                       |
| `labs`                      | Laboratory rooms                                     |
| `practical_subjects`        | Subjects with their year associations                |
| `subject_group_relations`   | Subject ↔ group assignments                          |
| `subject_lecture_relations` | Subject ↔ lecturer assignments                       |
| `lecturer_requests`         | Incoming lecturer slot requests                      |
| `news`                      | News items                                           |
| `images`                    | Uploaded images linked to news items                 |

### Database Migration

If you are upgrading an existing installation that predates the lecturer request enhancements, apply:

```bash
mysql -u root -p < database_migration_lecturer_requests.sql
```

---

## Backend Libraries

| Package                  | Version | Purpose                              |
|--------------------------|---------|--------------------------------------|
| `vlucas/phpdotenv`       | ^5.6    | Environment variable loading         |
| `firebase/php-jwt`       | ^6.11   | JWT creation and verification        |
| `respect/validation`     | ^2.4    | Request body validation middleware   |
| `phpmailer/phpmailer`    | ^7.0    | SMTP email notifications             |
| `paragonie/sodium_compat`| ^2.4    | Cryptographic compatibility layer    |

---

## Pages

| Page        | Path                                    |
|-------------|-----------------------------------------|
| Home        | `Frontend/index.php`                    |
| Timetable   | `Frontend/Pages/timetable.php`          |
| News        | `Frontend/Pages/news.php`               |
| Login       | `Frontend/Pages/login.php`              |
| Admin Panel | `Frontend/Pages/AdminPanel/admin.php`   |

---

## Notes

- The timetable grid is fully database-driven — dimensions, time slots, and column headings are all configurable from the admin panel.
- Temporary timetable records are keyed by date, so confirmed lecturer requests appear only on the day they are scheduled.
- Admin cancellations require a cancel reason (`admin_message`), which is included in the notification email sent to the lecturer.
- The `created_by` and `updated_by` audit fields on all records are populated automatically from the authenticated user's JWT — clients never send these values.
- The seed script is locked after its first successful run. To re-seed, delete `Backend/seeds/.seed.lock` first.
- News image files are stored in `storage/images/` at the project root. This directory is created automatically on the first upload.

---

## Author

- **Lahiru Lakmina**
- Email: `lahirulakmina1999@gmail.com`
- GitHub: [HALLakmina](https://github.com/HALLakmina)
