# Database Structure & Schema Design

Database: `stagedecore_db` (MySQL 8.4 Engine: InnoDB, Charset: utf8mb4_unicode_ci)

## Table Definitions

### 1. `users`
System operators and administrators.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `name`: VARCHAR(255) NOT NULL
- `email`: VARCHAR(255) UNIQUE NOT NULL
- `password`: VARCHAR(255) NOT NULL
- `role`: ENUM('admin', 'staff') NOT NULL DEFAULT 'staff'
- `is_active`: BOOLEAN NOT NULL DEFAULT 1
- `remember_token`: VARCHAR(100) NULL
- `created_at`, `updated_at`: TIMESTAMP NULL

### 2. `business_settings`
Single-record configuration for business profile and print headers/footers.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `business_name`: VARCHAR(255) NOT NULL DEFAULT 'Stage Decor & Events'
- `owner_name`: VARCHAR(255) NULL
- `mobile_number`: VARCHAR(50) NULL
- `alternate_number`: VARCHAR(50) NULL
- `email`: VARCHAR(255) NULL
- `address`: TEXT NULL
- `logo_path`: VARCHAR(255) NULL
- `currency`: VARCHAR(10) NOT NULL DEFAULT 'PKR'
- `currency_symbol`: VARCHAR(10) NOT NULL DEFAULT 'Rs.'
- `receipt_footer`: TEXT NULL
- `terms_and_conditions`: TEXT NULL
- `created_at`, `updated_at`: TIMESTAMP NULL

### 3. `customers`
Customer directory with contact and balance summary.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `name`: VARCHAR(255) NOT NULL
- `mobile_number`: VARCHAR(50) NOT NULL (INDEXED)
- `alternate_mobile`: VARCHAR(50) NULL
- `address`: TEXT NULL
- `notes`: TEXT NULL
- `is_active`: BOOLEAN NOT NULL DEFAULT 1
- `created_at`, `updated_at`, `deleted_at`: TIMESTAMP NULL

### 4. `event_types`
Types of events handled (Barat, Walima, Mehndi, Birthday, etc.).
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `name`: VARCHAR(100) NOT NULL UNIQUE
- `description`: TEXT NULL
- `is_active`: BOOLEAN NOT NULL DEFAULT 1
- `created_at`, `updated_at`: TIMESTAMP NULL

### 5. `venues`
Venues directory (indoor marriage halls, outdoor grounds, homes, etc.).
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `name`: VARCHAR(255) NOT NULL
- `venue_type`: ENUM('Marriage Hall', 'Banquet Hall', 'Hotel', 'Home', 'Outdoor Ground', 'Other') NOT NULL
- `address`: TEXT NULL
- `contact_number`: VARCHAR(50) NULL
- `notes`: TEXT NULL
- `is_active`: BOOLEAN NOT NULL DEFAULT 1
- `created_at`, `updated_at`: TIMESTAMP NULL

### 6. `decoration_categories`
Stage decoration tiers/packages.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `name`: VARCHAR(100) NOT NULL
- `description`: TEXT NULL
- `base_price`: DECIMAL(12, 2) NOT NULL DEFAULT 0.00
- `is_active`: BOOLEAN NOT NULL DEFAULT 1
- `created_at`, `updated_at`: TIMESTAMP NULL

### 7. `bookings`
Central stage booking records.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `booking_number`: VARCHAR(50) NOT NULL UNIQUE (INDEXED)
- `customer_id`: BIGINT UNSIGNED NOT NULL (FK -> `customers.id`)
- `event_type_id`: BIGINT UNSIGNED NOT NULL (FK -> `event_types.id`)
- `venue_id`: BIGINT UNSIGNED NULL (FK -> `venues.id`)
- `decoration_category_id`: BIGINT UNSIGNED NULL (FK -> `decoration_categories.id`)
- `booking_type`: ENUM('indoor', 'outdoor') NOT NULL DEFAULT 'indoor'
- `booking_date`: DATE NOT NULL (INDEXED)
- `event_date`: DATE NOT NULL (INDEXED)
- `event_start_time`: TIME NULL
- `event_end_time`: TIME NULL
- `venue_address`: TEXT NULL
- `decoration_details`: TEXT NULL
- `special_instructions`: TEXT NULL
- `total_amount`: DECIMAL(12, 2) NOT NULL DEFAULT 0.00
- `discount`: DECIMAL(12, 2) NOT NULL DEFAULT 0.00
- `final_amount`: DECIMAL(12, 2) NOT NULL DEFAULT 0.00
- `status`: ENUM('tentative', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'tentative'
- `cancellation_reason`: TEXT NULL
- `cancelled_at`: TIMESTAMP NULL
- `created_by`: BIGINT UNSIGNED NOT NULL (FK -> `users.id`)
- `created_at`, `updated_at`, `deleted_at`: TIMESTAMP NULL

### 8. `booking_payments`
Customer payments received or refunded against bookings.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `receipt_number`: VARCHAR(50) NOT NULL UNIQUE (INDEXED)
- `booking_id`: BIGINT UNSIGNED NOT NULL (FK -> `bookings.id`)
- `customer_id`: BIGINT UNSIGNED NOT NULL (FK -> `customers.id`)
- `payment_date`: DATE NOT NULL (INDEXED)
- `amount`: DECIMAL(12, 2) NOT NULL
- `payment_type`: ENUM('advance', 'partial', 'final', 'refund') NOT NULL DEFAULT 'advance'
- `payment_method`: ENUM('cash', 'bank', 'online_transfer', 'cheque', 'other') NOT NULL DEFAULT 'cash'
- `transaction_reference`: VARCHAR(100) NULL
- `remarks`: TEXT NULL
- `received_by`: BIGINT UNSIGNED NOT NULL (FK -> `users.id`)
- `is_refund`: BOOLEAN NOT NULL DEFAULT 0
- `created_at`, `updated_at`: TIMESTAMP NULL

### 9. `expense_categories`
Master categories for operating and event expenses.
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `name`: VARCHAR(100) NOT NULL UNIQUE
- `description`: TEXT NULL
- `is_active`: BOOLEAN NOT NULL DEFAULT 1
- `created_at`, `updated_at`: TIMESTAMP NULL

### 10. `expenses`
Business expenses, either tagged to a booking (direct) or untagged (general overhead).
- `id`: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
- `expense_number`: VARCHAR(50) NOT NULL UNIQUE (INDEXED)
- `expense_category_id`: BIGINT UNSIGNED NOT NULL (FK -> `expense_categories.id`)
- `booking_id`: BIGINT UNSIGNED NULL (FK -> `bookings.id`, ON DELETE SET NULL)
- `expense_date`: DATE NOT NULL (INDEXED)
- `description`: VARCHAR(255) NOT NULL
- `amount`: DECIMAL(12, 2) NOT NULL
- `payment_method`: ENUM('cash', 'bank', 'online_transfer', 'other') NOT NULL DEFAULT 'cash'
- `remarks`: TEXT NULL
- `attachment_path`: VARCHAR(255) NULL
- `recorded_by`: BIGINT UNSIGNED NOT NULL (FK -> `users.id`)
- `created_at`, `updated_at`, `deleted_at`: TIMESTAMP NULL
