# Laboratory Schedule System Website

A PHP and JavaScript web application for managing laboratory schedules, lecturer requests, temporary timetable changes, news updates, and administration tasks for a faculty or department.

## Stack

| Layer    | Technology                                            |
|----------|-------------------------------------------------------|
| Server   | XAMPP (Apache + MySQL / MariaDB)                      |
| Backend  | PHP 8+, Composer                                      |
| Frontend | PHP templates, Vanilla JavaScript, Tailwind CSS (CDN) |
| Database | MySQL / MariaDB                                       |

---

## Overview

**Lecturers** can:
- view the permanent weekly timetable and filter it by week (current week + up to 3 weeks ahead)
- view temporary timetable changes for any visible week
- submit requests for extra lecture slots
- check slot availability before submitting a request
- receive email notifications when a request is confirmed or cancelled

**Admins** can:
- review incoming lecturer requests, check slot availability, confirm (with lab assignment) or cancel (with a mandatory reason)
- manage the full timetable structure — settings, column headings, time slots, cells
- assign lecturers to practical subjects and define their responsibility type (e.g. Lab In-Charge, Demonstrator)
- manage reference data — years, lecture groups, labs, subjects, lecturer responsibility types
- manage user accounts (create, update, delete, reset passwords)
- publish and manage news items with optional image uploads

---

## Features

### Public Timetable
- dynamic timetable grid built from database configuration (columns, rows, break row all configurable)
- week-by-week filter — current week + up to 3 future weeks
- temporary timetable overlay showing confirmed lecturer requests for the selected week
- timetable slot summary with lecture codes
- lab allocation modal for each time slot
- lab list modal showing the lecture schedule for a selected lab
- lecturer request form (shown to logged-in users when a slot is available)
- submit-locking prevents duplicate form submissions

### Lecturer Request Flow
- lecturers submit requests with: subject, year, group, day, time slot, date, description
- slot availability check before submission prevents double-booking
- email notification sent to all admins when a new request arrives
- admins confirm (assign a lab) or cancel (provide a mandatory cancel reason)
- email notification sent to the requesting lecturer on confirmation or cancellation
- confirmed requests are written to `temporary_timetable` and appear in the weekly view

### Lecturer Assignments
- admins define responsibility types (e.g. Lab In-Charge, Demonstrator, Assistant)
- admins assign lecturers to practical subjects with an optional responsibility type
- assignments are stored in `subject_lecture_relations` linked to `lecturer_responsibility`
- both sections are fully manageable from the admin panel (create, update, delete)

### Admin Panel
- overview dashboard with system statistics
- timetable settings management (rows, columns, break row)
- timetable CRUD — manage the permanent lecture schedule
- incoming lecturer requests with confirm / cancel workflow
- news management with image upload support
- years, groups, labs, subjects CRUD
- user management — create, update, delete, reset passwords
- Responsibilities — manage lecturer responsibility type definitions
- Lecturer Assignments — assign lecturers to subjects with responsibility

### Email Notifications
- powered by `phpmailer/phpmailer`
- configured via SMTP environment variables (graceful fallback when not configured)
- templates:
  - new request → all admins
  - confirmed / cancelled → requesting lecturer (includes cancel reason when cancelled)

### Authentication & Authorization
- login issues a signed JWT stored in an **HttpOnly** cookie
- `validateToken` middleware verifies the JWT on every protected route
- `requireRole('admin')` middleware guards all admin-only state-changing routes
- `created_by` / `updated_by` / `assigned_by` audit fields are populated automatically from the JWT — clients never send these values
- public GET routes (timetable, news, responsibilities, assignments) require no authentication

---

## Project Structure

