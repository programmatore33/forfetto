# Forfetto AI Coding Instructions

## Project Overview

Forfetto is an Italian flat-tax regime (regime forfettario) income tracking application built with **Laravel 12.35.1 + Vue 3 + Inertia.js + TypeScript**. It helps freelancers and sole proprietors track invoices, expenses, and calculate real net income after taxes and contributions.

**Key Tech Stack:**

- Backend: Laravel 12 (uses `bootstrap/app.php` structure, not Kernel)
- Frontend: Vue 3 Composition API + TypeScript, Inertia.js for SPA routing
- UI: Shadcn/UI + Tailwind CSS + PrimeVue (tables/calendars)
- Data Layer: Eloquent models → Spatie Laravel Data DTOs → Services
- Auth: Laravel Fortify (2FA, password reset)

## Critical Architecture: Multi-Tenant Demo System

**ALL data models MUST use `HasUserScope` trait** (`app/Traits/HasUserScope.php`). This is non-negotiable for data isolation.

### How HasUserScope Works

```php
// Every model with user data needs this
class Invoice extends Model {
    use HasUserScope;  // Auto-filters by user_id + session_id (for demos)
}

// Global scope automatically applied:
Invoice::all();  // Only returns current user's invoices

// For demo users, also filters by session_id from cookie
// Demo users with same account get isolated data per browser session
```

### Demo System Architecture (`docs/sistema-demo.md`)

1. **DemoSession Middleware** (`app/Http/Middleware/DemoSession.php`) runs on EVERY authenticated request
2. Checks if `Auth::user()->is_demo === true`
3. Looks for `demo_session_id` cookie:
   - **Exists + valid?** Continue with that session
   - **Expired/missing?** Create new UUID session, populate with `DemoDataSeeder`
4. Sessions expire after 24 hours, cleaned by `demo:cleanup` (scheduled hourly in `routes/console.php`)

**CRITICAL:** When adding new data models, add `session_id` column (nullable) and `HasUserScope` trait, or demo users will see each other's data.

### Admin Access Patterns

```php
// Bypass user scope for admin operations
Model::withoutUserScope()->get();
Model::forUser($userId)->get();  // View specific user's data
Model::forDemoSession($sessionId, $userId);  // Demo session data
Model::demoOnly()->get();  // All demo records (cleanup)
```

## Development Workflows

### Essential Commands

```bash
# Full project setup (first time)
composer run setup

# Development (starts Laravel server, queue, logs, Vite in parallel)
composer run dev

# Run tests
composer run test

# Demo system management
php artisan demo:cleanup --dry-run  # Preview what would be deleted
php artisan demo:cleanup --force    # Delete expired demo sessions

# Laravel Sail (Docker)
./vendor/bin/sail up
```

### Code Quality (Automatic via Husky)

Pre-commit hooks run automatically on `git commit`:

- **PHP files**: Laravel Pint (PSR-12)
- **JS/TS/Vue files**: ESLint + Prettier

No manual formatting needed. If commit fails, hooks found unfixable issues.

## Code Conventions

### Backend (Laravel)

**Comments:**

- All comments in English
- PHPDoc required for public/protected methods
- Minimal comments in migrations unless critical
- Seeder data and user-facing strings in Italian

**Laravel 12 Specifics:**

- Middleware registered in `bootstrap/app.php` (not Kernel)
- Scheduled tasks in `routes/console.php` (not Console Kernel)
- Application structure: `Application::configure()` pattern

**Data Flow:**

```php
// Standard request pattern
Controller → Service (business logic) → Model (Eloquent)
         ← DTO (Spatie Data) ← Service ← Model
```

**Example:**

```php
// InvoiceController.php
public function index(InputIndexDto $inputIndexDto) {
    $result = $this->invoiceIndexService->getInvoices($inputIndexDto);
    return Inertia::render('Invoices/Index', [
        'invoices' => PaginatedResponseDto::fromServiceResult($result),
    ]);
}

// InvoiceIndexService.php extends IndexService
public function getInvoices(InputIndexDto $input): array {
    $query = Invoice::query();  // HasUserScope auto-applied
    $paginator = $this->buildQuery($query, $input, $searchable, $sortable);
    return [
        'data' => InvoiceDto::collect($paginator->items()),
        'meta' => [...pagination data...],
    ];
}
```

**Enums:**
Use PHP 8.2+ enums for constants (see `app/Enums/`):

