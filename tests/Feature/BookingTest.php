<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\EventType;
use App\Models\Venue;
use App\Models\DecorationCategory;
use App\Models\Booking;
use App\Models\BookingPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Customer $customer;
    protected EventType $eventType;
    protected Venue $venue;
    protected DecorationCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->customer = Customer::create(['name' => 'Kashif Ali', 'mobile_number' => '0301-4455667']);
        $this->eventType = EventType::create(['name' => 'Barat']);
        $this->venue = Venue::create(['name' => 'Royal Hall', 'venue_type' => 'Marriage Hall']);
        $this->category = DecorationCategory::create(['name' => 'Floral Stage', 'base_price' => 70000]);
    }

    public function test_can_create_booking_with_advance_payment(): void
    {
        $response = $this->actingAs($this->user)->post('/bookings', [
            'customer_id' => $this->customer->id,
            'booking_date' => '2026-11-20',
            'event_date' => '2026-12-15',
            'event_start_time' => '19:00',
            'event_end_time' => '23:30',
            'event_type_id' => $this->eventType->id,
            'booking_type' => 'indoor',
            'venue_id' => $this->venue->id,
            'decoration_category_id' => $this->category->id,
            'decoration_details' => 'White and peach theme with fresh imported lilies',
            'total_amount' => 100000,
            'discount' => 10000,
            'advance_amount' => 40000,
            'payment_method' => 'cash',
            'status' => 'confirmed',
        ]);

        $booking = Booking::where('customer_id', $this->customer->id)->first();
        $this->assertNotNull($booking);
        $this->assertStringStartsWith('STG-' . date('Ym') . '-', $booking->booking_number);
        $this->assertEquals(90000.00, $booking->final_amount);

        // Check advance payment was recorded in booking_payments
        $payment = BookingPayment::where('booking_id', $booking->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(40000.00, $payment->amount);
        $this->assertEquals('advance', $payment->payment_type);

        // Remaining balance = 90,000 - 40,000 = 50,000
        $this->assertEquals(50000.00, $booking->remaining_balance);
    }

    public function test_warns_on_overlapping_venue_or_stage_resource(): void
    {
        // First booking
        Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'customer_id' => $this->customer->id,
            'event_type_id' => $this->eventType->id,
            'venue_id' => $this->venue->id,
            'decoration_category_id' => $this->category->id,
            'booking_type' => 'indoor',
            'booking_date' => '2026-11-20',
            'event_date' => '2026-12-15',
            'total_amount' => 70000,
            'final_amount' => 70000,
            'status' => 'confirmed',
            'created_by' => $this->user->id,
        ]);

        // Second booking attempting same date and venue
        $customer2 = Customer::create(['name' => 'Waqas Munir', 'mobile_number' => '0322-8889900']);

        $response = $this->actingAs($this->user)->post('/bookings', [
            'customer_id' => $customer2->id,
            'booking_date' => '2026-11-20',
            'event_date' => '2026-12-15',
            'event_type_id' => $this->eventType->id,
            'booking_type' => 'indoor',
            'venue_id' => $this->venue->id,
            'decoration_category_id' => $this->category->id,
            'total_amount' => 70000,
            'discount' => 0,
            'status' => 'confirmed',
        ]);

        // Allowed to proceed, but flash contains overlap notice
        $response->assertSessionHas('success');
        $this->assertStringContainsString('Notice:', session('success'));
    }

    public function test_booking_status_can_be_cancelled_with_reason(): void
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'customer_id' => $this->customer->id,
            'event_type_id' => $this->eventType->id,
            'booking_type' => 'indoor',
            'booking_date' => '2026-11-20',
            'event_date' => '2026-12-15',
            'total_amount' => 80000,
            'final_amount' => 80000,
            'status' => 'confirmed',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post("/bookings/{$booking->id}/cancel", [
            'cancellation_reason' => 'Client postponed event due to family emergency',
        ]);

        $booking->refresh();
        $this->assertEquals('cancelled', $booking->status);
        $this->assertEquals('Client postponed event due to family emergency', $booking->cancellation_reason);
        $this->assertNotNull($booking->cancelled_at);
    }

    public function test_confirmation_slip_renders(): void
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'customer_id' => $this->customer->id,
            'event_type_id' => $this->eventType->id,
            'booking_type' => 'indoor',
            'booking_date' => '2026-11-20',
            'event_date' => '2026-12-15',
            'total_amount' => 80000,
            'final_amount' => 80000,
            'status' => 'confirmed',
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get("/bookings/{$booking->id}/confirmation-slip");
        $response->assertStatus(200);
        $response->assertSee($booking->booking_number);
        $response->assertSee('Booking Confirmation');
    }
}
