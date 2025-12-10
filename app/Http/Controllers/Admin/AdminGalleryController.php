<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use App\Models\ServiceComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminGalleryController extends Controller
{
    /**
     * Display the gallery management page with service cards
     * This shows all 8 services and allows admin to manage images for each
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
                'name' => 'Sofa Mattress Deep Cleaning',
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

        // Get image counts for each service
        // Only count images where the actual file exists on disk
        foreach ($services as &$service) {
            $allImages = GalleryImage::forService($service['type'])->active()->get();
            
            // Filter out images where the file doesn't exist
            $validImages = $allImages->filter(function ($image) {
                return Storage::disk('public')->exists($image->image_path);
            });
            
            // Count only valid images
            $service['image_count'] = $validImages->count();
            
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
        $validServices = ['carpet', 'disinfection', 'glass', 'carInterior', 'postConstruction', 'sofa', 'houseCleaning', 'curtainCleaning'];
        if (!in_array($serviceType, $validServices)) {
            return redirect()->route('admin.gallery')->with('error', 'Invalid service type.');
        }

        // Get service details
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

        $serviceName = $serviceNames[$serviceType];
        
        // Get all images for this service (including inactive ones for admin management)
        $allImages = GalleryImage::forService($serviceType)->ordered()->get();
        

        $images = $allImages->filter(function ($image) {
            return Storage::disk('public')->exists($image->image_path);
        });

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
            'service_type' => 'required|in:carpet,disinfection,glass,carInterior,postConstruction,sofa,houseCleaning,curtainCleaning',
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
            
            // Verify the file was actually stored
            if (!$path || !Storage::disk('public')->exists($path)) {
                throw new \Exception('Failed to store the uploaded file.');
            }

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
            // If database record was created but file storage failed, clean up the record
            if (isset($galleryImage)) {
                $galleryImage->delete();
            }
            
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

    /**
     * Get comments for a specific service type (Admin view)
     * This allows admin to view all comments for a service, including pending ones
     */
    public function getServiceComments($serviceType, Request $request)
    {
        // Validate service type
        $validServices = ['carpet', 'disinfection', 'glass', 'carInterior', 'postConstruction', 'sofa', 'houseCleaning', 'curtainCleaning'];
        if (!in_array($serviceType, $validServices)) {
            return response()->json(['error' => 'Invalid service type'], 400);
        }

        try {
            // Get filter parameters
            $ratingFilter = $request->get('rating', 'all'); // 'all', '1', '2', '3', '4', '5'
            $sortBy = $request->get('sort', 'newest'); // 'newest', 'oldest', 'rating_high', 'rating_low'
            $statusFilter = $request->get('status', 'all'); // 'all', 'approved', 'pending'

            // Start with all comments for this service (including pending ones for admin)
            $query = ServiceComment::forService($serviceType)
                ->with('customer.user'); // Load customer and user relationships

            // Apply status filter
            if ($statusFilter === 'approved') {
                $query->approved();
            } elseif ($statusFilter === 'pending') {
                $query->where('is_approved', false);
            }

            // Apply rating filter
            if ($ratingFilter !== 'all' && is_numeric($ratingFilter)) {
                $query->where('rating', $ratingFilter);
            }

            // Apply sorting
            switch ($sortBy) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'rating_high':
                    $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
                    break;
                case 'rating_low':
                    $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
                    break;
                case 'newest':
                default:
                    $query->latest();
                    break;
            }

            $comments = $query->get()
                ->map(function ($comment) {
                    // Get the customer avatar URL if it exists
                    $customerAvatar = null;
                    if ($comment->customer && $comment->customer->user && $comment->customer->user->avatar) {
                        $customerAvatar = asset('storage/' . $comment->customer->user->avatar);
                    }
                    
                    return [
                        'id' => $comment->id,
                        'comment' => $comment->comment,
                        'rating' => $comment->rating,
                        'is_approved' => $comment->is_approved,
                        'is_edited' => $comment->is_edited,
                        'formatted_date' => $comment->formatted_date,
                        'service_display_name' => $comment->service_display_name,
                        'customer_name' => $comment->customer_display_name,
                        'customer_avatar' => $customerAvatar,
                        'created_at' => $comment->created_at->toISOString(),
                        'updated_at' => $comment->updated_at->toISOString()
                    ];
                });

            return response()->json([
                'success' => true,
                'comments' => $comments,
                'total' => $comments->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load comments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a service comment (Admin only)
     * This allows admin to remove inappropriate or unwanted comments
     */
    public function deleteServiceComment($id)
    {
        try {
            // Find the comment
            $comment = ServiceComment::findOrFail($id);
            
            // Delete the comment
            $comment->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Comment not found'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete comment: ' . $e->getMessage()
            ], 500);
        }
    }
}
