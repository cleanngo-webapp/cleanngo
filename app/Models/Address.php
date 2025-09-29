<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $guarded = [];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_primary' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Boot method to trigger notifications for address updates
     */
    protected static function boot()
    {
        parent::boot();

        // Trigger notification when address is updated
        static::updated(function ($address) {
            // Only trigger notifications for address-related fields
            $addressFields = [
                'label', 'line1', 'line2', 'barangay', 'city', 'province', 
                'postal_code', 'latitude', 'longitude', 'is_primary'
            ];
            $hasAddressChanges = false;
            
            foreach ($addressFields as $field) {
                if ($address->isDirty($field)) {
                    $hasAddressChanges = true;
                    break;
                }
            }
            
            if ($hasAddressChanges) {
                $notificationService = app(\App\Services\NotificationService::class);
                
                // Load the user relationship
                $address->load('user');
                
                if ($address->user && $address->user->role === 'customer') {
                    // Get original data for comparison
                    $originalData = $address->getOriginal();
                    $newData = $address->toArray();
                    
                    // Notify admin about customer address updates
                    $notificationService->notifyCustomerAddressUpdated($address->user, $originalData, $newData);
                }
            }
        });
    }
}


