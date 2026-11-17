<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpenseCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    public function test_can_view_expense_categories_list(): void
    {
        ExpenseCategory::create([
            'name' => 'Generator Fuel',
            'description' => 'Diesel for backup generator',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get('/expense-categories');
        $response->assertStatus(200);
        $response->assertSee('Generator Fuel');
    }

    public function test_can_create_expense_category(): void
    {
        $response = $this->actingAs($this->user)->post('/expense-categories', [
            'name' => 'Sound & DJ Equipment',
            'description' => 'Microphones, amplifiers, speakers, wiring',
            'is_active' => 1,
        ]);

        $response->assertRedirect('/expense-categories');
        $this->assertDatabaseHas('expense_categories', [
            'name' => 'Sound & DJ Equipment',
        ]);
    }

    public function test_can_update_expense_category(): void
    {
        $category = ExpenseCategory::create([
            'name' => 'Stage Carpets',
            'description' => 'Red and white carpets',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->put("/expense-categories/{$category->id}", [
            'name' => 'Luxury Carpeting & Flooring',
            'description' => 'Red, white, and acrylic floorings',
            'is_active' => 1,
        ]);

        $response->assertRedirect('/expense-categories');
        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'name' => 'Luxury Carpeting & Flooring',
        ]);
    }

    public function test_can_toggle_expense_category_status(): void
    {
        $category = ExpenseCategory::create([
            'name' => 'Disposable Crockery',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->patch("/expense-categories/{$category->id}/toggle");
        $response->assertSessionHas('success');
        $this->assertFalse($category->fresh()->is_active);

        $this->actingAs($this->user)->patch("/expense-categories/{$category->id}/toggle");
        $this->assertTrue($category->fresh()->is_active);
    }

    public function test_cannot_delete_expense_category_with_existing_expenses(): void
    {
        $category = ExpenseCategory::create(['name' => 'Tentage Materials', 'is_active' => true]);

        Expense::create([
            'expense_number' => Expense::generateExpenseNumber(),
            'expense_category_id' => $category->id,
            'expense_date' => now()->toDateString(),
            'description' => '10 Tent poles and canvas sheets',
            'amount' => 15000,
            'payment_method' => 'cash',
            'recorded_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete("/expense-categories/{$category->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('expense_categories', ['id' => $category->id]);
    }

    public function test_can_delete_unused_expense_category(): void
    {
        $category = ExpenseCategory::create(['name' => 'Unused Category', 'is_active' => true]);

        $response = $this->actingAs($this->user)->delete("/expense-categories/{$category->id}");
        $response->assertRedirect('/expense-categories');
        $this->assertDatabaseMissing('expense_categories', ['id' => $category->id]);
    }
}
