<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->enum('type', ['purchase','adjustment_in','adjustment_out','usage']);
            $table->decimal('quantity', 10, 2);
            $table->integer('unit_cost_cents')->nullable();
            $table->text('reason')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index(['item_id', 'created_at']);
            $table->index('booking_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventory_transactions');
    }
};
