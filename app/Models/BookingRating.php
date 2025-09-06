<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRating extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'booking_ratings';

    protected $guarded = [];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}


