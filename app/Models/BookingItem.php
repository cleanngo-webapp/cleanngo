<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * BookingItem model for handling individual services within a booking
 * 
 * This model represents each service item that is part of a booking,
 * allowing multiple services to be booked together.
 */
class BookingItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'booking_id',
        'service_id',
        'item_type',
        'quantity',
        'area_sqm',
        'unit_price_cents',
        'line_total_cents',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'area_sqm' => 'decimal:2',
        'unit_price_cents' => 'integer',
        'line_total_cents' => 'integer',
    ];

    /**
     * Get the booking that owns this item
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the service for this booking item
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
