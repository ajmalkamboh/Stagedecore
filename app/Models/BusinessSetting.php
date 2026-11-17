<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'business_name',
        'owner_name',
        'mobile_number',
        'alternate_number',
        'email',
        'address',
        'logo_path',
        'currency',
        'currency_symbol',
        'receipt_footer',
        'terms_and_conditions',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'business_name' => 'Royal Stage Decor & Event Management',
            'owner_name' => 'Muhammad Bilal',
            'mobile_number' => '0300-1234567',
            'alternate_number' => '0321-7654321',
            'email' => 'contact@stagedecor.pk',
            'address' => 'Main Boulevard, Gulberg III, Lahore, Pakistan',
            'currency' => 'PKR',
            'currency_symbol' => 'Rs.',
            'receipt_footer' => 'Thank you for choosing our stage decoration services. For queries call 0300-1234567.',
            'terms_and_conditions' => "1. 50% advance payment required at the time of booking confirmation.\n2. In case of cancellation within 7 days of the event, advance payment is non-refundable.\n3. Any damages to decorative structures, lighting, or flowers caused by external elements will be billed to the client.\n4. Balance payment must be cleared prior to or at the start of the event.",
        ]);
    }
}
