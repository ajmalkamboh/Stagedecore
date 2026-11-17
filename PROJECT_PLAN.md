# Stage Decoration Booking & Management System - Project Plan

## 1. Project Overview
A specialized, lightweight, reliable Stage Decoration Booking & Management System tailored for small to medium-sized stage decoration businesses handling both indoor (Marriage Halls, Banquet Halls, Hotels) and outdoor (Homes, Grounds, Hotels, Marquees) events.

## 2. Technology Stack & Architectural Guidelines
- **Framework**: Laravel 12 / Framework v13 (PHP 8.3+)
- **Architecture**: MVC + Service Layer + Reusable Blade Components + Livewire 3 Components
- **UI Framework**: Bootstrap 5 + Falcon Admin Template
- **Database**: MySQL 8.4 (`stagedecore_db`) with strict normalization, foreign keys, decimal precision, indexes, and transaction safety
- **Timezone**: Pakistan Standard Time (`Asia/Karachi`, UTC+5)
- **Currency**: Pakistani Rupee (`PKR`, `Rs.`)
- **No SPA/Fat-Client Bloat**: No React, Vue, Inertia, Tailwind. High speed, low memory footprint.

## 3. Phased Implementation Roadmap

| Phase | Description | Deliverables | Status |
|---|---|---|---|
| **Phase 01** | Foundation & Authentication | User Auth (Admin/Staff), Timezone Asia/Karachi, Business Settings, Falcon Layout Shell | In Progress |
| **Phase 02** | Dashboard & Navigation | Real-time statistics, Financial KPI cards, Recent Bookings, Upcoming Events | Pending |
| **Phase 03** | Master Data Management | Event Types, Venues (with Venue Types), Decoration Categories (CRUD + Activation) | Pending |
| **Phase 04** | Customer Management | Customer CRUD, duplicate mobile warning, Booking & Payment History, Outstanding Balance | Pending |
| **Phase 05** | Stage Booking Management | Booking creation/edit with auto booking #, overlapping warnings, status workflows, financials | Pending |
| **Phase 06** | Booking Calendar & Availability | Interactive FullCalendar monthly/weekly view, status/type filters, quick details modal | Pending |
| **Phase 07** | Payment Management & Customer Ledger | Multi-payment records, Receipts, Customer Ledgers, Refund handling, Balance sync | Pending |
| **Phase 08** | Expense Management | Booking-specific & General Business Expenses, Expense Categories, Expense Reports | Pending |
| **Phase 09** | Financial Reports & Profit/Loss | Sales, Payments, Balances, Direct & General Expenses, Net Profit vs Cash Movement | Pending |
| **Phase 10** | Printing & Documents | A4 & Thermal Printable Booking Confirmation Slip, Payment Receipts, Ledger Statements | Pending |
| **Phase 11** | Final Testing, Security & Backup | Comprehensive automated & manual tests, authorization, audit checks, DB backup tool | Pending |

## 4. Key Business Logic & Financial Integrity Rules
1. **Booking Number**: Auto-generated sequential format `STG-YYYYMM-XXXX`.
2. **Receipt Number**: Auto-generated sequential format `REC-YYYYMM-XXXX`.
3. **Expense Number**: Auto-generated sequential format `EXP-YYYYMM-XXXX`.
4. **Financial Calculations**:
   - `Final Booking Amount = Total Booking Amount - Discount`
   - `Customer Remaining Balance = Final Booking Amount - Total Non-Refunded Payments`
   - `Eligible Sales = Sum of Final Booking Amounts for Confirmed & Completed Bookings` (Cancelled bookings are excluded)
   - `Booking Profit = Booking Final Amount - Direct Booking Expenses`
   - `Overall Net Profit = Eligible Sales - All Expenses (Direct + General)`
   - `Net Cash Movement = Customer Receipts - Total Expenses Paid - Refunds Paid`
5. **Data Protection**:
   - Completed and Cancelled bookings lock financial fields from unintended edits.
   - Financial ledger entries use DB transactions with strict rollback on failure.
