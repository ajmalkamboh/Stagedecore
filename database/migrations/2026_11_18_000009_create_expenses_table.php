<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number', 50)->unique()->index();
            $table->foreignId('expense_category_id')->constrained('expense_categories');
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->date('expense_date')->index();
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'bank', 'online_transfer', 'other'])->default('cash');
            $table->text('remarks')->nullable();
            $table->string('attachment_path')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
