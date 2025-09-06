<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void {
        DB::statement("
            CREATE VIEW inventory_stock_levels AS
            SELECT
              ii.id          AS item_id,
              ii.sku,
              ii.name,
              COALESCE(SUM(
                CASE itt.type
                  WHEN 'purchase'       THEN itt.quantity
                  WHEN 'adjustment_in'  THEN itt.quantity
                  WHEN 'adjustment_out' THEN -itt.quantity
                  WHEN 'usage'          THEN -itt.quantity
                  ELSE 0
                END
              ), 0) AS qty_on_hand
            FROM inventory_items ii
            LEFT JOIN inventory_transactions itt ON itt.item_id = ii.id
            GROUP BY ii.id, ii.sku, ii.name
        ");
    }

    public function down(): void {
        DB::statement("DROP VIEW IF EXISTS inventory_stock_levels");
    }
};

