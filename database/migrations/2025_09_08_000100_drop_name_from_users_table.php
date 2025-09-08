<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Remove legacy NOT NULL name column from users (now using username).
     */
    public function up(): void {
        if (!Schema::hasTable('users')) {
            return;
        }
        // Only drop if it exists; some local devs might already not have it
        if (Schema::hasColumn('users', 'name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }

    /**
     * Recreate the name column on rollback as nullable to avoid future conflicts.
     */
    public function down(): void {
        if (!Schema::hasTable('users')) {
            return;
        }
        if (!Schema::hasColumn('users', 'name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('name')->nullable();
            });
        }
    }
};


