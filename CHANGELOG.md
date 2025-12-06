# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## 0.3.0 (2025-12-06)

* [feat] add color palette documentation and components for Forfetto 6c2224807da9d92cedc9a3cf6c6ed2550c97465f
* [feat] Add customer management functionality with DataTable and PrimeVue integration e46fa60b1a9c647b4eb59271b8ebcfa7e22dd86b
* [feat] add expense and invoice management pages b13310bdaa9d85a891ebb6b9256f69d979ca1b9a
* [feat] add fiscal settings management and ATECO code handling 63e7b8543d36f88cc6aa7bf87aa4b241b25d9ca0
* [feat] Add Husky and lint-staged configuration for pre-commit hooks de569d8fb975b2d26d6e9b2ca075bcdcc691235a
* [feat] Add Husky commit message validation 7c3059d1b7e864130296918df60d243c72cda312
* [feat] add ignores for merge commits in commitlint configuration 14e44fe1a5d78b3c8ec0c10ab32ae7124d0bca96
* [feat] add no-padding prop to Card component and update DataTableWithPagination usage 7a0e0f80ac4aa1efacb7817ca67870c518ef3526
* [feat] add product management functionality with CRUD operations b1e5c5f860009e65117a3ef12af54b071a9b5cd8
* [feat] add release-it configuration for automated versioning and changelog generation 5f16093a5292e8f4418e2a8700fdefe54a05bd00
* [feat] add SSR script to package.json 241010d15e958f46265e60fe266fed76809fa92f
* [feat] add user initialization and default expense categories setup 49a4acba8b1ebde540a993c379aa6cb26edb7de4
* [feat] Add web app manifest and icons for Forfetto a39b9c673c375c9ffb0592cff5b6804ae996e9a0
* [feat] enhance invoice management with product autocomplete and demo data seeding 56427afe8678408b0f9e4fecf0d07da6a179124b
* [feat] implement contributo integrativo handling in invoice processing and UI e420542db7655c647bce59e2c26a42d3f180e023
* [feat] Implement demo session management and cleanup functionality 4431ba6b9fd356f18ad570f797291d2430e86e88
* [feat] Install laravel 12 85a797f33c1967b70d544dd766aa9a97ef8653c5
* [feat] integrate release-it for automated versioning and changelog generation 6643e75aff26a2f8aec971a5d0d2806bd95c3d94
* [feat] Refactor demo session management and cleanup process 9ac4fc6faad43c19e440bc27665b9229982a0bb1
* [feat] update accent color from modern purple to cool gray for improved readability fa41de07b36b892ffede6345d3a4e3f729708bed
* [feat] update customer management UI and add delete confirmation dialog a8b86e797d33ee9174b40afaed31ec7b126ecdee
* [refactor] Adjust dimensions of AppLogoIcon component for better alignment 099987a8057733a6a292af1a1c6d16077bf3f111
* [refactor] Adjust template structure in AppLogoIcon component for improved layout 7d415e6c1ea601cdef67f8d5d2e07914297199a4
* [refactor] Refactor AppLogo and AppLogoIcon components for improved styling and structure a79027820c6b806e97f6e10169f76bd44159aae9
* [refactor] Refactor code style and improve documentation across multiple files e6fcb4744f86fdf19d888ef68d25645e9bf71f38
* [refactor] Refactor expense category management and related models 3ec017068d3ec92048959dbf51bb47745a845095
* [refactor] remove document_path field from Expense model and related factory and migration b09e3eb2f79cf46d74a1f6146a1c06b894686d60
* [refactor] remove user-specific expense category creation and update related logic f741cb03c4c59e2d366da4ecacd3e32b89301113
* [refactor] remove VAT and deductibility fields from Expense model and related DTOs, forms, and migrations de86e195bd4bfcdbb0a34202247ab63b7cc3e8bd
* [refactor] streamline ATECO code retrieval and enhance demo user factory 55d8f21068da1b203b0f463b42674aadbec8153e
* [style] Update DataTable and Card components for improved layout and styling 3ddca5545af5f70e832fa8cafbd10fb49498e029
* [docs] Update demo credentials information and clarify security notes in documentation 22d4a2d11aa03e18dbc94070ab1562093bb9b157
* [fix] Add missing use statements for UserSession, Carbon, Log, and Str in DemoSession middleware e409f6e430ecd0a9a0fa4de33181a0f7bbd6234c
* [fix] update logo asset path in README eb4045e18c3adc6cbff0c917c7ebd3d8b6ec5415
* [chore] Add comprehensive AI coding instructions for Forfetto project d4ea3f853773e0ffd0d0ffa050b9a7ce49a7fc57
* [chore] Add README content and logo asset aab4a18a088fb42f96b728c4b940fc49b7acd86a
* [chore] Improve formatting and clarity in husky-lint documentation 5569d15d85f54433666e04b3e9aa504f54aa9afd
* [chore] Simplify Husky scripts by removing unnecessary shebang and sourcing 638ef15fc9aa238380a5af35acf896cd2ef2bbdf
* [chore] update logo image and add new assets 2a41cda9f69a2a18a48591370c06d0d569c7b6f7
* first commit 041c479b5ec12152bb0f6f012ab5389b2ce9a189

## [Unreleased]

### ✨ Features

- Demo system with isolated sessions for demo users
- Customer management with complete registry
- Invoice management with automatic withholding tax calculation
- Expense management with custom categories
- Dashboard with KPIs and charts
- Two-factor authentication (2FA)
- Support for Italian flat-tax regime with 5% and 15% rates

### 📚 Documentation

- Demo system documentation
- Expense categories documentation
- Application colors guide
- Husky and linting documentation
