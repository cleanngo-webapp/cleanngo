<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->integer('hours_worked')->default(0);
            $table->integer('base_pay_cents');
            $table->integer('overtime_pay_cents')->default(0);
            $table->integer('bonus_cents')->default(0);
            $table->integer('deductions_cents')->default(0);
            $table->integer('net_pay_cents');
            $table->timestamps();

            $table->unique(['payroll_period_id', 'employee_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('payroll_items');
    }
};
