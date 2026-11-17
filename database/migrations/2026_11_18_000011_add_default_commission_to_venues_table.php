<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->string('default_commission_type', 20)->default('none')->after('notes'); // none, percentage, fixed
            $table->decimal('default_commission_rate', 10, 2)->default(0.00)->after('default_commission_type');
        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn(['default_commission_type', 'default_commission_rate']);
        });
    }
};
