# SIMORA HRIS — Agent Instructions

> **Human Resource Information System for Sinergi Hotel & Villa**  
> A production-grade Laravel 12 application with multi-level document approval workflows, digital signatures, and role-based dashboards.

---

## 🎯 Project Overview

**Purpose**: Centralized HRIS system enabling:
- Structured document workflows (multi-level approval for official letters/"surats")
- Employee profile management with encrypted PII
- Digital signature (TTD) capture, verification, and PDF stamping
- Attendance tracking with AI name-matching
- Role-based dashboards (HR / Supervisor / HOD / Direktur / Staff)
- Comprehensive audit trails

**Status**: Production-grade, actively maintained.

---

## 🏗️ Architecture

### Service Layer Pattern
Business logic is isolated in `app/Services/` to separate concerns from controllers:
- **[ApprovalService](app/Services/ApprovalService.php)** — Orchestrates multi-step approval workflows
- **[PdfStampService](app/Services/PdfStampService.php)** — Reads approved signatures and overlays them on PDFs
- **[NotificationService](app/Services/NotificationService.php)** — Sends alerts to approvers
- **[PdfMergeService](app/Services/PdfMergeService.php)** — Merges multi-page PDFs
- **[SuratNumberService](app/Services/SuratNumberService.php)** — Generates unique document numbers
- **[PinVerificationService](app/Services/PinVerificationService.php)** — PIN-based approval verification

**Pattern**: Controllers delegate to services → services handle business logic independently → easy to test and reuse.

### Polymorphic Document Approval System
Documents use a flexible approval system via `document_approvals` table:
- **[DocumentApproval](app/Models/DocumentApproval.php)** — Tracks each approval step with: `document_type`, `document_id`, `step_id`, `status`, `approver_id`, `signature_path`, `coordinates` (JSON)
- **[ApprovalStep](app/Models/ApprovalStep.php)** — Defines approval workflow per document type (role, order, optional PIN requirement)
- **[SuratType](app/Models/SuratType.php)** — Template for official documents with associated approval steps

**Why**: New document types (e.g., requisitions, leave requests) inherit approval logic automatically—just add rows to `approval_steps`.