```text
LaboratoryScheduleSystemWebsite/
├── Backend/
│   ├── controllers/
│   │   ├── lecturer_assignments_controller.php   ← new
│   │   ├── lecturer_requests_controller.php
│   │   ├── news_controller.php
│   │   ├── timetable_controller.php
│   │   └── users_controller.php
│   ├── middleware/
│   │   ├── jwtToken.php          # validateToken + requireRole('admin')
│   │   └── validation.php        # Respect\Validation rules for all endpoints
│   ├── routers/
│   │   ├── lecturer_assignments_router.php
│   │   ├── lecturer_requests_router.php
│   │   ├── news_router.php
│   │   ├── timetable_router.php
│   │   └── users_router.php
│   ├── services/
│   │   ├── lecturer_assignments_service.php
│   │   ├── lecturer_requests_service.php
│   │   ├── news_service.php
│   │   ├── timetable_service.php
│   │   └── users_service.php
│   ├── templates/
│   │   ├── admin_lecturer_request_email_template.php
│   │   └── lecturer_request_status_email_template.php
│   ├── seeds/
│   │   ├── laboratory_schedule_system.sql   # full DB schema
│   │   └── users_seed.php
│   ├── scripts/
│   │   └── run_seed.php          # one-time setup CLI script
│   ├── utils/
│   │   ├── route.php
│   │   ├── httpOnlyCookie.php
│   │   └── database_seed.php
│   ├── DB/
│   │   └── dbConnection.php
│   ├── server.php                # API entry point + CORS headers
│   ├── .htaccess
│   ├── .env                      # not in repo
│   └── .env-example
├── Frontend/
│   ├── API/
│   │   ├── lecturerAssignmentsApi.js
│   │   ├── lecturerRequestApi.js
│   │   ├── newsApi.js
│   │   ├── timetableApi.js
│   │   └── userApi.js
│   ├── Components/
│   │   ├── NavigationBar.php
│   │   └── FooterBar.php
│   ├── JS/
│   │   ├── admin.js
│   │   ├── login.js
│   │   ├── loginUser.js
│   │   ├── main.js
│   │   ├── news.js
│   │   ├── timetable.js
│   │   └── utils.js
│   ├── Pages/
│   │   ├── AdminPanel/admin.php
│   │   ├── login.php
│   │   ├── news.php
│   │   └── timetable.php
│   ├── resources/img/
│   ├── config.php
│   └── index.php
├── storage/
│   └── images/                   # uploaded news images (auto-created)
├── database_migration_lecturer_requests.sql
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

```
D:\Software\RunApps\xampp\htdocs\LaboratoryScheduleSystemWebsite
```

### 2. Start Apache and MySQL in the XAMPP Control Panel

### 3. Install backend dependencies

```bash
cd Backend
composer install
```

### 4. Create the environment file

```bash
copy Backend\.env-example Backend\.env
```

Edit `Backend/.env` — see the full environment variable reference below.

### 5. Run the one-time seed script

```bash
php Backend/scripts/run_seed.php
```

This script:
- creates the database if it does not exist
- imports the full schema from `Backend/seeds/laboratory_schedule_system.sql`
- inserts the initial admin and lecturer accounts

**The terminal output shows the generated credentials — save them before closing the window.**

After a successful run it creates `Backend/seeds/.seed.lock`. The script refuses to run again while this file exists (delete it to re-seed on a fresh database).

### 6. Open the application

```
http://localhost/LaboratoryScheduleSystemWebsite/Frontend/
```

---

## Environment Variables

All configuration goes in `Backend/.env`. Use `Backend/.env-example` as the template.

### Application

| Variable          | Required | Description                                                         | Example              |
|-------------------|----------|---------------------------------------------------------------------|----------------------|
| `APP_ENV`         | No       | Set to `production` to enable secure cookies on HTTPS deployments. | `local`              |
| `ALLOWED_ORIGINS` | No       | Comma-separated list of origins the API reflects in CORS headers.  | `http://localhost`   |

### Database

| Variable      | Required | Description                                   | Example                         |
|---------------|----------|-----------------------------------------------|---------------------------------|
| `DB_HOST`     | Yes      | MySQL host — usually `localhost` in XAMPP.    | `localhost`                     |
| `DB_USER`     | Yes      | MySQL username.                               | `root`                          |
| `DB_PASSWORD` | Yes      | MySQL password. Leave empty if none.          | `your-database-password`        |
| `DB_NAME`     | Yes      | Database name the application will create.   | `laboratory_schedule_system`    |

### Authentication

| Variable  | Required | Description                                                             | Example           |
|-----------|----------|-------------------------------------------------------------------------|-------------------|
| `JWT_KEY` | Yes      | Secret key used to sign and verify JWT tokens. Use a long random value. | `your-secret-key` |
| `DOMAIN`  | Yes      | Domain used in JWT issuer / audience claims.                            | `localhost`       |

### Email (SMTP) — all optional

When `SMTP_HOST` or `SMTP_FROM_EMAIL` is not set, email notifications are silently skipped. The rest of the system works normally.

| Variable          | Description                                    | Example                        |
|-------------------|------------------------------------------------|--------------------------------|
| `SMTP_HOST`       | SMTP server hostname.                          | `smtp.gmail.com`               |
| `SMTP_PORT`       | SMTP port.                                     | `587`                          |
| `SMTP_USERNAME`   | SMTP login username.                           | `you@example.com`              |
| `SMTP_PASSWORD`   | SMTP login password or app-specific password.  | `your-smtp-password`           |
| `SMTP_ENCRYPTION` | `tls` or `ssl`.                               | `tls`                          |
| `SMTP_AUTH`       | Whether SMTP authentication is required.       | `true`                         |
| `SMTP_FROM_EMAIL` | Sender address shown in outgoing emails.       | `no-reply@example.com`         |
| `SMTP_FROM_NAME`  | Sender display name.                           | `Laboratory Schedule System`   |

