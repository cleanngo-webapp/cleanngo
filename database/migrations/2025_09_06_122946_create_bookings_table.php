<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->nullable();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('address_id')->constrained('addresses')->restrictOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end')->nullable();
            $table->enum('status', ['pending','confirmed','in_progress','completed','cancelled','no_show'])
                  ->default('pending');
            $table->text('notes')->nullable();
            $table->integer('base_price_cents')->unsigned();
            $table->integer('addon_total_cents')->unsigned()->default(0);
            $table->integer('discount_cents')->unsigned()->default(0);
            $table->integer('tax_cents')->unsigned()->default(0);
            $table->integer('total_due_cents')->unsigned();
            $table->enum('payment_method', ['cash','gcash'])->nullable();
            $table->enum('payment_status', ['unpaid','partial','paid','refunded'])->default('unpaid');
            $table->integer('amount_paid_cents')->unsigned()->default(0);
            $table->timestamps();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancelled_reason')->nullable();

            $table->index(['status', 'scheduled_start']);
            $table->index('customer_id');
            $table->index('service_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('bookings');
    }
};

