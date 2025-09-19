@extends('layouts.employee')

@section('title','Notifications')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold">Notifications</h1>
        @if($unreadCount > 0)
            <button onclick="markAllAsRead()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                Mark All as Read ({{ $unreadCount }})
            </button>
        @endif
    </div>

    <div class="mt-6 space-y-3" id="notifications-container">
        @forelse($notifications as $notification)
            <div class="bg-{{ $notification->is_read ? 'gray-200' : 'emerald-300' }} rounded-full px-4 py-3 flex justify-between items-center cursor-pointer hover:bg-emerald-400 transition-colors"
                 onclick="markAsRead({{ $notification->id }})"
                 data-notification-id="{{ $notification->id }}">
                <div class="flex-1">
                    <div class="font-semibold text-sm">{{ $notification->title }}</div>
                    <div class="text-sm mt-1">{{ $notification->message }}</div>
                </div>
                <div class="text-right">
                    <span class="text-sm">{{ $notification->created_at->format('M d') }}</span>
                    @if(!$notification->is_read)
                        <div class="w-2 h-2 bg-red-500 rounded-full ml-2 inline-block"></div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                <p class="text-lg">No notifications yet</p>
                <p class="text-sm">You'll see notifications here when you're assigned to jobs, payments are processed, or there are updates to your assignments.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
// Function to mark a specific notification as read
function markAsRead(notificationId) {
    fetch('/employee/notifications/mark-read', {
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
                notificationElement.classList.remove('bg-emerald-300');
                notificationElement.classList.add('bg-gray-200');
                
                // Remove the unread indicator
                const unreadIndicator = notificationElement.querySelector('.bg-red-500');
                if (unreadIndicator) {
                    unreadIndicator.remove();
                }
            }
            
            // Update unread count
            updateUnreadCount();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// Function to mark all notifications as read
function markAllAsRead() {
    fetch('/employee/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to show updated notifications
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}

// Function to update unread count (for future real-time updates)
function updateUnreadCount() {
    fetch('/employee/notifications/unread-count')
    .then(response => response.json())
    .then(data => {
        // Update any unread count displays in the UI
        console.log('Unread count:', data.unread_count);
    })
    .catch(error => {
        console.error('Error fetching unread count:', error);
    });
}
</script>
@endsection


