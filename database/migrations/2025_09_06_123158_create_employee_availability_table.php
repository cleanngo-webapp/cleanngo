<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employee_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0 = Sunday, 6 = Saturday
            $table->string('start_time'); // stored as HH:MM
            $table->string('end_time');
            $table->unique(['employee_id', 'day_of_week', 'start_time', 'end_time']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('employee_availability');
    }
};

