<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Booking;
use App\Models\PaymentProof;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\User;
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
                'title' => 'Booking In Progress',
                'message' => sprintf(
                    'Your %s service (Code: %s) has started',
                    $servicesText,
                    $booking->code
                ),
            ],
            'completed' => [
                'title' => 'Booking Completed',
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

        // Notify admin with specific title and message based on status change
        $adminTitles = [
            'confirmed' => 'Booking Confirmed',
            'cancelled' => 'Booking Cancelled', 
            'in_progress' => 'Booking In Progress',
            'completed' => 'Booking Completed'
        ];
        
        $adminTitle = $adminTitles[$newStatus] ?? 'Booking Status Updated';
        
        // Create specific admin messages for each status change
        $adminMessages = [
            'confirmed' => sprintf(
                'Booking %s has been confirmed for customer %s',
                $booking->code,
                $customerName
            ),
            'cancelled' => sprintf(
                'Booking %s has been cancelled for customer %s',
                $booking->code,
                $customerName
            ),
            'in_progress' => sprintf(
                'Booking %s has now started by %s for customer %s',
                $booking->code,
                $this->getAssignedEmployeeName($booking),
                $customerName
            ),
            'completed' => sprintf(
                'Booking %s has been completed by %s for customer %s',
                $booking->code,
                $this->getAssignedEmployeeName($booking),
                $customerName
            )
        ];
        
        $adminMessage = $adminMessages[$newStatus] ?? sprintf(
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

        // Notify assigned employees about booking status changes
        $assignedEmployees = $booking->staffAssignments()->with('employee.user')->get();
        foreach ($assignedEmployees as $assignment) {
            $employee = $assignment->employee;
            $employeeName = $employee->user->first_name . ' ' . $employee->user->last_name;
            
            // Create employee-specific messages based on status change
            $employeeMessages = [
                'confirmed' => [
                    'title' => 'Booking Confirmed',
                    'message' => sprintf(
                        'Booking %s for customer %s has been confirmed. You are assigned to this job scheduled for %s at %s',
                        $booking->code,
                        $customerName,
                        $booking->scheduled_start->format('M d, Y'),
                        $booking->scheduled_start->format('g:i A')
                    ),
                ],
                'cancelled' => [
                    'title' => 'Booking Cancelled',
                    'message' => sprintf(
                        'Booking %s for customer %s has been cancelled. This job assignment is no longer active',
                        $booking->code,
                        $customerName
                    ),
                ],
                'in_progress' => [
                    'title' => 'Booking In Progress',
                    'message' => sprintf(
                        'Booking %s for customer %s is now in progress. You can start working on this job',
                        $booking->code,
                        $customerName
                    ),
                ],
                'completed' => [
                    'title' => 'Booking Completed',
                    'message' => sprintf(
                        'Booking %s for customer %s has been completed. Great job!',
                        $booking->code,
                        $customerName
                    ),
                ],
            ];

            if (isset($employeeMessages[$newStatus])) {
                $employeeData = $employeeMessages[$newStatus];
                
                $this->createNotification([
                    'type' => 'booking_status_changed',
                    'title' => $employeeData['title'],
                    'message' => $employeeData['message'],
                    'data' => [
                        'booking_id' => $booking->id,
                        'customer_id' => $customer->id,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'services' => $services,
                    ],
                    'recipient_type' => 'employee',
                    'recipient_id' => $employee->id,
                ]);
            }
        }
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
     * Create notification when a payment proof is submitted
     * Notifies admin that a new payment proof needs review
     */
    public function notifyPaymentProofSubmitted(PaymentProof $paymentProof): void
    {
        // Load the necessary relationships
        $paymentProof->load(['booking.customer.user', 'booking.bookingItems.service', 'employee.user']);
        
        $booking = $paymentProof->booking;
        $customer = $booking->customer;
        $customerName = $customer->user->first_name . ' ' . $customer->user->last_name;
        $employee = $paymentProof->employee;
        $employeeName = $employee->user->first_name . ' ' . $employee->user->last_name;
        
        // Format services text to avoid duplication
        $servicesData = $this->formatServicesText($booking);
        $servicesText = $servicesData['text'];
        $services = $servicesData['services'];

        $title = 'Payment Proof Submitted';
        $message = sprintf(
            'Employee %s has submitted a payment proof of ₱%s for booking %s (Customer: %s). Please review.',
            $employeeName,
            number_format($paymentProof->amount, 2),
            $booking->code,
            $customerName
        );

        // Notify admin
        $this->createNotification([
            'type' => 'payment_proof_submitted',
            'title' => $title,
            'message' => $message,
            'data' => [
                'booking_id' => $booking->id,
                'payment_proof_id' => $paymentProof->id,
                'employee_id' => $employee->id,
                'customer_id' => $customer->id,
                'amount' => $paymentProof->amount,
                'payment_method' => $paymentProof->payment_method,
                'services' => $services,
            ],
            'recipient_type' => 'admin',
            'recipient_id' => null,
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
            $title = 'Payment Accepted';
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

        // Notify admin with specific title based on payment status
        $adminTitle = $newStatus === 'approved' ? 'Payment Accepted' : 'Payment Declined';
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

        // Notify employee with specific title based on payment status
        $employeeTitle = $newStatus === 'approved' ? 'Payment Accepted' : 'Payment Declined';
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
     * Get the name of the assigned employee for a booking
     * Helper method for admin notifications
     */
    private function getAssignedEmployeeName(Booking $booking): string
    {
        $assignment = $booking->staffAssignments()->with('employee.user')->first();
        
        if ($assignment && $assignment->employee && $assignment->employee->user) {
            return $assignment->employee->user->first_name . ' ' . $assignment->employee->user->last_name;
        }
        
        return 'assigned employee'; // Fallback if no employee is assigned
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

    // ==================== INVENTORY NOTIFICATIONS ====================

    /**
     * Create a notification when a new inventory item is added
     * Notifies admin when a new item is created
     */
    public function notifyInventoryItemCreated(InventoryItem $item): void
    {
        Notification::create([
            'type' => 'inventory_item_created',
            'recipient_type' => 'admin',
            'recipient_id' => null, // Admin notifications don't need specific ID
            'title' => 'New Inventory Item Added',
            'message' => "New inventory item '{$item->name}' ({$item->item_code}) has been added to the inventory.",
            'data' => [
                'item_id' => $item->id,
                'item_code' => $item->item_code,
                'item_name' => $item->name,
                'category' => $item->category,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'reorder_level' => $item->reorder_level,
                'status' => $item->status,
            ],
            'is_read' => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Create a notification when an inventory item is updated
     * Notifies admin when item attributes are modified
     */
    public function notifyInventoryItemUpdated(InventoryItem $item, array $originalData): void
    {
        $changes = $this->getInventoryChanges($item, $originalData);
        
        if (empty($changes)) {
            return; // No significant changes to notify about
        }

        $changeText = implode(', ', $changes);
        
        Notification::create([
            'type' => 'inventory_item_updated',
            'recipient_type' => 'admin',
            'recipient_id' => null,
            'title' => 'Inventory Item Updated',
            'message' => "Inventory item '{$item->name}' ({$item->item_code}) has been updated: {$changeText}.",
            'data' => [
                'item_id' => $item->id,
                'item_code' => $item->item_code,
                'item_name' => $item->name,
                'category' => $item->category,
                'changes' => $changes,
                'current_quantity' => $item->quantity,
                'current_status' => $item->status,
            ],
            'is_read' => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Create a notification when an item reaches low stock level
     * Notifies admin when quantity falls below reorder level
     */
    public function notifyInventoryLowStock(InventoryItem $item): void
    {
        Notification::create([
            'type' => 'inventory_low_stock',
            'recipient_type' => 'admin',
            'recipient_id' => null,
            'title' => 'Low Stock Alert',
            'message' => "Inventory item '{$item->name}' ({$item->item_code}) is running low. Current quantity: {$item->quantity}, Reorder level: {$item->reorder_level}.",
            'data' => [
                'item_id' => $item->id,
                'item_code' => $item->item_code,
                'item_name' => $item->name,
                'category' => $item->category,
                'current_quantity' => $item->quantity,
                'reorder_level' => $item->reorder_level,
                'status' => $item->status,
            ],
            'is_read' => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Create a notification when an item is out of stock
     * Notifies admin when quantity reaches zero
     */
    public function notifyInventoryOutOfStock(InventoryItem $item): void
    {
        Notification::create([
            'type' => 'inventory_out_of_stock',
            'recipient_type' => 'admin',
            'recipient_id' => null,
            'title' => 'Out of Stock Alert',
            'message' => "Inventory item '{$item->name}' ({$item->item_code}) is now out of stock. Please restock immediately.",
            'data' => [
                'item_id' => $item->id,
                'item_code' => $item->item_code,
                'item_name' => $item->name,
                'category' => $item->category,
                'current_quantity' => $item->quantity,
                'reorder_level' => $item->reorder_level,
                'status' => $item->status,
            ],
            'is_read' => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Helper method to detect and format inventory changes
     */
    private function getInventoryChanges(InventoryItem $item, array $originalData): array
    {
        $changes = [];
        
        // Check for quantity changes
        if (isset($originalData['quantity']) && $originalData['quantity'] != $item->quantity) {
            $changes[] = "quantity from {$originalData['quantity']} to {$item->quantity}";
        }
        
        // Check for unit price changes
        if (isset($originalData['unit_price']) && $originalData['unit_price'] != $item->unit_price) {
            $changes[] = "unit price from ₱{$originalData['unit_price']} to ₱{$item->unit_price}";
        }
        
        // Check for reorder level changes
        if (isset($originalData['reorder_level']) && $originalData['reorder_level'] != $item->reorder_level) {
            $changes[] = "reorder level from {$originalData['reorder_level']} to {$item->reorder_level}";
        }
        
        // Check for name changes
        if (isset($originalData['name']) && $originalData['name'] != $item->name) {
            $changes[] = "name from '{$originalData['name']}' to '{$item->name}'";
        }
        
        // Check for category changes
        if (isset($originalData['category']) && $originalData['category'] != $item->category) {
            $changes[] = "category from '{$originalData['category']}' to '{$item->category}'";
        }
        
        // Check for status changes
        if (isset($originalData['status']) && $originalData['status'] != $item->status) {
            $changes[] = "status from '{$originalData['status']}' to '{$item->status}'";
        }
        
        return $changes;
    }

    // ==================== USER MANAGEMENT NOTIFICATIONS ====================

    /**
     * Create a notification when a new customer is registered
     * Notifies admin when a new customer account is created
     */
    public function notifyNewCustomerRegistered(User $user): void
    {
        Notification::create([
            'type' => 'new_customer_registered',
            'recipient_type' => 'admin',
            'recipient_id' => null,
            'title' => 'New Customer Registered',
            'message' => "A new customer '{$user->first_name} {$user->last_name}' ({$user->username}) has registered in the system.",
            'data' => [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'role' => $user->role,
            ],
            'is_read' => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Create a notification when a new employee is created by admin
     * Notifies admin when a new employee account is created
     */
    public function notifyNewEmployeeCreated(User $user, Employee $employee): void
    {
        Notification::create([
            'type' => 'new_employee_created',
            'recipient_type' => 'admin',
            'recipient_id' => null,
            'title' => 'New Employee Created',
            'message' => "A new employee '{$user->first_name} {$user->last_name}' ({$employee->employee_code}) has been added to the system.",
            'data' => [
                'user_id' => $user->id,
                'employee_id' => $employee->id,
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'employee_code' => $employee->employee_code,
                'employment_status' => $employee->employment_status,
                'date_hired' => $employee->date_hired,
            ],
            'is_read' => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Create a notification when a customer is deleted by admin
     * Notifies admin when a customer account is permanently removed
     */
    public function notifyCustomerDeleted(string $customerName, string $customerCode, string $username): void
    {
        Notification::create([
            'type' => 'customer_deleted',
            'recipient_type' => 'admin',
            'recipient_id' => null,
            'title' => 'Customer Deleted',
            'message' => "Customer '{$customerName}' ({$customerCode}) with username '{$username}' has been permanently deleted from the system.",
            'data' => [
                'customer_name' => $customerName,
                'customer_code' => $customerCode,
                'username' => $username,
                'deleted_at' => now()->toISOString(),
            ],
            'is_read' => false,
            'created_at' => now(),
        ]);
    }

    /**
     * Create a notification when an employee is deleted by admin
     * Notifies admin when an employee account is permanently removed
     */
    public function notifyEmployeeDeleted(string $employeeName, string $employeeCode, string $username): void
    {
        Notification::create([
            'type' => 'employee_deleted',
            'recipient_type' => 'admin',
            'recipient_id' => null,
            'title' => 'Employee Deleted',
            'message' => "Employee '{$employeeName}' ({$employeeCode}) with username '{$username}' has been permanently deleted from the system.",
            'data' => [
                'employee_name' => $employeeName,
                'employee_code' => $employeeCode,
                'username' => $username,
                'deleted_at' => now()->toISOString(),
            ],
            'is_read' => false,
            'created_at' => now(),
        ]);
    }
}
