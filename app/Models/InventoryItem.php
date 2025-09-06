<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'inventory_items';

    protected $guarded = [];

    protected $casts = [
        'min_stock' => 'float',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'item_id');
    }
}


