<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\NotificationService;

class PaymentProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'employee_id',
        'image_path',
        'amount',
        'payment_method',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the booking that owns the payment proof
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the employee who uploaded the payment proof
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the admin who reviewed the payment proof
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if payment proof is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if payment proof is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment proof is declined
     */
    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    /**
     * Boot method to handle model events and trigger notifications
     */
    protected static function boot()
    {
        parent::boot();

        // Trigger notification when payment status changes
        static::updated(function ($paymentProof) {
            // Check if status has changed
            if ($paymentProof->isDirty('status')) {
                $oldStatus = $paymentProof->getOriginal('status');
                $newStatus = $paymentProof->status;
                
                // Only notify for approved or declined status changes
                if (in_array($newStatus, ['approved', 'declined'])) {
                    $notificationService = app(NotificationService::class);
                    $notificationService->notifyPaymentStatusChanged($paymentProof, $oldStatus, $newStatus);
                }
            }
        });
    }
}