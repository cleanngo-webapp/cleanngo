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
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('service_type'); // carpet, disinfection, glass, carInterior, postConstruction, sofa
            $table->string('image_path'); // Path to the uploaded image
            $table->string('original_name'); // Original filename
            $table->string('alt_text')->nullable(); // Alt text for accessibility
            $table->integer('sort_order')->default(0); // For ordering images
            $table->boolean('is_active')->default(true); // To show/hide images
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
