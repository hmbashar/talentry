# Project Structure

Every directory and file explained — **where** it lives, **why** it exists, and **how** it works.

---

## Root Level

```
talentry/
├── app/                Laravel application code
├── bootstrap/          App bootstrap & config
├── config/             All configuration files
├── database/           Migrations, seeders, factories
├── docs/               This documentation
├── public/             Web server document root
├── resources/          Frontend source (Vue, CSS)
├── routes/             Route definitions
├── storage/            Logs, uploaded files, cache
├── tests/              Pest/PHPUnit test suite
├── vendor/             Composer packages (git-ignored)
├── .env.example        Environment variable template
├── artisan             Laravel CLI entry point
├── composer.json       PHP dependency manifest
├── package.json        JS dependency manifest
├── vite.config.ts      Vite bundler config
├── tsconfig.json       TypeScript compiler config
├── pint.json           Laravel Pint (PHP formatter) config
├── phpunit.xml         PHPUnit / Pest config
└── README.md           Project introduction
```

---

## `app/` — Application Code

### `app/Enums/`

**Where:** `app/Enums/`  
**Why:** PHP 8.1 backed enums that centralise all "magic string" values. Prevents typos across the codebase.  
**How:** Each enum case maps to a database string value. They also carry helper methods (`label()`, `color()`) used in UI rendering.

| File | Values | Used In |
|------|--------|---------|
| `UserRole.php` | `Admin`, `Recruiter` | `User` model, middleware, blade/Vue conditionals |
| `ApplicationStatus.php` | `Applied`, `Shortlisted`, `Interview`, `Rejected`, `Hired` | `Application` model, API status updates, badge colors |
| `JobStatus.php` | `Published`, `Draft` | `JobPosting` model, job listing filters |
| `EmploymentType.php` | `FullTime`, `PartTime`, `Contract`, `Remote` | `JobPosting` model, careers filter UI |

---

### `app/Contracts/`

**Where:** `app/Contracts/`  
**Why:** Interface definitions for all repositories. Allows the `AppServiceProvider` to bind interfaces to concrete implementations — making the code loosely coupled and easily testable/mockable.  
**How:** Each file is a PHP interface with method signatures. The actual implementations are in `app/Repositories/`.

```php
// AppServiceProvider binds these:
$app->bind(JobRepositoryInterface::class, JobRepository::class);
$app->bind(ApplicationRepositoryInterface::class, ApplicationRepository::class);
$app->bind(CandidateRepositoryInterface::class, CandidateRepository::class);
```

---

### `app/Models/`

**Where:** `app/Models/`  
**Why:** Eloquent models represent database tables and define relationships, casts, scopes, and business methods.  
**How:** Each model extends `Illuminate\Database\Eloquent\Model`. Relationships are defined as methods returning `HasMany`, `BelongsTo`, etc.

| Model | Table | Notable Features |
|-------|-------|-----------------|
| `User` | `users` | `HasApiTokens`, `TwoFactorAuthenticatable`, `isAdmin()`, `isRecruiter()` helpers |
| `JobPosting` | `job_postings` | `SoftDeletes`, `scopePublished()`, enum casts for `status` and `employment_type` |
| `Application` | `applications` | `SoftDeletes`, enum cast for `status`, relationships to `JobPosting`, `Candidate`, `ApplicationNote` |
| `Candidate` | `candidates` | `HasMany` applications, `withCount('applications')` used in profile view |
| `Company` | `companies` | `HasMany` job postings and users |
| `ApplicationNote` | `application_notes` | Internal recruiter notes on an application |
| `HomepageSetting` | `homepage_settings` | Single-row CMS — JSON `content` column, `defaults()` + `current()` static helpers |

---

### `app/Repositories/`

**Where:** `app/Repositories/`  
**Why:** Isolate all database querying logic. Controllers and services never write Eloquent queries directly — they call repository methods.  
**How:** Each repository implements its corresponding contract interface. Methods always eager-load relationships to prevent N+1 query problems.

```php
// Example: ApplicationRepository::all() eager-loads everything needed in one query
Application::query()
    ->with(['jobPosting', 'candidate', 'notes.user'])
    ->paginate(20);
```

---

### `app/Services/`

**Where:** `app/Services/`  
**Why:** Business logic that spans multiple repositories or requires multi-step operations. Services are injected into controllers via Laravel's service container.  
**How:** Services receive repository interfaces in their constructor (constructor property promotion). They orchestrate operations but never output HTTP responses.

| Service | Key Methods |
|---------|-------------|
| `ApplicationService` | `applyToJob()`, `updateStatus()`, `addNote()`, `delete()` |
| `CandidateService` | `list()`, `findByUuid()` |
| `DashboardService` | `getStats()` — returns aggregated counts for the dashboard |

