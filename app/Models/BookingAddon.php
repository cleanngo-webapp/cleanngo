<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Pivot model for booking_addons (optional to use directly).
 */
class BookingAddon extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'booking_addons';

    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function addon()
    {
        return $this->belongsTo(ServiceAddon::class, 'addon_id');
    }
}


