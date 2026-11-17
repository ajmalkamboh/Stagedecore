<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_number',
        'customer_id',
        'event_type_id',
        'venue_id',
        'decoration_category_id',
        'booking_type',
        'booking_date',
        'event_date',
        'event_start_time',
        'event_end_time',
        'venue_address',
        'decoration_details',
        'special_instructions',
        'total_amount',
        'discount',
        'final_amount',
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
        'status',
        'cancellation_reason',
        'cancelled_at',
        'created_by',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'event_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'has_commission' => 'boolean',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_paid_amount' => 'decimal:2',
        'commission_paid_at' => 'date',
        'cancelled_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function decorationCategory(): BelongsTo
    {
        return $this->belongsTo(DecorationCategory::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BookingPayment::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function commissionExpense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'commission_expense_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeEligibleSales(Builder $query): Builder
    {
        return $query->whereIn('status', ['confirmed', 'completed']);
    }

    // Financial Accessors
    public function getTotalPaidAttribute(): float
    {
        $paid = (float) $this->payments()->where('is_refund', false)->sum('amount');
        $refunded = (float) $this->payments()->where('is_refund', true)->sum('amount');
        return max(0, $paid - $refunded);
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, (float) $this->final_amount - $this->total_paid);
    }

    public function getTotalExpensesAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function getEstimatedProfitAttribute(): float
    {
        if ($this->status === 'cancelled') {
            return 0.00;
        }
        return (float) $this->final_amount - $this->total_expenses;
    }

    public function getPendingCommissionAttribute(): float
    {
        if ($this->has_commission && $this->commission_status === 'pending') {
            return (float) $this->commission_amount;
        }
        return 0.00;
    }

    public function getPaymentStatusAttribute(): string
    {
        if ($this->remaining_balance <= 0 && $this->final_amount > 0) {
            return 'Paid';
        }
        if ($this->total_paid > 0) {
            return 'Partial';
        }
        return 'Unpaid';
    }

    public static function generateBookingNumber(): string
    {
        $prefix = 'STG-' . date('Ym') . '-';
        $last = static::withTrashed()
            ->where('booking_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->booking_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
