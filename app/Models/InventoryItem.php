<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $table = 'inventory_items';

    protected $fillable = [
        'item_code',
        'name',
        'category',
        'quantity',
        'unit_price',
        'reorder_level',
        'status',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Calculate total value (unit price × quantity) in Philippine Peso
     */
    public function getTotalValueAttribute()
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * Automatically update status based on quantity
     */
    public function updateStatus()
    {
        if ($this->quantity <= 0) {
            $this->status = 'Out of Stock';
        } elseif ($this->quantity <= $this->reorder_level) {
            $this->status = 'Low Stock';
        } else {
            $this->status = 'In Stock';
        }
    }

    /**
     * Boot method to automatically update status when quantity changes
     * and trigger notifications for inventory events
     */
    protected static function boot()
    {
        parent::boot();

        // Store original data before saving for comparison
        static::saving(function ($item) {
            $item->updateStatus();
        });

        // Trigger notification when a new item is created
        static::created(function ($item) {
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifyInventoryItemCreated($item);
        });

        // Trigger notifications when an item is updated
        static::updated(function ($item) {
            $notificationService = app(\App\Services\NotificationService::class);
            
            // Get the original data from the model's original attributes
            $originalData = $item->getOriginal();
            
            // Notify about general updates
            $notificationService->notifyInventoryItemUpdated($item, $originalData);
            
            // Check for stock level changes and notify accordingly
            $originalQuantity = $originalData['quantity'] ?? 0;
            $currentQuantity = $item->quantity;
            $reorderLevel = $item->reorder_level;
            
            // If quantity decreased and now below reorder level, notify low stock
            if ($currentQuantity <= $reorderLevel && $currentQuantity > 0 && $originalQuantity > $reorderLevel) {
                $notificationService->notifyInventoryLowStock($item);
            }
            
            // If quantity reached zero, notify out of stock
            if ($currentQuantity <= 0 && $originalQuantity > 0) {
                $notificationService->notifyInventoryOutOfStock($item);
            }
        });
    }

    /**
     * Scope for active items only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->where('status', 'Low Stock');
    }

    /**
     * Scope for out of stock items
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('status', 'Out of Stock');
    }
}


