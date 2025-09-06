<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Customer profile linked 1:1 to a User.
 * Note: Table has no created_at/updated_at columns.
 */
class Customer extends Model
{
    use HasFactory;

    /**
     * Disable timestamps because table doesn't have them.
     */
    public $timestamps = false;

    /**
     * Allow simple mass assignment for now (forms/services will validate data).
     */
    protected $guarded = [];

    /**
     * The owning User record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Customer's default address (nullable FK to addresses.id).
     */
    public function defaultAddress()
    {
        return $this->belongsTo(Address::class, 'default_address_id');
    }

    /**
     * Bookings made by this customer.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}


