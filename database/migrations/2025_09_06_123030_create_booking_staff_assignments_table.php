<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_staff_assignments', function (Blueprint $table) {
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->enum('role', ['team_lead','cleaner','assistant']);
            $table->dateTime('assigned_at')->useCurrent();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->primary(['booking_id', 'employee_id']);

            $table->index('employee_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('booking_staff_assignments');
    }
};
