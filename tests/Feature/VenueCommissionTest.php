<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\EventType;
use App\Models\Venue;
use App\Models\DecorationCategory;
use App\Models\Booking;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\BusinessSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VenueCommissionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $customer;
    protected $eventType;
    protected $venue;
    protected $decorationCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@stagedecor.pk',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        BusinessSetting::create([
            'business_name' => 'Royal Stage Decorators',
            'currency_code' => 'PKR',
            'currency_symbol' => 'Rs.',
            'mobile_number' => '0300-1234567',
        ]);

        $this->customer = Customer::create([
            'name' => 'Kamran Ali',
            'mobile_number' => '0300-9988771',
            'address' => 'Model Town, Lahore',
            'is_active' => true,
        ]);

        $this->eventType = EventType::create([
            'name' => 'Walima',
            'is_active' => true,
        ]);

        $this->venue = Venue::create([
            'name' => 'Grand Marquee Gulberg',
            'venue_type' => 'Banquet Hall',
            'address' => 'Main Boulevard, Gulberg, Lahore',
            'contact_number' => '0321-4455667',
            'default_commission_type' => 'percentage',
            'default_commission_rate' => 10.00,
            'is_active' => true,
        ]);

        $this->decorationCategory = DecorationCategory::create([
            'name' => 'Luxury Floral Stage',
            'base_price' => 150000.00,
            'is_active' => true,
        ]);
    }

    public function test_venue_can_be_created_with_default_commission()
    {
        $response = $this->actingAs($this->admin)->post(route('venues.store'), [
            'name' => 'Royal Palace Marquee',
            'venue_type' => 'Banquet Hall',
            'address' => 'Canal Road, Lahore',
            'contact_number' => '0300-1122334',
            'notes' => '10% commission on all events',
            'default_commission_type' => 'percentage',
            'default_commission_rate' => 10.00,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('venues.index'));
        $this->assertDatabaseHas('venues', [
            'name' => 'Royal Palace Marquee',
            'default_commission_type' => 'percentage',
            'default_commission_rate' => 10.00,
        ]);
    }

    public function test_can_create_booking_with_percentage_commission()
    {
        $response = $this->actingAs($this->admin)->post(route('bookings.store'), [
            'customer_id' => $this->customer->id,
            'booking_date' => now()->format('Y-m-d'),
            'event_date' => now()->addDays(10)->format('Y-m-d'),
            'event_type_id' => $this->eventType->id,
            'booking_type' => 'indoor',
            'venue_id' => $this->venue->id,
            'decoration_category_id' => $this->decorationCategory->id,
            'total_amount' => 150000.00,
            'discount' => 10000.00, // final amount = 140,000
            'status' => 'confirmed',
            'has_commission' => 1,
            'commission_type' => 'percentage',
            'commission_rate' => 10.00, // 10% of 140,000 = 14,000
            'commission_recipient' => 'Manager Aslam',
            'commission_notes' => '10% referral commission',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $this->customer->id,
            'final_amount' => 140000.00,
            'has_commission' => true,
            'commission_type' => 'percentage',
            'commission_rate' => 10.00,
            'commission_amount' => 14000.00,
            'commission_recipient' => 'Manager Aslam',
            'commission_status' => 'pending',
        ]);
    }

    public function test_can_create_booking_with_fixed_commission()
    {
        $response = $this->actingAs($this->admin)->post(route('bookings.store'), [
            'customer_id' => $this->customer->id,
            'booking_date' => now()->format('Y-m-d'),
            'event_date' => now()->addDays(15)->format('Y-m-d'),
            'event_type_id' => $this->eventType->id,
            'booking_type' => 'indoor',
            'venue_id' => $this->venue->id,
            'decoration_category_id' => $this->decorationCategory->id,
            'total_amount' => 200000.00,
            'discount' => 0.00,
            'status' => 'confirmed',
            'has_commission' => 1,
            'commission_type' => 'fixed',
            'commission_rate' => 20000.00,
            'commission_recipient' => 'Farmhouse Owner',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $this->customer->id,
            'final_amount' => 200000.00,
            'has_commission' => true,
            'commission_type' => 'fixed',
            'commission_amount' => 20000.00,
            'commission_status' => 'pending',
        ]);
    }

    public function test_can_record_commission_payout_and_creates_expense()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'customer_id' => $this->customer->id,
            'event_type_id' => $this->eventType->id,
            'venue_id' => $this->venue->id,
            'decoration_category_id' => $this->decorationCategory->id,
            'booking_type' => 'indoor',
            'booking_date' => now()->format('Y-m-d'),
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'total_amount' => 150000.00,
            'discount' => 0.00,
            'final_amount' => 150000.00,
            'status' => 'confirmed',
            'has_commission' => true,
            'commission_type' => 'percentage',
            'commission_rate' => 10.00,
            'commission_amount' => 15000.00,
            'commission_recipient' => 'Manager Aslam',
            'commission_status' => 'pending',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('commissions.pay', $booking), [
            'paid_amount' => 15000.00,
            'paid_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'recipient' => 'Manager Aslam',
            'notes' => 'Settled in cash at hall reception',
        ]);

        $response->assertRedirect();
        $booking->refresh();

        $this->assertEquals('paid', $booking->commission_status);
        $this->assertEquals(15000.00, $booking->commission_paid_amount);
        $this->assertNotNull($booking->commission_expense_id);

        // Verify expense record was created
        $this->assertDatabaseHas('expenses', [
            'id' => $booking->commission_expense_id,
            'booking_id' => $booking->id,
            'amount' => 15000.00,
            'payment_method' => 'cash',
        ]);

        // Verify ExpenseCategory "Venue Commission / Referral Fee" was assigned
        $this->assertDatabaseHas('expense_categories', [
            'name' => 'Venue Commission / Referral Fee',
        ]);
    }

    public function test_cannot_repay_already_paid_commission()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'customer_id' => $this->customer->id,
            'event_type_id' => $this->eventType->id,
            'venue_id' => $this->venue->id,
            'decoration_category_id' => $this->decorationCategory->id,
            'booking_type' => 'indoor',
            'booking_date' => now()->format('Y-m-d'),
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'total_amount' => 100000.00,
            'discount' => 0.00,
            'final_amount' => 100000.00,
            'status' => 'confirmed',
            'has_commission' => true,
            'commission_type' => 'percentage',
            'commission_rate' => 10.00,
            'commission_amount' => 10000.00,
            'commission_recipient' => 'Manager Aslam',
            'commission_status' => 'paid',
            'commission_paid_amount' => 10000.00,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('commissions.pay', $booking), [
            'paid_amount' => 10000.00,
            'paid_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'recipient' => 'Manager Aslam',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_commission_screens_render_successfully()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'customer_id' => $this->customer->id,
            'event_type_id' => $this->eventType->id,
            'venue_id' => $this->venue->id,
            'decoration_category_id' => $this->decorationCategory->id,
            'booking_type' => 'indoor',
            'booking_date' => now()->format('Y-m-d'),
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'total_amount' => 120000.00,
            'discount' => 0.00,
            'final_amount' => 120000.00,
            'status' => 'confirmed',
            'has_commission' => true,
            'commission_type' => 'percentage',
            'commission_rate' => 10.00,
            'commission_amount' => 12000.00,
            'commission_recipient' => 'Manager Aslam',
            'commission_status' => 'pending',
            'created_by' => $this->admin->id,
        ]);

        // 1. Commission Index
        $indexResponse = $this->actingAs($this->admin)->get(route('commissions.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Grand Marquee Gulberg');
        $indexResponse->assertSee('12,000.00');

        // 2. Commission Voucher
        $voucherResponse = $this->actingAs($this->admin)->get(route('commissions.voucher', $booking));
        $voucherResponse->assertStatus(200);
        $voucherResponse->assertSee('Venue Commission Voucher');
        $voucherResponse->assertSee($booking->booking_number);

        // 3. Commission Ledger & Statement Report
        $reportResponse = $this->actingAs($this->admin)->get(route('commissions.report'));
        $reportResponse->assertStatus(200);
        $reportResponse->assertSee('Venue Commission Summary by Partner Venue');
        $reportResponse->assertSee('Grand Marquee Gulberg');
    }
}
