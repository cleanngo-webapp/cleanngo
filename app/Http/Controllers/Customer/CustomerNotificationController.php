<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * CustomerNotificationController handles notification operations for customer users
 * 
 * This controller provides methods to view, manage, and interact with notifications
 * specifically for customer users in the system.
 */
class CustomerNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display the notifications page for customer users
     * Shows all notifications relevant to the authenticated customer
     */
    public function index()
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            abort(404, 'Customer profile not found');
        }

        // Get all notifications for this customer
        $notifications = $this->notificationService->getNotificationsForRecipient('customer', $customer->id);
        $unreadCount = $this->notificationService->getUnreadCountForRecipient('customer', $customer->id);

        return view('customer.notifications', compact('notifications', 'unreadCount'));
    }

    /**
     * Get notifications as JSON for AJAX requests
     * Useful for real-time notification updates
     */
    public function getNotifications(): JsonResponse
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            return response()->json(['error' => 'Customer profile not found'], 404);
        }

        $notifications = $this->notificationService->getNotificationsForRecipient('customer', $customer->id);
        $unreadCount = $this->notificationService->getUnreadCountForRecipient('customer', $customer->id);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id',
        ]);

        $customer = Auth::user()->customer;
        
        if (!$customer) {
            return response()->json(['error' => 'Customer profile not found'], 404);
        }

        $notification = \App\Models\Notification::findOrFail($request->notification_id);
        
        // Ensure this notification belongs to this customer
        if ($notification->recipient_type !== 'customer' || $notification->recipient_id !== $customer->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all customer notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            return response()->json(['error' => 'Customer profile not found'], 404);
        }

        $this->notificationService->markAllAsReadForRecipient('customer', $customer->id);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count for customer
     */
    public function getUnreadCount(): JsonResponse
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            return response()->json(['error' => 'Customer profile not found'], 404);
        }

        $unreadCount = $this->notificationService->getUnreadCountForRecipient('customer', $customer->id);

        return response()->json(['unread_count' => $unreadCount]);
    }

    /**
     * Get unread notifications for dropdown display
     */
    public function getDropdownNotifications(): JsonResponse
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            return response()->json(['error' => 'Customer profile not found'], 404);
        }

        // Get unread notifications only, limited to 4 for dropdown
        $notifications = \App\Models\Notification::where('recipient_type', 'customer')
            ->where('recipient_id', $customer->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return response()->json(['notifications' => $notifications]);
    }
}
