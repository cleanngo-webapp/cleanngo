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
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('equipment_borrowed_by')->nullable()->after('status');
            $table->timestamp('equipment_borrowed_at')->nullable()->after('equipment_borrowed_by');
            $table->unsignedBigInteger('job_started_by')->nullable()->after('equipment_borrowed_at');
            $table->timestamp('job_started_at')->nullable()->after('job_started_by');
            $table->unsignedBigInteger('job_completed_by')->nullable()->after('job_started_at');
            $table->timestamp('job_completed_at')->nullable()->after('job_completed_by');
            
            // Add foreign key constraints
            $table->foreign('equipment_borrowed_by')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('job_started_by')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('job_completed_by')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['equipment_borrowed_by']);
            $table->dropForeign(['job_started_by']);
            $table->dropForeign(['job_completed_by']);
            $table->dropColumn([
                'equipment_borrowed_by',
                'equipment_borrowed_at',
                'job_started_by',
                'job_started_at',
                'job_completed_by',
                'job_completed_at'
            ]);
        });
    }
};
