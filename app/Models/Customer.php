<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'mobile_number',
        'alternate_mobile',
        'address',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BookingPayment::class);
    }

    public function getTotalBookingAmountAttribute(): float
    {
        return (float) $this->bookings()->where('status', '!=', 'cancelled')->sum('final_amount');
    }

    public function getTotalPaidAmountAttribute(): float
    {
        $paid = (float) $this->payments()->where('is_refund', false)->sum('amount');
        $refunded = (float) $this->payments()->where('is_refund', true)->sum('amount');
        return max(0, $paid - $refunded);
    }

    public function getOutstandingBalanceAttribute(): float
    {
        return max(0, $this->total_booking_amount - $this->total_paid_amount);
    }
}
