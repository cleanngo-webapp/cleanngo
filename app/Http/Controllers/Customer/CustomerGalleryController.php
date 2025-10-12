<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerGalleryController extends Controller
{
    /**
     * Display the customer gallery page
     * This shows all 8 services with their gallery images in a view-only format
     */
    public function index()
    {
        // Define the 8 services with their details
        $services = [
            [
                'type' => 'carpet',
                'name' => 'Carpet Deep Cleaning',
                'description' => 'Removes dirt and allergens to restore freshness and promote a healthier home.',
                'image' => 'cs-dashboard-carpet-cleaning.webp'
            ],
            [
                'type' => 'disinfection',
                'name' => 'Home/Office Disinfection',
                'description' => 'Advanced disinfection for safer homes and workplaces.',
                'image' => 'cs-dashboard-home-dis.webp'
            ],
            [
                'type' => 'glass',
                'name' => 'Glass Cleaning',
                'description' => 'Streak-free shine for windows and glass surfaces.',
                'image' => 'cs-services-glass-cleaning.webp'
            ],
            [
                'type' => 'carInterior',
                'name' => 'Home Service Car Interior Detailing',
                'description' => 'Specialized deep cleaning right at your doorstep for a refreshed car interior.',
                'image' => 'cs-dashboard-car-detailing.webp'
            ],
            [
                'type' => 'postConstruction',
                'name' => 'Post Construction Cleaning',
                'description' => 'Thorough cleanup to remove dust and debris for move-in ready spaces.',
                'image' => 'cs-services-post-cons-cleaning.webp'
            ],
            [
                'type' => 'sofa',
                'name' => 'Sofa / Mattress Deep Cleaning',
                'description' => 'Eliminates dust, stains, and allergens to restore comfort and hygiene.',
                'image' => 'cs-services-sofa-mattress-cleaning.webp'
            ],
            [
                'type' => 'houseCleaning',
                'name' => 'House Cleaning',
                'description' => 'Comprehensive cleaning service for residential spaces.',
                'image' => 'home-cleaning.webp'
            ],
            [
                'type' => 'curtainCleaning',
                'name' => 'Curtain Cleaning',
                'description' => 'Professional curtain and drapery cleaning service.',
                'image' => 'curtain-cleaning.webp'
            ]
        ];

        // Get image counts for each service and all active images grouped by service type
        $galleryImages = [];
        foreach ($services as &$service) {
            $service['image_count'] = GalleryImage::forService($service['type'])->active()->count();
            
            $images = GalleryImage::forService($service['type'])
                ->active()
                ->ordered()
                ->get();
            
            if ($images->count() > 0) {
                $galleryImages[$service['type']] = $images;
            }
        }

        return view('customer.csgallery', compact('services', 'galleryImages'));
    }

    /**
     * Show images for a specific service type (customer view)
     * This displays all active images for a service in a view-only format
     */
    public function showService($serviceType)
    {
        // Validate service type
        $validServices = ['carpet', 'disinfection', 'glass', 'carInterior', 'postConstruction', 'sofa', 'houseCleaning', 'curtainCleaning'];
        if (!in_array($serviceType, $validServices)) {
            return redirect()->route('customer.gallery')->with('error', 'Invalid service type.');
        }

        // Get service details
        $serviceNames = [
            'carpet' => 'Carpet Deep Cleaning',
            'disinfection' => 'Home/Office Disinfection',
            'glass' => 'Glass Cleaning',
            'carInterior' => 'Home Service Car Interior Detailing',
            'postConstruction' => 'Post Construction Cleaning',
            'sofa' => 'Sofa / Mattress Deep Cleaning',
            'houseCleaning' => 'House Cleaning',
            'curtainCleaning' => 'Curtain Cleaning'
        ];

        $serviceName = $serviceNames[$serviceType];
        
        // Get only active images for this service
        $allImages = GalleryImage::forService($serviceType)->active()->ordered()->get();
        
        // Filter out images where the file doesn't exist
        $images = $allImages->filter(function ($image) {
            return Storage::disk('public')->exists($image->image_path);
        });

        return view('customer.gallery-service', compact('serviceType', 'serviceName', 'images'));
    }
}
