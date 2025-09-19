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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Type of notification (booking_created, booking_confirmed, etc.)
            $table->string('title'); // Notification title
            $table->text('message'); // Notification message content
            $table->json('data')->nullable(); // Additional data (booking_id, employee_id, etc.)
            $table->enum('recipient_type', ['admin', 'customer', 'employee']); // Who should receive this
            $table->unsignedBigInteger('recipient_id')->nullable(); // Specific recipient ID (customer_id, employee_id, null for admin)
            $table->boolean('is_read')->default(false); // Whether notification has been read
            $table->timestamp('read_at')->nullable(); // When it was read
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['recipient_type', 'recipient_id']);
            $table->index(['is_read', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
