<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('users') || !Schema::hasTable('customers')) {
            return;
        }

        // Find users with role=customer that have no customers row
        $userIds = DB::table('users')
            ->where('role', 'customer')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('customers')
                  ->whereColumn('customers.user_id', 'users.id');
            })
            ->pluck('id');

        foreach ($userIds as $uid) {
            $code = $this->generateCode();
            DB::table('customers')->insert([
                'user_id' => $uid,
                'customer_code' => $code,
            ]);
        }

        // Ensure codes for existing customer rows
        $rows = DB::table('customers')->select('id','customer_code')->get();
        foreach ($rows as $row) {
            if (!empty($row->customer_code)) {
                continue;
            }
            DB::table('customers')->where('id', $row->id)->update([
                'customer_code' => $this->generateCode(),
            ]);
        }
    }

    public function down(): void {
        // no-op: don't delete data on rollback
    }

    private function generateCode(): string
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


