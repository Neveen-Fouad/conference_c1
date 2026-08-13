# Journovo Travel Platform

Journovo is a full-stack travel-planning platform. It combines live destination, hotel, restaurant, flight, weather, and routing data with AI-generated itineraries, authenticated bookings, Paymob payments, shared trip memories, user dashboards, and an administration panel.

This README describes the current `mohand2` backend and frontend integration, including the latest authentication, ownership, trip, notification, payment, configuration, and casing fixes.

## Repositories and branches

- Backend: `conference_c1`, branch `mohand2`
- Frontend: `TestF`, branch `mohand2`
- Backend API URL in development: `http://127.0.0.1:8000/api`
- Generated API documentation: `http://127.0.0.1:8000/docs/api`

## Current integration status

The frontend and backend contracts are aligned at code level for the implemented user and admin journeys.

- Registration returns an authenticated JWT and the real `client_id`.
- Verification emails open the frontend verification page and retain backend signed-URL validation.
- Verification resend is authenticated and rate-limited.
- Login and profile responses expose the real client record identifier.
- JWT refresh requires a currently valid authenticated JWT. The frontend refreshes proactively before expiry.
- Hotel listing uses `SearchController::searchHotels` at `GET /hotels/search`.
- Flight listing uses `FlightController::listFlights` at `GET /flights/search`.
- Flight details are carried from search results in browser session storage; there is no stale `/flights/{id}` request.
- Restaurant paging accepts the provider-compatible zero-based first page.
- Review identifiers support numeric local IDs and string external hotel, restaurant, and flight IDs.
- Payment and notification ownership is derived from the authenticated user, not trusted request identifiers.
- Trip creation, membership, daily details, and Time Capsule access use the same `client_has_trips` membership relationship.

Production deployment still requires valid third-party credentials, a reachable database and Redis service, mail delivery, Cloudinary configuration, and Paymob sandbox/production verification. Those are environment requirements rather than unresolved route-contract problems.

## Features

### Public travel discovery

- Browse countries and individual country information.
- Load destination weather and attraction data.
- Search hotels by destination, dates, guests, budget, and sorting preference.
- Open hotel details and compare up to three hotels in the frontend.
- Search restaurants by city and provider page.
- Resolve airport names to the provider's Sky ID and entity ID.
- Search live flight itineraries by route, date, cabin, travellers, sorting, and currency.
- Browse curated pre-made trips without authentication.
- Submit contact messages through a rate-limited public form.
- Browse the public interest catalogue.

### Authentication and account security

- Register a user and linked client profile in one database transaction.
- Capture location coordinates from the frontend's OpenStreetMap/Leaflet picker.
- Enforce a minimum age of 18 during registration.
- Login with email and password using JWT authentication.
- Return the JWT, user details, role, and real `client_id` after registration and login.
- Refresh a JWT while the current JWT is still valid.
- Invalidate the JWT on logout.
- Send signed email-verification links to the frontend verification page.
- Resend email verification for authenticated, unverified users.
- Request and complete password resets.
- Rate-limit login, registration, password-reset, email-verification, and contact endpoints.
- Enforce active-account and verified-email middleware where required.

### User profile and preferences

- View and update first name, last name, phone, birth date, and location settings.
- Change password after confirming the current password.
- Select between one and four travel interests.
- Cache profile and dashboard data and invalidate relevant cache entries after updates.

### Trip planning

- Browse pre-made trips.
- List trips belonging to the authenticated client.
- View a trip and its day-by-day itinerary.
- Create manual trips as an administrator.
- Generate personalized trips for users with Groq AI.
- Combine destination, hotel, weather, attraction, restaurant, and transportation information when generating an itinerary.
- Persist destination, dates, class, traveller count, budget, estimated expenses, number of days, style, favorite state, and AI-generated state.
- Create and store daily trip details with title, plan, and estimated expenses.
- Update and delete trips using policy-based ownership checks.
- Produce trip statistics for administrators.

### Joy AI travel assistant

- Start and list per-client chat conversations.
- Load a conversation only when it belongs to the authenticated client.
- Store user and assistant messages with valid `user` and `assistant` roles.
- Generate contextual travel replies through Google Gemini.
- Preserve previous conversation messages as AI context.

### Trip Time Capsule

