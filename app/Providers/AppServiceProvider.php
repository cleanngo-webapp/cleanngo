<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share unread notification count across all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $unreadNotificationCount = 0;
                
                // Get unread count based on user type
                if ($user->role === 'customer') {
                    $notificationService = app(NotificationService::class);
                    $unreadNotificationCount = $notificationService->getUnreadCountForRecipient('customer', $user->id);
                } elseif ($user->role === 'employee') {
                    $notificationService = app(NotificationService::class);
                    $unreadNotificationCount = $notificationService->getUnreadCountForRecipient('employee', $user->id);
                } elseif ($user->role === 'admin') {
                    $notificationService = app(NotificationService::class);
                    $unreadNotificationCount = $notificationService->getUnreadCountForRecipient('admin', null);
                }
                
                $view->with('unreadNotificationCount', $unreadNotificationCount);
            }
        });
    }
}
