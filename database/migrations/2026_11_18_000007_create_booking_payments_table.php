<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number', 50)->unique()->index();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->date('payment_date')->index();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_type', ['advance', 'partial', 'final', 'refund'])->default('advance');
            $table->enum('payment_method', ['cash', 'bank', 'online_transfer', 'cheque', 'other'])->default('cash');
            $table->string('transaction_reference', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('received_by')->constrained('users');
            $table->boolean('is_refund')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_payments');
    }
};
