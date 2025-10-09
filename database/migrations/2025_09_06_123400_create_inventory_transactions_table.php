<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the inventory_transactions table to track equipment borrowing and returning
     * for employees during their job assignments.
     */
    public function up()
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys for relationships
            $table->foreignId('inventory_item_id')->constrained()->onDelete('restrict');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            
            // Transaction details
            $table->enum('transaction_type', ['borrow', 'return']);
            $table->decimal('quantity', 8, 2); // Amount borrowed or returned
            $table->timestamp('transaction_at'); // When the transaction occurred
            
            // Additional metadata
            $table->text('notes')->nullable(); // Optional notes about the transaction
            $table->timestamp('expected_return_date')->nullable(); // Expected return date for borrowed items
            
            // Tracking fields
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['employee_id', 'transaction_type']);
            $table->index(['booking_id', 'transaction_type']);
            $table->index(['inventory_item_id', 'transaction_type']);
            $table->index('transaction_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
