# Frontend

The frontend is a **Vue 3 SPA** driven by **Inertia.js v2**. It is compiled by **Vite** and styled with **Tailwind CSS v4**. TypeScript is used throughout.

---

## Entry Points

| File | Purpose |
|------|---------|
| `resources/js/app.ts` | Vue + Inertia bootstrap — creates the Vue app, mounts on `#app` |
| `resources/js/ssr.ts` | SSR entry point (if SSR is enabled) |
| `resources/css/app.css` | Tailwind v4 entry — `@import 'tailwindcss'` + CSS custom properties |

**`app.ts` pattern:**
```ts
createInertiaApp({
  resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, ...),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(pinia)
      .mount(el)
  },
})
```

---

## Inertia.js — How It Works

Inertia replaces traditional full-page HTML with a SPA experience while keeping server-side routing.

### Page Components

Every route maps to a **page component** in `resources/js/pages/`. The mapping is by filename:

```
GET /admin/jobs  →  Inertia::render('Admin/Jobs/Index')  →  pages/Admin/Jobs/Index.vue
```

### Props

Data is passed from Laravel controllers as **Inertia props** — they arrive as the component's `defineProps`:

```php
// Laravel controller
return Inertia::render('Admin/Jobs/Index', [
    'jobs' => JobResource::collection($jobs),
]);
```

```vue
<!-- Vue page component -->
<script setup lang="ts">
defineProps<{ jobs: Job[] }>()
</script>
```

### Navigation

Use Inertia's `<Link>` component instead of `<a>` tags — it intercepts clicks and makes XHR navigation instead of full page loads:

```vue
<Link :href="route('admin.jobs.index')">All Jobs</Link>
```

### Forms

Use Inertia's `<Form>` component or `useForm()` hook. With Wayfinder:

```vue
<Form v-bind="store.form()" v-slot="{ errors, processing }">
  <Input name="title" />
  <InputError :message="errors.title" />
  <Button :disabled="processing">Save</Button>
</Form>
```

---

## Wayfinder — Type-Safe Routes

