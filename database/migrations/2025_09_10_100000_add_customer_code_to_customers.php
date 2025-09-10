<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('customers')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'customer_code')) {
                $table->string('customer_code')->nullable()->unique();
            }
        });

        // Backfill codes for existing rows
        $existing = DB::table('customers')->select('id', 'customer_code')->get();
        foreach ($existing as $row) {
            if (!empty($row->customer_code)) {
                continue;
            }
            $code = self::generateCode();
            DB::table('customers')->where('id', $row->id)->update(['customer_code' => $code]);
        }
    }

    public function down(): void {
        if (!Schema::hasTable('customers')) {
            return;
        }
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'customer_code')) {
                $table->dropUnique('customers_customer_code_unique');
                $table->dropColumn('customer_code');
            }
        });
    }

    private static function generateCode(): string
    {
        $year = date('Y');
        for ($i = 0; $i < 1000; $i++) {
            $suffix = str_pad((string)random_int(0, 999), 3, '0', STR_PAD_LEFT);
            $code = 'C' . $year . $suffix;
            $exists = DB::table('customers')->where('customer_code', $code)->exists();
            if (!$exists) {
                return $code;
            }
        }
        return 'C' . $year . substr((string)microtime(true), -3);
    }
};

?>


