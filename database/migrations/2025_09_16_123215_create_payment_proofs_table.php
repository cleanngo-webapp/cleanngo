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
        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('image_path'); // Path to uploaded payment proof image
            $table->decimal('amount', 10, 2); // Payment amount
            $table->enum('payment_method', ['cash', 'gcash']); // Payment method used
            $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');
            $table->text('admin_notes')->nullable(); // Admin notes when declining
            $table->unsignedBigInteger('reviewed_by')->nullable(); // Admin who reviewed
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            // Index for faster queries
            $table->index(['booking_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_proofs');
    }
};