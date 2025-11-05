# Forfetto AI Coding Instructions

## Project Overview

Forfetto is an Italian flat-tax regime (regime forfettario) income tracking application built with Laravel + Vue 3 + Inertia.js. It helps freelancers and sole proprietors track invoices, expenses, and calculate real net income after taxes and contributions.

## Architecture Patterns

### Multi-Tenant User Isolation

All models use `HasUserScope` trait (`app/Traits/HasUserScope.php`) for automatic user scoping:

- Global scope filters by `user_id` automatically
- Auto-assigns `user_id` on creation
- Use `withoutUserScope()` or `forUser($userId)` for admin access

### Data Layer Pattern

- **Models**: Standard Eloquent with relationships (`app/Models/`)
- **DTOs**: Spatie Laravel Data objects for type-safe data transfer (`app/Dtos/`)
- **Services**: Business logic and query building (`app/Services/`)
- **Example**: `CustomerIndexService` extends base `IndexService` with pagination, search, and filtering

### Frontend Architecture

- **Inertia.js** for SPA-like experience with server-side routing
- **Vue 3 Composition API** with TypeScript
- **Shadcn/UI + Tailwind CSS** for UI components
- **PrimeVue** for complex components (data tables, calendars)

## Development Workflows

### Quick Setup

```bash
composer run setup    # Full project setup
composer run dev       # Start dev server with queue, logs, and Vite
composer run test      # Run PHPUnit tests
```

### Code Quality (Automated via Husky)

- **PHP**: Laravel Pint (PSR-12 formatting)
- **JS/TS/Vue**: ESLint + Prettier
- **Pre-commit hooks** run automatically (`docs/husky-lint.md`)

### Docker Development

```bash
./vendor/bin/sail up    # Start Laravel Sail environment
./vendor/bin/sail down  # Stop containers
```

## Code Conventions

### Backend (Laravel)

- **Comments**: Always in English, PHPDoc for public/protected methods
- **Migrations**: Minimal comments unless critical
- **Seeder data**: Use Italian language for realistic data
- **Enums**: Use for constants like `TaxRateEnum`, `PaymentMethodEnum`

### Frontend (Vue 3)

- **Components**: PascalCase, place in appropriate subdirectories
- **Composables**: `use*` pattern for reusable logic (`useTableConfigs`, `useTwoFactorAuth`)
- **Routes**: Use Laravel Wayfinder for type-safe routing
- **Tables**: Use `DataTableWithPagination` component with configuration objects

### File Organization

- **Docs**: Create `.md` files in Italian in `docs/` folder
- **Components**: Shadcn/UI in `components/ui/`, custom in `components/`
- **Types**: Domain-specific types in `types/` directory

## Key Integration Points

### Authentication & Authorization

- **Laravel Fortify** for auth features (2FA, password reset)
- **Inertia middleware** shares auth state globally
- Custom auth views in `resources/js/pages/auth/`

### Database Relationships

- `User` → `Customer` → `Invoice` (one-to-many chains)
- `User` → `Expense` with `ExpenseCategory`
- ATECO codes linked to invoices for tax calculations

### Italian Business Logic

- **Tax calculations**: 5% (first 5 years) vs 15% (standard) rates
- **Invoice fields**: VAT number, tax code, SDI code, PEC for Italian requirements
- **Withholding tax**: 20% deduction calculations when applicable

## Domain-Specific Patterns

### Table Management

Use `useTableConfigs` composable for consistent table behavior:

```vue
const config = useCustomerTableConfig(); const routes =
useRouteHelper('customers');
```

### Data Transfer

Always use DTOs for API responses:

```php
return CustomerDto::collect($paginator->items());
```

### Form Handling

Combine Inertia forms with VeeValidate for client-side validation:

```vue
import { Form } from '@inertiajs/vue3';
```

### Color System

Custom CSS variables for income/expense colors:

- Income: `--color-forfetto-income: #4caf50` (green)
- Expense: `--color-forfetto-expense: #ff5722` (red)
- Accent: `--color-forfetto-accent: #cfd8dc` (cool gray)

## Testing Guidelines

- **Feature tests**: Focus on user workflows and business logic
- **Unit tests**: Critical calculations (tax rates, net income)
- Use factories for test data generation (`database/factories/`)

Remember: This is an MVP focused on Italian business requirements. Keep features simple and user-friendly for small business owners who want to avoid accounting complexity.
