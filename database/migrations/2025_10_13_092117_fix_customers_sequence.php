<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix the customers table sequence to prevent primary key violations
        // This ensures the sequence is set to the correct value based on existing data
        DB::statement("SELECT setval('customers_id_seq', (SELECT MAX(id) FROM customers))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this fix
    }
};