<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('employees')) {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'employee_code')) {
                $table->string('employee_code')->nullable()->unique();
            }
        });

        // Backfill codes for existing rows
        $already = DB::table('employees')->select('id','employee_code')->get();
        foreach ($already as $row) {
            if (!empty($row->employee_code)) {
                continue;
            }
            $code = self::generateCode();
            DB::table('employees')->where('id', $row->id)->update(['employee_code' => $code]);
        }
    }

    public function down(): void {
        if (!Schema::hasTable('employees')) {
            return;
        }
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'employee_code')) {
                $table->dropUnique('employees_employee_code_unique');
                $table->dropColumn('employee_code');
            }
        });
    }

    private static function generateCode(): string
    {
        $year = date('Y');
        for ($i = 0; $i < 1000; $i++) {
            $suffix = str_pad((string)random_int(0, 999), 3, '0', STR_PAD_LEFT);
            $code = 'E' . $year . $suffix;
            $exists = DB::table('employees')->where('employee_code', $code)->exists();
            if (!$exists) {
                return $code;
            }
        }
        // Fallback: append micro second digits if collisions (unlikely)
        return 'E' . $year . substr((string)microtime(true), -3);
    }
};


