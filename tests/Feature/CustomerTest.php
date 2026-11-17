<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\EventType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_can_create_customer(): void
    {
        $response = $this->actingAs($this->user)->post('/customers', [
            'name' => 'Chaudhry Pervaiz',
            'mobile_number' => '0300-9998877',
            'alternate_mobile' => '0321-1112233',
            'address' => 'House 45, Phase 6, DHA Lahore',
            'notes' => 'Referred by Falettis Hotel manager',
        ]);

        $customer = Customer::where('mobile_number', '0300-9998877')->first();
        $this->assertNotNull($customer);
        $response->assertRedirect("/customers/{$customer->id}");
    }

    public function test_warns_on_duplicate_mobile_number(): void
    {
        Customer::create([
            'name' => 'Original Customer',
            'mobile_number' => '0300-5554433',
        ]);

        // Attempt to create another with same mobile without confirmation
        $response = $this->actingAs($this->user)->from('/customers/create')->post('/customers', [
            'name' => 'Duplicate Person',
            'mobile_number' => '0300-5554433',
            'confirm_duplicate' => '0',
        ]);

        $response->assertRedirect('/customers/create');
        $response->assertSessionHas('duplicate_warning');

        // Confirming allows creation
        $responseConfirm = $this->actingAs($this->user)->post('/customers', [
            'name' => 'Duplicate Person',
            'mobile_number' => '0300-5554433',
            'confirm_duplicate' => '1',
        ]);

        $this->assertEquals(2, Customer::where('mobile_number', '0300-5554433')->count());
    }

    public function test_customer_show_page_displays_balance_and_records(): void
    {
        $customer = Customer::create([
            'name' => 'Sohail Ahmed',
            'mobile_number' => '0333-1234567',
        ]);

        $response = $this->actingAs($this->user)->get("/customers/{$customer->id}");
        $response->assertStatus(200);
        $response->assertSee('Sohail Ahmed');
        $response->assertSee('Outstanding Balance');
    }
}
