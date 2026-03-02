# API Reference

All API routes are prefixed with `/api`. The SPA uses **Sanctum stateful authentication** (cookie-based session) — the same session as the web app. No Bearer tokens are needed when calling from the browser.

---

## Authentication

### SPA (browser-based)

The Vue frontend authenticates via Laravel Sanctum's stateful cookie authentication. Login/logout are handled by Inertia form submissions through the standard Fortify routes (`/login`, `/logout`). The browser's session cookie is automatically sent with every `/api/*` request.

### External API Clients

For external API access (e.g., mobile apps, Postman), use Sanctum API tokens:

```bash
POST /api/sanctum/token
Content-Type: application/json

{ "email": "admin@talentry.test", "password": "password", "device_name": "my-app" }
```

Then include in requests:
```
Authorization: Bearer {token}
```

---

## Base URL

```
http://localhost:8000/api
```

---

## Public Routes (No Auth Required)

### List Published Jobs

```
GET /api/public/jobs
```

**Query Parameters:**

| Param | Type | Description |
|-------|------|-------------|
| `search` | string | Filter by title or location |
| `type` | string | Filter by employment type: `full_time`, `part_time`, `contract`, `remote` |
| `page` | int | Pagination (default: 1, per page: 15) |

**Response `200`:**
```json
{
  "data": [
    {
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "title": "Senior Laravel Developer",
      "location": "Remote",
      "employment_type": "remote",
      "deadline": "2026-06-01",
      "company": { "name": "Acme Corp", "logo_path": null },
      "applications_count": 12
    }
  ],
  "meta": { "current_page": 1, "last_page": 3, "total": 42 }
}
```

---

### Get Single Job

```
GET /api/public/jobs/{uuid}
```

**Response `200`:**
```json
{
  "uuid": "550e8400-...",
  "title": "Senior Laravel Developer",
  "description": "<rich text HTML>",
  "location": "Remote",
  "employment_type": "remote",
  "deadline": "2026-06-01",
  "company": { "name": "Acme Corp", "website": "https://acme.com" }
}
```

**Response `404`:** Job not found or not published.

---

### Apply for a Job

```
POST /api/public/jobs/{uuid}/apply
Content-Type: multipart/form-data
```

**Request Body:**

| Field | Type | Required | Validation |
|-------|------|----------|-----------|
| `name` | string | ✅ | max:255 |
| `email` | string | ✅ | valid email |
| `phone` | string | ❌ | max:20 |
| `cover_letter` | string | ❌ | max:5000 |
| `resume` | file | ✅ | pdf, doc, docx — max 5MB |

**Response `201`:**
```json
{
  "message": "Application submitted successfully.",
  "uuid": "application-uuid-here"
}
```

**Response `422`:** Validation errors.
```json
{
  "message": "The resume field is required.",
  "errors": { "resume": ["The resume field is required."] }
}
```

---

## Authenticated Routes

All routes below require an active session or Bearer token.

### Current User

```
GET /api/user
```

Returns the authenticated user object.

---

## Dashboard

### Get Stats

```
GET /api/dashboard/stats
```

**Auth:** Any authenticated user.

**Response `200`:**
```json
{
  "total_jobs": 42,
  "total_applications": 158,
  "total_candidates": 134,
  "by_status": {
    "applied": 80,
    "shortlisted": 30,
    "interview": 20,
    "rejected": 15,
    "hired": 13
  },
  "by_employment_type": {
    "full_time": 20,
    "remote": 12,
    "contract": 7,
    "part_time": 3
  }
}
```

---

## Jobs

### List Jobs

```
GET /api/jobs
```

**Auth:** Any authenticated user.

**Query Parameters:**

| Param | Type | Description |
|-------|------|-------------|
| `status` | string | `published` or `draft` |
| `page` | int | |

**Response `200`:** Paginated list of job postings with company and applications count.

---

### Create Job

```
POST /api/jobs
Content-Type: application/json
```

**Auth:** Recruiter or Admin.

**Request Body:**

| Field | Required | Notes |
|-------|----------|-------|
| `title` | ✅ | max:255 |
| `description` | ✅ | |
| `location` | ✅ | max:255 |
| `employment_type` | ✅ | `full_time`, `part_time`, `contract`, `remote` |
| `status` | ✅ | `published`, `draft` |
| `deadline` | ❌ | date format `YYYY-MM-DD` |
| `company_id` | ❌ | defaults to user's company |

**Response `201`:** Created job resource.

---

### Get Job

```
GET /api/jobs/{uuid}
```

**Auth:** Any authenticated user.

**Response `200`:** Full job resource with company.  
**Response `404`:** Not found.

---

### Update Job

```
PUT /api/jobs/{uuid}
Content-Type: application/json
```

