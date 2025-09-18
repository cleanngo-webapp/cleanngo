<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminGalleryController extends Controller
{
    /**
     * Display the gallery management page with service cards
     * This shows all 6 services and allows admin to manage images for each
     */
    public function index()
    {
        // Define the 6 services with their details
        $services = [
            [
                'type' => 'carpet',
                'name' => 'Carpet Deep Cleaning',
                'description' => 'Removes dirt and allergens to restore freshness and promote a healthier home.',
                'image' => 'cs-dashboard-carpet-cleaning.webp'
            ],
            [
                'type' => 'disinfection',
                'name' => 'Enhanced Disinfection',
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
            ]
        ];

        // Get image counts for each service
        foreach ($services as &$service) {
            $service['image_count'] = GalleryImage::forService($service['type'])->active()->count();
        }

        return view('admin.gallery', compact('services'));
    }

    /**
     * Show images for a specific service type
     * This displays all images for a service and allows admin to manage them
     */
    public function showService($serviceType)
    {
        // Validate service type
        $validServices = ['carpet', 'disinfection', 'glass', 'carInterior', 'postConstruction', 'sofa'];
        if (!in_array($serviceType, $validServices)) {
            return redirect()->route('admin.gallery')->with('error', 'Invalid service type.');
        }

        // Get service details
        $serviceNames = [
            'carpet' => 'Carpet Deep Cleaning',
            'disinfection' => 'Enhanced Disinfection',
            'glass' => 'Glass Cleaning',
            'carInterior' => 'Home Service Car Interior Detailing',
            'postConstruction' => 'Post Construction Cleaning',
            'sofa' => 'Sofa / Mattress Deep Cleaning'
        ];

        $serviceName = $serviceNames[$serviceType];
        
        // Get all images for this service (including inactive ones for admin management)
        $images = GalleryImage::forService($serviceType)->ordered()->get();

        return view('admin.gallery-service', compact('serviceType', 'serviceName', 'images'));
    }

    /**
     * Store a newly uploaded image
     * This handles the file upload and creates a new gallery image record
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'service_type' => 'required|in:carpet,disinfection,glass,carInterior,postConstruction,sofa',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB
            'alt_text' => 'nullable|string|max:255'
        ]);

        try {
            // Handle file upload
            $file = $request->file('image');
            $originalName = $file->getClientOriginalName();
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Store the file in public/gallery directory
            $path = $file->storeAs('gallery', $filename, 'public');

            // Create gallery image record
            $galleryImage = GalleryImage::create([
                'service_type' => $request->service_type,
                'image_path' => $path,
                'original_name' => $originalName,
                'alt_text' => $request->alt_text,
                'sort_order' => GalleryImage::forService($request->service_type)->max('sort_order') + 1,
                'is_active' => true
            ]);

            return redirect()->back()->with('success', 'Image uploaded successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upload image: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing gallery image
     * This allows admin to update image details like alt text and sort order
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        $image = GalleryImage::findOrFail($id);
        
        $image->update($request->only(['alt_text', 'sort_order', 'is_active']));

        return redirect()->back()->with('success', 'Image updated successfully!');
    }

    /**
     * Delete a gallery image
     * This removes both the database record and the physical file
     */
    public function destroy($id)
    {
        $image = GalleryImage::findOrFail($id);
        
        try {
            // Delete the physical file
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            // Delete the database record
            $image->delete();

            return redirect()->back()->with('success', 'Image deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete image: ' . $e->getMessage());
        }
    }
}
