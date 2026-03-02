# Architecture Overview

Talentry is a **monolithic Laravel application** that renders its UI as a Single Page Application (SPA) via **Inertia.js**. There is no separate API server for the frontend — the same Laravel app serves both the Inertia pages and JSON API endpoints consumed by Vue components for dynamic data loading.

---

## High-Level Architecture

```
Browser
  │
  ▼
┌──────────────────────────────────────────────────────────┐
│                     Laravel Application                   │
│                                                          │
│  Routes (web.php / api.php / settings.php)               │
│       │                    │                             │
│  Inertia Pages         JSON API Endpoints               │
│  (server renders       (Vue components fetch             │
│   initial props)        data after page load)            │
│       │                    │                             │
│  Controllers ──────── Services ──────── Repositories     │
│                            │                             │
│                        Eloquent ORM                      │
│                            │                             │
│                        SQLite / MySQL / PostgreSQL       │
└──────────────────────────────────────────────────────────┘
         │
         ▼
    Vue 3 + Inertia.js (client-side SPA, Vite-compiled)
```

---

## Design Patterns

### Repository Pattern

All database access is abstracted behind **Interfaces** and **Repository** classes.

```
Controller → Service → RepositoryInterface → Repository → Eloquent Model
```

**Why?**
- Controllers stay thin — they only handle HTTP input/output
- Services contain business logic and are testable in isolation
- Repositories can be swapped (e.g. swap SQLite for an external API) without touching business logic
- Interface-based dependency injection (`AppServiceProvider` binds interfaces to implementations)

**Example flow for listing applications:**
```
ApplicationController::index()
  → ApplicationService::list($filters)
    → ApplicationRepositoryInterface::all($filters)
      → ApplicationRepository::all()   ← actual Eloquent query
        → Application::query()->with(...)->paginate()
```

### Service Layer

Services sit between Controllers and Repositories. They orchestrate multi-step business operations:

| Service | Responsibility |
|---------|---------------|
| `ApplicationService` | Apply to job, update status, add notes, delete applications |
| `CandidateService` | Find or create candidates, list with filters |
| `DashboardService` | Aggregate stats (total jobs, applications, candidates) |

### Interface Contracts

Located in `app/Contracts/`, each repository has a corresponding interface:

- `JobRepositoryInterface`
- `ApplicationRepositoryInterface`
- `CandidateRepositoryInterface`

Registered in `AppServiceProvider::register()` via `$app->bind(Interface::class, Concrete::class)`.

---

## Request Lifecycle

### Inertia Page Request (first load)

```
1. Browser requests GET /admin/jobs
2. Laravel Router matches → AdminJobController or Route::inertia()
3. Controller loads data → returns Inertia::render('Admin/Jobs/Index', $props)
4. Inertia middleware serialises $props as JSON embedded in the HTML response
5. Vue app boots on the client, Inertia hydrates the page component with props
6. Vue renders the page — no further HTTP request needed for initial data
```

### Inertia Subsequent Navigation

```
1. User clicks a <Link> in Vue
2. Inertia intercepts the click, makes XHR GET with X-Inertia headers
3. Laravel returns JSON of { component, props, url } — NOT full HTML
4. Inertia client swaps the page component and updates props reactively
```

### API-only Requests (for dynamic data after page load)

```
1. Vue component mounts → calls fetch('/api/jobs') or similar
2. Laravel api.php routes handle it → JSON response
3. Vue updates reactive state in a Pinia store or local ref
```

---

## Authentication & Authorization

### Authentication — Laravel Fortify

Fortify handles all auth routes (login, register, password reset, 2FA) without controllers you write. It is configured in `config/fortify.php` and the service provider in `app/Providers/FortifyServiceProvider.php`.

### Authorization — Roles + Policies

Two roles exist: `admin` and `recruiter` (see `app/Enums/UserRole.php`).

**Middleware guards:**
- `EnsureIsAdmin` — blocks non-admins, HTTP 403
- `EnsureIsRecruiter` — blocks non-recruiters/non-admins, HTTP 403

**Policies (`app/Policies/`):**
- `JobPostingPolicy` — controls who can update/delete a job posting
- `ApplicationPolicy` — controls who can view/update an application

---

## Frontend Architecture

The frontend is a **Vue 3 SPA** driven by Inertia.js. Key decisions:

| Decision | Choice | Why |
|----------|--------|-----|
| Routing | Inertia (server-side) | No need for Vue Router; Laravel owns URLs |
| State | Pinia stores + Inertia props | Server props for initial load; Pinia for shared runtime state |
| Styling | Tailwind CSS v4 | Utility-first, no CSS files to maintain |
| Type safety | TypeScript + Wayfinder | Wayfinder auto-generates typed route functions from Laravel routes |
| Component library | shadcn-style (in `/components/ui`) | Accessible headless components with Tailwind styling |

---

## Data Flow Diagram

```
User submits "Apply" form on /careers/{uuid}
       │
       ▼
POST /api/public/jobs/{uuid}/apply   (no auth required)
       │
       ▼
CareerController::apply()
       │
       ├─ Validates request (StoreApplicationRequest)
       ├─ Resolves JobPosting by UUID
       └─ ApplicationService::applyToJob($job, $data, $resume)
               │
               ├─ CandidateRepository::findOrCreateByEmail()
               │        └─ Upserts Candidate record
               ├─ Stores resume file → storage/app/public/resumes/
               └─ ApplicationRepository::create()
                        └─ Application record created in DB
                               │
                               ▼
                        JSON 201 response back to Vue
```

---

## Key Configuration Files

| File | Purpose |
|------|---------|
| `bootstrap/app.php` | Registers middleware, exception handler, routing files |
| `bootstrap/providers.php` | Lists application service providers |
| `config/fortify.php` | Fortify feature flags (registration, 2FA, etc.) |
| `config/inertia.php` | Inertia SSR and shared data config |
| `config/sanctum.php` | Sanctum stateful domain config |
| `vite.config.ts` | Vite + Wayfinder plugin config |
| `tsconfig.json` | TypeScript path aliases (`@/` → `resources/js/`) |
