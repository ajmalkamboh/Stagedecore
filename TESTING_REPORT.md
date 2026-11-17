# Testing & Quality Assurance Report

## Summary
- **Test Framework**: PHPUnit 12.5.44
- **Database Engine**: MySQL 8.4 (Production), SQLite in-memory (Automated Tests)
- **Total Tests Executed**: 40
- **Total Assertions**: 151
- **Pass Rate**: 100% (40 passed, 0 failed, 0 errors)
- **Execution Time**: ~4.7 seconds

---

## Detailed Test Log

### 1. General & Navigation (`tests/Feature/ExampleTest.php`)
- `test_root_redirects_to_login_for_guests`: Verifies unauthenticated users attempting to access root `/` are redirected to `/login`, and `/login` returns HTTP 200. **[PASSED]**

### 2. Authentication & Authorization (`tests/Feature/AuthTest.php`)
- `test_login_screen_can_be_rendered`: Verifies login card renders with Falcon theme styling and default credentials preview. **[PASSED]**
- `test_user_can_login_with_valid_credentials`: Verifies admin can log in and receives redirect to `/dashboard`. **[PASSED]**
- `test_user_cannot_login_with_invalid_credentials`: Verifies invalid passwords fail with error message. **[PASSED]**
- `test_inactive_user_cannot_login`: Verifies deactivated accounts are rejected. **[PASSED]**
- `test_user_can_logout`: Verifies session invalidation and CSRF token regeneration on logout. **[PASSED]**
- `test_staff_cannot_access_admin_settings_or_user_management`: Verifies staff accounts receive HTTP 403 Forbidden when attempting to access `/settings/business` or `/users`. **[PASSED]**
- `test_admin_can_access_admin_settings_and_user_management`: Verifies administrators receive HTTP 200 for management screens. **[PASSED]**

### 3. Master Data Management (`tests/Feature/MasterDataTest.php`)
- `test_can_create_and_toggle_event_type`: Verifies EventType creation, persistence, and status toggle. **[PASSED]**
- `test_can_create_and_toggle_venue`: Verifies Venue creation with venue types (Hotel, Marriage Hall, Home, Ground) and status toggle. **[PASSED]**
- `test_can_create_and_toggle_decoration_category`: Verifies Decoration Category creation with decimal base price and status toggle. **[PASSED]**

### 4. Customer Directory (`tests/Feature/CustomerTest.php`)
- `test_can_create_customer`: Verifies customer creation with primary mobile and address. **[PASSED]**
- `test_warns_on_duplicate_mobile_number`: Verifies that adding a customer with an existing mobile triggers a warning alert with an override confirmation button. **[PASSED]**
- `test_customer_show_page_displays_balance_and_records`: Verifies customer details page renders billing, paid amount, and outstanding balances. **[PASSED]**

### 5. Stage Booking Management (`tests/Feature/BookingTest.php`)
- `test_can_create_booking_with_advance_payment`: Verifies sequential `STG-YYYYMM-XXXX` generation, final amount calculation (`total - discount`), and automatic recording of initial advance payment into `booking_payments`. **[PASSED]**
- `test_warns_on_overlapping_venue_or_stage_resource`: Verifies system detects conflict when another event shares the same venue or category on the same event date and issues a non-blocking warning. **[PASSED]**
- `test_booking_status_can_be_cancelled_with_reason`: Verifies cancelling a booking records cancellation reason and cancellation timestamp. **[PASSED]**
- `test_confirmation_slip_renders`: Verifies printable confirmation slip loads with client details, schedule, venue, decor specifications, and terms. **[PASSED]**

### 6. Payment & Customer Ledger (`tests/Feature/PaymentTest.php`)
- `test_can_record_multiple_payments_and_update_balance`: Verifies advance, partial, and final payments incrementally reduce remaining balance until paid. **[PASSED]**
- `test_cannot_exceed_remaining_balance`: Verifies validation blocks payments exceeding the current remaining balance. **[PASSED]**
- `test_can_process_refund_and_adjust_balance`: Verifies refunds cannot exceed total paid, are marked with `is_refund=true`, and adjust remaining balance. **[PASSED]**
- `test_customer_ledger_statement_renders`: Verifies customer statement computes debits, credits, and running balance. **[PASSED]**

### 7. Expense Management (`tests/Feature/ExpenseTest.php`)
- `test_can_record_direct_booking_expense`: Verifies expense tied to a `booking_id` correctly computes booking-level estimated profit (`final_amount - direct_expenses`). **[PASSED]**
- `test_can_record_general_business_overhead`: Verifies unlinked business overhead expenses are recorded with `EXP-YYYYMM-XXXX` reference. **[PASSED]**

### 8. Financial Reports & Database Backup (`tests/Feature/FinancialReportsTest.php`)
- `test_all_10_reports_render_successfully`: Verifies all 10 reports return HTTP 200:
  1. Daily Sales Report
  2. Monthly Sales Report
  3. Date-wise Sales Report
  4. Total Payments Received Report
  5. Outstanding Customer Balances Report
  6. Daily Expense Report
  7. Monthly Expense Report
  8. Booking-wise Profit Report
  9. Overall Profit/Loss & Cash Movement Report
  10. Cancelled Bookings Report **[PASSED]**
- `test_financial_calculations_strictly_verified`:
  - Eligible Sales strictly excludes cancelled bookings.
  - Total operating expenses sums direct + general expenses.
  - Net Profit equals Eligible Sales minus Total Expenses.
  - Net Cash Movement equals Actual Receipts minus Refunds minus Expenses Paid. **[PASSED]**
- `test_database_backup_download`: Verifies streamed SQL database backup produces valid SQL content-type download. **[PASSED]**

### 9. Expense Category Master Data (`tests/Feature/ExpenseCategoryTest.php`)
- `test_expense_categories_screen_can_be_rendered`: Verifies index table renders with categories, status badges, and action buttons. **[PASSED]**
- `test_can_create_expense_category`: Verifies creating new expense category persists with name, description, and active status. **[PASSED]**
- `test_can_update_expense_category`: Verifies updating expense category updates details in database. **[PASSED]**
- `test_can_toggle_expense_category_status`: Verifies status toggles between active and inactive. **[PASSED]**
- `test_cannot_delete_expense_category_with_existing_expenses`: Verifies deletion is prevented and redirected with warning when category has linked expense records. **[PASSED]**
- `test_can_delete_unused_expense_category`: Verifies unused expense category is safely deleted. **[PASSED]**

### 10. Venue Commission & Referral Management (`tests/Feature/VenueCommissionTest.php`)
- `test_venue_can_be_created_with_default_commission`: Verifies setting default commission rate and type on a venue persists. **[PASSED]**
- `test_can_create_booking_with_percentage_commission`: Verifies percentage commission auto-computes from final amount and marks status as pending. **[PASSED]**
- `test_can_create_booking_with_fixed_commission`: Verifies fixed cash commission is recorded and marked pending. **[PASSED]**
- `test_can_record_commission_payout_and_creates_expense`: Verifies payout updates booking status to `paid`, stores paid date/mode/recipient, and automatically generates linked `Expense` under "Venue Commission / Referral Fee". **[PASSED]**
- `test_cannot_repay_already_paid_commission`: Verifies guard against accidental duplicate payouts. **[PASSED]**
- `test_commission_screens_render_successfully`: Verifies Commission Dashboard (`/commissions`), Official Printable Voucher (`/commissions/{id}/voucher`), and Venue Commission Statements Report (`/commissions/report`) all return HTTP 200 with accurate figures. **[PASSED]**
