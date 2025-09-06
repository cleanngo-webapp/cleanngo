<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->date('hire_date')->nullable();
            $table->integer('hourly_rate_cents')->default(0);
            $table->boolean('is_cleaner')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('employees');
    }
};
