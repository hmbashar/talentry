# Talentry — Job Portal & ATS

A full-featured **Applicant Tracking System (ATS)** and **public job portal** built with Laravel 12, Inertia.js v2, Vue 3, and Tailwind CSS v4. Recruiters post jobs, candidates apply through the public portal, and admins manage the entire pipeline from a premium dashboard.

---

## Screenshots

> Visit the live app after setup at `http://localhost:8000` (or your Herd domain `https://talentry.test`)

| Homepage | Job Listing | Login |
|----------|-------------|-------|
| Premium hero with live stats | Career listings with search & filter | Split-panel brand layout |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.4 · Laravel 12 · Laravel Fortify · Laravel Sanctum |
| Frontend | Vue 3 · Inertia.js v2 · TypeScript |
| Styling | Tailwind CSS v4 |
| Routing | Laravel Wayfinder (type-safe route bindings) |
| Testing | Pest v4 · PHPUnit v12 |
| Database | SQLite (default) · MySQL / PostgreSQL compatible |
| Dev Tools | Laravel Pail · Laravel Sail · Vite · ESLint · Prettier |

---

## Features

### Public Portal
- **Homepage** — live stats (total jobs, applications, candidates), 6 latest job cards, How It Works section
- **Careers listing** — client-side search + employment-type filter
- **Job details & apply** — rich job description, resume upload, apply form

### ATS Dashboard
- **Job management** — create, edit, publish / draft job postings
- **Applications pipeline** — view all applications, update status (pending → reviewing → accepted / rejected)
- **Candidate profiles** — application history, notes per application
- **User management** — admin-only user listing

### Admin CMS
- **Homepage settings** — edit all homepage text (hero, stats labels, How It Works steps, CTA, footer links) from the dashboard without touching code

### Auth
- Registration, login, password reset (Laravel Fortify)
- Role-based access: `admin` · `recruiter` · (public candidate)

---

## Prerequisites

- **PHP** ≥ 8.2
- **Composer** ≥ 2.x
- **Node.js** ≥ 20 + **npm**
- **SQLite** (bundled with PHP) **or** a MySQL/PostgreSQL database

---

## Quick Start

```bash
# 1. Clone
git clone https://github.com/hmbashar/talentry.git
cd talentry

# 2. One-command setup (installs deps, copies .env, generates key, runs migrations, builds assets)
composer run setup

# 3. Seed the database with demo data
php artisan db:seed

# 4. Start the development server
composer run dev
```

Open [http://localhost:8000](http://localhost:8000) in your browser.

---

## Manual Setup

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# (SQLite — create the database file)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed demo data
php artisan db:seed

# Install JS dependencies & build assets
npm install
npm run build
```

---

## Environment Variables

Copy `.env.example` to `.env` and adjust as needed:

```dotenv
APP_NAME=Talentry
APP_URL=http://localhost:8000

# Database — SQLite by default, change to mysql/pgsql as needed
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=talentry
# DB_USERNAME=root
# DB_PASSWORD=

# Mail — uses log driver by default (check storage/logs/laravel.log)
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@talentry.test"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Demo Credentials

After running `php artisan db:seed`:

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@talentry.test` | `password` |
| Recruiter | `recruiter@talentry.test` | `password` |

---

## Development

### Start all services concurrently (server + queue + logs + Vite)

```bash
composer run dev
```

### Frontend only

```bash
npm run dev       # Vite HMR dev server
npm run build     # Production build
```

### Code Quality

```bash
# PHP formatting (Laravel Pint)
vendor/bin/pint

# JS/TS linting
npm run lint

# TypeScript type check
npm run types:check

# Prettier formatting
npm run format
```

### Testing

```bash
# Run all tests
php artisan test

# Compact output
php artisan test --compact

# Filter by name
php artisan test --filter HomepageTest
```

---

## Project Structure

```
app/
├── Enums/              # ApplicationStatus, EmploymentType, JobStatus, UserRole
├── Http/
│   ├── Controllers/    # HomepageController, Api/*
│   ├── Middleware/     # EnsureIsAdmin, EnsureIsRecruiter
│   ├── Requests/       # Form Request validation classes
│   └── Resources/      # Eloquent API Resources
├── Models/             # User, JobPosting, Application, Candidate, Company, HomepageSetting
├── Policies/           # ApplicationPolicy, JobPostingPolicy
├── Repositories/       # ApplicationRepository, CandidateRepository, JobRepository
└── Services/           # ApplicationService, CandidateService, DashboardService

resources/js/
├── components/         # Reusable UI components (shadcn-style)
├── layouts/            # AuthLayout, AppLayout, auth/AuthSimpleLayout
├── pages/
│   ├── Welcome.vue         # Public homepage
│   ├── Careers/            # Index (listing) + Show (detail + apply)
│   ├── Admin/              # Dashboard, Jobs, Applications, Candidates, Settings
│   └── auth/               # Login, Register, ForgotPassword, etc.
└── stores/             # Pinia stores (job, candidate, application, dashboard)

routes/
├── web.php             # Public + authenticated routes
├── api.php             # API routes (HomepageSettings, ATS data)
├── settings.php        # Profile / account settings routes
└── console.php         # Artisan console routes

tests/
├── Feature/            # Feature tests (HTTP, auth, homepage, ATS)
└── Unit/               # Unit tests
```

---

## API Endpoints

All API routes are prefixed with `/api` and protected where noted.

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/homepage-settings` | Admin | Fetch homepage CMS content |
| PATCH | `/api/homepage-settings` | Admin | Update homepage CMS content |
| GET | `/api/admin/jobs` | Auth | List job postings |
| POST | `/api/admin/jobs` | Recruiter/Admin | Create job posting |
| PUT | `/api/admin/jobs/{uuid}` | Recruiter/Admin | Update job posting |
| DELETE | `/api/admin/jobs/{uuid}` | Recruiter/Admin | Delete job posting |
| GET | `/api/admin/applications` | Auth | List applications |
| PATCH | `/api/admin/applications/{uuid}/status` | Recruiter/Admin | Update application status |
| GET | `/api/admin/candidates` | Auth | List candidates |
| GET | `/api/admin/candidates/{uuid}` | Auth | Get candidate profile |
| GET | `/api/admin/dashboard` | Auth | Dashboard stats |
| GET | `/api/public/jobs` | Public | Public job listings |
| POST | `/api/public/jobs/{uuid}/apply` | Public | Submit application |

---

## Roles & Permissions

| Permission | Admin | Recruiter |
|-----------|-------|-----------|
| Manage jobs | ✅ | ✅ (own) |
| View applications | ✅ | ✅ |
| Update application status | ✅ | ✅ |
| View candidates | ✅ | ✅ |
| Manage users | ✅ | ❌ |
| Edit homepage settings | ✅ | ❌ |

---

## Deployment

```bash
# Install production dependencies
composer install --no-dev --optimize-autoloader

# Build frontend assets
npm run build

# Run migrations
php artisan migrate --force

# Optimise
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Set `APP_ENV=production` and `APP_DEBUG=false` in your production `.env`.

---

## License

[MIT](LICENSE)
