# Issue Tracker - Smart Library

This document tracks bugs, issues, and improvements discovered during development and testing.

## Issues Fixed

### ISSUE-001: Role Enum Missing isAdmin() Method
**Status**: ✅ Fixed
**Priority**: High
**Discovered**: During functional testing implementation
**Impact**: UserPolicy failed when checking if user is admin

**Description**:
The `Role` enum was missing an `isAdmin()` method, causing the `UserPolicy` to fail when attempting to check if a user is an admin before allowing role changes.

**Root Cause**:
- `UserPolicy` was calling `$model->roleEnum()->isAdmin()` which didn't exist
- The `Role` enum only had helper methods like `canManageUsers()`, `isStaff()`, etc.

**Fix Applied**:
- Updated `UserPolicy` to use direct comparison: `$model->roleEnum() === \App\Enums\Role::Admin`
- Applied to both `update()` and `delete()` methods in `UserPolicy`

**Files Modified**:
- `app/Policies/UserPolicy.php`

**Test Coverage**:
- `RbacAccessTest::test_admin_cannot_demote_the_last_admin`

---

### ISSUE-002: FinePolicy Relationship Loading Issue
**Status**: ✅ Fixed
**Priority**: Medium
**Discovered**: During fine management testing
**Impact**: Anti-IDOR check for fines could fail due to lazy loading

**Description**:
The `FinePolicy::view()` method was attempting to access `$fine->loan->user_id` without ensuring the relationship was loaded, which could cause N+1 queries or fail in certain scenarios.

**Root Cause**:
- Eloquent relationships are lazy-loaded by default
- Accessing nested relationships without eager loading can cause issues

**Fix Applied**:
- Added `$fine->load('loan')` before accessing `$fine->loan->user_id`
- Ensures the relationship is loaded before checking ownership

**Files Modified**:
- `app/Policies/FinePolicy.php`

**Test Coverage**:
- `FineManagementTest::test_member_can_view_own_fines`
- `FineManagementTest::test_member_cannot_view_other_members_fines`

---

### ISSUE-003: Admin Self-Update Role Change Not Protected
**Status**: ✅ Fixed
**Priority**: High
**Discovered**: During RBAC testing
**Impact**: Admin could potentially demote themselves if they were the last admin

**Description**:
The `UserPolicy::update()` method allowed admins to update their own profile without checking if they were attempting to change their own role when they are the last admin.

**Root Cause**:
- The policy returned `true` immediately when `$user->is($model)` without checking for last admin protection
- This bypassed the last admin check in the logic flow

**Fix Applied**:
- Added last admin check within the self-update condition
- Now self-updates are also subject to last admin protection

**Files Modified**:
- `app/Policies/UserPolicy.php`

**Test Coverage**:
- `RbacAccessTest::test_admin_cannot_demote_the_last_admin`

---

### ISSUE-004: Password Hashing Missing in Profile Update
**Status**: ✅ Fixed
**Priority**: Medium
**Discovered**: During edge case testing
**Impact**: Password could be stored in plain text

**Description**:
The `AkunController::updateSandi()` method was assigning the password directly without hashing it before saving to the database.

**Root Cause**:
- Missing `Hash::make()` call when updating password
- Code used `$user->update(['password' => $validated['password']])` instead of `$user->update(['password' => Hash::make($validated['password'])])`

**Fix Applied**:
- Added `Hash::make()` wrapper around the password value
- Ensures password is always hashed before storage

**Files Modified**:
- `app/Http/Controllers/Siswa/AkunController.php`

**Test Coverage**:
- `EdgeCaseTest::test_password_update_with_confirmation`

---

### ISSUE-005: Responsive Layout Breakdown on Mobile and Tablet Breakpoints
**Status**: ✅ Fixed
**Priority**: High
**Discovered**: During Responsive & Mobile Quality Check (390px, 768px, 1280px)
**Impact**: Fixed sidebar with 288px width (`w-72`) squeezed mobile view to 102px, header text and footer elements overlapped, and unscrollable tables/modals broke mobile UX.

**Description**:
1. Admin layouts used a fixed non-collapsing sidebar (`pl-72`, `left-72`) which left only ~102px on 390px mobile screens, causing content and text overlapping.
2. `admin/dashboard.blade.php` was standalone with hardcoded non-responsive layout.
3. Login page header & footer had static large padding and non-wrapping elements that caused text colliding and horizontal scroll on 390px.
4. Siswa views had excessive padding (`px-10 py-8`) and unscrollable modal forms on mobile/tablet.

**Fix Applied**:
- Implemented responsive mobile drawer navigation with backdrop overlay and hamburger menu toggles across admin and siswa layouts.
- Refactored `admin/dashboard.blade.php` to extend `layouts.admin` with responsive Bento grid KPI cards and interactive split view.
- Added responsive typography, flex wrapping, and safe margins (`px-4 sm:px-6 lg:px-8/px-10`).
- Ensured all tables have `overflow-x-auto` with `whitespace-nowrap` on action/badge columns to eliminate text overlapping.
- Made modal dialogs responsive with `max-h-[90vh]` and `overflow-y-auto`.