### Seed passwords — optional

Set these before running the seed to use your own passwords. If not set, the seed generates random passwords and prints them to the terminal.

| Variable                | Description                              |
|-------------------------|------------------------------------------|
| `SEED_ADMIN_PASSWORD`   | Initial password for the admin account.  |
| `SEED_LECTURER_PASSWORD`| Initial password for the lecturer account.|

---

## Seed Accounts

| Role     | Email                       | Password                                                              |
|----------|-----------------------------|-----------------------------------------------------------------------|
| Admin    | `admin@laboratory.local`    | Printed to terminal on first run, or `SEED_ADMIN_PASSWORD` from `.env` |
| Lecturer | `lecturer@laboratory.local` | Printed to terminal on first run, or `SEED_LECTURER_PASSWORD` from `.env` |

No default passwords are committed to the repository.

---

## API Endpoints

Base: `/LaboratoryScheduleSystemWebsite/Backend/api/v1`

**Auth key** — 🔓 public · 🔑 authenticated · 🛡️ admin only

### Users `/user`

| Method | Path              | Auth | Description                   |
|--------|-------------------|------|-------------------------------|
| GET    | `/`               | 🔑  | List all users                |
| POST   | `/`               | 🛡️  | Create a user                 |
| POST   | `/update`         | 🛡️  | Update a user                 |
| POST   | `/delete`         | 🛡️  | Delete a user                 |
| POST   | `/reset-password` | 🛡️  | Reset a user's password       |
| POST   | `/login`          | 🔓  | Login — returns JWT cookie    |
| POST   | `/logout`         | 🔓  | Logout — clears JWT cookie    |

### Timetable `/timetable`

| Method | Path                     | Auth | Description                                 |
|--------|--------------------------|------|---------------------------------------------|
| GET    | `/`                      | 🔓  | Get full timetable                          |
| GET    | `/temporary`             | 🔓  | Get temporary timetable (date range filter) |
| GET    | `/years`                 | 🔓  | List years                                  |
| GET    | `/timeSlots`             | 🔓  | List time slots                             |
| GET    | `/columnHeadings`        | 🔓  | List column headings                        |
| GET    | `/lectureGroups`         | 🔓  | List lecture groups                         |
| GET    | `/labs`                  | 🔓  | List labs                                   |
| GET    | `/cells`                 | 🔓  | List timetable cells                        |
| GET    | `/settings`              | 🔓  | Get timetable settings                      |
| GET    | `/subjectCodes`          | 🔓  | List subject codes                          |
| POST   | `/`                      | 🛡️  | Create timetable record                     |
| POST   | `/update`                | 🛡️  | Update timetable record                     |
| POST   | `/delete`                | 🛡️  | Delete timetable record                     |
| POST   | `/settings/update`       | 🛡️  | Update timetable settings                   |
| POST   | `/settings/reset`        | 🛡️  | Reset timetable settings                    |
| POST   | `/years`                 | 🛡️  | Create year                                 |
| POST   | `/years/update`          | 🛡️  | Update year                                 |
| POST   | `/years/delete`          | 🛡️  | Delete year                                 |
| POST   | `/lectureGroups`         | 🛡️  | Create group                                |
| POST   | `/lectureGroups/update`  | 🛡️  | Update group                                |
| POST   | `/lectureGroups/delete`  | 🛡️  | Delete group                                |
| POST   | `/labs`                  | 🛡️  | Create lab                                  |
| POST   | `/labs/update`           | 🛡️  | Update lab                                  |
| POST   | `/labs/delete`           | 🛡️  | Delete lab                                  |
| POST   | `/columnHeadings`        | 🛡️  | Create column heading                       |
| POST   | `/columnHeadings/update` | 🛡️  | Update column heading                       |
| POST   | `/columnHeadings/delete` | 🛡️  | Delete column heading                       |
| POST   | `/timeSlots`             | 🛡️  | Create time slot                            |
| POST   | `/timeSlots/update`      | 🛡️  | Update time slot                            |
| POST   | `/timeSlots/delete`      | 🛡️  | Delete time slot                            |
| POST   | `/subjects`              | 🛡️  | Create subject                              |
| POST   | `/subjects/update`       | 🛡️  | Update subject                              |
| POST   | `/subjects/delete`       | 🛡️  | Delete subject                              |

### Lecturer Requests `/lecturer-request`

| Method | Path                  | Auth | Description                               |
|--------|-----------------------|------|-------------------------------------------|
| GET    | `/`                   | 🔑  | List all lecturer requests                |
| POST   | `/`                   | 🔑  | Submit a new lecturer request             |
| POST   | `/check-availability` | 🔑  | Check slot availability for a date        |
| POST   | `/update`             | 🛡️  | Confirm or cancel a request (admin)       |
| POST   | `/delete`             | 🛡️  | Delete a request (admin)                  |