### Role-Based Access Control (Spatie Permission)
- Uses **[Spatie Larevel Permission](https://spatie.be/docs/laravel-permission/)** for fine-grained access
- Roles: `hr_staff`, `supervisor`, `hod`, `direktur`, `owner_rep`
- Check permissions: `auth()->user()->hasRole('hod')` or `auth()->user()->can('approve_surat')`
- Policy-based authorization: [SuratPolicy](app/Policies/SuratPolicy.php) enforces who can view/edit/delete documents

### Custom Middleware Stack
Each route group includes:
- `auth` — Requires logged-in user
- `CheckOnboarding` — User must have completed digital signature (TTD) setup + set PIN before accessing approval features
- `ForceChangePassword` — Redirect if password change is required
- `SessionTimeout` — Auto-logout after inactivity
- `PreventBackHistory` — Prevent browser back-button cache issues

---

## 📂 Key Directory Structure

| Path | Purpose |
|------|---------|
| **[app/Models/](app/Models/)** | Core entities: `Surat`, `EmployeeProfile`, `DocumentApproval`, `ApprovalStep`, `Absensi`, `ActivityLog`, `Notification` |
| **[app/Services/](app/Services/)** | Business logic layer (approval, PDF, notifications) |
| **[app/Http/Controllers/](app/Http/Controllers/)** | Request handlers for routes |
| **[app/Http/Middleware/](app/Http/Middleware/)** | Authorization, onboarding, session checks |
| **[app/Http/Policies/](app/Policies/)** | Model-level access control (e.g., `SuratPolicy`) |
| **[app/Helper/helpers.php](app/Helper/helpers.php)** | Shared utility functions (auto-loaded via composer.json) |
| **[database/migrations/](database/migrations/)** | Schema definitions + constraints for approval workflows |
| **[resources/views/](resources/views/)** | Blade templates (Tailwind CSS 4, Lucide Icons) |
| **[resources/js/](resources/js/)** | JavaScript, Vite-bundled |
| **[routes/web.php](routes/web.php)** | Route definitions |
| **[config/](config/)** | Application config (app, auth, database, permissions) |

---

## 🔄 Common Development Tasks

### 1. **Adding a New Document Type (e.g., Leave Request)**

Steps:
1. Create migration: `php artisan make:migration create_leave_requests_table`
2. Define model: `app/Models/LeaveRequest.php`
3. Add routes in `routes/web.php`
4. Create approval steps via seeder or UI (insert rows into `approval_steps` table with `document_type='leave_request'`)
5. Reuse `ApprovalService::initApproval('leave_request', $requestId)` — no new approval logic needed!

**Pattern**: New document types inherit the entire approval pipeline automatically.

### 2. **Implementing an Approval Endpoint**

Template:
```php
// In SuratController or similar
public function approve(Request $request, Surat $surat)
{
    $this->authorize('approve', $surat);  // Use Policy
    
    // Verify PIN
    PinVerificationService::verify($request->pin, auth()->user());
    
    // Record approval
    ApprovalService::recordApproval(
        document_type: 'surat',
        document_id: $surat->id,
        approver_id: auth()->id(),
        signature_path: $signaturePath,
        coordinates: $coords
    );
    
    // Notify next approver or finalize
    NotificationService::alertNextApprover($surat);
    
    return redirect()->back()->with('success', 'Approval recorded');
}
```

### 3. **Generating a PDF with Signature**

Use `PdfStampService`:
```php
$surat = Surat::find($id);
$pdfPath = PdfStampService::stamp($surat);  // Overlays all signatures onto PDF
Storage::download($pdfPath);
```

### 4. **Adding a Role-Based Feature**

Use `Spatie Permission`:
```php
// In controller
if (auth()->user()->hasRole('hod')) {
    // Show HOD-only dashboard
}

// Or use middleware
Route::middleware('role:hod')->group(function() {
    Route::post('/surats/{id}/approve', [SuratController::class, 'approve']);
});
```

### 5. **Encrypting Sensitive Data**

Use Laravel's built-in encryption via `$casts`:
```php
class EmployeeProfile extends Model {
    protected $casts = [
        'nik' => 'encrypted',          // Encrypted by default
        'bpjs_number' => 'encrypted',
    ];
}
```
Data is automatically encrypted on save, decrypted on retrieval.

---

## 🔐 Security Patterns

### Digital Signature (TTD) Workflow
1. **Capture**: User uploads signature image or draws via canvas
2. **Verify PIN**: `PinVerificationService::verify($pin, $user)`
3. **Store**: Save path and JSON coordinates to `DocumentApproval.ttd_path` / `coordinates`
4. **Stamp PDF**: `PdfStampService::stamp()` overlays signature at stored coordinates

### Data Encryption
- PII fields (`nik`, `bpjs_number`, etc.) are encrypted via `$casts`
- Always decrypt for display: `$user->employee_profile->nik` → auto-decrypted

### Authorization
- Use `Spatie Permission` for role checks
- Use `Policy` classes for model-level authorization: `$this->authorize('approve', $surat)`
- Middleware enforces onboarding (TTD setup) before approval access

---

## 📊 Database Key Concepts

### Approval Workflow Tables
- **`approval_steps`** — Defines workflow per document type (role, order)
  - Example: `document_type='surat', role='hod', step_order=1`
- **`document_approvals`** — Audit log of actual approvals
  - Polymorphic: `document_type` + `document_id` (e.g., `'surat'`, `123`)
- **`surats`** — Official documents with content, current status, creator
- **`surat_types`** — Document templates with associated approval workflow

### Encryption
- `nik`, `bpjs_number` (in `employee_profiles`) — automatically encrypted
- Never query encrypted fields directly; use PHP-side filtering if needed

### Relationships
```
User (1) → (many) EmployeeProfile
User (1) → (many) Surat (creator)
Surat (1) → (many) DocumentApproval
SuratType (1) → (many) ApprovalStep
ApprovalStep (1) → (many) DocumentApproval
```

---

## 🛠️ Build & Run

### First-Time Setup
```bash
composer setup    # Installs deps, generates .env, creates key, runs migrations, builds assets
```

### Development (Concurrent Processes)
```bash
composer dev      # Runs: artisan serve, queue:listen, pail logs, npm dev
# Manually:
php artisan serve        # Backend server
php artisan queue:listen # Job queue
npm run dev             # Vite hot reload
```

### Production Build
```bash
npm run build         # Minifies assets
php artisan migrate   # Apply pending migrations
php artisan db:seed --class=ProductionSeeder --force  # Initial data
```

### Testing
```bash
composer test         # Runs PHPUnit tests
```

---

## 🧠 Conventions & Patterns

### Naming
- **Models**: Singular, PascalCase (`Surat`, `EmployeeProfile`)
- **Services**: Suffix with `Service` (`ApprovalService`, `PdfStampService`)
- **Controllers**: Suffix with `Controller`, use resource methods (`SuratController@store`, `@show`)
- **Migrations**: Timestamp + descriptive snake_case (`2026_04_24_111237_create_approval_tables.php`)
- **Helper functions**: Auto-loaded from `app/Helper/helpers.php`

### Code Style
- PSR-4 autoloading
- Type hints on methods: `public function approve(Surat $surat, Request $request): RedirectResponse`
- Encrypted fields: `$casts['nik'] => 'encrypted'`
- ASCII box comments for visual separation: `// ── relasi ─────────`

### Middleware Stack Order (Important!)
```php
Route::middleware(['auth', 'CheckOnboarding', 'ForceChangePassword', 'SessionTimeout', 'PreventBackHistory'])
    ->group(function() { ... });
```
Order matters: authenticate first, then check onboarding, then check password, then session, then history.

---

## 📝 Key Files to Understand First

1. **[app/Models/Surat.php](app/Models/Surat.php)** — Central document model; defines relationships
2. **[app/Services/ApprovalService.php](app/Services/ApprovalService.php)** — Core approval logic
3. **[database/migrations/](database/migrations/)** — Review approval-related migrations to understand workflow
4. **[routes/web.php](routes/web.php)** — Route structure and middleware assignments
5. **[resources/views/](resources/views/)** — Blade templates using Tailwind CSS 4

---

## ⚠️ Common Pitfalls

| Pitfall | Fix |
|---------|-----|
| Bypassing `CheckOnboarding` middleware | Always include it on approval routes—users need TTD + PIN first |
| Forgetting to decrypt encrypted fields | Use `$user->employee_profile->nik`—Laravel auto-decrypts via `$casts` |
| Creating new document type without adding `approval_steps` rows | Reuse `ApprovalService`; just seed `approval_steps` for your new type |
| Not checking Spatie Permission before showing UI | Use `auth()->user()->hasRole()` or Policy in controller |
| Querying encrypted fields | Don't use `where('nik', '=', value)` on encrypted columns; filter in PHP instead |
| Missing PDF signature coordinates | `DocumentApproval.coordinates` must be set correctly or PDF stamping fails |

---

## 📚 Resources

- **[README.md](README.md)** — Deployment guide and project overview
- **[Laravel 12 Docs](https://laravel.com/docs/12.x)** — Official framework reference
- **[Spatie Permission](https://spatie.be/docs/laravel-permission/)** — Role/permission management
- **[FPDI/FPDF](https://github.com/Setasign/FPDI)** — PDF manipulation library used here

---

## 🚀 Getting Started as an Agent

1. **Understand the problem**: Read the issue or task carefully
2. **Check the Model**: Find the relevant model in `app/Models/` to understand the domain
3. **Locate the Service**: Most business logic is in `app/Services/`—reuse it!
4. **Check Authorization**: Use `Policy` classes and `Spatie Permission`—never skip auth
5. **Review Migrations**: Understand the schema; check `database/migrations/` for table structure
6. **Test Locally**: Run `composer dev` to spin up the full stack; test your changes
7. **Link Existing Code**: Don't duplicate; leverage existing services and patterns

---

**Last Updated**: 2026-07-14  
**Maintained by**: Development Team  
**Questions?** Refer to the [README.md](README.md) or codebase comments.