**Auth:** Job owner (recruiter) or Admin.  
**Policy:** `JobPostingPolicy::update`

**Request Body:** Same fields as Create Job (all optional for update).

**Response `200`:** Updated job resource.  
**Response `403`:** Not authorised.

---

### Delete Job

```
DELETE /api/jobs/{uuid}
```

**Auth:** Job owner or Admin.  
**Note:** Soft deletes the record — does not destroy it.

**Response `204`:** No content.

---

### Publish Job

```
PATCH /api/jobs/{uuid}/publish
```

Sets `status = 'published'`.

**Response `200`:** Updated job resource.

---

### Draft Job

```
PATCH /api/jobs/{uuid}/draft
```

Sets `status = 'draft'`.

**Response `200`:** Updated job resource.

---

## Applications

### List Applications

```
GET /api/applications
```

**Auth:** Any authenticated user.

**Query Parameters:**

| Param | Type | Description |
|-------|------|-------------|
| `status` | string | Filter by status |
| `job_posting_id` | int | Filter by job |
| `page` | int | |

**Response `200`:** Paginated applications with `jobPosting`, `candidate`, `notes.user` eager-loaded.

---

### Get Application

```
GET /api/applications/{uuid}
```

**Response `200`:** Full application including candidate, job posting, and all notes.

---

### Update Application Status

```
PATCH /api/applications/{uuid}/status
Content-Type: application/json
```

**Request Body:**
```json
{ "status": "shortlisted" }
```

Valid statuses: `applied`, `shortlisted`, `interview`, `rejected`, `hired`

**Response `200`:** Updated application resource.  
**Response `422`:** Invalid status value.

---

### Add Note

```
POST /api/applications/{uuid}/notes
Content-Type: application/json
```

**Request Body:**
```json
{ "note": "Strong candidate — schedule technical interview." }
```

**Response `201`:** Created note with author.

---

### Delete Application

```
DELETE /api/applications/{uuid}
```

**Note:** Soft deletes.

**Response `204`:** No content.

---

## Candidates

### List Candidates

```
GET /api/candidates
```

**Auth:** Any authenticated user.

**Response `200`:** Paginated candidates with applications count.

---

### Get Candidate Profile

```
GET /api/candidates/{uuid}
```

**Response `200`:**
```json
{
  "uuid": "...",
  "name": "Jane Doe",
  "email": "jane@example.com",
  "phone": "+1-555-0100",
  "applications_count": 3,
  "applications": [
    {
      "uuid": "...",
      "status": "shortlisted",
      "job_posting": { "title": "Senior Developer", "uuid": "..." }
    }
  ]
}
```

---

## Users (Admin Only)

### List Users

```
GET /api/users
```

**Auth:** Admin only.

**Response `200`:** All users with company.

---

### Update User Role

```
PATCH /api/users/{id}/role
Content-Type: application/json
```

**Auth:** Admin only.

**Request Body:**
```json
{ "role": "recruiter" }
```

Valid roles: `admin`, `recruiter`

**Response `200`:** Updated user.

---

## Homepage Settings (Admin Only)

### Get Settings

```
GET /api/homepage-settings
```

**Auth:** Admin only.

**Response `200`:**
```json
{
  "hero_title": "Find Your Dream Job Today",
  "hero_subtitle": "...",
  "how_it_works_steps": [ ... ],
  "footer_links": [ ... ]
}
```

---

### Update Settings

```
PATCH /api/homepage-settings
Content-Type: application/json
```

**Auth:** Admin only.

**Request Body:** Any subset of the settings object. Only provided fields are updated.

```json
{
  "hero_title": "Your Next Opportunity Awaits",
  "cta_button": "Start Now"
}
```

**Validation rules:**

| Field | Rules |
|-------|-------|
| `hero_title` | max:200 |
| `hero_subtitle` | max:500 |
| `hero_cta_primary` | max:100 |
| `how_it_works_steps` | array |
| `how_it_works_steps.*.title` | string, max:100 |
| `how_it_works_steps.*.description` | string, max:300 |
| `footer_links` | array |
| `footer_links.*.label` | string, max:50 |
| `footer_links.*.url` | string, max:200 |

**Response `200`:** Full updated settings object.  
**Response `422`:** Validation errors.

---

## Error Responses

All errors follow a consistent shape:

```json
{
  "message": "Human-readable error message.",
  "errors": {
    "field_name": ["Validation message."]
  }
}
```

| HTTP Status | Meaning |
|------------|---------|
| `200` | Success |
| `201` | Created |
| `204` | Success, no content (deletes) |
| `401` | Unauthenticated — no session/token |
| `403` | Forbidden — authenticated but no permission |
| `404` | Resource not found |
| `422` | Validation failed |
| `500` | Server error |
