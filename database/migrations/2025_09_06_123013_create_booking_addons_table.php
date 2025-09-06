<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_addons', function (Blueprint $table) {
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('addon_id')->constrained('service_addons')->restrictOnDelete();
            $table->integer('quantity')->unsigned()->default(1);
            $table->integer('unit_price_cents')->unsigned();
            $table->primary(['booking_id', 'addon_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('booking_addons');
    }
};
