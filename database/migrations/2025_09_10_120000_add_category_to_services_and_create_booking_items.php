<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('services') && !Schema::hasColumn('services','category')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('category')->default('generic');
            });
        }

        if (!Schema::hasTable('booking_items')) {
            Schema::create('booking_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
                $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
                $table->string('item_type')->nullable(); // sofa_1_seater, mattress_king, sedan, etc
                $table->integer('quantity')->default(1);
                $table->decimal('area_sqm', 10, 2)->nullable();
                $table->integer('unit_price_cents')->default(0);
                $table->integer('line_total_cents')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('booking_items')) {
            Schema::dropIfExists('booking_items');
        }
        if (Schema::hasTable('services') && Schema::hasColumn('services','category')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};

?>


