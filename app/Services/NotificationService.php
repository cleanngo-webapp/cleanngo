<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Booking;
use App\Models\PaymentProof;
use App\Models\Customer;
use App\Models\Employee;
use Carbon\Carbon;

/**
 * NotificationService handles all notification creation and management
 * 
 * This service centralizes notification logic for different booking events
 * and ensures consistent notification messages across the system.
 */
class NotificationService
{
    /**
     * Create a notification for booking creation
     * Notifies admin when a customer creates a new booking
     */
    public function notifyBookingCreated(Booking $booking): void
    {
        // Load the necessary relationships
        $booking->load(['customer.user', 'bookingItems.service']);
        
        $customer = $booking->customer;
        $customerName = $customer->user->first_name . ' ' . $customer->user->last_name;
        
        // Format services text to avoid duplication
        $servicesData = $this->formatServicesText($booking);
        $servicesText = $servicesData['text'];
        $services = $servicesData['services'];
        
        $title = 'New Booking Request';
        $message = sprintf(
            'New booking request from %s - %s (Code: %s) scheduled for %s at %s',
            $customerName,
            $servicesText,
            $booking->code,
            $booking->scheduled_start->format('M d, Y'),
            $booking->scheduled_start->format('g:i A')
        );

        $this->createNotification([
            'type' => 'booking_created',
            'title' => $title,
            'message' => $message,
            'data' => [
                'booking_id' => $booking->id,
                'customer_id' => $customer->id,
                'services' => $services,
            ],
            'recipient_type' => 'admin',
            'recipient_id' => null, // Admin notifications don't need specific ID
        ]);
    }

    /**
     * Create notifications for booking status changes
     * Notifies customer and admin when booking status changes (confirmed, declined, in_progress, completed)
     */
    public function notifyBookingStatusChanged(Booking $booking, string $oldStatus, string $newStatus): void
    {
        // Load the necessary relationships
        $booking->load(['customer.user', 'bookingItems.service']);
        
        $customer = $booking->customer;
        $customerName = $customer->user->first_name . ' ' . $customer->user->last_name;
        
        // Format services text to avoid duplication
        $servicesData = $this->formatServicesText($booking);
        $servicesText = $servicesData['text'];
        $services = $servicesData['services'];
        
        // Determine notification details based on status change
        $statusMessages = [
            'confirmed' => [
                'title' => 'Booking Confirmed',
                'message' => sprintf(
                    'Your booking for %s (Code: %s) has been confirmed for %s at %s',
                    $servicesText,
                    $booking->code,
                    $booking->scheduled_start->format('M d, Y'),
                    $booking->scheduled_start->format('g:i A')
                                    ),
            ],
            'cancelled' => [
                'title' => 'Booking Cancelled',
                'message' => sprintf(
                    'Your booking for %s (Code: %s) has been cancelled',
                    $servicesText,
                    $booking->code
                ),
            ],
            'in_progress' => [
                'title' => 'Service Started',
                'message' => sprintf(
                    'Your %s service (Code: %s) has started',
                    $servicesText,
                    $booking->code
                ),
            ],
            'completed' => [
                'title' => 'Service Completed',
                'message' => sprintf(
                    'Your %s service (Code: %s) has been completed. Please rate your experience',
                    $servicesText,
                    $booking->code
                    
                ),
            ],
        ];

        if (!isset($statusMessages[$newStatus])) {
            return; // No notification needed for this status
        }

        $notificationData = $statusMessages[$newStatus];
        
        // Notify customer
        $this->createNotification([
            'type' => 'booking_status_changed',
            'title' => $notificationData['title'],
            'message' => $notificationData['message'],
            'data' => [
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'services' => $services,
            ],
            'recipient_type' => 'customer',
            'recipient_id' => $customer->id,
        ]);

        // Notify admin
        $adminTitle = 'Booking Status Updated';
        $adminMessage = sprintf(
            'Booking %s status changed from %s to %s for customer %s',
            $booking->code,
            ucfirst(str_replace('_', ' ', $oldStatus)),
            ucfirst(str_replace('_', ' ', $newStatus)),
            $customerName
        );

        $this->createNotification([
            'type' => 'booking_status_changed',
            'title' => $adminTitle,
            'message' => $adminMessage,
            'data' => [
                'booking_id' => $booking->id,
                'customer_id' => $customer->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'services' => $services,
            ],
            'recipient_type' => 'admin',
            'recipient_id' => null,
        ]);
    }

