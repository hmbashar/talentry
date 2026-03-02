# Changelog

All notable changes to Talentry are documented here.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

---

## [Unreleased]

---

## [0.3.0] — 2026-03-03

### Added
- **Auth layout redesign** — split-panel premium design with decorative gradient orbs, dot-grid pattern, stats grid (10k+ jobs, 500+ companies, 50k+ placed), star testimonial card, and white form card with `shadow-2xl`
- **Careers/Index.vue redesign** — sticky branded nav, dark gradient hero with live search + employment-type filter (client-side), horizontal job cards with badges and deadlines, empty state with clear-filters button
- **Careers/Show.vue redesign** — dark gradient hero banner, two-column layout (description + apply form), drag-target resume upload zone, success state card
- `Star` icon from `lucide-vue-next` imported in `AuthSimpleLayout.vue`

### Fixed
- Removed non-existent `@/components/ui/textarea` import from `Admin/Settings/Homepage.vue` — replaced with plain `<textarea class="...">` elements

---

## [0.2.0] — 2026-03-02

### Added
- **Full homepage redesign** — premium SPA homepage (`Welcome.vue`) with live backend data
  - Sticky branded nav
  - Dark gradient hero with search bar and dual CTAs
  - Stats grid (4 cards: total jobs, total applications, total candidates, employment types)
  - Latest 6 published jobs grid
  - "How It Works" steps section (CMS-driven)
  - Apply/interest form section
  - CTA band + branded footer
- **HomepageSetting model** — single-row CMS for all homepage text; `defaults()` + `current()` helpers; JSON `content` column; auto-seeds on first page visit
- **HomepageController** — invokable; loads live `jobs`, `stats`, and `settings` for the homepage
- **HomepageSettingController** (API) — `GET /api/homepage-settings`, `PATCH /api/homepage-settings`; admin-only
- **Admin Settings page** (`Admin/Settings/Homepage.vue`) — full CMS editor for hero, stats labels, How It Works steps, apply section, CTA band, footer links
- **AppSidebar** — "Settings" nav item (admin only) with `Settings` icon linking to `/admin/settings/homepage`
- **9 new homepage tests** — renders with all props, seeds defaults, only published jobs shown, capped at 6, admin CRUD, recruiter 403, guest 401, validation
- Homepage route changed from `Route::inertia('/')` to `HomepageController::class`
- `create_homepage_settings_table` migration

---

## [0.1.0] — 2026-03-02

### Added
- Initial Laravel 12 + Inertia.js v2 + Vue 3 application scaffold
- **User authentication** via Laravel Fortify (login, register, password reset, 2FA support)
- **Role-based access control** — `admin` and `recruiter` roles with dedicated middleware
- **Job Posting CRUD** — create, edit, publish/draft, soft-delete; recruiter-scoped
- **Public Careers listing** — `/careers` and `/careers/{uuid}`
- **Public apply form** — file upload (resume), cover letter; creates candidate if email is new
- **Applications pipeline** — list, detail view, status transitions (`applied → shortlisted → interview → rejected → hired`), recruiter notes
- **Candidates module** — list all candidates, view profile with application history
- **ATS Dashboard** — stats summary (totals + by status/type)
- **User management** (admin only) — list users, update role
- **Repository pattern** — `JobRepository`, `ApplicationRepository`, `CandidateRepository` with interface contracts
- **Service layer** — `ApplicationService`, `CandidateService`, `DashboardService`
- **Sanctum SPA auth** — stateful cookie authentication for the Vue frontend
- SQLite default database with 12 migrations
- `DatabaseSeeder` — admin, recruiter, 5 published jobs, 2 drafts, 20 candidates, applications with notes
- Pinia stores — `jobStore`, `applicationStore`, `candidateStore`, `dashboardStore`
- Wayfinder integration — type-safe TypeScript route functions
- Tailwind CSS v4 + shadcn-style UI component library

### Fixed
- `CandidateRepository::findByUuid()` was missing `->withCount('applications')` causing candidate profile badge to always show "0 Applications"
