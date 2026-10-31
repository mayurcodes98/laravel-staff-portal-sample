# Staff Portal Sample

**Staff Portal Sample** is an original, self-contained portfolio project built with
**Laravel 13** and **PHP 8.3+** to demonstrate production-grade coding standards: thin
controllers, single-purpose Actions, Policy-driven authorization, Form Request validation,
queued event listeners, and a swappable SMS abstraction. It models a small internal staff
portal with passwordless OTP login, role-based access control, a department-scoped document
library, and an activity log.

> **Note**: This is a wholly original sample project. It shares no code, logic,
> configuration, or naming with any client engagement. All names, identifiers, and
> data are invented.

---

## Requirements

| Requirement | Version |
|---|---|
| PHP | **≥ 8.3** |
| Laravel | **13** (`^13.17`) |
| Composer | ≥ 2.x |
| SQLite | bundled with PHP (default) or MySQL 8+ |
| Queue worker | `php artisan queue:work` (database driver by default) |

---

## Folder Structure

```
app/
  Actions/                   # Single-purpose business-logic classes (one public execute())
    Auth/
      SendOtpAction.php      #   Generate OTP, store hash, dispatch SendOtpJob
      VerifyOtpAction.php    #   Check expiry / attempts / hash, authenticate on success
    Documents/
      UploadDocumentAction.php   #   Store file + DB record atomically, fire DocumentUploaded
      DownloadDocumentAction.php #   Stream stored file to caller
      DeleteDocumentAction.php   #   Remove file from disk + delete DB record
  Contracts/
    SmsGatewayInterface.php  # send(string $phone, string $message): void
  Events/
    DocumentUploaded.php     # Plain event carrying Document + User
  Exceptions/
    OtpException.php         # Typed domain exceptions (expired / invalid / tooManyAttempts)
  Http/
    Controllers/             # Thin: validate → call action → return response (≤25 lines/method)
      Auth/
        OtpRequestController.php
        OtpVerifyController.php
      Admin/
        DocumentController.php
        UserController.php
      Employee/
        DocumentController.php
    Middleware/
      EnsureRole.php         # Registered as alias 'role'; aborts 403 on mismatch
    Requests/                # All validation rules live here — never inline in controllers
      Auth/
        OtpRequestRequest.php      # phone: E.164 regex
        OtpVerifyRequest.php       # code: N digits
      Documents/
        UploadDocumentRequest.php  # MIME types, max file size, dept exists
    Resources/
      DocumentResource.php   # API Resource — never return raw Eloquent models
      UserResource.php
  Jobs/
    SendOtpJob.php           # Queued; resolves SmsGatewayInterface from container
  Listeners/
    LogDocumentActivity.php  # ShouldQueue; auto-discovered via handle(DocumentUploaded) type-hint
  Models/
    User.php                 # isAdmin(), departments(), otpCodes(), activityLogs()
    Department.php           # users(), documents()
    OtpCode.php              # scopeActive(), isExpired(), isUsed()
    Document.php             # scopeVisibleTo(User), department(), uploader()
    ActivityLog.php          # UPDATED_AT = null; meta cast to array
  Policies/
    DocumentPolicy.php       # viewAny, view (dept-scoped), create, delete — all admin/employee logic here
  Providers/
    AppServiceProvider.php   # Binds SmsGatewayInterface; registers named OTP rate limiters
    AuthServiceProvider.php  # Registers DocumentPolicy; defines manage-users Gate
  Services/
    Sms/
      LogSmsGateway.php      # Implements SmsGatewayInterface via Log::info (swap for prod)
config/
  otp.php                    # TTL, digits, max attempts, rate limits, sms_driver
  portal.php                 # Role names, allowed MIME types, max upload size
database/
  factories/                 # Model factories with named states (admin, employee, expired, maxedOut)
  migrations/                # Schema with FK constraints and composite indexes
  seeders/                   # 3 departments, 1 admin, 3 employees
resources/views/             # Minimal unstyled Blade templates (UI intentionally omitted)
routes/
  web.php                    # Auth routes, admin group (role:admin), employee group (role:employee)
tests/
  Feature/
    Auth/OtpTest.php         # 8 scenarios: job dispatch, enumeration guard, success, expired,
                             #   wrong code, maxed attempts, send 429, verify 429
    Authorization/
      AccessControlTest.php  # Role & department access control (5 scenarios)
    Documents/
      UploadValidationTest.php  # MIME rejection, size rejection, valid upload
      ActivityLogTest.php       # Log row created on upload
```

