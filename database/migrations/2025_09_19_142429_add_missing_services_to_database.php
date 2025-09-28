<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add the missing services that are referenced in the booking controller
     * but don't exist in the database, causing them to fall back to "General"
     */
    public function up(): void
    {
        // Updated services with new pricing structure and added new services
        $services = [
            [
                'name' => 'Sofa Mattress Deep Cleaning',
                'description' => 'Professional deep cleaning for all types of sofas and mattresses (removed L-shape and cross sectional)',
                'base_price_cents' => 75000, // ₱750.00 - Single chair (1 seater) base price
                'duration_minutes' => 60, // 1 hour base duration
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mattress Deep Cleaning',
                'description' => 'Thorough deep cleaning for mattresses of all sizes',
                'base_price_cents' => 95000, // ₱950.00 - Single mattress base price
                'duration_minutes' => 60, // 1 hour base duration
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carpet Deep Cleaning',
                'description' => 'Professional carpet cleaning to remove dirt, stains, and allergens',
                'base_price_cents' => 3000, // ₱30.00 per square foot
                'duration_minutes' => 15, // 15 minutes per square foot
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Home Service Car Interior Detailing',
                'description' => 'Complete interior detailing service for cars, SUVs, vans, and coasters',
                'base_price_cents' => 290000, // ₱2,900.00 - Sedan base price
                'duration_minutes' => 180, // 3 hours base duration
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Post Construction Cleaning',
                'description' => 'Comprehensive cleaning service for post-construction areas',
                'base_price_cents' => 10167, // ₱101.67 per sqm
                'duration_minutes' => 30, // 30 minutes per sqm
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Home/Office Disinfection',
                'description' => 'Advanced disinfection service for homes and offices',
                'base_price_cents' => 9000, // ₱90.00 per sqm
                'duration_minutes' => 20, // 20 minutes per sqm
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Glass Cleaning',
                'description' => 'Professional glass cleaning for windows, mirrors, and glass surfaces',
                'base_price_cents' => 5000, // ₱50.00 per square foot
                'duration_minutes' => 10, // 10 minutes per square foot
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Curtain Cleaning',
                'description' => 'Professional curtain cleaning service',
                'base_price_cents' => 5000, // ₱50.00 per yard
                'duration_minutes' => 15, // 15 minutes per yard
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'House Cleaning',
                'description' => 'Comprehensive house cleaning service',
                'base_price_cents' => 9100, // ₱91.00 per sqm
                'duration_minutes' => 25, // 25 minutes per sqm
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'General',
                'description' => 'General cleaning service for miscellaneous items',
                'base_price_cents' => 100000, // ₱1,000.00
                'duration_minutes' => 60, // 1 hour
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert services only if they don't already exist
        foreach ($services as $service) {
            DB::table('services')->updateOrInsert(
                ['name' => $service['name']], // Check if service with this name exists
                $service // Insert/update with this data
            );
        }
    }

    /**
     * Reverse the migrations.
     * Remove the services that were added
     */
    public function down(): void
    {
        // Remove the services that were added
        $serviceNames = [
            'Sofa Mattress Deep Cleaning',
            'Mattress Deep Cleaning', 
            'Carpet Deep Cleaning',
            'Home Service Car Interior Detailing',
            'Post Construction Cleaning',
            'Home/Office Disinfection',
            'Glass Cleaning',
            'Curtain Cleaning',
            'House Cleaning',
            'General'
        ];

        DB::table('services')->whereIn('name', $serviceNames)->delete();
    }
};