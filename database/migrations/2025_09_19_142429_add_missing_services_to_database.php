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
        // Insert the missing services that are mapped in the booking controller
        $services = [
            [
                'name' => 'Sofa Deep Cleaning',
                'description' => 'Professional deep cleaning for all types of sofas and couches',
                'base_price_cents' => 400000, // ₱4,000.00
                'duration_minutes' => 120, // 2 hours
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mattress Deep Cleaning',
                'description' => 'Thorough deep cleaning for mattresses of all sizes',
                'base_price_cents' => 400000, // ₱4,000.00
                'duration_minutes' => 90, // 1.5 hours
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Home Service Car Interior Detailing',
                'description' => 'Complete interior detailing service for cars, SUVs, vans, and coasters',
                'base_price_cents' => 400000, // ₱4,000.00
                'duration_minutes' => 180, // 3 hours
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Post Construction Cleaning',
                'description' => 'Comprehensive cleaning service for post-construction areas',
                'base_price_cents' => 50000, // ₱500.00 per sqm
                'duration_minutes' => 60, // 1 hour per sqm
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carpet Deep Cleaning',
                'description' => 'Professional carpet cleaning to remove dirt, stains, and allergens',
                'base_price_cents' => 50000, // ₱500.00 per sqm
                'duration_minutes' => 45, // 45 minutes per sqm
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enhanced Disinfection',
                'description' => 'Advanced disinfection service for homes and offices',
                'base_price_cents' => 50000, // ₱500.00 per sqm
                'duration_minutes' => 30, // 30 minutes per sqm
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Glass Cleaning',
                'description' => 'Professional glass cleaning for windows, mirrors, and glass surfaces',
                'base_price_cents' => 50000, // ₱500.00 per sqm
                'duration_minutes' => 30, // 30 minutes per sqm
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
            'Sofa Deep Cleaning',
            'Mattress Deep Cleaning', 
            'Home Service Car Interior Detailing',
            'Post Construction Cleaning',
            'Carpet Deep Cleaning',
            'Enhanced Disinfection',
            'Glass Cleaning',
            'General'
        ];

        DB::table('services')->whereIn('name', $serviceNames)->delete();
    }
};