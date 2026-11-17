<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number', 50)->unique()->index();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('event_type_id')->constrained('event_types');
            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->foreignId('decoration_category_id')->nullable()->constrained('decoration_categories')->nullOnDelete();
            $table->enum('booking_type', ['indoor', 'outdoor'])->default('indoor');
            $table->date('booking_date')->index();
            $table->date('event_date')->index();
            $table->time('event_start_time')->nullable();
            $table->time('event_end_time')->nullable();
            $table->text('venue_address')->nullable();
            $table->text('decoration_details')->nullable();
            $table->text('special_instructions')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('final_amount', 12, 2)->default(0.00);
            $table->enum('status', ['tentative', 'confirmed', 'completed', 'cancelled'])->default('tentative')->index();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
