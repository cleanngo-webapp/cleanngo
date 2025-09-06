<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('job_time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->dateTime('clock_in');
            $table->dateTime('clock_out')->nullable();
            // minutes_worked is a computed column in SQLite; emulate with accessor in Laravel
            $table->unique(['booking_id', 'employee_id', 'clock_in']);

            $table->index(['employee_id', 'clock_in']);
            $table->index('booking_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('job_time_logs');
    }
};

