<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <title>@yield('title','Employee')</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen bg-emerald-100 font-sans">
    <nav class="bg-emerald-900 fixed top-0 left-0 right-0 z-50 shadow-lg backdrop-blur-sm">
        <div class="h-12 md:h-14 flex items-center justify-between relative">
            <!-- Logo -->
            <div class="flex gap-4 pl-10 items-center">
                <img src="{{ asset('assets/clean_saver_logo.png') }}" alt="Logo" class="h-12">
                <span class="font-semibold text-white hidden sm:block">CLEANSAVER NAGA</span>
            </div>
    
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-4 text-white absolute right-4">
                <div class="relative">
                    <button onclick="toggleNotificationDropdown()" class="relative hover:text-emerald-700 cursor-pointer transition-colors">
                        <i class="ri-notification-3-line text-2xl"></i>
                        @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                            <span class="notification-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
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
                                <a href="{{ route('employee.notifications') }}" class="block w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer">
                                    View All Notifications
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('employee.profile.show') }}" class="hover:text-emerald-700 cursor-pointer transition-colors"><i class="ri-user-3-fill text-xl"></i></a>
            </div>

            <!-- Mobile Navigation Icons -->
            <div class="md:hidden flex items-center gap-2 pr-4">
                <!-- Notification Icon with Count - Mobile: Direct Link -->
                <a href="{{ route('employee.notifications') }}" class="relative text-xl px-3 py-1 rounded text-white cursor-pointer hover:bg-white hover:text-emerald-700 transition-colors">
                    <i class="ri-notification-3-line"></i>
                    @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                        <span class="notification-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                            {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                        </span>
                    @endif
                </a>

                <!-- Profile Icon -->
                <a href="{{ route('employee.profile.show') }}" class="text-xl px-3 py-1 rounded text-white cursor-pointer hover:bg-white hover:text-emerald-700">
                    <i class="ri-user-3-fill"></i>
                </a>

                <!-- Mobile Menu Button -->
                <button onclick="toggleMobileMenu()" class="text-white text-2xl p-2 hover:bg-white/10 rounded-lg transition-colors">
                    <i id="mobile-menu-icon" class="ri-menu-line"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-emerald-700 border-t border-white/20">
            <div class="px-4 py-6 space-y-4">
                <!-- Mobile Navigation Links -->
                <a href="{{ route('employee.dashboard') }}" onclick="closeMobileMenu()" class="block border rounded-full border-white px-4 py-3 text-center {{ request()->routeIs('employee.dashboard') ? 'bg-white text-emerald-700' : 'text-white hover:bg-white hover:text-emerald-700' }}">Dashboard</a>
                <a href="{{ route('employee.jobs') }}" onclick="closeMobileMenu()" class="block border rounded-full border-white px-4 py-3 text-center {{ request()->routeIs('employee.jobs') ? 'bg-white text-emerald-700' : 'text-white hover:bg-white hover:text-emerald-700' }}">My Jobs</a>
                <a href="{{ route('employee.payroll') }}" onclick="closeMobileMenu()" class="block border rounded-full border-white px-4 py-3 text-center {{ request()->routeIs('employee.payroll') ? 'bg-white text-emerald-700' : 'text-white hover:bg-white hover:text-emerald-700' }}">Payroll</a>
                <a href="{{ route('employee.notifications') }}" onclick="closeMobileMenu()" class="block border rounded-full border-white px-4 py-3 text-center {{ request()->routeIs('employee.notifications') ? 'bg-white text-emerald-700' : 'text-white hover:bg-white hover:text-emerald-700' }}">Notifications</a>
                <a href="{{ route('employee.profile.show') }}" onclick="closeMobileMenu()" class="block border rounded-full border-white px-4 py-3 text-center {{ request()->routeIs('employee.profile.*') ? 'bg-white text-emerald-700' : 'text-white hover:bg-white hover:text-emerald-700' }}">My Profile</a>
                <a href="{{ route('employee.settings') }}" onclick="closeMobileMenu()" class="block border rounded-full border-white px-4 py-3 text-center {{ request()->routeIs('employee.settings') ? 'bg-white text-emerald-700' : 'text-white hover:bg-white hover:text-emerald-700' }}">Settings</a>
                
                <!-- Mobile Logout -->
                <div class="border-t border-white/20 pt-4">
                    <form id="employee-mobile-logout-form" method="POST" action="{{ route('logout') }}" class="block">
                        @csrf  
                        <button type="button" onclick="confirmEmployeeLogout('employee-mobile-logout-form', true)" class="w-full text-white hover:text-emerald-300 transition-colors text-center py-3">
                            <i class="ri-logout-box-line text-xl"></i>
                            <span class="ml-2">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <div class="flex">
        <aside class="hidden md:block w-56 bg-emerald-700 text-white fixed left-0 top-14 h-[calc(100vh-3.5rem)] overflow-y-auto z-10">
            <div class="bg-brand-green flex items-center gap-2 px-4 py-4 w-full">
                <i class="ri-user-line"></i>
                <span class=" text-white font-semibold">Hi, {{ auth()->user()->first_name }}!</span>
            </div>
            <nav class="mt-2 px-2 space-y-1">
                <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('employee.dashboard') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-home-3-line"></i> <span>Dashboard</span></a>
                <a href="{{ route('employee.jobs') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('employee.jobs') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-briefcase-2-line"></i> <span>My Jobs</span></a>
                <a href="{{ route('employee.payroll') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('employee.payroll') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-money-dollar-circle-line"></i> <span>Payroll</span></a>
                <a href="{{ route('employee.notifications') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('employee.notifications') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-notification-3-line"></i> <span>Notifications</span></a>
                <a href="{{ route('employee.profile.show') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('employee.profile.*') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-user-3-fill"></i> <span>My Profile</span></a>
                <a href="{{ route('employee.settings') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors {{ request()->routeIs('employee.settings') ? 'bg-white text-emerald-800 font-semibold' : '' }}"><i class="ri-settings-3-line"></i> <span>Settings</span></a>
                <form id="employee-desktop-logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="button" onclick="confirmEmployeeLogout('employee-desktop-logout-form')" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded hover:bg-white hover:text-emerald-800 cursor-pointer transition-colors"><i class="ri-logout-box-line"></i> <span>Logout</span></button>
                </form>
            </nav>
        </aside>
        <main class="flex-1 p-6 md:ml-56 pt-20 md:pt-14">
            @yield('content')
        </main>
    </div>

    <!-- Mobile Menu and Notification Dropdown JavaScript -->
    <script>
        // Mobile menu functionality
        let mobileMenuOpen = false;

        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('mobile-menu-icon');
            
            if (mobileMenuOpen) {
                closeMobileMenu();
            } else {
                mobileMenu.classList.remove('hidden');
                menuIcon.className = 'ri-close-line';
                mobileMenuOpen = true;
                
                // Close mobile menu when clicking outside
                setTimeout(() => {
                    document.addEventListener('click', handleMobileMenuOutsideClick);
                }, 100);
            }
        }

        function closeMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('mobile-menu-icon');
            
            mobileMenu.classList.add('hidden');
            menuIcon.className = 'ri-menu-line';
            mobileMenuOpen = false;
            document.removeEventListener('click', handleMobileMenuOutsideClick);
        }

        function handleMobileMenuOutsideClick(event) {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuButton = event.target.closest('button[onclick="toggleMobileMenu()"]');
            
            if (!mobileMenu.contains(event.target) && !menuButton) {
                closeMobileMenu();
            }
        }

        // Notification dropdown functionality
        let notificationDropdownOpen = false;
        let notificationsLoaded = false;

        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notification-dropdown');
            const content = document.getElementById('notification-dropdown-content');
            
            if (notificationDropdownOpen) {
                closeNotificationDropdown();
            } else {
                // Always load fresh notifications when opening dropdown
                loadNotificationDropdown();
                
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

            fetch('/employee/notifications/dropdown')
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
                'payment_proof_submitted': 'ri-file-upload-line',
                'employee_account_created': 'ri-user-settings-line',
                'employee_payroll_record': 'ri-money-dollar-circle-line'
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
            fetch('/employee/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    notification_id: notificationId
                })
            }).then(() => {
                // Update notification count immediately
                updateNotificationCount();
                // Redirect to notifications page
                window.location.href = '/employee/notifications';
            });
        }

        // Real-time notification polling system
        let notificationPollingInterval;
        let lastNotificationCount = {{ $unreadNotificationCount ?? 0 }};

        function startNotificationPolling() {
            // Poll every 5 seconds for new notifications
            notificationPollingInterval = setInterval(() => {
                checkForNewNotifications();
            }, 5000); // 5 seconds
        }

        function stopNotificationPolling() {
            if (notificationPollingInterval) {
                clearInterval(notificationPollingInterval);
                notificationPollingInterval = null;
            }
        }

        function checkForNewNotifications() {
            fetch('/employee/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    const currentCount = data.unread_count || 0;
                    
                    // If count has changed, update the badge
                    if (currentCount !== lastNotificationCount) {
                        updateNotificationBadge(currentCount);
                        lastNotificationCount = currentCount;
                        
                        // If count increased, show a subtle notification
                        if (currentCount > lastNotificationCount) {
                            showNewNotificationAlert(currentCount - lastNotificationCount);
                        }
                    }
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

        function showNewNotificationAlert(newCount) {
            // Create a subtle notification alert
            const alert = document.createElement('div');
            alert.className = 'fixed top-20 right-4 bg-emerald-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-all duration-300';
            alert.innerHTML = `
                <div class="flex items-center gap-2">
                    <i class="ri-notification-3-line"></i>
                    <span>${newCount} new notification${newCount > 1 ? 's' : ''}</span>
                </div>
            `;
            
            document.body.appendChild(alert);
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 300);
            }, 3000);
        }

        function updateNotificationCount() {
            // Refresh the notification count immediately
            checkForNewNotifications();
        }

        // Start polling when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startNotificationPolling();
        });

        // Stop polling when page is hidden (tab switch, etc.)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopNotificationPolling();
            } else {
                startNotificationPolling();
                // Check immediately when tab becomes visible again
                checkForNewNotifications();
            }
        });

        // Stop polling when page is about to unload
        window.addEventListener('beforeunload', function() {
            stopNotificationPolling();
        });

        // Logout confirmation (employee)
        function confirmEmployeeLogout(formId, isMobile = false) {
            const form = document.getElementById(formId);
            if (!form) return;

            Swal.fire({
                title: 'Are you sure you want to log out?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#047857',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (isMobile) {
                        closeMobileMenu();
                    }
                    form.submit();
                }
            });
        }
    </script>
</body>
@stack('scripts')
</html>


