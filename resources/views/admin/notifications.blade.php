@extends('layouts.admin')

@section('title','Notifications')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-emerald-100 p-3 rounded-lg">
                    <i class="ri-notification-3-line text-2xl text-emerald-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                    <p class="text-sm text-gray-500">Stay updated with booking, payment, and employee activities</p>
                </div>
            </div>
            @if($unreadCount > 0)
                <button id="mark-all-read-btn" onclick="markAllAsRead()" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer flex items-center gap-2">
                    <i class="ri-check-double-line"></i>
                    <span id="mark-all-read-text">Mark All as Read ({{ $unreadCount }})</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Notifications Container -->
    <div class="space-y-4" id="notifications-container">
        @forelse($notifications as $notification)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all duration-200 cursor-pointer group {{ $notification->is_read ? 'opacity-75' : 'ring-2 ring-emerald-100' }}"
                 onclick="markAsRead({{ $notification->id }})"
                 data-notification-id="{{ $notification->id }}">
                
                <div class="flex items-start gap-4">
                    <!-- Notification Icon -->
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $notification->is_read ? 'bg-gray-100' : 'bg-emerald-100' }} group-hover:bg-emerald-200 transition-colors">
                            @if($notification->type === 'booking_status_changed')
                                <i class="ri-calendar-check-line text-emerald-600"></i>
                            @elseif($notification->type === 'employee_assigned')
                                <i class="ri-user-add-line text-emerald-600"></i>
                            @elseif($notification->type === 'payment_status_changed')
                                <i class="ri-money-dollar-circle-line text-emerald-600"></i>
                            @elseif($notification->type === 'payment_proof_submitted')
                                <i class="ri-file-upload-line text-emerald-600"></i>
                            @elseif($notification->type === 'inventory_item_created')
                                <i class="ri-add-box-line text-blue-600"></i>
                            @elseif($notification->type === 'inventory_item_updated')
                                <i class="ri-edit-box-line text-orange-600"></i>
                            @elseif($notification->type === 'inventory_low_stock')
                                <i class="ri-alert-line text-yellow-600"></i>
                            @elseif($notification->type === 'inventory_out_of_stock')
                                <i class="ri-error-warning-line text-red-600"></i>
                            @elseif($notification->type === 'new_customer_registered')
                                <i class="ri-user-add-line text-blue-600"></i>
                            @elseif($notification->type === 'new_employee_created')
                                <i class="ri-user-settings-line text-green-600"></i>
                            @elseif($notification->type === 'customer_deleted')
                                <i class="ri-user-unfollow-line text-red-600"></i>
                            @elseif($notification->type === 'employee_deleted')
                                <i class="ri-user-unfollow-line text-red-600"></i>
                            @else
                                <i class="ri-notification-3-line text-emerald-600"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Notification Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 text-sm mb-1">{{ $notification->title }}</h3>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ $notification->message }}</p>
                            </div>
                            
                            <!-- Time and Status -->
                            <div class="flex items-center gap-2 ml-4">
                                <span class="text-xs text-gray-500 whitespace-nowrap">{{ $notification->created_at->format('M d, g:i A') }}</span>
                                @if(!$notification->is_read)
                                    <div class="w-2 h-2 bg-emerald-500 rounded-full flex-shrink-0"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-notification-off-line text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications yet</h3>
                <p class="text-sm text-gray-500 max-w-sm mx-auto">
                    You'll see notifications here when there are updates to bookings, payments, or assignments.
                </p>
            </div>
        @endforelse
    </div>
</div>

<script>
// Function to mark a specific notification as read
function markAsRead(notificationId) {
    fetch('/admin/notifications/mark-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            notification_id: notificationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the notification appearance
            const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationElement) {
                // Remove unread styling
                notificationElement.classList.remove('ring-2', 'ring-emerald-100');
                notificationElement.classList.add('opacity-75');
                
                // Update icon background
                const iconContainer = notificationElement.querySelector('.w-10.h-10');
                if (iconContainer) {
                    iconContainer.classList.remove('bg-emerald-100');
                    iconContainer.classList.add('bg-gray-100');
                }
                
                // Remove the unread indicator
                const unreadIndicator = notificationElement.querySelector('.bg-emerald-500');
                if (unreadIndicator) {
                    unreadIndicator.remove();
                }
            }
            
            // Update unread count in real-time
            if (typeof updateNotificationCount === 'function') {
                updateNotificationCount();
            }
            
            // Update the "Mark All as Read" button count
            updateMarkAllButtonCount();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// Function to update the "Mark All as Read" button count
function updateMarkAllButtonCount() {
    const unreadNotifications = document.querySelectorAll('.ring-2.ring-emerald-100');
    const count = unreadNotifications.length;
    const button = document.getElementById('mark-all-read-btn');
    const textSpan = document.getElementById('mark-all-read-text');
    
    if (count > 0 && button && textSpan) {
        textSpan.textContent = `Mark All as Read (${count})`;
        button.style.display = 'flex';
    } else if (button) {
        button.style.display = 'none';
    }
}

// Function to mark all notifications as read
function markAllAsRead() {
    fetch('/admin/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update all notifications to appear as read
            const notifications = document.querySelectorAll('[data-notification-id]');
            notifications.forEach(notification => {
                // Remove unread styling
                notification.classList.remove('ring-2', 'ring-emerald-100');
                notification.classList.add('opacity-75');
                
                // Update icon background
                const iconContainer = notification.querySelector('.w-10.h-10');
                if (iconContainer) {
                    iconContainer.classList.remove('bg-emerald-100');
                    iconContainer.classList.add('bg-gray-100');
                }
                
                // Remove the unread indicator
                const unreadIndicator = notification.querySelector('.bg-emerald-500');
                if (unreadIndicator) {
                    unreadIndicator.remove();
                }
            });
            
            // Update unread count in real-time
            if (typeof updateNotificationCount === 'function') {
                updateNotificationCount();
            }
            
            // Hide the "Mark All as Read" button
            const markAllButton = document.getElementById('mark-all-read-btn');
            if (markAllButton) {
                markAllButton.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}

// Function to update unread count (for real-time updates)
function updateUnreadCount() {
    fetch('/admin/notifications/unread-count')
    .then(response => response.json())
    .then(data => {
        // Update any unread count displays in the UI
        console.log('Unread count:', data.unread_count);
    })
    .catch(error => {
        console.error('Error fetching unread count:', error);
    });
}

// Real-time notification functions for badge updates
function updateNotificationCount() {
    // Refresh the notification count immediately
    checkForNewNotifications();
}

function checkForNewNotifications() {
    fetch('/admin/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const currentCount = data.unread_count || 0;
            updateNotificationBadge(currentCount);
        })
        .catch(error => {
            console.error('Error checking notification count:', error);
        });
}

function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-badge');
    
    if (count > 0) {
        if (badge) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        } else {
            // Create badge if it doesn't exist
            const button = document.querySelector('button[onclick="toggleNotificationDropdown()"]');
            if (button) {
                const newBadge = document.createElement('span');
                newBadge.className = 'notification-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium';
                newBadge.textContent = count > 99 ? '99+' : count;
                button.appendChild(newBadge);
            }
        }
    } else {
        if (badge) {
            badge.style.display = 'none';
        }
    }
}
</script>
@endsection
