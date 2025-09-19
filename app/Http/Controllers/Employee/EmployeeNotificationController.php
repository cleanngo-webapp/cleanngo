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
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            abort(404, 'Employee profile not found');
        }

        // Get all notifications for this employee
        $notifications = $this->notificationService->getNotificationsForRecipient('employee', $employee->id);
        $unreadCount = $this->notificationService->getUnreadCountForRecipient('employee', $employee->id);

        return view('employee.notifications', compact('notifications', 'unreadCount'));
    }

    /**
     * Get notifications as JSON for AJAX requests
     * Useful for real-time notification updates
     */
    public function getNotifications(): JsonResponse
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found'], 404);
        }

        $notifications = $this->notificationService->getNotificationsForRecipient('employee', $employee->id);
        $unreadCount = $this->notificationService->getUnreadCountForRecipient('employee', $employee->id);

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

        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found'], 404);
        }

        $notification = \App\Models\Notification::findOrFail($request->notification_id);
        
        // Ensure this notification belongs to this employee
        if ($notification->recipient_type !== 'employee' || $notification->recipient_id !== $employee->id) {
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
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found'], 404);
        }

        $this->notificationService->markAllAsReadForRecipient('employee', $employee->id);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count for employee
     */
    public function getUnreadCount(): JsonResponse
    {
        $employee = Auth::user()->employee;
        
        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found'], 404);
        }

        $unreadCount = $this->notificationService->getUnreadCountForRecipient('employee', $employee->id);

        return response()->json(['unread_count' => $unreadCount]);
    }
}
