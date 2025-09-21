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
     * Boot method to trigger notifications for new employee creation
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
                $notificationService->notifyNewEmployeeCreated($employee->user, $employee);
            }
        });
    }
}


