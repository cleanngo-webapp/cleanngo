<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Update Postgres check constraint for users.role to include 'employee'.
     * - On PostgreSQL, Laravel's enum() becomes a CHECK constraint. Changing
     *   allowed values requires dropping and recreating that constraint.
     * - On other drivers (SQLite/MySQL), this is a no-op because the base
     *   migration already includes 'employee'.
     */
    public function up(): void {
        if (!Schema::hasTable('users')) {
            return; // Safety guard: nothing to do if table doesn't exist
        }

        // Only run on PostgreSQL to avoid cross-DB issues
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Drop old constraint if it exists (e.g., users_role_check allowing 'staff'/'cleaner')
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");

        // Recreate the constraint with the updated allowed roles
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin','employee','customer'))");
    }

    /**
     * Revert to the previous constraint set, if needed.
     */
    public function down(): void {
        if (!Schema::hasTable('users')) {
            return;
        }
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
        // Restore the earlier allowed roles (admin, staff, cleaner, customer)
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin','staff','cleaner','customer'))");
    }
};


