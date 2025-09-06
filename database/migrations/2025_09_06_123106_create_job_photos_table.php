<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('job_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('file_path');
            $table->text('caption')->nullable();
            $table->dateTime('uploaded_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('job_photos');
    }
};
