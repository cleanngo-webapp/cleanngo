<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\NotificationService;

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

    public function staffAssignments()
    {
        return $this->hasMany(BookingStaffAssignment::class);
    }

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }

    /**
     * Boot method to handle model events and trigger notifications
     */
    protected static function boot()
    {
        parent::boot();

        // Trigger notification when booking status changes
        static::updated(function ($booking) {
            // Check if status has changed
            if ($booking->isDirty('status')) {
                $oldStatus = $booking->getOriginal('status');
                $newStatus = $booking->status;
                
                $notificationService = app(NotificationService::class);
                $notificationService->notifyBookingStatusChanged($booking, $oldStatus, $newStatus);
            }
            
            // Check if this booking just became a payroll record (completed + paid)
            if ($booking->isDirty('payment_status') && $booking->payment_status === 'paid' && $booking->status === 'completed') {
                // This booking just became a payroll record, notify admin and employee
                $notificationService = app(NotificationService::class);
                $notificationService->notifyNewPayrollRecord($booking);
            }
        });
    }

    /**
     * Trigger notification for booking creation
     * This should be called after all booking items are created
     */
    public function triggerBookingCreatedNotification()
    {
        $notificationService = app(NotificationService::class);
        $notificationService->notifyBookingCreated($this);
    }
}