### News `/news`

| Method | Path      | Auth | Description          |
|--------|-----------|------|----------------------|
| GET    | `/`       | 🔓  | List all news items  |
| GET    | `/byId`   | 🔓  | Get a single news item |
| POST   | `/`       | 🛡️  | Create a news item   |
| POST   | `/update` | 🛡️  | Update a news item   |
| POST   | `/delete` | 🛡️  | Delete a news item   |

### Lecturer Assignments `/lecturer-assignment` *(new)*

| Method | Path                      | Auth | Description                                           |
|--------|---------------------------|------|-------------------------------------------------------|
| GET    | `/responsibilities`       | 🔓  | List all responsibility types                         |
| POST   | `/responsibilities`       | 🛡️  | Create a responsibility type                          |
| POST   | `/responsibilities/update`| 🛡️  | Update a responsibility type                          |
| POST   | `/responsibilities/delete`| 🛡️  | Delete a responsibility type                          |
| GET    | `/assignments`            | 🔓  | List all subject–lecturer assignments (with joins)    |
| POST   | `/assignments`            | 🛡️  | Assign a lecturer to a subject                        |
| POST   | `/assignments/update`     | 🛡️  | Update an assignment                                  |
| POST   | `/assignments/delete`     | 🛡️  | Remove an assignment                                  |

---

## Database Schema

Full schema: `Backend/seeds/laboratory_schedule_system.sql`

| Table                       | Purpose                                                     |
|-----------------------------|-------------------------------------------------------------|
| `users`                     | Admin and lecturer accounts                                 |
| `timetable_settings`        | Grid dimensions (rows, columns, break row)                  |
| `timetable_time_slots`      | Time slot definitions                                       |
| `timetable_column_headings` | Column (day) headings                                       |
| `timetable`                 | Permanent timetable records                                 |
| `timetable_cells`           | Grid cell reference points                                  |
| `temporary_timetable`       | Confirmed lecturer requests shown in the weekly view        |
| `years`                     | Academic years                                              |
| `lecture_groups`            | Student groups                                              |
| `labs`                      | Laboratory rooms                                            |
| `practical_subjects`        | Subjects with year associations                             |
| `subject_group_relations`   | Subject ↔ group assignments                                 |
| `lecturer_responsibility`   | Responsibility type definitions (Lab In-Charge, etc.)       |
| `subject_lecture_relations` | Subject ↔ lecturer assignments with optional responsibility |
| `lecturer_requests`         | Incoming lecturer slot requests                             |
| `news`                      | News items                                                  |
| `images`                    | Uploaded images linked to news items                        |

### Database Migration

If upgrading an existing installation that predates the lecturer request enhancements, apply:

```bash
mysql -u root -p < database_migration_lecturer_requests.sql
```

---

## Backend Libraries

| Package                   | Version | Purpose                            |
|---------------------------|---------|------------------------------------|
| `vlucas/phpdotenv`        | ^5.6    | Environment variable loading       |
| `firebase/php-jwt`        | ^6.11   | JWT creation and verification      |
| `respect/validation`      | ^2.4    | Request body validation middleware |
| `phpmailer/phpmailer`     | ^7.0    | SMTP email notifications           |
| `paragonie/sodium_compat` | ^2.4    | Cryptographic compatibility        |

---

## Pages

| Page        | Path                                  |
|-------------|---------------------------------------|
| Home        | `Frontend/index.php`                  |
| Timetable   | `Frontend/Pages/timetable.php`        |
| News        | `Frontend/Pages/news.php`             |
| Login       | `Frontend/Pages/login.php`            |
| Admin Panel | `Frontend/Pages/AdminPanel/admin.php` |

---

## Notes

- The timetable grid is fully database-driven — dimensions, headings, and time slots are all configurable from the admin panel without any code changes.
- Temporary timetable records are keyed by date, so confirmed lecturer requests appear only on the day they are scheduled.
- Admin cancellations require a cancel reason (`admin_message`), which is included in the notification email sent to the requesting lecturer.
- Audit fields (`created_by`, `updated_by`, `assigned_by`) are populated automatically from the authenticated user's JWT token. Clients never send these values.
- The `lecturer_responsibility` table stores reusable responsibility labels. Deleting a responsibility sets `responsibility_id` to `NULL` on related assignments (FK `ON DELETE SET NULL`) — existing assignments are not removed.
- News images are stored in `storage/images/` at the project root. This directory is created automatically on the first upload.
- The seed script is locked after its first successful run. Delete `Backend/seeds/.seed.lock` to allow re-seeding on a fresh database.

---

## Author

- **Lahiru Lakmina**
- Email: `lahirulakmina1999@gmail.com`
- GitHub: [HALLakmina](https://github.com/HALLakmina)
