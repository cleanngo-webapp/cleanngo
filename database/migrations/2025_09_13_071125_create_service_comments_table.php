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
        Schema::create('service_comments', function (Blueprint $table) {
            $table->id();
            $table->string('service_type'); // carpet, disinfection, glass, carInterior, postConstruction, sofa
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete(); // Link to customer who made the comment
            $table->text('comment'); // The actual comment content
            $table->integer('rating')->nullable(); // Optional rating from 1-5 stars
            $table->boolean('is_approved')->default(true); // Admin can moderate comments
            $table->boolean('is_edited')->default(false); // Track if comment was edited
            $table->timestamps();
            
            // Index for better performance when querying by service type
            $table->index('service_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_comments');
    }
};
