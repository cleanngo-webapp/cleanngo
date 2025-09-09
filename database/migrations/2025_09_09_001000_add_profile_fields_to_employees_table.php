<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('employees')) {
            return;
        }
        Schema::table('employees', function (Blueprint $table) {
            // Personal info
            $table->string('position')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email_address')->nullable();
            $table->string('home_address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number')->nullable();

            // Employment details
            $table->string('department')->nullable();
            $table->enum('employment_type', ['full-time','part-time','contract'])->nullable();
            $table->date('date_hired')->nullable();
            $table->enum('employment_status', ['active','inactive','terminated'])->nullable()->default('active');
            $table->string('work_schedule')->nullable(); // e.g., "8:00 AM – 5:00 PM (Mon–Sat)"

            // Work history / records
            $table->integer('jobs_completed')->nullable();
            $table->string('recent_job')->nullable();
            $table->string('attendance_summary')->nullable(); // e.g., "98% (Last 3 months)"
            $table->string('performance_rating')->nullable(); // e.g., "4.5/5"
        });
    }

    public function down(): void {
        if (!Schema::hasTable('employees')) {
            return;
        }
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'position','date_of_birth','gender','contact_number','email_address','home_address',
                'emergency_contact_name','emergency_contact_number','department','employment_type',
                'date_hired','employment_status','work_schedule','jobs_completed','recent_job',
                'attendance_summary','performance_rating'
            ]);
        });
    }
};


