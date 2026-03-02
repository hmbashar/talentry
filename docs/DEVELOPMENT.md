# Development Guide

Everything a developer needs to work on Talentry — setup, daily workflow, conventions, and testing.

---

## Prerequisites

| Tool | Minimum Version | Install |
|------|----------------|---------|
| PHP | 8.2 | [php.net](https://php.net) or [Herd](https://herd.laravel.com) |
| Composer | 2.x | [getcomposer.org](https://getcomposer.org) |
| Node.js | 20.x | [nodejs.org](https://nodejs.org) |
| npm | 10.x | Included with Node |
| SQLite | any | Bundled with PHP |

> **Recommended:** Use [Laravel Herd](https://herd.laravel.com) on macOS/Windows. It provides PHP, a local DNS server (`.test` domains), and zero-config HTTPS.

---

## Initial Setup

```bash
# Clone
git clone https://github.com/hmbashar/talentry.git
cd talentry

# One-command setup
composer run setup
# This runs: composer install, copies .env, generates APP_KEY, runs migrations, npm install, npm build

# Seed demo data
php artisan db:seed

# Start all services
composer run dev
```

Open [http://localhost:8000](http://localhost:8000) — or [https://talentry.test](https://talentry.test) if using Herd.

---

## Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### Key variables

```dotenv
APP_NAME=Talentry
APP_ENV=local          # local | production
APP_DEBUG=true         # false in production
APP_URL=http://localhost:8000

# Database — SQLite (default, no setup required)
DB_CONNECTION=sqlite

# Switch to MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=talentry
# DB_USERNAME=root
# DB_PASSWORD=secret

# Mail — log driver captures emails to storage/logs/laravel.log
MAIL_MAILER=log
```

---

## Daily Development Workflow

### Start everything

```bash
composer run dev
```

This starts 4 concurrent processes:
1. `php artisan serve` — PHP dev server on :8000
2. `php artisan queue:listen` — Queue worker for background jobs
3. `php artisan pail` — Real-time log viewer in terminal
4. `npm run dev` — Vite HMR + Wayfinder code generation

> With **Herd**, you don't need `php artisan serve`. Just run `npm run dev` for Vite HMR.

### Frontend only

```bash
npm run dev     # Vite dev server + Wayfinder
npm run build   # Production build (also runs Wayfinder)
```

---

## Artisan Commands

### Most-used commands

```bash
# Run migrations
php artisan migrate

# Reset and re-seed (dev only!)
php artisan migrate:fresh --seed

# Create a model with migration, factory, seeder, and controller
php artisan make:model Report -mfsc

# Create a form request
php artisan make:request StoreReportRequest

# Create a policy
php artisan make:policy ReportPolicy --model=Report

# Create a resource (API transformer)
php artisan make:resource ReportResource

# Clear all caches
php artisan optimize:clear

# List all available commands
php artisan list
```

### Useful during development

```bash
# Interactive REPL (Tinker)
php artisan tinker

# See all registered routes
php artisan route:list

# See all middleware
php artisan middleware:list

# Queue management
php artisan queue:work
php artisan queue:failed
php artisan queue:retry all
```

---

## Code Style & Formatting

### PHP — Laravel Pint

Pint enforces PSR-12 with Laravel-specific rules (configured in `pint.json`).

```bash
# Fix all PHP files
vendor/bin/pint

# Check without fixing (CI)
vendor/bin/pint --test

# Fix only changed files
vendor/bin/pint --dirty
```

> **Required:** Run `vendor/bin/pint --dirty` before every commit to keep formatting consistent.

### JavaScript/TypeScript — ESLint + Prettier

```bash
# Lint and auto-fix
npm run lint

# Format with Prettier
npm run format

# Check without changing (CI)
npm run lint:check
npm run format:check

# TypeScript type check
npm run types:check
```

### Full CI check

```bash
composer run ci:check
# Runs: PHP lint, JS lint, format check, type check, all tests
```

---

## Testing

Talentry uses **Pest v4** — a wrapper around PHPUnit with a fluent, expressive API.

### Run tests

```bash
# All tests
php artisan test

# Compact output (one dot per test)
php artisan test --compact

# Filter by test name
php artisan test --filter HomepageTest

# Filter by file
php artisan test tests/Feature/Feature/Homepage/HomepageTest.php

# With code coverage (requires Xdebug or PCOV)
php artisan test --coverage
```

### Create tests

```bash
# Feature test (most common)
php artisan make:test --pest HomepageSettingsTest

# Unit test
php artisan make:test --pest --unit CandidateServiceTest
```

### Test conventions

Tests live in:
- `tests/Feature/` — HTTP-level tests (routes, controllers, policies)
- `tests/Unit/` — Isolated unit tests for services, helpers

**Feature test template:**
```php
it('admin can update homepage settings', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->patchJson('/api/homepage-settings', [
            'hero_title' => 'New Hero Title',
        ]);

    $response->assertOk();
    expect($response->json('hero_title'))->toBe('New Hero Title');
});
```

**Key helpers:**
```php
actingAs($user)           // Authenticate as user
actingAs($user, 'sanctum') // Auth via Sanctum
$this->getJson('/api/...')  // JSON GET request
$this->postJson('/api/...', $data)
$this->patchJson('/api/...', $data)
assertOk()                // 200
assertCreated()           // 201
assertNoContent()         // 204
assertForbidden()         // 403
assertUnauthorized()      // 401
assertUnprocessable()     // 422
```

**Database helpers (via `Pest.php`):**
```php
// RefreshDatabase is applied globally — each test gets a clean DB
User::factory()->admin()->create()
JobPosting::factory()->published()->count(3)->create()
```

---

## Adding a New Feature — Step by Step

This example adds a **Reports** feature.

### 1. Create the model and migration

```bash
php artisan make:model Report -mf
```

Edit the migration in `database/migrations/`:
```php
Schema::create('reports', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    $table->foreignId('user_id')->constrained();
    $table->string('title');
    $table->json('data');
    $table->timestamps();
});
```

Run: `php artisan migrate`

### 2. Create the Contract (Interface)

```bash
php artisan make:class Contracts/ReportRepositoryInterface
```

Define methods: `all()`, `findByUuid()`, `create()`, `delete()`.

### 3. Create the Repository

```bash
php artisan make:class Repositories/ReportRepository
```

Implement the interface, write Eloquent queries.

### 4. Create the Service

```bash
php artisan make:class Services/ReportService
```

Inject `ReportRepositoryInterface`.

### 5. Bind in AppServiceProvider

```php
$this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
```

### 6. Create Form Requests

```bash
php artisan make:request Report/StoreReportRequest
```

### 7. Create the API Controller

```bash
php artisan make:controller Api/ReportController --api
```

Inject `ReportService`, call service methods, return `JsonResource`.

### 8. Add API Routes

In `routes/api.php`:
```php
Route::prefix('reports')->group(function () {
    Route::get('/', [ReportController::class, 'index']);
    Route::post('/', [ReportController::class, 'store']);
    Route::get('{uuid}', [ReportController::class, 'show']);
    Route::delete('{uuid}', [ReportController::class, 'destroy']);
});
```

### 9. Add Web Route (Inertia page)

In `routes/web.php`:
```php
Route::inertia('reports', 'Admin/Reports/Index')->name('reports.index');
```

### 10. Create Vue Page

`resources/js/pages/Admin/Reports/Index.vue`

### 11. Create Pinia Store (if needed)

`resources/js/stores/report.ts`

### 12. Write Tests

```bash
php artisan make:test --pest ReportTest
```

### 13. Format

```bash
vendor/bin/pint --dirty
npm run lint
```

---

## Git Workflow

```bash
# Create a feature branch
git checkout -b feature/reports-module

# Make changes, run tests
php artisan test --compact

# Format
vendor/bin/pint --dirty && npm run lint

# Commit
git add -A
git commit -m "feat: add reports module with CRUD and Vue page"

# Push
git push origin feature/reports-module
```

### Commit message convention

```
feat: add new feature
fix: fix a bug
refactor: restructure code (no behaviour change)
test: add or update tests
docs: documentation changes
style: formatting, no logic change
chore: build config, deps updates
```

---

## Common Issues

### "Vite manifest not found"

```bash
npm run build
# or
npm run dev   # if in development
```

### "Class not found" after adding new file

```bash
composer dump-autoload
```

### Wayfinder routes not updating

Wayfinder regenerates on every `npm run dev` run. If it seems stale:

```bash
# Kill the dev server and restart
npm run dev
```

### DB migration error "column already exists"

Your local migration state is out of sync. For development, reset:

```bash
php artisan migrate:fresh --seed
```

### Queue jobs not running

```bash
php artisan queue:work
# or
php artisan queue:listen --tries=1
```

---

## IDE Setup (VS Code)

Recommended extensions:

- **Volar** (`Vue.volar`) — Vue 3 language support
- **TypeScript Vue Plugin** — TypeScript in `.vue` files
- **PHP Intelephense** — PHP IntelliSense
- **Tailwind CSS IntelliSense** — Autocomplete for Tailwind classes
- **ESLint** — Inline linting
- **Prettier** — Code formatting

`tsconfig.json` is configured with `@/` path alias — Volar picks this up automatically.
