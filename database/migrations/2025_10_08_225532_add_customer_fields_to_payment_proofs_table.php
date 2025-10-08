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
        Schema::table('payment_proofs', function (Blueprint $table) {
            // Add customer_id field to track customer uploads
            $table->unsignedBigInteger('customer_id')->nullable()->after('employee_id');
            
            // Add uploaded_by field to track who uploaded the proof
            $table->enum('uploaded_by', ['employee', 'customer'])->default('employee')->after('customer_id');
            
            // Make employee_id nullable since customers can also upload
            $table->unsignedBigInteger('employee_id')->nullable()->change();
            
            // Add foreign key constraint for customer_id
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            
            // Update index to include customer_id
            $table->index(['booking_id', 'status', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_proofs', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['customer_id']);
            
            // Drop index
            $table->dropIndex(['booking_id', 'status', 'customer_id']);
            
            // Remove added columns
            $table->dropColumn(['customer_id', 'uploaded_by']);
            
            // Revert employee_id to not nullable
            $table->unsignedBigInteger('employee_id')->nullable(false)->change();
        });
    }
};
