<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\EventType;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinancialReportsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $customer = Customer::create(['name' => 'Adnan Siddiqui', 'mobile_number' => '0300-8877665']);
        $eventType = EventType::create(['name' => 'Wedding Reception']);
        $category = ExpenseCategory::create(['name' => 'Labour']);

        // 1. Confirmed Booking (Eligible Sale): 100,000 Final Amount
        $booking1 = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'customer_id' => $customer->id,
            'event_type_id' => $eventType->id,
            'booking_type' => 'indoor',
            'booking_date' => now()->toDateString(),
            'event_date' => now()->toDateString(),
            'total_amount' => 110000,
            'discount' => 10000,
            'final_amount' => 100000,
            'status' => 'confirmed',
            'created_by' => $this->user->id,
        ]);

        // Payment for Booking 1: 60,000 received
        BookingPayment::create([
            'receipt_number' => BookingPayment::generateReceiptNumber(),
            'booking_id' => $booking1->id,
            'customer_id' => $customer->id,
            'payment_date' => now()->toDateString(),
            'amount' => 60000,
            'payment_type' => 'advance',
            'payment_method' => 'cash',
            'received_by' => $this->user->id,
            'is_refund' => false,
        ]);

        // Direct Expense for Booking 1: 15,000
        Expense::create([
            'expense_number' => Expense::generateExpenseNumber(),
            'expense_category_id' => $category->id,
            'booking_id' => $booking1->id,
            'expense_date' => now()->toDateString(),
            'description' => 'Labour for stage setup',
            'amount' => 15000,
            'payment_method' => 'cash',
            'recorded_by' => $this->user->id,
        ]);

        // 2. Cancelled Booking: 50,000 (Should NOT inflate completed sales)
        $booking2 = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'customer_id' => $customer->id,
            'event_type_id' => $eventType->id,
            'booking_type' => 'outdoor',
            'booking_date' => now()->toDateString(),
            'event_date' => now()->toDateString(),
            'total_amount' => 50000,
            'final_amount' => 50000,
            'status' => 'cancelled',
            'cancellation_reason' => 'Bad weather forecast',
            'cancelled_at' => now(),
            'created_by' => $this->user->id,
        ]);

        // Refund disbursed for cancelled booking: 10,000
        BookingPayment::create([
            'receipt_number' => BookingPayment::generateReceiptNumber(),
            'booking_id' => $booking2->id,
            'customer_id' => $customer->id,
            'payment_date' => now()->toDateString(),
            'amount' => 10000,
            'payment_type' => 'refund',
            'payment_method' => 'cash',
            'received_by' => $this->user->id,
            'is_refund' => true,
        ]);

        // 3. General Business Expense: 5,000
        Expense::create([
            'expense_number' => Expense::generateExpenseNumber(),
            'expense_category_id' => $category->id,
            'booking_id' => null,
            'expense_date' => now()->toDateString(),
            'description' => 'Workshop repairs',
            'amount' => 5000,
            'payment_method' => 'cash',
            'recorded_by' => $this->user->id,
        ]);
    }

    public function test_all_10_reports_render_successfully(): void
    {
        $tabs = [
            'profit-loss',
            'sales-daily',
            'sales-monthly',
            'sales-datewise',
            'payments',
            'outstanding',
            'expenses-daily',
            'expenses-monthly',
            'booking-profit',
            'cancelled',
        ];

        foreach ($tabs as $tab) {
            $response = $this->actingAs($this->user)->get("/reports?tab={$tab}");
            $response->assertStatus(200);
        }
    }

    public function test_financial_calculations_strictly_verified(): void
    {
        $response = $this->actingAs($this->user)->get("/reports?tab=profit-loss");
        $response->assertStatus(200);

        // Eligible Sales = 100,000 (Booking 1 only, Cancelled Booking 2 excluded)
        $response->assertSee('100,000.00');

        // Total Expenses = 15,000 (Direct) + 5,000 (General) = 20,000
        $response->assertSee('20,000.00');

        // Accrual Net Profit = 100,000 - 20,000 = 80,000.00
        $response->assertSee('80,000.00');

        // Actual Client Receipts = 60,000.00
        $response->assertSee('60,000.00');

        // Refunds Disbursed = 10,000.00
        $response->assertSee('10,000.00');

        // Net Cash Received = 60,000 - 10,000 = 50,000.00
        $response->assertSee('50,000.00');

        // Net Cash Movement = 50,000 - 20,000 expenses = 30,000.00
        $response->assertSee('30,000.00');
    }

    public function test_database_backup_download(): void
    {
        $response = $this->actingAs($this->user)->get('/backup/download');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/sql');
    }
}
