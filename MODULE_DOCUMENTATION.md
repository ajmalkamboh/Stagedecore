# Module Documentation & User Guide

## System Credentials & Quick Access
- **Application URL**: `http://localhost/Stagedecore/public` or via `php artisan serve` (`http://localhost:8000`)
- **Admin Account**: `admin@stagedecor.pk` / `password123` (Full system access)
- **Staff Account**: `staff@stagedecor.pk` / `password123` (Operations access)
- **Timezone**: Pakistan Standard Time (`Asia/Karachi`, UTC+5)
- **Currency**: Pakistani Rupee (`PKR`, `Rs.`)

---

## 1. Authentication & User Management
- **Routes**:
  - `GET /login`: Falcon styled login screen
  - `POST /login`: Secure authentication with `is_active` validation
  - `POST /logout`: Session invalidation & token refresh
  - `GET /users`: User management table (Admin only)
  - `GET /users/create`: Create new admin or staff user
  - `GET /users/{user}/edit`: Edit name, email, role, active status, reset password
- **Role Permissions**:
  - `admin`: Has access to all modules, financial reports, user management, business profile settings, and database backups.
  - `staff`: Can manage customers, create/edit bookings, record payments, record expenses, view calendar, and print slips.

---

## 2. Business Profile & Settings
- **Route**: `GET /settings/business` (Admin only)
- **Fields Configurable**:
  - Business Name (e.g. Royal Stage Decor & Event Management)
  - Proprietor / Owner Name
  - Primary Contact Number & Alternate WhatsApp Number
  - Office / Shop Address
  - Currency Code (`PKR`) and Symbol (`Rs.`)
  - Receipt Footer Message (printed on receipts and booking confirmation slips)
  - Standard Terms & Conditions (printed on booking confirmation slips for client signature)
  - Logo Upload: Publicly stored and displayed across slips and headers
- **One-Click Backup**:
  - `GET /backup/download`: Instantly exports complete SQL dump of `stagedecore_db` as an attachment for disaster recovery.

---

## 3. Dashboard
- **Route**: `GET /dashboard`
- **Features**:
  - **Operational Metrics**: Total Bookings, Today's Events count, Confirmed bookings count, Tentative bookings count, Completed count, Cancelled count.
  - **Financial KPI Cards**:
    - Total Sales (Eligible bookings only: Confirmed + Completed)
    - Payments Received (Net cash collected from clients)
    - Pending Customer Balances (Receivables from active bookings)
    - Estimated Net Profit (Eligible Sales minus Total Business Expenses)
  - **Today's Events Widget**: Chronological list of events scheduled for today with customer contact, venue, timing, and quick view link.
  - **Upcoming Events Widget**: Next 6 scheduled events with day, date, venue, and decoration package.
  - **Recent Bookings Table**: Displays latest bookings with final amount, paid amount, balance, status badge, and print action.

---

## 4. Master Data Management
- **Event Types** (`/event-types`):
  - Presets: Wedding, Barat, Walima, Mehndi, Engagement, Birthday, Corporate Event, Other.
  - Supports adding custom event types, editing, and one-click activation/deactivation.
- **Venues Directory** (`/venues`):
  - Preset Venues across Hotels, Marriage Halls, Banquet Halls, Homes, and Outdoor Grounds.
  - Stores venue name, address, contact numbers, setup ceiling height / power notes, and active status toggle.
- **Decoration Categories** (`/decoration-categories`):
  - Stage packages: Basic Stage, Premium Stage, Luxury Stage, Floral Decoration, Custom Decoration, Other.
  - Stores package descriptions, specifications, base price, and active toggle.
- **Expense Categories** (`/expense-categories`):
  - Standard categories: Flowers, Decoration Material, Labour Charges, Transportation, Lighting, Equipment Rental, Stage Material, Refreshments, Miscellaneous, Other.
  - Add, edit, and toggle active status (`Active` / `Inactive`).
  - Search categories by name or description with real-time expense counter.
  - Deletion protection: System strictly prevents deleting categories with linked expense transactions to maintain financial audit integrity.

---

## 5. Customer Directory
- **Routes**: `GET /customers`, `GET /customers/create`, `GET /customers/{customer}`, `GET /customers/{customer}/edit`
- **Features**:
  - Instant live search by customer name, primary mobile, or alternate mobile.
  - **Duplicate Prevention Warning**: Checks if the mobile number is already registered and displays an advisory with a direct link to the existing profile or an option to confirm duplicate entry.
  - **Customer Profile View**:
    - Customer contact information and address.
    - Summary cards: Total Bookings count, Total Invoiced Billed, Payments Received, Current Outstanding Balance.
    - Booking history table with quick actions.
    - Payment transactions table with receipt printing links.

---

