<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\EventType;
use App\Models\Venue;
use App\Models\DecorationCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_can_create_and_toggle_event_type(): void
    {
        $response = $this->actingAs($this->user)->post('/event-types', [
            'name' => 'Qawwali Night',
            'description' => 'Traditional sufi qawwali floor stage and acoustic setup',
            'is_active' => 1,
        ]);

        $response->assertRedirect('/event-types');
        $this->assertDatabaseHas('event_types', ['name' => 'Qawwali Night']);

        $eventType = EventType::where('name', 'Qawwali Night')->first();
        $this->assertTrue($eventType->is_active);

        // Toggle
        $this->actingAs($this->user)->patch("/event-types/{$eventType->id}/toggle");
        $this->assertFalse($eventType->fresh()->is_active);
    }

    public function test_can_create_and_toggle_venue(): void
    {
        $response = $this->actingAs($this->user)->post('/venues', [
            'name' => 'Pearl Continental Crystal Ballroom',
            'venue_type' => 'Hotel',
            'address' => 'Mall Road, Lahore',
            'contact_number' => '042-111505505',
            'notes' => 'Ceiling height 18ft, goods lift access at rear.',
            'is_active' => 1,
        ]);

        $response->assertRedirect('/venues');
        $this->assertDatabaseHas('venues', ['name' => 'Pearl Continental Crystal Ballroom']);

        $venue = Venue::where('name', 'Pearl Continental Crystal Ballroom')->first();
        $this->assertTrue($venue->is_active);

        $this->actingAs($this->user)->patch("/venues/{$venue->id}/toggle");
        $this->assertFalse($venue->fresh()->is_active);
    }

    public function test_can_create_and_toggle_decoration_category(): void
    {
        $response = $this->actingAs($this->user)->post('/decoration-categories', [
            'name' => 'Luxury Royal Stage',
            'description' => 'Imported fresh roses, crystal chandeliers, velvet seating',
            'base_price' => 180000.00,
            'is_active' => 1,
        ]);

        $response->assertRedirect('/decoration-categories');
        $this->assertDatabaseHas('decoration_categories', [
            'name' => 'Luxury Royal Stage',
            'base_price' => '180000.00',
        ]);

        $category = DecorationCategory::where('name', 'Luxury Royal Stage')->first();
        $this->actingAs($this->user)->patch("/decoration-categories/{$category->id}/toggle");
        $this->assertFalse($category->fresh()->is_active);
    }
}
