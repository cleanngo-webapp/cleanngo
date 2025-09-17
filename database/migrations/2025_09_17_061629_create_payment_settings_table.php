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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gcash_name')->nullable(); // GCash account name
            $table->string('gcash_number')->nullable(); // GCash phone number
            $table->string('qr_code_path')->nullable(); // Path to uploaded QR code image
            $table->boolean('is_active')->default(true); // Whether these settings are active
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
