<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceComment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * This allows us to safely assign these fields when creating/updating comments
     */
    protected $fillable = [
        'service_type',
        'customer_id',
        'comment',
        'rating',
        'is_approved',
        'is_edited'
    ];

    /**
     * The attributes that should be cast to native types.
     * This ensures proper data type handling
     */
    protected $casts = [
        'is_approved' => 'boolean',
        'is_edited' => 'boolean',
        'rating' => 'integer'
    ];

    /**
     * Get the customer who made this comment
     * This creates a relationship between comments and customers
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the service type display name
     * Converts the service_type code to a human-readable format
     */
    public function getServiceDisplayNameAttribute()
    {
        $serviceNames = [
            'carpet' => 'Carpet Deep Cleaning',
            'disinfection' => 'Home/Office Disinfection',
            'glass' => 'Glass Cleaning',
            'carInterior' => 'Home Service Car Interior Detailing',
            'postConstruction' => 'Post Construction Cleaning',
            'sofa' => 'Sofa Mattress Deep Cleaning',
            'houseCleaning' => 'House Cleaning',
            'curtainCleaning' => 'Curtain Cleaning'
        ];

        return $serviceNames[$this->service_type] ?? 'Unknown Service';
    }

    /**
     * Get formatted date for display
     * Shows relative time like "2 days ago" or "1 week ago"
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the customer's display name (first name + last initial for privacy)
     * This provides some anonymity while still showing who commented
     */
    public function getCustomerDisplayNameAttribute()
    {
        if (!$this->customer) {
            return 'Anonymous';
        }

        // Get name from customer first, then fallback to user
        $firstName = $this->customer->first_name;
        $lastName = $this->customer->last_name;
        
        // If customer name is empty, get from user
        if (empty($firstName) && $this->customer->user) {
            $firstName = $this->customer->user->first_name;
            $lastName = $this->customer->user->last_name;
        }
        
        // Final fallback
        if (empty($firstName)) {
            $firstName = 'Anonymous';
        }
        
        if ($lastName) {
            return $firstName . ' ' . substr($lastName, 0, 1) . '.';
        }
        
        return $firstName;
    }

    /**
     * Scope to get comments for a specific service type
     * This makes it easy to filter comments by service
     */
    public function scopeForService($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    /**
     * Scope to get only approved comments
     * This ensures we only show comments that are meant to be displayed
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to order comments by creation date (newest first)
     * This allows us to control the display order of comments
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get comments with ratings
     * This filters comments that have star ratings
     */
    public function scopeWithRating($query)
    {
        return $query->whereNotNull('rating');
    }
}