[Laravel Wayfinder](https://github.com/laravel/wayfinder) generates TypeScript functions for every Laravel route and controller action. The generated files live in (git-ignored):

```
resources/js/actions/    ← One file per controller
resources/js/routes/     ← Named route functions
```

### Usage pattern

```ts
// Import from @/actions for controller actions
import { store } from '@/routes/register'
import { show } from '@/actions/Api/JobController'

// Use in templates
<Link :href="show({ uuid: job.uuid })">View Job</Link>

// With Inertia form
<Form v-bind="store.form()">
```

### Why not use `route()` helper?

Wayfinder gives **TypeScript type checking** on route parameters and is tree-shakeable. Standard Laravel `route()` helper is a runtime JS string and gives no compile-time safety.

---

## Layouts

Layouts wrap page components. They are assigned via the `layout` option in page components or via `defineOptions`.

### `AppLayout.vue`

The **dashboard shell** — sidebar + top nav + main content area. Used by all authenticated admin/recruiter pages.

```
┌──────────────────────────────────────┐
│  AppSidebar  │  <slot /> (page)      │
│              │                        │
│  Nav items:  │                        │
│  Dashboard   │                        │
│  Jobs        │                        │
│  Applications│                        │
│  Candidates  │                        │
│  Users*      │                        │
│  Settings*   │ (*admin only)          │
└──────────────────────────────────────┘
```

### `AuthLayout.vue` → `auth/AuthSimpleLayout.vue`

Wraps the login, register, password reset pages. Renders a **split-panel** layout:

```
┌──────────────────────┬────────────────────┐
│  Left: Dark brand    │  Right: White card  │
│  - Gradient orbs     │  - Form card        │
│  - Hero headline     │  - Title + desc     │
│  - Stats grid        │  - <slot /> (form)  │
│  - Testimonial card  │                     │
└──────────────────────┴────────────────────┘
```

### Public pages (no layout)

`Welcome.vue`, `Careers/Index.vue`, `Careers/Show.vue` are **standalone full-page components** with their own nav and footer. No layout wrapper.

---

## Pages Reference

### Public Pages

| Page | Route | File | Data Source |
|------|-------|------|-------------|
| Homepage | `/` | `Welcome.vue` | Inertia props: `settings`, `jobs`, `stats`, `canRegister` |
| Job Listings | `/careers` | `Careers/Index.vue` | Inertia prop: `jobs` (all published), client-side filter |
| Job Detail + Apply | `/careers/{uuid}` | `Careers/Show.vue` | API: `/api/public/jobs/{uuid}`, POST to apply |

### Admin/Recruiter Pages

| Page | Route | File | Data Source |
|------|-------|------|-------------|
| ATS Dashboard | `/admin/dashboard` | `Admin/Dashboard.vue` | API: `/api/dashboard/stats` |
| Job Listings | `/admin/jobs` | `Admin/Jobs/Index.vue` | Pinia `jobStore` → `/api/jobs` |
| Create Job | `/admin/jobs/create` | `Admin/Jobs/Create.vue` | POST `/api/jobs` |
| Edit Job | `/admin/jobs/{uuid}/edit` | `Admin/Jobs/Edit.vue` | GET/PUT `/api/jobs/{uuid}` |
| Applications | `/admin/applications` | `Admin/Applications/Index.vue` | Pinia `applicationStore` |
| Application Detail | `/admin/applications/{uuid}` | `Admin/Applications/Show.vue` | API: `/api/applications/{uuid}` |
| Candidates | `/admin/candidates` | `Admin/Candidates/Index.vue` | Pinia `candidateStore` |
| Candidate Profile | `/admin/candidates/{uuid}` | `Admin/Candidates/Show.vue` | API: `/api/candidates/{uuid}` |
| User Management | `/admin/users` | `Admin/Users/Index.vue` | API: `/api/users` *(admin only)* |
| Homepage CMS | `/admin/settings/homepage` | `Admin/Settings/Homepage.vue` | API: GET/PATCH `/api/homepage-settings` |

### Auth Pages

| Page | File |
|------|------|
| Login | `auth/Login.vue` |
| Register | `auth/Register.vue` |
| Forgot Password | `auth/ForgotPassword.vue` |
| Reset Password | `auth/ResetPassword.vue` |

---

## Pinia Stores

Stores manage shared client-side state for data that multiple components need.

### `stores/job.ts`

```ts
// Manages job postings list + CRUD actions
const jobStore = useJobStore()
await jobStore.fetchJobs()
await jobStore.createJob(data)
await jobStore.updateJob(uuid, data)
await jobStore.publishJob(uuid)
await jobStore.draftJob(uuid)
await jobStore.deleteJob(uuid)
```

### `stores/application.ts`

```ts
// List + status updates + notes
const applicationStore = useApplicationStore()
await applicationStore.fetchApplications({ status: 'applied' })
await applicationStore.updateStatus(uuid, 'shortlisted')
await applicationStore.addNote(uuid, 'Great candidate!')
```

### `stores/candidate.ts`

```ts
// Candidate listing + profile
const candidateStore = useCandidateStore()
await candidateStore.fetchCandidates()
await candidateStore.fetchCandidate(uuid)
```

### `stores/dashboard.ts`

```ts
// Aggregated dashboard stats
const dashboardStore = useDashboardStore()
await dashboardStore.fetchStats()
// { totalJobs, totalApplications, totalCandidates, byStatus, byEmploymentType }
```

---

## UI Component Library

Located in `resources/js/components/ui/`. These are **shadcn-style** components — accessible, unstyled at their core, styled with Tailwind classes passed as props.

### Available Components

| Component | Usage |
|-----------|-------|
| `Button` | `<Button variant="default|outline|ghost|destructive">` |
| `Input` | `<Input type="text" placeholder="..." />` |
| `Label` | `<Label for="field-id">` |
| `Badge` | `<Badge variant="default|secondary|destructive|outline">` |
| `Card` / `CardHeader` / `CardContent` / `CardFooter` | Content containers |
| `Dialog` / `DialogTrigger` / `DialogContent` | Modal dialogs |
| `DropdownMenu` | Context menus |
| `Select` | Styled select |
| `Checkbox` | Styled checkbox |
| `Separator` | Visual divider |
| `Avatar` | User avatar with fallback initials |
| `Skeleton` | Loading placeholder |
| `Spinner` | Inline loading indicator |
| `Tooltip` | Hover tooltip |
| `Sheet` | Slide-in panel |
| `Sidebar` | Dashboard sidebar primitive |

> **Note:** There is NO `Textarea` component. Use a plain `<textarea>` element styled with Tailwind:
> ```html
> <textarea class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring" />
> ```

---

## Tailwind CSS v4

Talentry uses Tailwind v4. Key differences from v3:

| v3 | v4 | Notes |
|----|----|-|
| `bg-gradient-to-br` | `bg-linear-to-br` | Gradient utility renamed |
| `tailwind.config.js` | No config file | Config via CSS custom properties |
| `@apply` in CSS | Still supported | But prefer utility classes in templates |

### Design Tokens (CSS custom properties in `app.css`)

```css
--color-background: ...
--color-foreground: ...
--color-primary: ...
--color-muted: ...
--color-border: ...
--color-input: ...
--color-ring: ...
--radius: 0.5rem
```

### Brand Colors Used

| Usage | Tailwind Classes |
|-------|----------------|
| Hero/gradient backgrounds | `from-slate-950 via-violet-950 to-indigo-950` |
| Brand gradient (buttons, icons) | `from-violet-600 to-indigo-600` |
| Card borders (light) | `ring-1 ring-slate-100` |
| Accent/focus rings | `ring-violet-500` |
| Amber stars (testimonials) | `fill-amber-400 text-amber-400` |

---

## TypeScript

### Path Aliases

`@/` maps to `resources/js/` (configured in both `tsconfig.json` and `vite.config.ts`):

```ts
import { Button } from '@/components/ui/button'
import { useJobStore } from '@/stores/job'
import AppLayout from '@/layouts/AppLayout.vue'
```

### Type Checking

```bash
npm run types:check   # vue-tsc --noEmit
```

---

## Adding a New Page

1. **Create the Vue page component** in `resources/js/pages/`
   ```
   resources/js/pages/Admin/Reports/Index.vue
   ```

2. **Add the Laravel route** in `routes/web.php`
   ```php
   Route::inertia('reports', 'Admin/Reports/Index')->name('reports.index');
   ```

3. **Pass initial data** from the controller (or use `Route::inertia()` for static pages)
   ```php
   Route::get('reports', ReportController::class)->name('reports.index');
   ```

4. **Add to sidebar** in `AppSidebar.vue` if it needs a nav link

5. **Run Wayfinder** to generate typed route functions
   ```bash
   npm run dev   # Wayfinder runs automatically via Vite plugin
   ```