    /**
     * Create notifications for employee assignment
     * Notifies customer, admin, and assigned employee when an employee is assigned to a booking
     */
    public function notifyEmployeeAssigned(Booking $booking, Employee $employee): void
    {
        // Load the necessary relationships
        $booking->load(['customer.user', 'bookingItems.service']);
        $employee->load('user');
        
        $customer = $booking->customer;
        $customerName = $customer->user->first_name . ' ' . $customer->user->last_name;
        $employeeName = $employee->user->first_name . ' ' . $employee->user->last_name;
        
        // Format services text to avoid duplication
        $servicesData = $this->formatServicesText($booking);
        $servicesText = $servicesData['text'];
        $services = $servicesData['services'];
        
        $title = 'Employee Assigned';
        $message = sprintf(
            'Your booking for %s (Code: %s) has been assigned to %s',
            $servicesText,
            $booking->code,
            $employeeName
        );

        // Notify customer
        $this->createNotification([
            'type' => 'employee_assigned',
            'title' => $title,
            'message' => $message,
            'data' => [
                'booking_id' => $booking->id,
                'employee_id' => $employee->id,
                'customer_id' => $customer->id,
                'services' => $services,
            ],
            'recipient_type' => 'customer',
            'recipient_id' => $customer->id,
        ]);

        // Notify admin
        $adminTitle = 'Employee Assignment';
        $adminMessage = sprintf(
            'Employee %s has been assigned to booking %s for customer %s',
            $employeeName,
            $booking->code,
            $customerName
        );

        $this->createNotification([
            'type' => 'employee_assigned',
            'title' => $adminTitle,
            'message' => $adminMessage,
            'data' => [
                'booking_id' => $booking->id,
                'employee_id' => $employee->id,
                'customer_id' => $customer->id,
                'services' => $services,
            ],
            'recipient_type' => 'admin',
            'recipient_id' => null,
        ]);

        // Notify assigned employee
        $employeeTitle = 'New Job Assignment';
        $employeeMessage = sprintf(
            'You have been assigned to %s for customer %s (Code: %s) scheduled for %s at %s',
            $servicesText,
            $customerName,
            $booking->code,
            $booking->scheduled_start->format('M d, Y'),
            $booking->scheduled_start->format('g:i A')
        );

        $this->createNotification([
            'type' => 'employee_assigned',
            'title' => $employeeTitle,
            'message' => $employeeMessage,
            'data' => [
                'booking_id' => $booking->id,
                'customer_id' => $customer->id,
                'services' => $services,
            ],
            'recipient_type' => 'employee',
            'recipient_id' => $employee->id,
        ]);
    }

