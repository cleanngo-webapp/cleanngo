<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmailVerification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'otp_code',
        'expires_at',
        'is_used',
        'verification_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    /**
     * Generate a random 6-digit OTP code
     *
     * @return string
     */
    public static function generateOTP(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new OTP verification record
     *
     * @param string $email
     * @param string $verificationType
     * @param int $expiryMinutes
     * @return self
     */
    public static function createOTP(string $email, string $verificationType = 'registration', int $expiryMinutes = 15): self
    {
        // Invalidate any existing unused OTPs for this email
        self::where('email', $email)
            ->where('is_used', false)
            ->where('verification_type', $verificationType)
            ->update(['is_used' => true]);

        // Create new OTP
        return self::create([
            'email' => $email,
            'otp_code' => self::generateOTP(),
            'expires_at' => now()->addMinutes($expiryMinutes),
            'verification_type' => $verificationType,
        ]);
    }

    /**
     * Verify an OTP code
     *
     * @param string $email
     * @param string $otpCode
     * @param string $verificationType
     * @return bool
     */
    public static function verifyOTP(string $email, string $otpCode, string $verificationType = 'registration'): bool
    {
        $verification = self::where('email', $email)
            ->where('otp_code', $otpCode)
            ->where('is_used', false)
            ->where('verification_type', $verificationType)
            ->where('expires_at', '>', now())
            ->first();

        if ($verification) {
            // Mark as used
            $verification->update(['is_used' => true]);
            return true;
        }

        return false;
    }

    /**
     * Validate an OTP code without marking it as used
     * This allows us to check if the OTP is valid before proceeding with registration
     *
     * @param string $email
     * @param string $otpCode
     * @param string $verificationType
     * @return bool
     */
    public static function validateOTP(string $email, string $otpCode, string $verificationType = 'registration'): bool
    {
        $verification = self::where('email', $email)
            ->where('otp_code', $otpCode)
            ->where('is_used', false)
            ->where('verification_type', $verificationType)
            ->where('expires_at', '>', now())
            ->first();

        return $verification !== null;
    }

    /**
     * Mark an OTP as used after successful verification
     *
     * @param string $email
     * @param string $otpCode
     * @param string $verificationType
     * @return bool
     */
    public static function markOTPAsUsed(string $email, string $otpCode, string $verificationType = 'registration'): bool
    {
        $verification = self::where('email', $email)
            ->where('otp_code', $otpCode)
            ->where('is_used', false)
            ->where('verification_type', $verificationType)
            ->where('expires_at', '>', now())
            ->first();

        if ($verification) {
            $verification->update(['is_used' => true]);
            return true;
        }

        return false;
    }

    /**
     * Check if OTP is expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Clean up expired OTPs (can be called via scheduler)
     *
     * @return int Number of deleted records
     */
    public static function cleanupExpired(): int
    {
        return self::where('expires_at', '<', now())->delete();
    }
}
