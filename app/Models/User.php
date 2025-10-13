<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Replaced legacy `name` with `username` for authentication
        'username',
        'email',
        'first_name',
        'last_name',
        'phone',
        'role',
        'password_hash',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Use password_hash column for authentication
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // Relationships
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Boot method to trigger notifications for new user registrations and profile updates
     */
    protected static function boot()
    {
        parent::boot();

        // Trigger notification when a new user is created
        static::created(function ($user) {
            try {
                $notificationService = app(\App\Services\NotificationService::class);
                
                // Notify admin about new customer registrations
                if ($user->role === 'customer') {
                    $notificationService->notifyNewCustomerRegistered($user);
                    // Also notify the customer about their account creation
                    $notificationService->notifyCustomerAccountCreated($user);
                }
            } catch (\Exception $e) {
                // Log notification errors but don't fail user creation
                Log::error('Notification error during user creation', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        });

        // Trigger notification when user profile is updated
        static::updated(function ($user) {
            // Only trigger notifications for profile-related fields
            $profileFields = ['email', 'first_name', 'last_name', 'phone'];
            $hasProfileChanges = false;
            
            foreach ($profileFields as $field) {
                if ($user->isDirty($field)) {
                    $hasProfileChanges = true;
                    break;
                }
            }
            
            if ($hasProfileChanges) {
                try {
                    $notificationService = app(\App\Services\NotificationService::class);
                    
                    // Get original data for comparison
                    $originalData = $user->getOriginal();
                    
                    // Notify admin based on user role
                    if ($user->role === 'customer') {
                        $notificationService->notifyCustomerProfileUpdated($user, $originalData);
                    } elseif ($user->role === 'admin') {
                        $notificationService->notifyAdminProfileUpdated($user, $originalData);
                    }
                    // Note: Employee profile updates are handled in Employee model observer
                } catch (\Exception $e) {
                    // Log notification errors but don't fail user update
                    Log::error('Notification error during user profile update', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        });
    }
}
