# Talentry — Project Roadmap

> **Product**: Talentry — Job Application Management System (ATS)
> **Vision**: A scalable, SaaS-ready Applicant Tracking System for modern recruiting teams.
> **Tech Stack**: Laravel 12 · Vue 3 · Inertia.js · TailwindCSS · Pinia · Pest

---

## Current Status

| Version | Status      | Target Date |
|---------|-------------|-------------|
| 1.0     | 🔧 Building  | Q1 2026     |
| 1.1     | 📋 Planned   | Q2 2026     |
| 1.2     | 📋 Planned   | Q3 2026     |
| 2.0     | 🔮 Vision    | Q1 2027     |

---

## Version 1.0 — MVP (Current)

> Foundation: single-tenant, clean admin panel, core recruiting workflow.

### ✅ Implemented

_Nothing yet — building now._

### 🔧 In Progress

#### Authentication & Authorization
- Admin login + Recruiter login (via Laravel Fortify)
- Role-based access control (`admin`, `recruiter`)
- Policies for Jobs and Applications
- Role middleware (`EnsureIsAdmin`, `EnsureIsRecruiter`)

#### Job Management
- Create / Edit / Delete job postings
- Rich text description
- Employment type (Full-time, Part-time, Contract, Remote)
- Location field
- Deadline date
- Publish / Draft status toggle

#### Public Career Page
- Public job listing (no auth required)
- Job detail page
- Application form with resume upload (PDF/DOC)
- Basic validation

#### Candidate & Application Management
- Candidate storage (auto-created on apply)
- Application linked to job posting
- Application statuses: Applied → Shortlisted → Interview → Rejected → Hired
- Status update from admin panel
- Internal notes per application (recruiter-only)

#### Admin Dashboard
- Total jobs count
- Total applications count
- Applications per job
- Status breakdown stats

#### Architecture (SaaS-Ready)
- Repository pattern
- Service layer
- Form Request validation
- API Resources
- UUID for all main models
- Soft deletes
- `company_id` nullable column on all primary tables (for future SaaS)
- Clean migration structure

---

## Version 1.1 — Enhanced UX

> Better workflow visualization and communication tools.

### Planned Features

#### Kanban Board
- Drag-and-drop application status board
- Visual pipeline view per job posting
- Quick status update from card

#### Email Templates
- Customizable email templates for each status stage
- Manual email trigger ("Send rejection email", "Invite to interview")
- Template editor (rich text)

#### Advanced Filters & Search
- Filter applications by status, date, job, employment type
- Full-text candidate search
- Job listing filters on public page

---

## Version 1.2 — Automation & Collaboration

> Streamline the interview process and improve hiring team efficiency.

### Planned Features

#### Interview Scheduling
- Schedule interview from application panel
- Date/time slot selection
- Google Calendar / iCal export

#### Email Automation
- Triggered emails on status change (e.g., auto-send shortlist notification)
- Configurable automation rules
- Unsubscribe handling

#### Candidate Rating System
- 1–5 star rating per application
- Recruiter comments per rating
- Sort applications by rating

#### Activity Logs
- Timeline of all actions per application
- Status change logs with timestamp + user
- Note creation / deletion tracking

---

## Version 2.0 — SaaS Platform

> Full multi-tenant SaaS product with subscription billing and company isolation.

### Planned Features

#### Multi-Tenancy
- Company-based data isolation
- Subdomain routing per company (e.g., `acme.talentry.io`)
- Tenant-scoped middleware
- Shared database architecture (via `company_id`)

#### Subscription System
- Stripe integration (Stripe Billing)
- Free / Pro / Enterprise plans
- Plan-based feature gating
- Usage limits (job postings per plan)

#### Company Branding
- Custom logo upload
- Brand color configuration
- Custom career page theme

#### Custom Domains
- Connect a custom domain to the career page (e.g., `careers.acme.com`)
- SSL provisioning support

#### Role Customization
- Custom roles beyond admin/recruiter
- Granular permission configuration per role
- Team-level access controls

#### Webhooks & API Access
- Outgoing webhooks on key events (new application, status change)
- Public REST API with API key authentication
- Rate limiting per API key

---

## Future Advanced Features (Post-2.0)

> Innovation roadmap — subject to change based on user feedback.

| Feature | Description |
|---------|-------------|
| 🤖 AI Resume Ranking | Auto-score resumes against job description using LLM |
| 📄 Resume Parsing | Extract name, skills, experience from PDF/Word |
| ⚖️ Candidate Comparison | Side-by-side comparison of multiple candidates |
| 📃 Offer Letter Generator | Dynamic PDF offer letter generation |
| 💬 Slack Integration | Slack notifications for new applications / status changes |
| 📊 Advanced Analytics | Funnel metrics, time-to-hire, source tracking |
| 🏁 Hiring Funnel Tracking | Conversion rates across pipeline stages |
| ⏱️ Time-to-Hire Metrics | Track average days from application to hire |
| 🌍 Multi-Language | i18n support for career pages and admin panel |
| 📱 Mobile App | iOS/Android companion app for recruiters |

---

## Architecture Principles (Maintained across all versions)

- **Repository Pattern** — Decoupled data access layer
- **Service Layer** — Business logic isolated from controllers
- **UUID Primary Keys** — Secure, non-guessable IDs
- **Soft Deletes** — No hard data deletion, full audit trail
- **API-First** — All features built as API endpoints first
- **SaaS-Ready Schema** — `company_id` on all tenant-scoped tables from day one
- **Policy-Based Authorization** — Every action gate-checked
- **Test Coverage** — Pest feature tests for all critical paths

---

## Key Decisions Log

| Date | Decision | Reason |
|------|----------|--------|
| 2026-03 | Use Inertia.js (not separate SPA) | Faster MVP delivery with SSR benefits |
| 2026-03 | UUID primary keys from v1.0 | Prevents ID enumeration, SaaS-ready from start |
| 2026-03 | `company_id` nullable from v1.0 | Zero migration cost when enabling multi-tenancy |
| 2026-03 | Repository + Service pattern from v1.0 | Separates concerns early, prevents controller bloat |
| 2026-03 | Fortify for authentication | Built-in 2FA, already scaffolded in starter kit |
