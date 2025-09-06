<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Employee profile linked 1:1 to a User.
 */
class Employee extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignments()
    {
        return $this->hasMany(BookingStaffAssignment::class);
    }

    public function availability()
    {
        return $this->hasMany(EmployeeAvailability::class);
    }

    public function timeOff()
    {
        return $this->hasMany(EmployeeTimeOff::class);
    }
}