- Add private notes, photos, and voice memories to a trip.
- Upload media to Cloudinary.
- Restrict access to members of the trip.
- Show only the current member's content and an anonymous contribution count before unlock.
- Unlock all shared memories after the trip reaches its end time.
- Allow a member to delete only their own memory from the correct trip.
- Use one canonical membership pivot, `client_has_trips`; legacy `trip_participants` data is migrated before that table is removed.

### Hotels, flights, bookings, and payments

- Search and inspect live hotels.
- Create hotel bookings from live hotel offers.
- Search live flights and book the selected itinerary.
- Store normalized hotel and flight bookings with provider data, dates, traveller counts, price, currency, and JSON details.
- List all, hotel-only, or flight-only bookings for the authenticated client.
- Create Paymob payment intentions only for bookings owned by the authenticated client.
- Redirect the frontend to Paymob Unified Checkout.
- Verify Paymob webhook HMAC signatures.
- Update payment and booking state after Paymob callbacks.
- List and view only the authenticated client's payments.
- Create payment-success and payment-failure notifications.

### Favourites and reviews

- Save trips, hotels, restaurants, and flights as favourites.
- List, filter, and delete favourites belonging to the authenticated client.
- Submit trip, hotel, restaurant, and flight reviews with ratings, descriptions, and optional images.
- Support string identifiers returned by external providers.
- Let users view approved reviews and their own pending reviews.
- Let users update only their pending reviews and delete only their own reviews.
- Let administrators filter, approve, and reject reviews.
- Notify the correct client when a review is submitted, approved, or rejected.

### Notifications

- Create notifications from login, bookings, reviews, trips, and payments.
- List a client's notifications and unread notifications.
- Return the unread count used by the frontend navigation badge.
- Mark one notification or all client notifications as read.
- Enforce notification ownership in both the controller and repository.
- Restrict direct notification creation to administrators.

### User dashboard

- Show saved trips.
- Show favourite destinations.
- Show booking history.
- Show and update profile settings.
- Report total trips, favourite trips, bookings, favourites, budgets, and estimated expenses.

### Administration

- List and inspect users.
- Activate or deactivate user accounts.
- Create, update, and delete administrators.
- Report user statistics.
- Create, update, and delete travel interests.
- Review, update, and delete contact messages.
- Approve or reject user reviews.
- List bookings for a selected client.
- Create and manage manual trips.
- Read revenue totals through an authenticated admin-only endpoint.
- View monthly dashboard statistics for trips, users, verification, destinations, and revenue.
- Export an A4 PDF dashboard report with generated charts.
- Create and update site name, contact, social, banner, and logo settings.

### Frontend experience

- Responsive static HTML and CSS pages with native JavaScript modules.
- Shared guest, member, and administrator navigation.
- JWT session storage and proactive valid-token refresh.
- Centralized JSON, multipart, PDF-download, error, and unauthorized-response handling.
- Hotel comparison stored in browser session storage.
- Leaflet registration map using OpenStreetMap tiles.
- Dedicated pages for discovery, planning, trips, memories, bookings, payments, notifications, reviews, preferences, profile, and all admin workflows.
- No frontend framework or build step is required for normal static serving.

## Technology stack

### Backend

| Area | Technology |
| --- | --- |
| Language | PHP 8.2+ |
| Framework | Laravel 12 |
| Database | MySQL in normal deployment; SQLite is supported for local verification |
| ORM | Laravel Eloquent |
| Authentication | `tymon/jwt-auth` using the `api` guard |
| Authorization | Laravel policies and `auth:api`, `isAdmin`, `IsActive`, and `VerifiedEmail` middleware |
| Validation | Laravel Form Requests |
| Cache | Redis through Predis |
| Queue | Laravel database queue |
| Media | Cloudinary plus Laravel public storage for applicable assets |
| AI itinerary | Groq through `lucianotonet/groq-laravel` |
| AI chat | Google Gemini REST API |
| Payments | Paymob Unified Checkout and signed webhooks |
| Reports | Dompdf and QuickChart-generated chart images |
| API docs | Dedoc Scramble/OpenAPI |
| Static analysis | Larastan/PHPStan |
| Formatting | Laravel Pint |
| Tests | PHPUnit |

Laravel Sanctum and Stripe libraries are installed, but the active API authentication is JWT and the active payment flow is Paymob.

### Frontend

| Area | Technology |
| --- | --- |
| Markup and styling | HTML5 and CSS3 |
| Application code | Native ES modules and Fetch API |
| Maps | Leaflet with OpenStreetMap tiles |
| State | `localStorage` for authentication and `sessionStorage` for selected comparison/search data |
| Runtime configuration | `config.js` |
| Static server | Any HTTP static server, including `npx serve` |

