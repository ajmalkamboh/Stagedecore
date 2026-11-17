<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\EventType;
use App\Models\Booking;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ExpenseCategory $category;
    protected Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->category = ExpenseCategory::create(['name' => 'Flowers']);

        $customer = Customer::create(['name' => 'Hamza Javed', 'mobile_number' => '0300-1112244']);
        $eventType = EventType::create(['name' => 'Mehndi']);

        $this->booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'customer_id' => $customer->id,
            'event_type_id' => $eventType->id,
            'booking_type' => 'indoor',
            'booking_date' => '2026-11-20',
            'event_date' => '2026-12-10',
            'total_amount' => 80000,
            'final_amount' => 80000,
            'status' => 'confirmed',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_can_record_direct_booking_expense(): void
    {
        $response = $this->actingAs($this->user)->post('/expenses', [
            'expense_date' => '2026-12-09',
            'expense_category_id' => $this->category->id,
            'booking_id' => $this->booking->id,
            'description' => 'Fresh yellow marigold & imported roses for stage arch',
            'amount' => 25000,
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect("/bookings/{$this->booking->id}");
        $this->assertDatabaseHas('expenses', [
            'booking_id' => $this->booking->id,
            'amount' => '25000.00',
        ]);

        $this->assertEquals(25000, $this->booking->refresh()->total_expenses);
        // Estimated profit = 80,000 - 25,000 = 55,000
        $this->assertEquals(55000, $this->booking->estimated_profit);
    }

    public function test_can_record_general_business_overhead(): void
    {
        $response = $this->actingAs($this->user)->post('/expenses', [
            'expense_date' => '2026-11-20',
            'expense_category_id' => $this->category->id,
            'booking_id' => null,
            'description' => 'Office electricity bill & generator fuel',
            'amount' => 12000,
            'payment_method' => 'bank',
        ]);

        $response->assertRedirect('/expenses');
        $this->assertDatabaseHas('expenses', [
            'booking_id' => null,
            'amount' => '12000.00',
        ]);
    }
}