---

## Design Decisions

### Actions instead of fat controllers or fat models

Each Action class has exactly one public method (`execute`). This keeps controllers under
25 lines, makes business rules independently unit-testable, and avoids "God controller"
and "God model" anti-patterns. Adding a new delivery channel (CLI command, API endpoint)
reuses the same action without duplication.

### Form Requests own all validation

Rules live exclusively in `app/Http/Requests/` — never `$request->validate()` inline.
This keeps controllers single-responsibility, makes rules reusable across channels, and
means all validation errors serialize and redirect automatically with zero controller code.

### Policies for authorization

`DocumentPolicy` centralises all "who can do what with a Document" rules. Controllers call
`$this->authorize(...)` — there are no scattered `if ($user->role === 'admin')` checks in
business logic. The `manage-users` Gate in `AuthServiceProvider` provides a named shortcut
for the UserController.

### Queued listeners for activity logging

`LogDocumentActivity` implements `ShouldQueue` and is auto-discovered by Laravel via the
`handle(DocumentUploaded $event)` type-hint — no `EventServiceProvider` mapping required.
Deferring the write to a queue worker keeps the HTTP response fast and makes the log write
retriable on failure.

### SmsGatewayInterface + config-driven binding

The SMS carrier is fully swappable: `OTP_SMS_DRIVER` in `.env` controls which concrete
class `AppServiceProvider` binds to `SmsGatewayInterface`. The shipped `LogSmsGateway`
writes to the application log. No vendor SDK is referenced outside its own implementation
class, so changing carrier requires adding one class and one `match` arm.

### Two-dimensional OTP send rate limit

The `otp-send` limiter applies two independent `Limit` buckets per request: one keyed by
phone number and one by IP address. This prevents both targeted subscriber harassment
(many IPs → one phone) and credential-spray from a single source (one IP → many phones).

### API Resources

`DocumentResource` and `UserResource` define an explicit contract between the server and
view/client. Raw Eloquent models are never returned — this prevents accidental attribute
leakage and decouples the response shape from the database schema.

### Consistent file + database writes in UploadDocumentAction

The file is stored to disk before the database write. If the database write throws,
the `catch` block deletes the orphaned file, keeping storage and the database consistent
without a separate cleanup job.

---

## Running the Project

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy the environment file and generate an application key
cp .env.example .env
php artisan key:generate

# 3. Run migrations and seed demo data
#    Creates: Engineering / Finance / HR departments,
#             1 admin (Alex Admin, +12025550001),
#             3 employees across the three departments.
php artisan migrate --seed

# 4. (Optional) Start a queue worker to process jobs and listeners
php artisan queue:work

# 5. Run the test suite
php artisan test

# 6. Check code style
./vendor/bin/pint --test
```

> **SQLite default**: Laravel will create `database/database.sqlite` automatically.
> Switch to MySQL by setting `DB_CONNECTION=mysql` and the corresponding credentials.

---

## What I Would Add Next

- **Tailwind CSS + Blade component library** — views are intentionally unstyled; the
  architecture focus is the backend.
- **Two-factor grace period** — allow a short authenticated session window so users are
  not re-prompted on every page load.
- **Document versioning** — a `document_versions` table tracking revisions with diff metadata.
- **Full-text search** — Meilisearch via Laravel Scout for document titles and content.
- **Admin audit trail UI** — paginated, filterable view of the `activity_logs` table.
- **JSON API layer** — expose the same actions over a JSON API with Sanctum token auth,
  reusing all existing Form Requests and Actions unchanged.
- **GitHub Actions CI** — run `php artisan test` and `pint --test` on every push.
- **Docker Compose** — zero-config local setup with MySQL, Redis queue, and a worker
  container.
