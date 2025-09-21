<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * EmployeeNotificationController handles notification operations for employee users
 * 
 * This controller provides methods to view, manage, and interact with notifications
 * specifically for employee users in the system.
 */
class EmployeeNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display the notifications page for employee users
     * Shows all notifications relevant to the authenticated employee
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role !== 'employee') {
            abort(404, 'User is not an employee');
        }

        // Get all notifications for this employee using user ID
        $notifications = $this->notificationService->getNotificationsForRecipient('employee', $user->id);
        $unreadCount = $this->notificationService->getUnreadCountForRecipient('employee', $user->id);

        return view('employee.notifications', compact('notifications', 'unreadCount'));
    }

    /**
     * Get notifications as JSON for AJAX requests
     * Useful for real-time notification updates
     */
    public function getNotifications(): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role !== 'employee') {
            return response()->json(['error' => 'User is not an employee'], 404);
        }

        $notifications = $this->notificationService->getNotificationsForRecipient('employee', $user->id);
        $unreadCount = $this->notificationService->getUnreadCountForRecipient('employee', $user->id);

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

        $user = Auth::user();
        
        if ($user->role !== 'employee') {
            return response()->json(['error' => 'User is not an employee'], 404);
        }

        $notification = \App\Models\Notification::findOrFail($request->notification_id);
        
        // Ensure this notification belongs to this employee
        if ($notification->recipient_type !== 'employee' || $notification->recipient_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all employee notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role !== 'employee') {
            return response()->json(['error' => 'User is not an employee'], 404);
        }

        $this->notificationService->markAllAsReadForRecipient('employee', $user->id);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count for employee
     */
    public function getUnreadCount(): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role !== 'employee') {
            return response()->json(['error' => 'User is not an employee'], 404);
        }

        $unreadCount = $this->notificationService->getUnreadCountForRecipient('employee', $user->id);

        return response()->json(['unread_count' => $unreadCount]);
    }

    /**
     * Get unread notifications for dropdown display
     */
    public function getDropdownNotifications(): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->role !== 'employee') {
            return response()->json(['error' => 'User is not an employee'], 404);
        }

        // Get unread notifications only, limited to 4 for dropdown
        $notifications = \App\Models\Notification::where('recipient_type', 'employee')
            ->where('recipient_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return response()->json(['notifications' => $notifications]);
    }
}
