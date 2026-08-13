# Smart AI Travel Planner

A Laravel-based travel planning platform built as a case study for the Backend Development Council. Users can plan trips, search and book hotels/restaurants/flights, get AI-generated trip suggestions, and manage everything through a personal dashboard — with a full admin panel behind it.

## Tech Stack

- **Framework:** Laravel 12
- **Language:** PHP 8+
- **Database:** MySQL
- **Auth:** JWT (`tymon/jwt-auth` with `auth:api`)
- **Frontend:** Bootstrap 5
- **Media Storage:** Cloudinary
- **External APIs:** RapidAPI (hotels, restaurants, flights), Countries API, Weather API
- **Payments:** Paymob (webhook-based)

## Team

Built by **Team3** as part of a shared case study — three teams are independently building the same project brief.

## Features

### Core

- **Auth** — register, login, logout, token refresh, forgot/reset password, email verification
- **Profile** — view/update profile, change password
- **Trips** — full CRUD, AI-generated trip suggestions (`/ai/trips`), per-user trip history, day-by-day trip details
- **Hotels / Restaurants / Flights** — search, details, and booking, sourced from external APIs rather than stored locally
- **Interests & Favourites** — users tag interests and favourite destinations/hotels/etc., used to personalize results
- **Reviews** — users leave reviews on trips; admins approve or reject before they go public
- **Notifications** — per-client notification feed, including unread state
- **Payments** — Paymob integration with webhook handling for payment confirmation
- **Contact / Complaints** — public contact form; admins view, respond to, and manage status
- **Dashboard** — saved trips, favourite destinations, booking history, profile settings, personal statistics
- **Admin Panel** — user & admin management, site settings, interests management, revenue tracking, PDF statistics export
- **Countries & Explore** — destination data to support trip planning and discovery

### New: Trip Time Capsule 🎁

A feature built specifically to give the app a "family" angle no other team is likely to have.

Any client on a shared trip can quietly add a photo, note, or voice memo to the trip at any point — but nobody, including the person who added it, can see anyone else's contributions until the trip actually ends. Once the end date passes, the whole capsule unlocks at once: every photo and note from every family member, revealed together, like a small shared movie of the trip nobody saw being made.

**How it works:**
- Media (photos/voice notes) is uploaded directly to **Cloudinary** — nothing is stored on the app's own server.
- Access is scoped to actual trip members, using the existing `client_has_trips` relationship — no separate invite system needed.
- The "unlock" isn't a manual action; it's simply time-gated (`start_date + number_of_days`), so it happens naturally with no extra step for the user.

**Endpoints:**

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/trips/{trip}/memories` | Add a photo, note, or voice memo to a trip's capsule |
| `GET` | `/trips/{trip}/memories` | View the capsule — own contributions + a teaser count before unlock, full capsule after |
| `DELETE` | `/trips/{trip}/memories/{memory}` | Remove your own contribution before the trip ends |

All three require authentication and trip membership.

## API Documentation

Full endpoint-by-endpoint reference (request bodies, params, response codes) is generated from the project's OpenAPI spec — see `Conference1_API_Documentation.md`.

> ⚠️ **Known spec issue:** two Complaint routes (`DELETE`/`PATCH status`) currently show up without their `/admin/contact-messages` prefix in the generated spec, due to a route-naming collision. The actual routes in `routes/api.php` are correctly prefixed — this only affects the auto-generated documentation, not the live API.

## Getting Started

```bash
git clone <repo-url>
cd conference_c1
composer install
cp .env.example .env
php artisan key:generate
```

Configure your `.env`:

```env
DB_CONNECTION=mysql
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
```

Then:

```bash
php artisan migrate
php artisan serve
```

Base URL (local): `http://127.0.0.1:8000/api`

## Project Structure Highlights

```
app/
  Http/Controllers/   → thin controllers, one per resource
  Models/              → Eloquent models (Trip, Client, Booking, TripMemory, ...)
  Services/            → business logic layer (e.g. TripMemoryService)
database/migrations/   → schema history
routes/api.php         → all API routes
config/filesystems.php → includes the 'cloudinary' disk for media uploads
```

## Notes for Contributors

- Auth middleware is `auth:api` and uses JWT. Refreshing requires a currently valid authenticated JWT.
- Admin-only routes are additionally protected with an `isAdmin` middleware.
- File uploads (photos/voice notes) go through the `cloudinary` filesystem disk, not local storage — use `$file->store($path, 'cloudinary')`, not `Storage::disk('public')`.
- Trip membership, including Time Capsule access, uses the `client_has_trips` pivot table.
