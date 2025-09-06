<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('service_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price_cents')->unsigned();
            $table->integer('duration_minutes')->default(0)->unsigned();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['service_id', 'name']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('service_addons');
    }
};