    /**
     * Create notifications for payment status changes
     * Notifies customer, admin, and employee when payment is approved or declined
     */
    public function notifyPaymentStatusChanged(PaymentProof $paymentProof, string $oldStatus, string $newStatus): void
    {
        // Load the necessary relationships
        $paymentProof->load(['booking.customer.user', 'booking.bookingItems.service', 'employee.user']);
        
        $booking = $paymentProof->booking;
        $customer = $booking->customer;
        $customerName = $customer->user->first_name . ' ' . $customer->user->last_name;
        $employee = $paymentProof->employee;
        
        // Format services text to avoid duplication
        $servicesData = $this->formatServicesText($booking);
        $servicesText = $servicesData['text'];
        $services = $servicesData['services'];

        if ($newStatus === 'approved') {
            $title = 'Payment Approved';
            $message = sprintf(
                'Payment of ₱%s for your %s service (Code: %s) has been approved',
                number_format($paymentProof->amount, 2),
                $servicesText,
                $booking->code
            );
        } else {
            $title = 'Payment Declined';
            $message = sprintf(
                'Payment of ₱%s for your %s service (Code: %s) has been declined',
                number_format($paymentProof->amount, 2),
                $servicesText,
                $booking->code
            );
        }

        // Notify customer
        $this->createNotification([
            'type' => 'payment_status_changed',
            'title' => $title,
            'message' => $message,
            'data' => [
                'booking_id' => $booking->id,
                'payment_proof_id' => $paymentProof->id,
                'amount' => $paymentProof->amount,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'services' => $services,
            ],
            'recipient_type' => 'customer',
            'recipient_id' => $customer->id,
        ]);

        // Notify admin
        $adminTitle = 'Payment Status Updated';
        $adminMessage = sprintf(
            'Payment of ₱%s for booking %s has been %s',
            number_format($paymentProof->amount, 2),
            $booking->code,
            $newStatus
        );

        $this->createNotification([
            'type' => 'payment_status_changed',
            'title' => $adminTitle,
            'message' => $adminMessage,
            'data' => [
                'booking_id' => $booking->id,
                'payment_proof_id' => $paymentProof->id,
                'customer_id' => $customer->id,
                'employee_id' => $employee->id,
                'amount' => $paymentProof->amount,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'services' => $services,
            ],
            'recipient_type' => 'admin',
            'recipient_id' => null,
        ]);

        // Notify employee
        $employeeTitle = 'Payment Status Updated';
        $employeeMessage = sprintf(
            'Payment of ₱%s for booking %s has been %s',
            number_format($paymentProof->amount, 2),
            $booking->code,
            $newStatus
        );

        $this->createNotification([
            'type' => 'payment_status_changed',
            'title' => $employeeTitle,
            'message' => $employeeMessage,
            'data' => [
                'booking_id' => $booking->id,
                'payment_proof_id' => $paymentProof->id,
                'customer_id' => $customer->id,
                'amount' => $paymentProof->amount,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'services' => $services,
            ],
            'recipient_type' => 'employee',
            'recipient_id' => $employee->id,
        ]);
    }

    /**
     * Create a notification record in the database
     */
    private function createNotification(array $data): void
    {
        Notification::create($data);
    }

    /**
     * Format services text by grouping duplicate services and showing counts
     * This prevents duplicate service names in notifications when customers book multiple items of the same service
     * 
     * @return array ['text' => string, 'services' => array]
     */
    private function formatServicesText(Booking $booking): array
    {
        // Get all services from booking items
        $services = $booking->bookingItems->map(function ($item) {
            return $item->service->name;
        })->toArray();
        
        // If no booking items, fall back to the main service
        if (empty($services)) {
            $booking->load('service');
            $services = [$booking->service->name];
        }
        
        // Group services by name and count occurrences to avoid duplication
        $serviceCounts = array_count_values($services);
        $serviceTexts = [];
        
        foreach ($serviceCounts as $serviceName => $count) {
            if ($count > 1) {
                $serviceTexts[] = $serviceName . " (x{$count})";
            } else {
                $serviceTexts[] = $serviceName;
            }
        }
        
        return [
            'text' => implode(', ', $serviceTexts),
            'services' => $services
        ];
    }

    /**
     * Get notifications for a specific recipient
     */
    public function getNotificationsForRecipient(string $type, ?int $id = null, int $limit = 50)
    {
        return Notification::forRecipient($type, $id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread notification count for a specific recipient
     */
    public function getUnreadCountForRecipient(string $type, ?int $id = null): int
    {
        return Notification::forRecipient($type, $id)
            ->unread()
            ->count();
    }

    /**
     * Mark all notifications as read for a specific recipient
     */
    public function markAllAsReadForRecipient(string $type, ?int $id = null): void
    {
        Notification::forRecipient($type, $id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }
}
