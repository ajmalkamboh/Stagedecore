<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->default('Stage Decor & Events');
            $table->string('owner_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('alternate_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('currency')->default('PKR');
            $table->string('currency_symbol')->default('Rs.');
            $table->text('receipt_footer')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
