<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Notification model for handling system notifications
 * 
 * This model manages notifications for different user types (admin, customer, employee)
 * and various booking-related events like creation, confirmation, assignment, etc.
 */
class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'recipient_type',
        'recipient_id',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the customer recipient if this notification is for a customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'recipient_id');
    }

    /**
     * Get the employee recipient if this notification is for an employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recipient_id');
    }

    /**
     * Scope to get notifications for a specific recipient type and ID
     */
    public function scopeForRecipient($query, string $type, ?int $id = null)
    {
        return $query->where('recipient_type', $type)
                    ->where('recipient_id', $id);
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }
}
