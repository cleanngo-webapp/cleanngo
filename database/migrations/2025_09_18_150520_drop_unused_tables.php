<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop tables in reverse dependency order
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('employee_time_off');
        Schema::dropIfExists('employee_availability');
        Schema::dropIfExists('booking_ratings');
        Schema::dropIfExists('job_photos');
        Schema::dropIfExists('job_time_logs');
        Schema::dropIfExists('messages');
    }

    public function down(): void
    {
        // Leave empty - we don't want to recreate these unused tables
    }
};