```php
enum TaxRateEnum: string {
    case REDUCED = '5.00';   // 5% (Primi 5 anni)
    case STANDARD = '15.00'; // 15% (Standard)

    public function label(): string { ... }
    public function percentage(): float { ... }
}
```

### Frontend (Vue 3 + TypeScript)

**Inertia.js Patterns:**

```typescript
// app.ts: Single entry point
createInertiaApp({
  resolve: (name) =>
    resolvePageComponent(
      `./pages/${name}.vue`,
      import.meta.glob<DefineComponent>('./pages/**/*.vue'),
    ),
  // PrimeVue configured with Aura theme + dark mode support
});
```

**Component Organization:**

- `resources/js/components/ui/` - Shadcn/UI primitives (Button, Input, Card, etc.)
- `resources/js/components/` - Custom components (InvoiceForm, DataTableWithPagination)
- `resources/js/pages/` - Inertia page components (route targets)
- `resources/js/composables/` - Reusable logic (`use*` pattern)

**Table Management Pattern:**

```typescript
// composables/useTableConfigs.ts
export const useInvoiceTableConfig = () => ({
  columns: [...],
  actions: { edit: true, delete: true },
  entityName: 'fattura',
  routePrefix: 'invoices',
});

// In component:
import { useInvoiceTableConfig } from '@/composables/useTableConfigs';
const config = useInvoiceTableConfig();
```

**Form Handling:**

```vue
<script setup lang="ts">
import { reactive } from 'vue';

interface Invoice { ... }

const form = reactive<Invoice>({ ... });

const onSubmit = () => {
  emit('submit', form);  // Parent handles Inertia form submission
};
</script>
```

**Routing:**
Use Laravel Wayfinder for type-safe routes (avoids hardcoding URLs).

### File Organization

- **Documentation:** `.md` files in Italian in `docs/` folder
- **Migrations:** Minimal comments, use `session_id` nullable for demo support
- **Seeders:** Italian language for realistic data
- **Types:** Domain-specific TypeScript types in `resources/js/types/`

## Italian Business Logic (Regime Forfettario)

**Tax Rates (TaxRateEnum):**

- 5%: First 5 years (Primi 5 anni)
- 15%: Standard rate after

**Invoice Fields (Italian Requirements):**

- `vat_number` (P.IVA) - 11 digits
- `tax_code` (Codice Fiscale) - 16 alphanumeric
- `sdi_code` (Codice SDI) - 7 chars for e-invoicing
- `pec` (Posta Elettronica Certificata) - Certified email

**Contributo Integrativo:**

- Optional 4% professional contribution on invoices
- Checkbox in invoice form toggles calculation
- Added to net amount: `net_amount = amount + (amount * 0.04)`

**ATECO Codes:**

- Italian economic activity codes linked to invoices
- Each user can have multiple ATECO codes, one marked `is_primary`
- Used for tax calculations and reporting

## Testing

**Feature Tests:** User workflows (auth, CRUD operations, demo isolation)
**Unit Tests:** Calculations (tax rates, net income), trait behavior
**Factories:** Generate realistic Italian test data

```php
// Test demo isolation
$demoUser = User::factory()->demo()->create();
$this->actingAs($demoUser);
// Create data, verify it's isolated by session_id
```

Run: `composer run test` (clears config cache first)

## Common Pitfalls

1. **Forgetting HasUserScope on new models** → Data leakage between users/demos
2. **Hardcoding routes instead of using Wayfinder** → Breaks type safety
3. **Not handling demo `session_id` in migrations** → Demo system breaks
4. **Comments in wrong language** → Code comments English, user-facing Italian
5. **Using old Laravel Kernel patterns** → Laravel 12 uses `bootstrap/app.php`

## Quick Reference

**Demo Credentials (Public):** `demo@forfetto.it` / `demo123`
**Color CSS Variables:** `--color-forfetto-income` (green), `--color-forfetto-expense` (red)
**Decimal Precision:** Always `decimal:2` for currency fields
**Middleware Stack:** `HandleAppearance` → `HandleInertiaRequests` → `DemoSession` (see `bootstrap/app.php`)

## Project Philosophy

This is an **MVP focused on simplicity** for small business owners. Keep features:

- Simple (avoid accounting jargon)
- User-friendly (clear Italian labels)
- Fast (optimized queries with pagination)
- Reliable (automatic data isolation, no manual session management)
