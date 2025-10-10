<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSettings extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'gcash_name',
        'gcash_number',
        'qr_code_path',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the active payment settings for a specific user
     * Since we only expect one active payment setting per user at a time
     */
    public static function getActive($userId = null)
    {
        $query = self::where('is_active', true);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        return $query->first();
    }

    /**
     * Get the active payment settings for admin users only
     * This method is used for backward compatibility and admin-specific operations
     */
    public static function getActiveForAdmin()
    {
        return self::where('is_active', true)
            ->whereHas('user', function($query) {
                $query->where('role', 'admin');
            })
            ->first();
    }

    /**
     * Get the active payment settings for employee users only
     */
    public static function getActiveForEmployee()
    {
        return self::where('is_active', true)
            ->whereHas('user', function($query) {
                $query->where('role', 'employee');
            })
            ->first();
    }

    /**
     * Relationship to User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
