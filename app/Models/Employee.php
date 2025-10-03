<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Employee profile linked 1:1 to a User.
 */
class Employee extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_hired' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignments()
    {
        return $this->hasMany(BookingStaffAssignment::class);
    }

    /**
     * Get the count of completed jobs for this employee
     * This counts bookings where the employee was assigned and the booking is completed
     */
    public function getCompletedJobsCount()
    {
        return $this->assignments()
            ->whereHas('booking', function ($query) {
                $query->whereNotNull('completed_at');
            })
            ->count();
    }

    /**
     * Update the jobs_completed field with the actual count from the database
     * This should be called periodically or when jobs are completed
     */
    public function updateJobsCompletedCount()
    {
        $this->jobs_completed = $this->getCompletedJobsCount();
        $this->save();
        return $this->jobs_completed;
    }

    /**
     * Increment jobs completed count by 1
     * This can be called when a job is marked as completed
     */
    public function incrementJobsCompleted()
    {
        $this->increment('jobs_completed');
        return $this->jobs_completed;
    }

    /**
     * Boot method to trigger notifications for new employee creation and profile updates
     */
    protected static function boot()
    {
        parent::boot();

        // Trigger notification when a new employee is created
        static::created(function ($employee) {
            $notificationService = app(\App\Services\NotificationService::class);
            
            // Load the user relationship to get user details
            $employee->load('user');
            
            if ($employee->user) {
                // Notify admin about new employee creation
                $notificationService->notifyNewEmployeeCreated($employee->user, $employee);
                // Also notify the employee about their account creation
                $notificationService->notifyEmployeeAccountCreated($employee->user, $employee);
            }
        });

        // Trigger notification when employee profile is updated
        static::updated(function ($employee) {
            // Only trigger notifications for profile-related fields
            // Removed employment fields: department, employment_type, employment_status, work_schedule
            // Removed additional fields: position, hire_date, hourly_rate_cents, notes
            $profileFields = [
                'date_of_birth', 'gender', 'contact_number', 'email_address', 
                'home_address', 'emergency_contact_name', 'emergency_contact_number',
                'date_hired', 'jobs_completed', 'recent_job', 'attendance_summary', 
                'performance_rating'
            ];
            $hasProfileChanges = false;
            
            foreach ($profileFields as $field) {
                if ($employee->isDirty($field)) {
                    $hasProfileChanges = true;
                    break;
                }
            }
            
            if ($hasProfileChanges) {
                $notificationService = app(\App\Services\NotificationService::class);
                
                // Load the user relationship
                $employee->load('user');
                
                if ($employee->user) {
                    // Get original data for comparison
                    $originalData = $employee->getOriginal();
                    
                    // Notify admin about employee profile updates
                    $notificationService->notifyEmployeeProfileUpdated($employee->user, $employee, $originalData);
                }
            }
        });
    }
}


