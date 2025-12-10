<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * This allows us to safely assign these fields when creating/updating gallery images
     */
    protected $fillable = [
        'service_type',
        'image_path',
        'original_name',
        'alt_text',
        'sort_order',
        'is_active'
    ];

    /**
     * The attributes that should be cast to native types.
     * This ensures proper data type handling
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

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
     * Scope to get images for a specific service type
     * This makes it easy to filter images by service
     */
    public function scopeForService($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    /**
     * Scope to get only active images
     * This ensures we only show images that are meant to be displayed
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order images by sort_order
     * This allows us to control the display order of images
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /**
     * Get the full URL for the image
     * Returns the URL directly if it's a full URL (Supabase), 
     * or generates asset URL if it's a local path
     * 
     * @return string The full URL to the image
     */
    public function getUrlAttribute()
    {
        // Check if image_path is already a full URL (Supabase)
        if (filter_var($this->image_path, FILTER_VALIDATE_URL) !== false) {
            return $this->image_path;
        }
        
        // Otherwise, it's a local path - generate asset URL
        return asset('storage/' . $this->image_path);
    }
}