**Files Modified**:
- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/siswa.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/auth/login.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/buku/index.blade.php`
- `resources/views/admin/tambah-buku.blade.php`
- `resources/views/admin/peminjaman/index.blade.php`
- `resources/views/admin/pengembalian/index.blade.php`
- `resources/views/admin/users/index.blade.php`
- `resources/views/petugas/dashboard.blade.php`
- `resources/views/siswa/dashboard.blade.php`
- `resources/views/siswa/katalog.blade.php`
- `resources/views/siswa/pinjaman.blade.php`
- `resources/views/siswa/riwayat.blade.php`
- `resources/views/siswa/kartu.blade.php`
- `resources/views/siswa/profil.blade.php`

**Test Coverage**:
- `ResponsiveLayoutTest::test_login_page_renders_responsive_viewport_and_mobile_classes`
- `ResponsiveLayoutTest::test_admin_pages_render_responsive_mobile_drawer_and_desktop_sidebar`
- `ResponsiveLayoutTest::test_siswa_pages_render_responsive_mobile_drawer_and_desktop_sidebar`
- `ResponsiveLayoutTest::test_table_views_have_overflow_x_auto_for_mobile_and_tablet_scrolling`

---

### ISSUE-006: Security Hardening & Performance/Usability Refinement
**Status**: ✅ Fixed
**Priority**: High
**Discovered**: Security and Performance Audit
**Impact**: Missing database indexes on foreign keys reduced query speed; potential quote-breaking XSS vectors existed in inline modal data attributes; lack of file upload sanitization and submit loading feedback.

**Description**:
1. Foreign keys in `loans` (`book_id`, `user_id`) and high-frequency search columns lacked explicit B-Tree database indexes.
2. Edit buttons in `admin/buku/index` and `admin/users/index` embedded JSON payloads inside single-quoted `onclick` attributes, susceptible to attribute truncation and XSS if titles or names contained quotes.
3. No centralized secure file upload sanitization service existed to protect against executable uploads, path traversal, and polyglot files.
4. Absence of uniform breadcrumb navigation and form submit loading states to prevent double submissions.

**Fix Applied**:
- **Database Indexes**: Created migration `2026_11_01_165250_add_performance_indexes_to_tables.php` adding indexes for `loans (book_id, user_id, (user_id, status), (status, batas_kembali), tanggal_dikembalikan)`, `fines (status_pembayaran)`, `books (judul, pengarang, jumlah_stok)`, and `users (role)`.
- **Query Optimization**: Configured `Model::preventLazyLoading(! app()->isProduction())` in `AppServiceProvider` to strictly prevent N+1 queries.
- **XSS & SQL Injection Hardening**: Verified parameter binding across all Eloquent queries; migrated modal triggers to HTML5 `data-*` attributes with automatic attribute escaping and `dataset` reading.
- **File Upload Sanitization**: Created `App\Services\FileUploadSanitizer` providing MIME magic-byte verification, canonical extension mapping, cryptographic hash storage naming, path traversal neutralization, and executable signature detection.
- **Breadcrumb Navigation**: Created reusable `<x-breadcrumb>` component and integrated across all Admin and Siswa views.
- **Loading Indicators**: Added universal form submit loading script (`partials/loading-script.blade.php`) across `layouts.admin`, `layouts.siswa`, and `layouts.app` that displays a spinning indicator and disables submit buttons upon submission.

**Files Modified**:
- `app/Providers/AppServiceProvider.php`
- `app/Services/FileUploadSanitizer.php`
- `database/migrations/2026_11_01_165250_add_performance_indexes_to_tables.php`
- `database/migrations/2026_11_01_165423_add_avatar_to_users_and_cover_image_to_books_tables.php`
- `app/Http/Controllers/Admin/BukuController.php`
- `app/Http/Controllers/Siswa/AkunController.php`
- `app/Models/User.php`
- `app/Models/Book.php`
- `resources/views/components/breadcrumb.blade.php`
- `resources/views/partials/loading-script.blade.php`
- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/siswa.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/buku/index.blade.php`
- `resources/views/admin/tambah-buku.blade.php`
- `resources/views/admin/peminjaman/index.blade.php`
- `resources/views/admin/pengembalian/index.blade.php`
- `resources/views/admin/users/index.blade.php`
- `resources/views/petugas/dashboard.blade.php`
- `resources/views/siswa/dashboard.blade.php`
- `resources/views/siswa/katalog.blade.php`
- `resources/views/siswa/pinjaman.blade.php`
- `resources/views/siswa/riwayat.blade.php`
- `resources/views/siswa/kartu.blade.php`
- `resources/views/siswa/profil.blade.php`
- `tests/Feature/SecurityAndPerformanceTest.php`

