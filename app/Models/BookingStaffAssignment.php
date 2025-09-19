<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\NotificationService;

class BookingStaffAssignment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'booking_staff_assignments';

    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Boot method to handle model events and trigger notifications
     */
    protected static function boot()
    {
        parent::boot();

        // Trigger notification when an employee is assigned to a booking
        static::created(function ($assignment) {
            $notificationService = app(NotificationService::class);
            $notificationService->notifyEmployeeAssigned($assignment->booking, $assignment->employee);
        });
    }
}


