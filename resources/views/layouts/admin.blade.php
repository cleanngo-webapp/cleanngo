<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <title>@yield('title','Admin')</title>
    {{-- Tailwind classes are applied inline on links for reliability --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen bg-emerald-100 font-sans">
    <header class="h-12 md:h-14 bg-emerald-900 text-white flex items-center justify-between px-4 fixed top-0 left-0 right-0 z-20">
        <div class="flex items-center gap-4">
            <img src="{{ asset('assets/clean_saver_logo.png') }}" alt="Logo" class="h-12">
            <span class="font-semibold justify-center">CLEANSAVER NAGA</span>
        </div>
        <div class="flex items-center gap-4 text-white">
             <div class="relative">
                 <button onclick="toggleNotificationDropdown()" class="relative hover:text-emerald-700 cursor-pointer transition-colors">
                    <i class="ri-notification-3-line text-2xl"></i>
                    @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                            {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                        </span>
                    @endif
                 </button>
                 
                 <!-- Notification Dropdown Modal -->
                 <div id="notification-dropdown" class="hidden absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                     <div class="p-4">
                         <div class="flex items-center justify-between mb-3">
                             <h3 class="font-semibold text-gray-900">Notifications</h3>
                             <button onclick="closeNotificationDropdown()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                                 <i class="ri-close-line"></i>
                             </button>
                         </div>
                         
                         <div id="notification-dropdown-content" class="space-y-3 max-h-80 overflow-y-auto">
                             <!-- Notifications will be loaded here via JavaScript -->
                         </div>
                         
                         <div class="mt-3 pt-3 border-t border-gray-100">
                             <a href="{{ route('admin.notifications') }}" class="block w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer">
                                 View All Notifications
                             </a>
                         </div>
                     </div>
                 </div>
             </div>
        </div>
    </header>
    <div class="flex">
        <aside class="w-56 bg-emerald-700 text-white fixed left-0 top-14 h-[calc(100vh-3.5rem)] overflow-y-auto z-10">
            <div class="bg-brand-green flex items-center gap-2 px-4 py-4 w-full">
                <i class="ri-admin-line"></i>
                <span class="text-white font-semibold">Hi, {{ auth()->user()->first_name }}!</span>
            </div>
            <nav class="mt-2 px-2 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-home-3-line"></i> <span>Dashboard</span></a>
                <a href="{{ route('admin.bookings') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('admin.bookings') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-calendar-2-line"></i> <span>Bookings</span></a>
                <a href="{{ route('admin.employees') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('admin.employees') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-team-line"></i> <span>Employees</span></a>
                <a href="{{ route('admin.payroll') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('admin.payroll') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-money-dollar-circle-line"></i> <span>Payroll</span></a>
                <a href="{{ route('admin.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('admin.inventory') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-archive-2-line"></i> <span>Inventory</span></a>
                <a href="{{ route('admin.customers') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('admin.customers') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-user-star-line"></i> <span>Customers</span></a>
                <a href="{{ route('admin.gallery') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('admin.gallery') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-image-2-line"></i> <span>Gallery</span></a>
                <a href="{{ route('admin.notifications') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('admin.notifications') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-notification-3-line"></i> <span>Notifications</span></a>
                <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('admin.settings') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-settings-3-line"></i> <span>Settings</span></a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors"><i class="ri-logout-box-line"></i> <span>Logout</span></button>
                </form>
            </nav>
        </aside>
        <main class="flex-1 p-6 ml-56 mt-14">
            @yield('content')
        </main>
    </div>

    <!-- Notification Dropdown JavaScript -->
    <script>
        let notificationDropdownOpen = false;
        let notificationsLoaded = false;

        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notification-dropdown');
            const content = document.getElementById('notification-dropdown-content');
            
            if (notificationDropdownOpen) {
                closeNotificationDropdown();
            } else {
                // Load notifications if not already loaded
                if (!notificationsLoaded) {
                    loadNotificationDropdown();
                }
                
                dropdown.classList.remove('hidden');
                notificationDropdownOpen = true;
                
                // Close dropdown when clicking outside
                setTimeout(() => {
                    document.addEventListener('click', handleOutsideClick);
                }, 100);
            }
        }

        function closeNotificationDropdown() {
            const dropdown = document.getElementById('notification-dropdown');
            dropdown.classList.add('hidden');
            notificationDropdownOpen = false;
            document.removeEventListener('click', handleOutsideClick);
        }

        function handleOutsideClick(event) {
            const dropdown = document.getElementById('notification-dropdown');
            const button = event.target.closest('button[onclick="toggleNotificationDropdown()"]');
            
            if (!dropdown.contains(event.target) && !button) {
                closeNotificationDropdown();
            }
        }

        function loadNotificationDropdown() {
            const content = document.getElementById('notification-dropdown-content');
            content.innerHTML = `
                <div class="flex justify-center items-center py-4">
                    <div class="w-6 h-6 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
                    <span class="ml-2 text-sm text-gray-500">Loading notifications...</span>
                </div>
            `;

            fetch('/admin/notifications/dropdown')
                .then(response => response.json())
                .then(data => {
                    notificationsLoaded = true;
                    displayNotificationDropdown(data.notifications);
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    content.innerHTML = `
                        <div class="text-center py-4 text-gray-500">
                            <i class="ri-error-warning-line text-2xl mb-2"></i>
                            <p class="text-sm">Failed to load notifications</p>
                        </div>
                    `;
                });
        }

        function displayNotificationDropdown(notifications) {
            const content = document.getElementById('notification-dropdown-content');
            
            if (notifications.length === 0) {
                content.innerHTML = `
                    <div class="text-center py-4 text-gray-500">
                        <i class="ri-notification-off-line text-2xl mb-2"></i>
                        <p class="text-sm">No unread notifications</p>
                    </div>
                `;
                return;
            }

            let html = '';
            notifications.slice(0, 4).forEach(notification => {
                const timeAgo = getTimeAgo(notification.created_at);
                const iconClass = getNotificationIcon(notification.type);
                
                html += `
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer" 
                         onclick="markAsReadAndRedirect(${notification.id})">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                                <i class="${iconClass} text-emerald-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-gray-900 text-sm mb-1">${notification.title}</h4>
                            <p class="text-xs text-gray-600 line-clamp-2">${notification.message}</p>
                            <span class="text-xs text-gray-400 mt-1 block">${timeAgo}</span>
                        </div>
                    </div>
                `;
            });

            content.innerHTML = html;
        }

        function getNotificationIcon(type) {
            const icons = {
                'booking_status_changed': 'ri-calendar-check-line',
                'employee_assigned': 'ri-user-add-line',
                'payment_status_changed': 'ri-money-dollar-circle-line',
                'payment_proof_submitted': 'ri-file-upload-line'
            };
            return icons[type] || 'ri-notification-3-line';
        }

        function getTimeAgo(dateString) {
            const now = new Date();
            const date = new Date(dateString);
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
            return `${Math.floor(diffInSeconds / 86400)}d ago`;
        }

        function markAsReadAndRedirect(notificationId) {
            // Mark as read
            fetch('/admin/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    notification_id: notificationId
                })
            }).then(() => {
                // Redirect to notifications page
                window.location.href = '/admin/notifications';
            });
        }
    </script>
</body>
@stack('scripts')
</html>


