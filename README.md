# Laboratory Schedule System Website

Laboratory Schedule System Website is a PHP and JavaScript web application for managing laboratory schedules, lecturer requests, temporary timetable changes, news updates, and administration tasks for a faculty or department.

The project runs on:
- `XAMPP Apache`
- `MySQL`
- `PHP`
- `Vanilla JavaScript`

## Overview

This system helps lecturers and administrators coordinate laboratory usage more clearly.

Main use cases:
- view the permanent timetable
- view temporary timetable changes for the current week
- send lecturer requests for extra lecture slots
- confirm or cancel lecturer requests from the admin panel
- manage timetable settings, timetable records, labs, groups, years, subjects, users, and news

## Features

### Public timetable
- dynamic timetable built from database settings
- weekly temporary timetable override support
- timetable slot summary with lecture codes
- lab allocation modal for each time slot
- lecture detail modal
- lecturer request form for logged-in users

### Lecturer request flow
- lecturers can submit requests with:
  - subject
  - year
  - group
  - day
  - time slot
  - date
  - request description
- admins can:
  - review incoming requests
  - check slot availability
  - assign a lab
  - confirm or cancel requests
- confirmed requests are stored in `temporary_timetable`

### Admin panel
- overview dashboard
- timetable settings management
- timetable CRUD management
- incoming lecturer request management
- news CRUD management
- years CRUD management
- groups CRUD management
- labs CRUD management
- subjects CRUD management
- users CRUD management

### News
- create, update, and delete news
- upload and manage images
- public news list and viewer

### Backend validation
- request validation uses `respect/validation`
- middleware-based validation for:
  - users
  - timetable
  - lecturer requests
  - news

## Project Structure

```text
LaboratoryScheduleSystemWebsite/
├── Frontend/
│   ├── Components/
│   ├── Pages/
│   │   ├── AdminPanel/
│   │   ├── login.php
│   │   ├── news.php
│   │   └── timetable.php
│   ├── API/
│   └── JS/
├── Backend/
│   ├── controllers/
│   ├── middleware/
│   ├── routers/
│   ├── services/
│   ├── scripts/
│   ├── seeds/
│   ├── utils/
│   └── server.php
└── README.md
```

## Requirements

Before running the project, install:

- `XAMPP`
- `PHP 8+`
- `Composer`
- `MySQL` through XAMPP

## Running the Project on XAMPP

### 1. Place the project in `htdocs`

Put the project folder inside your XAMPP `htdocs` directory.

Example:

```text
D:\Software\RunApps\xampp\htdocs\LaboratoryScheduleSystemWebsite
```

### 2. Start Apache and MySQL

Open the XAMPP Control Panel and start:

- `Apache`
- `MySQL`

### 3. Install backend dependencies

Open a terminal in the project root and run:

```bash
cd Backend
composer install
```

### 4. Configure environment variables

Edit:

```text
Backend/.env
```

Current example values:

```env
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=timetable_system
JWT_KEY=your-secret-key
DOMAIN=localhost
```

Important:
- the application uses the database name from `Backend/.env`
- the included SQL file name may differ from `DB_NAME`, which is fine

### 5. Run the seed script

This project includes a one-time seed script that:
- creates the database if it does not exist
- imports the schema from `Backend/seeds/laboratory_schedule_system.sql`
- inserts the initial users

Run:

```bash
php Backend/scripts/run_seed.php
```

### 6. Open the project in the browser

Use:

```text
http://localhost/LaboratoryScheduleSystemWebsite/Frontend/
```

## Seed Users

The default seed users are defined in:

```text
Backend/seeds/users_seed.php
```

Default accounts:

### Admin
- Email: `admin@laboratory.local`
- Password: `Admin@123`

### Lecturer
- Email: `lecturer@laboratory.local`
- Password: `Lecturer@123`

## Seed Script Behavior

The seed script is intentionally locked to one-time use.

After a successful run, it creates:

```text
Backend/seeds/.seed.lock
```

If you run the seed command again after success, it will stop and show a lock message.

## Backend Notes

### API entry point

Backend API entry:

```text
Backend/server.php
```

### Validation

Validation logic is centralized in:

```text
Backend/middleware/validation.php
```

It uses:

```json
"respect/validation": "^2.4"
```

### Image storage

News images are stored in:

```text
Backend/storage/images
```

## Main Pages

- Home: `Frontend/index.php`
- Timetable: `Frontend/Pages/timetable.php`
- News: `Frontend/Pages/news.php`
- Login: `Frontend/Pages/login.php`
- Admin Panel: `Frontend/Pages/AdminPanel/admin.php`

## Technologies Used

### Frontend
- PHP
- HTML
- Tailwind-style utility classes
- Vanilla JavaScript

### Backend
- PHP
- Composer
- `respect/validation`

### Database
- MySQL

## Development Notes

- The timetable is database-driven.
- Temporary timetable data is shown week-by-week.
- Admin actions update timetable-related tables directly from the admin panel.
- Lecturer request confirmation can create temporary timetable records.

## Author

- Lahiru Lakmina
- Email: `lahirulakmina1999@gmail.com`
- GitHub: `HALLakmina`
