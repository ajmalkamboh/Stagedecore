<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\EventType;
use App\Models\Booking;
use App\Models\BookingPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Customer $customer;
    protected Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->customer = Customer::create(['name' => 'Faisal Butt', 'mobile_number' => '0300-4443322']);
        $eventType = EventType::create(['name' => 'Walima']);

        $this->booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'customer_id' => $this->customer->id,
            'event_type_id' => $eventType->id,
            'booking_type' => 'indoor',
            'booking_date' => '2026-11-20',
            'event_date' => '2026-12-20',
            'total_amount' => 100000,
            'discount' => 10000,
            'final_amount' => 90000,
            'status' => 'confirmed',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_can_record_multiple_payments_and_update_balance(): void
    {
        // 1. Advance Payment of 30,000
        $response1 = $this->actingAs($this->user)->post('/payments', [
            'booking_id' => $this->booking->id,
            'payment_date' => '2026-11-20',
            'amount' => 30000,
            'payment_type' => 'advance',
            'payment_method' => 'cash',
        ]);

        $this->booking->refresh();
        $this->assertEquals(30000, $this->booking->total_paid);
        $this->assertEquals(60000, $this->booking->remaining_balance);

        // 2. Partial Payment of 40,000
        $response2 = $this->actingAs($this->user)->post('/payments', [
            'booking_id' => $this->booking->id,
            'payment_date' => '2026-12-01',
            'amount' => 40000,
            'payment_type' => 'partial',
            'payment_method' => 'bank',
        ]);

        $this->booking->refresh();
        $this->assertEquals(70000, $this->booking->total_paid);
        $this->assertEquals(20000, $this->booking->remaining_balance);

        // 3. Final Payment of 20,000
        $response3 = $this->actingAs($this->user)->post('/payments', [
            'booking_id' => $this->booking->id,
            'payment_date' => '2026-12-20',
            'amount' => 20000,
            'payment_type' => 'final',
            'payment_method' => 'online_transfer',
        ]);

        $this->booking->refresh();
        $this->assertEquals(90000, $this->booking->total_paid);
        $this->assertEquals(0, $this->booking->remaining_balance);
        $this->assertEquals('Paid', $this->booking->payment_status);
    }

    public function test_cannot_exceed_remaining_balance(): void
    {
        $response = $this->actingAs($this->user)->from('/payments/create')->post('/payments', [
            'booking_id' => $this->booking->id,
            'payment_date' => '2026-11-20',
            'amount' => 95000, // Remaining balance is 90,000
            'payment_type' => 'final',
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect('/payments/create');
        $response->assertSessionHasErrors('amount');
        $this->assertEquals(0, $this->booking->refresh()->total_paid);
    }

    public function test_can_process_refund_and_adjust_balance(): void
    {
        // Pay 50,000
        BookingPayment::create([
            'receipt_number' => BookingPayment::generateReceiptNumber(),
            'booking_id' => $this->booking->id,
            'customer_id' => $this->customer->id,
            'payment_date' => '2026-11-20',
            'amount' => 50000,
            'payment_type' => 'advance',
            'payment_method' => 'cash',
            'received_by' => $this->user->id,
            'is_refund' => false,
        ]);

        $this->assertEquals(50000, $this->booking->refresh()->total_paid);

        // Refund 20,000
        $response = $this->actingAs($this->user)->post('/payments', [
            'booking_id' => $this->booking->id,
            'payment_date' => '2026-11-25',
            'amount' => 20000,
            'payment_type' => 'refund',
            'payment_method' => 'cash',
            'remarks' => 'Event date changed, partial advance returned',
        ]);

        $this->booking->refresh();
        // Net paid = 50,000 - 20,000 = 30,000
        $this->assertEquals(30000, $this->booking->total_paid);
        // Balance = 90,000 - 30,000 = 60,000
        $this->assertEquals(60000, $this->booking->remaining_balance);
    }

    public function test_customer_ledger_statement_renders(): void
    {
        $response = $this->actingAs($this->user)->get("/payments/ledger?customer_id={$this->customer->id}");
        $response->assertStatus(200);
        $response->assertSee('Statement of Account');
        $response->assertSee($this->customer->name);
    }
}
