<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $guarded = [];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'base_price_cents' => 'integer',
        'addon_total_cents' => 'integer',
        'discount_cents' => 'integer',
        'tax_cents' => 'integer',
        'total_due_cents' => 'integer',
        'amount_paid_cents' => 'integer',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function addons()
    {
        return $this->belongsToMany(ServiceAddon::class, 'booking_addons', 'booking_id', 'addon_id')
            ->withPivot(['quantity', 'unit_price_cents']);
    }

    public function staffAssignments()
    {
        return $this->hasMany(BookingStaffAssignment::class);
    }

    public function rating()
    {
        return $this->hasOne(BookingRating::class);
    }
}