### External services

- REST Countries API
- WeatherAPI
- Hotels.com provider through RapidAPI
- Sky Scrapper flight provider through RapidAPI
- Tripadvisor restaurant provider through RapidAPI
- OpenRouteService, with Haversine estimates as a routing fallback
- Groq
- Google Gemini
- Cloudinary
- Paymob
- SMTP mail provider
- QuickChart for report chart images

## Architecture

```text
Frontend pages
    -> shared Fetch API client
        -> Laravel routes and middleware
            -> controllers and Form Requests
                -> services and repositories
                    -> Eloquent/MySQL, Redis, and external APIs
```

Important backend directories:

```text
app/Http/Controllers/       HTTP orchestration
app/Http/Requests/          request validation
app/Http/Middleware/        account, role, and verification guards
app/Models/                 Eloquent domain models
app/Policies/               resource authorization
app/Repositories/           persistence and query layer
app/Services/               business logic and external integrations
database/migrations/        database schema history
routes/api.php              API routes
config/                     service, JWT, cache, mail, and storage configuration
```

## API groups

All paths below are relative to `/api`.

| Area | Main endpoints |
| --- | --- |
| Authentication | `POST /auth/register`, `/auth/login`, `/auth/logout`, `/auth/refresh`, `/auth/forgot-password`, `/auth/reset-password`, `/auth/email/verification-notification`; `GET /auth/email/verify/{id}/{hash}` |
| Profile | `GET/PATCH /profile`, `PATCH /profile/password` |
| Discovery | `GET /countries`, `/countries/{country}`, `/explore`, `/destination-data`, `/interests` |
| Hotels | `GET /hotels/search`, `/hotels/details`; `POST /hotels/bookings` |
| Flights | `GET /flights/search-airport`, `/flights/search`; `POST /flights/book` |
| Restaurants | `GET /restaurants`, `/restaurants/details` |
| Trips | `GET/POST /trips`, `GET/PUT/PATCH/DELETE /trips/{trip}`, `GET /trips/pre-made`, `/trips/{trip}/tripDays`, `POST /ai/trips` |
| Memories | `GET/POST /trips/{trip}/memories`, `DELETE /trips/{trip}/memories/{memory}` |
| Bookings | `GET /bookings`, `/bookings/hotels`, `/bookings/flights` |
| Payments | `POST /payments`, `GET /payments/client/{clientId}`, `/payments/{paymentId}`, `POST /paymob/webhook` |
| Favourites | `GET/POST /favourites`, `DELETE /favourites/{id}` |
| Reviews | `GET/POST /reviews`, `GET /reviews/my`, `GET/POST/DELETE /reviews/{id}` |
| Notifications | Client list, unread, count, single-read, and read-all routes under `/notifications` |
| Dashboard | Saved trips, favourites, bookings, profile settings, and statistics under `/dashboard` |
| Joy | Conversation list/detail and message creation under `/chat` |
| Transportation | `POST /transportation/tips` |
| Contact | `POST /contact` |
| Admin | Users, admins, interests, reviews, contact messages, bookings, trips, settings, revenue, dashboard statistics, and PDF export |

The live generated reference is available at `/docs/api` after starting Laravel.

## Local setup

### Requirements

- PHP 8.2 or newer
- Composer
- MySQL
- Redis
- Node.js only if using the optional backend Vite assets or `npx serve` for the frontend
- Credentials for the external features you intend to exercise

### Backend

```bash
git clone https://github.com/sarah-548/conference_c1.git
cd conference_c1
git switch mohand2
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan storage:link
php artisan serve
```

Start the queue worker in another terminal when using queued jobs:

```bash
php artisan queue:work
```

On Windows PowerShell, replace `cp` with `Copy-Item`.

### Required environment areas

Configure these values in `.env` without committing real secrets:

- `APP_URL` must be the externally reachable backend URL used in signed links.
- `FRONTEND_URL` must be the frontend origin, without a trailing page path.
- `CORS_ALLOWED_ORIGINS` is a comma-separated allowlist of frontend origins. Localhost ports are the development default; production must set the deployed HTTPS frontend origin.
- `DB_*` and `REDIS_*` configure MySQL and Redis.
- `JWT_SECRET` is generated with `php artisan jwt:secret`.
- `MAIL_*` configures verification and password-reset email delivery.
- `CLOUDINARY_URL` enables Time Capsule media uploads.
- Hotel, flight, restaurant, country, weather, Groq, Gemini, and OpenRouteService keys enable their respective features.
- `PAYMOB_*` values configure checkout, HMAC verification, notification URL, and redirect URL.

