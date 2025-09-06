<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->tinyInteger('rating'); // 1–5
            $table->text('comment')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('booking_ratings');
    }
};