## 6. Stage Booking Management
- **Routes**: `GET /bookings`, `GET /bookings/create`, `GET /bookings/{booking}`, `GET /bookings/{booking}/edit`
- **Features**:
  - **Sequential Booking Reference**: Auto-generated format `STG-YYYYMM-XXXX`.
  - **Schedule & Timings**: Booking date, event date, start time, end time.
  - **Booking Type**: Indoor (Halls/Hotels) vs Outdoor (Homes/Lawns/Open Grounds).
  - **Resource Overlap Detection**: Detects if another booking is already scheduled on the same event date for the selected venue or decoration category, issuing a non-blocking notification.
  - **Financial Calculations**:
    - `Final Amount = Total Amount - Discount`
    - `Remaining Balance = Final Amount - Advance Payment`
  - **Automatic Advance Payment**: If advance payment is entered during booking creation, a payment transaction is recorded in `booking_payments` in the same database transaction.
  - **Financial Record Protection**: Completed and Cancelled bookings lock financial amounts from unintended modification.
  - **Booking Cancellation Workflow**: Requires a mandatory cancellation reason and timestamps the cancellation.
  - **Booking Profitability**: Computes direct booking expenses and estimated profit on the booking details page.

---

## 7. Booking Calendar & Availability
- **Routes**: `GET /calendar`, `GET /calendar/events`
- **Features**:
  - FullCalendar monthly, weekly, and list views.
  - Filter by status: All, Confirmed, Tentative, Completed.
  - Filter by setup type: All, Indoor Events, Outdoor Events.
  - Status color codes: Green (Confirmed), Blue (Tentative), Navy (Completed), Red (Cancelled).
  - Interactive event click displays modal with customer, venue, decor package, amount, balance, and quick link to booking.

---

## 8. Payment Management & Customer Ledger
- **Routes**: `GET /payments`, `GET /payments/create`, `GET /payments/{payment}/receipt`, `GET /payments/ledger`
- **Features**:
  - **Sequential Receipt Number**: Auto-generated `REC-YYYYMM-XXXX`.
  - Multi-payment tracking: Advance, Partial, Final, and Refund transactions.
  - Payment modes: Cash, Bank Account, Online Transfer (Raast / JazzCash / Easypaisa), Cheque, Other.
  - Validation blocks payments exceeding the booking's remaining balance.
  - Refunds cannot exceed total paid amount on that booking.
  - **Customer Ledger (`/payments/ledger`)**: Select any customer to view chronological Statement of Account showing all invoices (debits), payments (credits), refunds, and running balances with one-click printing.

---

## 9. Expense Management
- **Routes**: `GET /expenses`, `GET /expenses/create`, `GET /expenses/{expense}/edit`
- **Features**:
  - **Sequential Expense Number**: Auto-generated `EXP-YYYYMM-XXXX`.
  - Direct Booking Expenses: Tagged to a specific `booking_id` for accurate per-event cost and profit calculations.
  - General Business Overhead: Unlinked expenses for shop rent, electricity, vehicle fuel, tool maintenance, staff tea, etc.
  - Category tracking: Flowers, Decoration Material, Labour Charges, Transportation, Lighting, Equipment Rental, Stage Material, Refreshments, Miscellaneous, Other.
  - Attachment upload: Photo or scan of vendor receipts/vouchers.

---

## 10. Financial Reports & Profit/Loss Engine
- **Route**: `GET /reports` (with tabbed navigation and date range filters)
- **Reports Included**:
  1. **Part A & B: Profit/Loss & Cash Movement Statement**:
     - Accrual Sales vs Total Business Expenses = Net Profit.
     - Actual Receipts minus Refunds minus Expenses Paid = Net Cash Movement.
  2. **Daily Sales Report**: Grouped by event date for eligible bookings.
  3. **Monthly Sales Report**: Aggregated by month with total events, gross amount, discounts, and net sales.
  4. **Date-wise Sales Report**: Detailed event list with date range filters.
  5. **Total Payments Received**: Chronological breakdown of client payments by mode and date.
  6. **Outstanding Customer Balances**: Active bookings with unpaid balances and quick collection buttons.
  7. **Daily Expense Report**: Itemized daily operating expenses.
  8. **Monthly Expense Report**: Aggregated monthly expenses.
  9. **Booking-wise Profit Report**: Each booking's revenue, direct expenses, estimated profit, and profit margin percentage.
  10. **Cancelled Booking Report**: List of cancelled bookings with cancellation reasons and refunds paid.

---

## 11. Printing & Printable Documents
- **Booking Confirmation Slip (`/bookings/{booking}/confirmation-slip`)**:
  - Official A4 printable confirmation with business header, client details, schedule, venue, stage decoration specifications, financial breakdown, advance paid, remaining balance, terms & conditions, and signature blocks.
- **Payment Receipt (`/payments/{payment}/receipt`)**:
  - Official payment receipt and refund voucher with customer info, booking reference, payment method, transaction reference, amount received, and updated remaining balance.
