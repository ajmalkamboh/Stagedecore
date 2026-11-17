<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('has_commission')->default(false)->after('final_amount');
            $table->string('commission_type', 20)->default('none')->after('has_commission'); // none, percentage, fixed
            $table->decimal('commission_rate', 10, 2)->default(0.00)->after('commission_type');
            $table->decimal('commission_amount', 12, 2)->default(0.00)->after('commission_rate');
            $table->string('commission_recipient', 191)->nullable()->after('commission_amount');
            $table->string('commission_status', 20)->default('none')->after('commission_recipient'); // none, pending, paid
            $table->decimal('commission_paid_amount', 12, 2)->default(0.00)->after('commission_status');
            $table->date('commission_paid_at')->nullable()->after('commission_paid_amount');
            $table->string('commission_payment_method', 50)->nullable()->after('commission_paid_at');
            $table->text('commission_notes')->nullable()->after('commission_payment_method');
            $table->foreignId('commission_expense_id')->nullable()->after('commission_notes')->constrained('expenses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['commission_expense_id']);
            $table->dropColumn([
                'has_commission',
                'commission_type',
                'commission_rate',
                'commission_amount',
                'commission_recipient',
                'commission_status',
                'commission_paid_amount',
                'commission_paid_at',
                'commission_payment_method',
                'commission_notes',
                'commission_expense_id',
            ]);
        });
    }
};
