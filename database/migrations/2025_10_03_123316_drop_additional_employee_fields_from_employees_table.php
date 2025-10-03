<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration removes additional employee fields from the employees table:
     * - position (from profile fields)
     * - hire_date (from original table structure)
     * - hourly_rate_cents (from original table structure)
     * - notes (from original table structure)
     */
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            // Drop additional employee fields that are no longer needed
            $table->dropColumn([
                'position',
                'hire_date',
                'hourly_rate_cents',
                'notes'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     * 
     * This will restore the dropped columns if the migration needs to be rolled back.
     */
    public function down(): void
    {
        if (!Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            // Restore the dropped columns
            $table->string('position')->nullable();
            $table->date('hire_date')->nullable();
            $table->integer('hourly_rate_cents')->default(0);
            $table->text('notes')->nullable();
        });
    }
};