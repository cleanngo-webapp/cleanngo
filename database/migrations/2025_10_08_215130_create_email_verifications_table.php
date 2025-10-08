<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('email_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index(); // Email address to verify
            $table->string('otp_code', 6); // 6-digit OTP code
            $table->timestamp('expires_at'); // OTP expiration time
            $table->boolean('is_used')->default(false); // Whether OTP has been used
            $table->string('verification_type')->default('registration'); // Type of verification
            $table->timestamps();
            
            // Index for efficient lookups
            $table->index(['email', 'otp_code', 'is_used']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_verifications');
    }
};
