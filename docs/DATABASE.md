# Database

## Default Database

Talentry uses **SQLite** by default (`database/database.sqlite`). This requires no installation and works out-of-the-box on any machine with PHP. For production, switch to **MySQL** or **PostgreSQL** by updating `DB_CONNECTION` in `.env`.

---

## Entity Relationship Diagram

```
┌──────────┐       ┌─────────────────┐       ┌──────────────────┐
│  users   │───┐   │   job_postings  │───────│   applications   │
│──────────│   │   │─────────────────│       │──────────────────│
│ id       │   │   │ id              │       │ id               │
│ uuid     │   │   │ uuid            │       │ uuid             │
│ name     │   └──>│ user_id (FK)    │   ┌──>│ job_posting_id   │
│ email    │   ┌──>│ company_id (FK) │   │   │ candidate_id (FK)│
│ password │   │   │ title           │───┘   │ status           │
│ role     │   │   │ description     │       │ resume_path      │
│ company_id│  │   │ location        │       │ cover_letter     │
└──────────┘   │   │ employment_type │       │ created_at       │
               │   │ status          │       └──────────────────┘
               │   │ deadline        │                │
               │   │ deleted_at      │                │
               │   └─────────────────┘                ▼
               │                             ┌──────────────────────┐
               │                             │  application_notes   │
               │   ┌─────────────┐          │──────────────────────│
               └───│  companies  │          │ id                   │
                   │─────────────│          │ uuid                 │
                   │ id          │          │ application_id (FK)  │
                   │ name        │          │ user_id (FK)         │
                   │ description │          │ note                 │
                   │ website     │          └──────────────────────┘
                   │ logo_path   │
                   └─────────────┘          ┌─────────────────┐
                                            │   candidates    │
                                            │─────────────────│
                                            │ id              │
                                            │ uuid            │
                                            │ name            │
                                            │ email           │
                                            │ phone           │
                                            └─────────────────┘

┌────────────────────────┐
│   homepage_settings    │
│────────────────────────│
│ id                     │   Single-row CMS table
│ content (JSON)         │
└────────────────────────┘
```

---

## Tables

### `users`

Stores all authenticated users (admins and recruiters). Candidates are **not** stored here — they are in the `candidates` table (no login required).

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | Auto-increment |
| `uuid` | string | Public-facing unique identifier |
| `name` | string | |
| `email` | string unique | |
| `email_verified_at` | timestamp nullable | |
| `password` | string | Hashed (bcrypt) |
| `role` | enum(`admin`,`recruiter`) | Via `UserRole` enum |
| `company_id` | bigint FK nullable | → `companies.id` |
| `two_factor_secret` | text nullable | Fortify 2FA |
| `two_factor_recovery_codes` | text nullable | Fortify 2FA |
| `two_factor_confirmed_at` | timestamp nullable | |
| `remember_token` | string nullable | |
| `timestamps` | | `created_at`, `updated_at` |

---

### `companies`

A company is linked to users (recruiters) and job postings.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `name` | string | |
| `description` | text nullable | |
| `website` | string nullable | |
| `logo_path` | string nullable | Path in public storage |
| `timestamps` | | |

---

### `job_postings`

Core ATS entity. Uses soft deletes so archived jobs are preserved for reporting.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `uuid` | string unique | Used in URLs (`/careers/{uuid}`) |
| `company_id` | bigint FK | → `companies.id` |
| `user_id` | bigint FK | → `users.id` (creator/recruiter) |
| `title` | string | |
| `description` | longtext | Rich text job description |
| `location` | string | |
| `employment_type` | string | `full_time`, `part_time`, `contract`, `remote` |
| `status` | string | `published`, `draft` |
| `deadline` | date nullable | Application closing date |
| `deleted_at` | timestamp nullable | Soft delete |
| `timestamps` | | |

**Scopes:**
- `scopePublished()` — filters to `status = 'published'`

---

### `candidates`

People who apply for jobs. They do **not** have user accounts — identified by email only.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `uuid` | string unique | Public-facing ID |
| `name` | string | |
| `email` | string unique | Used for deduplication in `findOrCreateByEmail()` |
| `phone` | string nullable | |
| `timestamps` | | |

---

### `applications`

Links a candidate to a job posting. One candidate can apply to many jobs.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `uuid` | string unique | Public-facing ID |
| `job_posting_id` | bigint FK | → `job_postings.id` |
| `candidate_id` | bigint FK | → `candidates.id` |
| `status` | string | `applied`, `shortlisted`, `interview`, `rejected`, `hired` |
| `resume_path` | string | Path in `storage/app/public/resumes/` |
| `cover_letter` | text nullable | |
| `deleted_at` | timestamp nullable | Soft delete |
| `timestamps` | | |

