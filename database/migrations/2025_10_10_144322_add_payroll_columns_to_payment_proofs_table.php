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
            // Add payroll-specific columns
            $table->string('payroll_code')->nullable()->after('status');
            $table->enum('payroll_status', ['unpaid', 'paid'])->default('unpaid')->after('payroll_code');
            $table->decimal('payroll_amount', 10, 2)->nullable()->after('payroll_status');
            $table->string('payroll_proof')->nullable()->after('payroll_amount');
            $table->enum('payroll_method', ['cash', 'gcash', 'bank_transfer'])->nullable()->after('payroll_proof');
            
            // Add index for payroll queries
            $table->index(['payroll_status', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_proofs', function (Blueprint $table) {
            // Drop payroll columns
            $table->dropColumn([
                'payroll_code',
                'payroll_status', 
                'payroll_amount',
                'payroll_proof',
                'payroll_method'
            ]);
            
            // Drop payroll index
            $table->dropIndex(['payroll_status', 'employee_id']);
        });
    }
};