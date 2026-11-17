<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'venue_type',
        'address',
        'contact_number',
        'notes',
        'default_commission_type',
        'default_commission_rate',
        'is_active',
    ];

    protected $casts = [
        'default_commission_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function commissionBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->where('has_commission', true);
    }

    public function getTotalCommissionPayableAttribute(): float
    {
        return (float) $this->commissionBookings()->sum('commission_amount');
    }

    public function getTotalCommissionPaidAttribute(): float
    {
        return (float) $this->commissionBookings()->where('commission_status', 'paid')->sum('commission_paid_amount');
    }

    public function getPendingCommissionBalanceAttribute(): float
    {
        return max(0, (float) $this->commissionBookings()->where('commission_status', 'pending')->sum('commission_amount'));
    }
}
