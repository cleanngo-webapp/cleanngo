<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration removes employment-related fields from the employees table
     * while keeping the date_hired field as requested by the user.
     */
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            // Drop employment fields that are no longer needed
            // Keeping date_hired as requested
            $table->dropColumn([
                'department',
                'employment_type', 
                'employment_status',
                'work_schedule'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     * 
     * This will restore the dropped columns if the migration needs to be rolled back.
     */
    public function down(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            // Restore the dropped columns
            $table->string('department')->nullable();
            $table->enum('employment_type', ['full-time','part-time','contract'])->nullable();
            $table->enum('employment_status', ['active','inactive','terminated'])->nullable()->default('active');
            $table->string('work_schedule')->nullable();
        });
    }
};