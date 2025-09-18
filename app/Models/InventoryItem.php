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
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->updateStatus();
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


