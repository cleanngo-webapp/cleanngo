<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique(); // Item code like T101, M201, etc.
            $table->string('name');
            $table->enum('category', ['Tools', 'Machine', 'Cleaning Agent', 'Consumables']);
            $table->decimal('quantity', 10, 2)->default(0); // Current quantity in stock
            $table->decimal('unit_price', 10, 2)->default(0); // Unit price in Philippine Peso
            $table->decimal('reorder_level', 10, 2)->default(0); // Reorder threshold
            $table->enum('status', ['In Stock', 'Low Stock', 'Out of Stock'])->default('In Stock');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps(); // For created_at and updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventory_items');
    }
};