---

### `application_notes`

Internal recruiter notes attached to an application.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `uuid` | string unique | |
| `application_id` | bigint FK | → `applications.id` |
| `user_id` | bigint FK | → `users.id` (note author) |
| `note` | text | |
| `timestamps` | | |

---

### `homepage_settings`

A **single-row** CMS table. All homepage text content is stored as a JSON blob in the `content` column and managed from the Admin Settings UI.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | Always 1 |
| `content` | json | Full homepage text content |
| `timestamps` | | |

**`content` JSON shape:**
```json
{
  "hero_title": "Find Your Dream Job Today",
  "hero_subtitle": "...",
  "hero_cta_primary": "Browse All Jobs",
  "hero_cta_secondary": "For Employers",
  "jobs_section_title": "Latest Opportunities",
  "jobs_section_subtitle": "...",
  "stats_section_title": "Trusted by Thousands",
  "stats_section_subtitle": "...",
  "how_it_works_title": "How It Works",
  "how_it_works_subtitle": "...",
  "how_it_works_steps": [
    { "icon": "UserPlus", "title": "Create Your Profile", "description": "..." },
    { "icon": "Search", "title": "Explore Opportunities", "description": "..." },
    { "icon": "CheckCircle", "title": "Apply & Get Hired", "description": "..." }
  ],
  "apply_section_title": "...",
  "apply_section_subtitle": "...",
  "cta_title": "Ready to Take the Next Step?",
  "cta_subtitle": "...",
  "cta_button": "Get Started Today",
  "footer_tagline": "...",
  "footer_links": [
    { "label": "About", "url": "/about" },
    { "label": "Careers", "url": "/careers" }
  ]
}
```

**How it auto-seeds:** `HomepageSetting::current()` calls `firstOrCreate([], ['content' => defaults()])` — so defaults are seeded automatically the first time the homepage is visited, with no seeder required.

---

## Eloquent Relationships

```
User
 ├── hasMany JobPosting        (jobs they posted)
 ├── hasMany ApplicationNote   (notes they wrote)
 └── belongsTo Company

Company
 ├── hasMany User
 └── hasMany JobPosting

JobPosting
 ├── belongsTo User
 ├── belongsTo Company
 └── hasMany Application

Candidate
 └── hasMany Application

Application
 ├── belongsTo JobPosting
 ├── belongsTo Candidate
 └── hasMany ApplicationNote

ApplicationNote
 ├── belongsTo Application
 └── belongsTo User
```

---

## Enums Used as Database Values

All enum columns store the string `value` of the PHP enum, not the case name.

| Column | PHP Enum | Stored Values |
|--------|----------|--------------|
| `users.role` | `UserRole` | `'admin'`, `'recruiter'` |
| `job_postings.status` | `JobStatus` | `'published'`, `'draft'` |
| `job_postings.employment_type` | `EmploymentType` | `'full_time'`, `'part_time'`, `'contract'`, `'remote'` |
| `applications.status` | `ApplicationStatus` | `'applied'`, `'shortlisted'`, `'interview'`, `'rejected'`, `'hired'` |

---

## Migrations Guide

### Add a new column

```php
// Generate migration
php artisan make:migration add_linkedin_to_candidates_table

// In the migration up() method:
Schema::table('candidates', function (Blueprint $table) {
    $table->string('linkedin_url')->nullable()->after('phone');
});
```

> **Important:** When modifying an existing column, include ALL previously defined attributes — otherwise Laravel will drop them.

### Rollback

```bash
php artisan migrate:rollback        # Rollback last batch
php artisan migrate:rollback --step=2   # Rollback last 2 batches
php artisan migrate:fresh --seed    # Drop all tables and re-migrate (dev only!)
```

---

## Soft Deletes

`JobPosting` and `Application` use `SoftDeletes`. Deleted records have a `deleted_at` timestamp and are excluded from all queries by default.

```php
// Restore a soft-deleted record
$job->restore();

// Include soft-deleted records in a query
JobPosting::withTrashed()->get();

// Permanently delete
$job->forceDelete();
```

---

## Seeder

Running `php artisan db:seed` creates:

| Data | Count |
|------|-------|
| Admin user (`admin@talentry.test` / `password`) | 1 |
| Recruiter user (`recruiter@talentry.test` / `password`) | 1 |
| Published job postings | 5 |
| Draft job postings | 2 |
| Candidates | 20 |
| Applications (random jobs) | ~20 |
| Application notes | ~1 per application |

> Factories are in `database/factories/`. Each factory has states like `->published()`, `->draft()`, `->admin()`, `->recruiter()`.
