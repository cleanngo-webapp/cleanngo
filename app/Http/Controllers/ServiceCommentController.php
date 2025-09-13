<?php

namespace App\Http\Controllers;

use App\Models\ServiceComment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceCommentController extends Controller
{
    /**
     * Get comments for a specific service type
     * This returns comments in JSON format for AJAX requests
     */
    public function getComments($serviceType)
    {
        // Validate service type
        $validServices = ['carpet', 'disinfection', 'glass', 'carInterior', 'postConstruction', 'sofa'];
        if (!in_array($serviceType, $validServices)) {
            return response()->json(['error' => 'Invalid service type'], 400);
        }

        // Get approved comments for this service with customer information
        $allComments = ServiceComment::forService($serviceType)->with('customer')->get();
        $approvedComments = ServiceComment::forService($serviceType)
            ->approved()
            ->latest()
            ->with('customer')
            ->get();
            
        // Debug: Log comment counts
        \Log::info('Comments debug', [
            'service_type' => $serviceType,
            'all_comments_count' => $allComments->count(),
            'approved_comments_count' => $approvedComments->count(),
            'all_comments' => $allComments->map(function($c) {
                return ['id' => $c->id, 'is_approved' => $c->is_approved, 'comment' => substr($c->comment, 0, 50)];
            })
        ]);
        
        $comments = $approvedComments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'rating' => $comment->rating,
                'customer_name' => $comment->customer_display_name,
                'formatted_date' => $comment->formatted_date,
                'is_edited' => $comment->is_edited,
                'can_edit' => $this->canEditComment($comment),
                'can_delete' => $this->canDeleteComment($comment)
            ];
        });

        return response()->json([
            'comments' => $comments,
            'count' => $comments->count()
        ]);
    }

    /**
     * Store a new comment
     * This handles creating new comments from customers
     */
    public function store(Request $request)
    {
        // Debug: Log the incoming request
        \Log::info('Comment store request received', [
            'all_data' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);
        
        // Validate the request
        try {
            $request->validate([
                'service_type' => 'required|in:carpet,disinfection,glass,carInterior,postConstruction,sofa',
                'comment' => 'required|string|min:1|max:1000',
                'rating' => 'nullable|integer|min:1|max:5'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        }

        // Get the authenticated user - try multiple approaches
        $user = Auth::guard('customer')->user();
        
        // Fallback to default auth if customer guard doesn't work
        if (!$user) {
            $user = Auth::user();
        }
        
        // Debug: Log authentication status
        \Log::info('User authentication check', [
            'customer_guard' => Auth::guard('customer')->user() ? Auth::guard('customer')->user()->id : 'null',
            'default_auth' => Auth::user() ? Auth::user()->id : 'null',
            'final_user' => $user ? $user->id : 'null'
        ]);
        
        if (!$user) {
            return response()->json(['error' => 'Authentication required. Please log in to post comments.'], 401);
        }
        
        // Get the customer profile for this user
        $customer = $user->customer;
        
        \Log::info('Customer profile check', [
            'user_id' => $user->id,
            'customer_exists' => $customer ? 'yes' : 'no',
            'customer_id' => $customer ? $customer->id : 'null'
        ]);
        
        if (!$customer) {
            return response()->json(['error' => 'Customer profile not found. Please contact support.'], 400);
        }

        try {
            // Debug: Log before creating comment
            \Log::info('About to create comment', [
                'service_type' => $request->service_type,
                'customer_id' => $customer->id,
                'comment_length' => strlen($request->comment),
                'rating' => $request->rating
            ]);
            
            // Create the comment
            $comment = ServiceComment::create([
                'service_type' => $request->service_type,
                'customer_id' => $customer->id,
                'comment' => $request->comment,
                'rating' => $request->rating,
                'is_approved' => true, // Auto-approve for now, can be changed to false for moderation
                'is_edited' => false
            ]);

            // Debug: Log the created comment
            \Log::info('Comment created successfully', [
                'comment_id' => $comment->id,
                'service_type' => $comment->service_type,
                'customer_id' => $comment->customer_id,
                'is_approved' => $comment->is_approved,
                'comment_text' => substr($comment->comment, 0, 50)
            ]);

            // Load the customer relationship for the response
            $comment->load('customer');

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully!',
                'comment' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'rating' => $comment->rating,
                    'customer_name' => $comment->customer_display_name,
                    'formatted_date' => $comment->formatted_date,
                    'is_edited' => $comment->is_edited,
                    'can_edit' => true,
                    'can_delete' => true
                ]
            ]);

        } catch (\Exception $e) {
            // Debug: Log the exception details
            \Log::error('Comment creation failed', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json(['error' => 'Failed to add comment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update an existing comment
     * This allows customers to edit their own comments
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'comment' => 'required|string|min:1|max:1000',
            'rating' => 'nullable|integer|min:1|max:5'
        ]);

        // Get the authenticated user
        $user = Auth::guard('customer')->user();
        if (!$user) {
            $user = Auth::user();
        }
        if (!$user) {
            return response()->json(['error' => 'Authentication required'], 401);
        }
        
        // Get the customer profile for this user
        $customer = $user->customer;
        if (!$customer) {
            return response()->json(['error' => 'Customer profile not found'], 400);
        }

        // Find the comment
        $comment = ServiceComment::findOrFail($id);

        // Check if customer can edit this comment
        if (!$this->canEditComment($comment, $customer)) {
            return response()->json(['error' => 'You can only edit your own comments'], 403);
        }

        try {
            // Update the comment
            $comment->update([
                'comment' => $request->comment,
                'rating' => $request->rating,
                'is_edited' => true
            ]);

            // Load the customer relationship for the response
            $comment->load('customer');

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully!',
                'comment' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'rating' => $comment->rating,
                    'customer_name' => $comment->customer_display_name,
                    'formatted_date' => $comment->formatted_date,
                    'is_edited' => $comment->is_edited,
                    'can_edit' => true,
                    'can_delete' => true
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update comment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a comment
     * This allows customers to delete their own comments
     */
    public function destroy($id)
    {
        // Get the authenticated user
        $user = Auth::guard('customer')->user();
        if (!$user) {
            $user = Auth::user();
        }
        if (!$user) {
            return response()->json(['error' => 'Authentication required'], 401);
        }
        
        // Get the customer profile for this user
        $customer = $user->customer;
        if (!$customer) {
            return response()->json(['error' => 'Customer profile not found'], 400);
        }

        // Find the comment
        $comment = ServiceComment::findOrFail($id);

        // Check if customer can delete this comment
        if (!$this->canDeleteComment($comment, $customer)) {
            return response()->json(['error' => 'You can only delete your own comments'], 403);
        }

        try {
            // Delete the comment
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete comment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check if a customer can edit a comment
     * This ensures customers can only edit their own comments
     */
    private function canEditComment($comment, $customer = null)
    {
        if (!$customer) {
            $user = Auth::guard('customer')->user();
            if (!$user) {
                $user = Auth::user();
            }
            if (!$user) {
                return false;
            }
            $customer = $user->customer;
        }

        if (!$customer) {
            return false;
        }

        return $comment->customer_id === $customer->id;
    }

    /**
     * Check if a customer can delete a comment
     * This ensures customers can only delete their own comments
     */
    private function canDeleteComment($comment, $customer = null)
    {
        if (!$customer) {
            $user = Auth::guard('customer')->user();
            if (!$user) {
                $user = Auth::user();
            }
            if (!$user) {
                return false;
            }
            $customer = $user->customer;
        }

        if (!$customer) {
            return false;
        }

        return $comment->customer_id === $customer->id;
    }
}
