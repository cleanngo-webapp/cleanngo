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
        'booking_photos' => 'array',
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
                
                // If booking was just completed, automatically return borrowed equipment
                if ($oldStatus !== 'completed' && $newStatus === 'completed') {
                    $booking->returnBorrowedEquipment();
                }
            }
            
            // Check if this booking just became a payroll record (completed + paid)
            // This handles both cases:
            // 1. Payment status changes to 'paid' while booking is already 'completed'
            // 2. Status changes to 'completed' while payment is already 'paid'
            // 3. Both status and payment_status change in the same update
            $wasEligibleForPayroll = $booking->getOriginal('status') === 'completed' && $booking->getOriginal('payment_status') === 'paid';
            $isEligibleForPayroll = $booking->status === 'completed' && $booking->payment_status === 'paid';
            
            if (!$wasEligibleForPayroll && $isEligibleForPayroll) {
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

    /**
     * Manually trigger payroll notification for this booking
     * This can be used to fix missing notifications for existing completed and paid bookings
     */
    public function triggerPayrollNotification(): void
    {
        if ($this->status === 'completed' && $this->payment_status === 'paid') {
            $notificationService = app(NotificationService::class);
            $notificationService->notifyNewPayrollRecord($this);
        }
    }

    /**
     * Static method to trigger payroll notifications for all eligible bookings
     * This can be used to fix missing notifications in bulk
     */
    public static function triggerMissingPayrollNotifications(): int
    {
        $eligibleBookings = static::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->get();
        
        $count = 0;
        foreach ($eligibleBookings as $booking) {
            $booking->triggerPayrollNotification();
            $count++;
        }
        
        return $count;
    }

    /**
     * Automatically return borrowed equipment when booking is completed
     */
    public function returnBorrowedEquipment(): array
    {
        $controller = app(\App\Http\Controllers\Employee\EmployeeJobsController::class);
        $returnedItems = $controller->returnEquipment($this->id);
        
        // Trigger notifications for returned equipment
        if ($returnedItems && !empty($returnedItems)) {
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifyEquipmentReturnedCompleted($this, $returnedItems);
        }
        
        return $returnedItems ?: [];
    }
}