---

### `app/Http/Controllers/`

**Where:** `app/Http/Controllers/`  
**Why:** Handle HTTP requests, validate input (via Form Requests), call services, and return responses.  
**How:** Controllers are kept thin — they only orchestrate the input→service→output flow. They never contain business logic.

```
app/Http/Controllers/
├── Controller.php                Base controller
├── HomepageController.php        Invokable — serves the public homepage (Inertia)
├── Api/
│   ├── ApplicationController.php  ATS application management API
│   ├── CandidateController.php    Candidate listing/profile API
│   ├── DashboardController.php    Dashboard stats API
│   ├── HomepageSettingController  CMS settings read/update API (admin only)
│   ├── JobController.php          Job CRUD + publish/draft API
│   ├── UserController.php         User listing + role update (admin only)
│   └── Public/
│       └── CareerController.php   Public job listing + apply (no auth)
└── Settings/
    ├── ProfileController.php
    ├── PasswordController.php
    └── TwoFactorAuthenticationController.php
```

---

### `app/Http/Requests/`

**Where:** `app/Http/Requests/`  
**Why:** Validation rules live in dedicated Form Request classes instead of inline in controllers. This keeps controllers clean and makes validation reusable and testable.  
**How:** Each request class has `authorize()` (can this user make this request?) and `rules()` (what are the validation rules?).

```
app/Http/Requests/
├── Application/
│   ├── StoreApplicationNoteRequest.php   Validates recruiter note text
│   └── UpdateApplicationStatusRequest.php  Validates status enum value
├── Career/
│   └── StoreCareerApplicationRequest.php  Validates public apply form
├── Job/
│   ├── StoreJobRequest.php
│   └── UpdateJobRequest.php
└── Settings/
    ├── UpdateProfileRequest.php
    └── UpdatePasswordRequest.php
```

---

### `app/Http/Middleware/`

**Where:** `app/Http/Middleware/`  
**Why:** Gate access to routes based on user role.  
**How:** Middleware is registered in `bootstrap/app.php` and referenced by alias in route definitions.

| Middleware | Alias | Effect |
|-----------|-------|--------|
| `EnsureIsAdmin` | `admin` | 403 if `user->role !== admin` |
| `EnsureIsRecruiter` | `recruiter` | 403 if user is not recruiter or admin |

---

### `app/Http/Resources/`

**Where:** `app/Http/Resources/`  
**Why:** Transform Eloquent models into consistent JSON shapes for API responses. Prevents over-exposing model attributes.  
**How:** Extend `JsonResource`. The `toArray()` method defines the exact fields included in the response.

---

### `app/Policies/`

**Where:** `app/Policies/`  
**Why:** Fine-grained per-model authorization (beyond just roles). Registered automatically via Laravel's policy discovery.  
**How:** Each method returns `bool|Response`. Called via `$this->authorize()` in controllers or `Gate::allows()`.

| Policy | Guards |
|--------|--------|
| `JobPostingPolicy` | Only the job owner or admin can update/delete |
| `ApplicationPolicy` | Only recruiters assigned to the job or admin can update |

---

### `app/Providers/`

**Where:** `app/Providers/`  
**Why:** Bootstrap application-wide bindings and configuration.  
**How:**

| Provider | Purpose |
|----------|---------|
| `AppServiceProvider` | Binds Repository interfaces, sets `CarbonImmutable`, configures password strength, prohibits destructive DB commands in production |
| `FortifyServiceProvider` | Customises Fortify's view responses to use Inertia instead of Blade |

---

## `database/`

```
database/
├── factories/       Model factories for test data generation
├── migrations/      Database schema version history
└── seeders/
    └── DatabaseSeeder.php   Seeds demo admin, recruiter, jobs, candidates, applications
```

### Migrations (in order)

| Migration | Creates |
|-----------|---------|
| `000000_create_users_table` | `users` with uuid, name, email, password |
| `000001_create_cache_table` | `cache`, `cache_locks` |
| `000002_create_jobs_table` | `jobs` (Laravel queue jobs table, not job postings) |
| `add_two_factor_columns_to_users_table` | 2FA columns for Fortify |
| `add_role_and_company_to_users_table` | `role`, `company_id` on users |
| `create_companies_table` | `companies` |
| `create_candidates_table` | `candidates` |
| `create_job_postings_table` | `job_postings` with full ATS fields |
| `create_applications_table` | `applications` |
| `create_application_notes_table` | `application_notes` |
| `create_personal_access_tokens_table` | Sanctum API tokens |
| `create_homepage_settings_table` | `homepage_settings` with JSON `content` |

---

## `routes/`

