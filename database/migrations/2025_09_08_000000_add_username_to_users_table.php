<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add a unique username column to users for username-based auth.
     * Note: We are not dropping the legacy `name` column here to avoid
     * SQLite/DBAL constraints during development. Application code will
     * exclusively use `username` going forward.
     */
    public function up(): void {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique();
            }
        });
    }

    /**
     * Drop the username column if rolling back.
     */
    public function down(): void {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
        });
    }
};


