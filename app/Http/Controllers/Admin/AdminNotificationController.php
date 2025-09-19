<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * AdminNotificationController handles notification operations for admin users
 * 
 * This controller provides methods to view, manage, and interact with notifications
 * specifically for admin users in the system.
 */
class AdminNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display the notifications page for admin users
     * Shows all notifications relevant to admin users
     */
    public function index()
    {
        // Get all admin notifications (recipient_id is null for admin notifications)
        $notifications = $this->notificationService->getNotificationsForRecipient('admin', null);
        $unreadCount = $this->notificationService->getUnreadCountForRecipient('admin', null);

        return view('admin.notifications', compact('notifications', 'unreadCount'));
    }

    /**
     * Get notifications as JSON for AJAX requests
     * Useful for real-time notification updates
     */
    public function getNotifications(): JsonResponse
    {
        $notifications = $this->notificationService->getNotificationsForRecipient('admin', null);
        $unreadCount = $this->notificationService->getUnreadCountForRecipient('admin', null);

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

        $notification = \App\Models\Notification::findOrFail($request->notification_id);
        
        // Ensure this notification belongs to admin
        if ($notification->recipient_type !== 'admin' || $notification->recipient_id !== null) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all admin notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $this->notificationService->markAllAsReadForRecipient('admin', null);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count for admin
     */
    public function getUnreadCount(): JsonResponse
    {
        $unreadCount = $this->notificationService->getUnreadCountForRecipient('admin', null);

        return response()->json(['unread_count' => $unreadCount]);
    }
}
