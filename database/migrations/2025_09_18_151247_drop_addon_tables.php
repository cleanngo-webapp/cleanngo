<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop addon tables in reverse dependency order
        Schema::dropIfExists('booking_addons');        // Drop first (depends on service_addons)
        Schema::dropIfExists('service_addons');        // Drop second
    }

    public function down(): void
    {
        // Leave empty - we don't want to recreate these unused tables
    }
};