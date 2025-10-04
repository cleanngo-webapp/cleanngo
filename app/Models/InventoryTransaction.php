<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * InventoryTransaction model for tracking equipment borrowing and returning
 * 
 * This model manages the loaning and returning of equipment by employees
 * during their job assignments, providing a complete audit trail.
 */
class InventoryTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'inventory_item_id',
        'employee_id',
        'booking_id',
        'transaction_type', // 'borrow' or 'return'
        'quantity',
        'transaction_at',
        'notes',
        'expected_return_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'transaction_at' => 'datetime',
        'expected_return_date' => 'datetime',
    ];

    /**
     * Get the inventory item associated with this transaction
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Get the employee associated with this transaction
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the booking associated with this transaction (if any)
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scope for borrow transactions
     */
    public function scopeBorrow($query)
    {
        return $query->where('transaction_type', 'borrow');
    }

    /**
     * Scope for return transactions
     */
    public function scopeReturn($query)
    {
        return $query->where('transaction_type', 'return');
    }

    /**
     * Scope for transactions related to a specific booking
     */
    public function scopeForBooking($query, $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

        /**
     * Scope for transactions by a specific employee
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope for transactions on a specific date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('transaction_at', $date);
    }

    /**
     * Get the current borrowed quantity for an employee and item combination
     */
    public static function getCurrentBorrowedQuantity($employeeId, $inventoryItemId, $bookingId = null)
    {
        $query = static::where('employee_id', $employeeId)
            ->where('inventory_item_id', $inventoryItemId);

        if ($bookingId) {
            $query->where('booking_id', $bookingId);
        }

        $borrowed = $query->borrow()->sum('quantity');
        $returned = $query->return()->sum('quantity');

        return max(0, $borrowed - $returned);
    }

    /**
     * Check if an employee has outstanding loans for a specific booking
     */
    public static function hasOutstandingLoans($bookingId, $employeeId = null)
    {
        $query = static::forBooking($bookingId)->borrow();

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $borrowed = $query->sum('quantity');
        $returned = static::forBooking($bookingId)->return()->sum('quantity');

        return $borrowed > $returned;
    }

    /**
     * Boot method to handle model events and trigger notifications
     */
    protected static function boot()
    {
        parent::boot();

        // Note: Notifications are now handled manually in controllers for batch operations
        // to avoid duplicate notifications. Individual transaction notifications are disabled.
        
        // If you need to trigger individual notifications for single transactions,
        // call the notification service manually from the calling code.
    }

    /**
     * Format quantity display with proper unit indication
     */
    public function getFormattedQuantityAttribute()
    {
        return number_format($this->quantity, $this->quantity == (int)$this->quantity ? 0 : 2);
    }

    /**
     * Check if this is a consumable item (doesn't need to be returned)
     */
    public function isConsumableItem()
    {
        $categories = $this->inventoryItem->category ?? '';
        return in_array($categories, ['Cleaning Agent', 'Consumables']);
    }

    /**
     * Check if this is a returnable item (Machine or Tools)
     */
    public function isReturnableItem()
    {
        $categories = $this->inventoryItem->category ?? '';
        return in_array($categories, ['Machine', 'Tools']);
    }
}