**Test Coverage**:
- `SecurityAndPerformanceTest::test_database_performance_indexes_exist`
- `SecurityAndPerformanceTest::test_sql_injection_payloads_in_search_are_parameterized_and_safe`
- `SecurityAndPerformanceTest::test_xss_payloads_in_user_and_book_data_are_properly_escaped`
- `SecurityAndPerformanceTest::test_csrf_token_directive_is_present_on_all_modifying_forms`
- `SecurityAndPerformanceTest::test_file_upload_sanitizer_accepts_valid_image_and_generates_random_hash_filename`
- `SecurityAndPerformanceTest::test_file_upload_sanitizer_blocks_dangerous_executable_extensions`
- `SecurityAndPerformanceTest::test_file_upload_sanitizer_blocks_polyglot_and_embedded_php_code`
- `SecurityAndPerformanceTest::test_file_upload_sanitizer_neutralizes_directory_traversal_filenames`
- `SecurityAndPerformanceTest::test_breadcrumbs_render_on_admin_and_siswa_pages`
- `SecurityAndPerformanceTest::test_loading_indicator_script_is_included_in_layouts`
- `SecurityAndPerformanceTest::test_no_lazy_loading_violations_occur_on_circulation_and_dashboard_pages`

---

### ISSUE-007: Implementation of Invoice & Receipt Generation Feature (IMPROVEMENT-001)
**Status**: ✅ Fixed
**Priority**: Medium
**Discovered**: Feature Roadmap (IMPROVEMENT-001)
**Impact**: Members and librarians lacked official printable receipts and invoice records for loans and overdue fine payments.

**Description**:
Required official printable and PDF-ready receipts for book loan transactions and fine payments, equipped with anti-IDOR protection so students can only access their own receipts, while librarians can access receipts for all circulation items.

**Fix Applied**:
- Created `App\Http\Controllers\InvoiceController` with dedicated endpoints `invoices.loan` and `invoices.fine`.
- Implemented policy-based authorization using `LoanPolicy` and `FinePolicy` to enforce strict anti-IDOR isolation.
- Built responsive, print-optimized Blade views (`resources/views/invoices/loan.blade.php` and `resources/views/invoices/fine.blade.php`) featuring official library header, transaction metadata, itemized collection/penalty tables, and system verification hash.
- Integrated print action buttons across `siswa.pinjaman`, `siswa.riwayat`, `admin.peminjaman`, and `admin.pengembalian`.

**Files Modified**:
- `routes/web.php`
- `app/Http/Controllers/InvoiceController.php`
- `app/Http/Controllers/Siswa/PinjamanController.php`
- `resources/views/invoices/loan.blade.php`
- `resources/views/invoices/fine.blade.php`
- `resources/views/siswa/pinjaman.blade.php`
- `resources/views/siswa/riwayat.blade.php`
- `resources/views/admin/peminjaman/index.blade.php`
- `resources/views/admin/pengembalian/index.blade.php`
- `tests/Feature/InvoiceGenerationTest.php`

**Test Coverage**:
- `InvoiceGenerationTest::test_member_can_view_and_print_own_loan_invoice`
- `InvoiceGenerationTest::test_member_cannot_view_other_members_loan_invoice_anti_idor`
- `InvoiceGenerationTest::test_staff_can_view_any_members_loan_invoice`
- `InvoiceGenerationTest::test_member_can_view_and_print_own_fine_receipt`
- `InvoiceGenerationTest::test_member_cannot_view_other_members_fine_receipt_anti_idor`
- `InvoiceGenerationTest::test_staff_can_view_any_fine_receipt`
- `InvoiceGenerationTest::test_unauthenticated_user_redirected_to_login`
- `InvoiceGenerationTest::test_receipt_links_are_rendered_on_circulation_views`

---

## Open Issues (No Known Issues)

All discovered issues have been resolved. The application currently has 0 open issues.

## Potential Improvements (Future Work)

### IMPROVEMENT-002: Add Email Notifications
**Status**: Not Started
**Priority**: Low
**Description**: Send email notifications for due date reminders and fine notifications
**Estimated Effort**: 3-4 hours

### IMPROVEMENT-003: Add Book Reservation System
**Status**: Not Started
**Priority**: Low
**Description**: Allow members to reserve books that are currently checked out
**Estimated Effort**: 6-8 hours

---

## Issue Statistics

- **Total Issues Tracked**: 7
- **Issues Fixed**: 7 (100%)
- **Open Issues**: 0
- **Test Coverage**: 111 tests covering all fixed issues

---

## Notes

- All issues were discovered during the implementation of functional tests
- Issues were prioritized based on security impact and user experience
- Each fix includes corresponding test coverage to prevent regression
- Code formatting (Laravel Pint) applied after all fixes