Paymob's notification URL must be a public HTTPS URL ending in `/api/paymob/webhook`.

### Frontend

```bash
git clone https://github.com/Neveen-Fouad/TestF.git
cd TestF
git switch mohand2
npx --yes serve . -l 3000
```

Configure `config.js`:

```js
window.JOURNOVO_CONFIG = Object.freeze({
  API_BASE_URL: "http://127.0.0.1:8000/api"
});
```

Open the frontend through its HTTP URL. Do not open HTML files with `file://`, because browser module and CORS rules will block the application.

## Authentication contract

Registration and login return this core shape:

```json
{
  "data": {
    "token": "jwt-token",
    "user": {
      "id": 1,
      "client_id": 1,
      "first_name": "Journovo",
      "last_name": "Traveler",
      "email": "traveler@example.com",
      "role": "user"
    }
  }
}
```

Authenticated requests use:

```http
Authorization: Bearer <jwt-token>
Accept: application/json
```

`POST /auth/refresh` is protected by `auth:api`; an already expired token is rejected. The frontend reads the JWT expiry and refreshes during the final valid minute. If the browser was inactive until after expiry, the user signs in again.

## Security and data ownership

- Email verification URLs require a valid temporary signature.
- Payment booking ownership is checked against the authenticated client's bookings.
- Payment and notification route IDs cannot be used to access another client's records.
- Revenue and direct notification creation are admin-only.
- Trip CRUD uses policy checks; memories additionally verify trip membership and memory ownership.
- Reviews and favourites derive ownership from the authenticated JWT user/client relationship.
- Public sensitive endpoints have request throttles.
- Example environment values contain placeholders only.
- Generated Laravel cache files and Redis dumps are not versioned.

Before production, set `CORS_ALLOWED_ORIGINS` to the deployed frontend and rotate any credential that was ever committed to Git history. Removing a secret from the latest file does not revoke it or erase earlier commits.

## Latest `mohand2` changes

- Implemented registration and authenticated verification resend.
- Added frontend-routed, backend-signed verification email links.
- Added real `client_id` values to registration, login, and profile data.
- Kept refresh protected and changed the frontend to refresh proactively while the JWT is valid.
- Secured payment, notification, and revenue ownership/access.
- Fixed hotel and flight listing routes and removed invalid resource routes.
- Corrected trip creation fields, user/client membership lookup, and Time Capsule authorization.
- Consolidated trip membership data into `client_has_trips`.
- Fixed chat roles, middleware authentication calls, notification client IDs, Redis configuration, and Paymob client data.
- Normalized PSR-4 model, service, and seeder names and removed case-colliding duplicate files.
- Removed generated cache/Redis artifacts and credentials from `.env.example`.
- Added string external review identifiers and provider-compatible restaurant paging.
- Removed the stale frontend flight-details API call and client-controlled payment/review ownership fields.

## Verification commands

```bash
composer validate --no-check-publish
composer dump-autoload --optimize
php artisan route:list
php artisan migrate:fresh --force
vendor/bin/phpstan analyse --no-progress
vendor/bin/pint --test
php artisan test
```

Frontend JavaScript modules can be syntax-checked with:

```bash
node --check src/shared/api.js
node --check src/pages/register.js
node --check src/pages/reviews.js
node --check src/pages/payments.js
```

No new feature test was added as part of the `mohand2` work.

## Deployment checklist

- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Set correct backend `APP_URL` and frontend `FRONTEND_URL`.
- Run migrations and seed only the data required by the environment.
- Configure MySQL, Redis, mail, Cloudinary, AI, provider, and Paymob credentials.
- Run the queue worker under a process manager.
- Cache Laravel configuration and routes after environment configuration.
- Restrict CORS to the deployed frontend origin.
- Use HTTPS for frontend, backend, verification links, and payment callbacks.
- Test registration email, hotel and flight providers, AI calls, Cloudinary upload, and Paymob in sandbox before switching to production credentials.
- Revoke any secret that appeared in repository history.

## License

This project is based on Laravel's MIT-licensed application skeleton and was developed as a team travel-platform case study.