```
routes/
├── web.php       Public pages + authenticated Inertia pages
├── api.php       All JSON API endpoints
├── settings.php  Profile/password/2FA settings routes (included by web.php)
└── console.php   Artisan console commands defined as closures
```

**web.php structure:**
- `/` → `HomepageController` (invokable, public)
- `/careers` + `/careers/{uuid}` → public Inertia pages
- `/dashboard` → authenticated
- `/admin/*` → authenticated (recruiter + admin)
- `/admin/users` + `/admin/settings/homepage` → admin only

**api.php structure:**
- `/api/public/*` → no authentication (job listings, apply)
- `/api/*` → `auth:sanctum` middleware (Sanctum session-based for SPA)
- `/api/users` + `/api/homepage-settings` → additional `admin` middleware

---

## `resources/`

See [FRONTEND.md](FRONTEND.md) for the full frontend documentation.

```
resources/
├── css/
│   └── app.css          Tailwind v4 entry point + CSS custom properties
└── js/
    ├── app.ts            Vue + Inertia bootstrap entry point
    ├── ssr.ts            SSR entry (if enabled)
    ├── components/       Reusable Vue components
    │   ├── ui/           shadcn-style headless UI components
    │   ├── AppSidebar.vue
    │   ├── InputError.vue
    │   └── TextLink.vue
    ├── layouts/          Page layout wrappers
    │   ├── AppLayout.vue         Dashboard shell (sidebar + topbar)
    │   ├── AuthLayout.vue        Auth shell (wraps AuthSimpleLayout)
    │   └── auth/
    │       └── AuthSimpleLayout.vue  Split-panel brand layout for login/register
    ├── pages/            Inertia page components (mapped 1:1 to routes)
    │   ├── Welcome.vue           Public homepage
    │   ├── Dashboard.vue         User role redirect dashboard
    │   ├── Careers/
    │   │   ├── Index.vue         Public job listings + search
    │   │   └── Show.vue          Job detail + apply form
    │   ├── Admin/
    │   │   ├── Dashboard.vue     ATS dashboard with stats
    │   │   ├── Jobs/             Index, Create, Edit
    │   │   ├── Applications/     Index, Show
    │   │   ├── Candidates/       Index, Show
    │   │   ├── Users/            Index (admin only)
    │   │   └── Settings/
    │   │       └── Homepage.vue  CMS editor for homepage content
    │   ├── auth/                 Login, Register, ForgotPassword, ResetPassword
    │   └── settings/             Profile, Password, Appearance, 2FA
    └── stores/           Pinia state stores
        ├── job.ts
        ├── application.ts
        ├── candidate.ts
        └── dashboard.ts
```

---

## `tests/`

```
tests/
├── Pest.php          Global Pest configuration (uses DatabaseMigrations trait)
├── TestCase.php      Base test case
├── Feature/          HTTP-level integration tests
│   ├── Auth/
│   │   └── RegistrationTest.php
│   ├── Applications/
│   ├── Candidates/
│   ├── Careers/
│   ├── Jobs/
│   ├── Settings/
│   ├── Feature/
│   │   └── Homepage/
│   │       └── HomepageTest.php   9 homepage + CMS tests
│   └── DashboardTest.php
└── Unit/             Unit tests for isolated classes/methods
```

---

## `bootstrap/`

| File | Purpose |
|------|---------|
| `app.php` | Central app bootstrap — registers middleware, exceptions, routing files. Replaces the old `Kernel.php` (Laravel 11+ streamlined structure) |
| `providers.php` | Lists all service providers that should be loaded |

---

## `config/`

| File | Key Settings |
|------|-------------|
| `app.php` | App name, locale, timezone |
| `auth.php` | Guard config (web session, sanctum API) |
| `fortify.php` | Enable/disable auth features (registration, 2FA, email verify) |
| `inertia.php` | SSR path, shared data config |
| `sanctum.php` | Stateful domains for SPA cookie auth |
| `database.php` | Connection defaults (SQLite by default) |
| `filesystems.php` | Storage disk config (local, public, S3) |

---

## Generated / Git-ignored Directories

These are **never committed** — they are generated automatically:

| Path | Generated By | When |
|------|-------------|------|
| `vendor/` | `composer install` | Setup |
| `node_modules/` | `npm install` | Setup |
| `public/build/` | `npm run build` | Build |
| `resources/js/actions/` | Wayfinder Vite plugin | `npm run dev/build` |
| `resources/js/routes/` | Wayfinder Vite plugin | `npm run dev/build` |
| `resources/js/wayfinder/` | Wayfinder Vite plugin | `npm run dev/build` |
| `storage/logs/` | Laravel runtime | Runtime |
| `.env` | Copied from `.env.example` | Setup |
