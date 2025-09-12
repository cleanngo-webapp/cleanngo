<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GalleryImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This creates sample gallery images for testing purposes
     */
    public function run(): void
    {
        // Sample gallery images for each service type
        $sampleImages = [
            [
                'service_type' => 'carpet',
                'image_path' => 'gallery/sample_carpet_1.jpg',
                'original_name' => 'carpet_cleaning_before_after.jpg',
                'alt_text' => 'Carpet deep cleaning before and after comparison',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'service_type' => 'carpet',
                'image_path' => 'gallery/sample_carpet_2.jpg',
                'original_name' => 'carpet_stain_removal.jpg',
                'alt_text' => 'Professional carpet stain removal results',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'service_type' => 'disinfection',
                'image_path' => 'gallery/sample_disinfection_1.jpg',
                'original_name' => 'office_disinfection.jpg',
                'alt_text' => 'Complete office disinfection service',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'service_type' => 'glass',
                'image_path' => 'gallery/sample_glass_1.jpg',
                'original_name' => 'window_cleaning_results.jpg',
                'alt_text' => 'Crystal clear window cleaning results',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'service_type' => 'carInterior',
                'image_path' => 'gallery/sample_car_1.jpg',
                'original_name' => 'car_interior_detailing.jpg',
                'alt_text' => 'Professional car interior detailing service',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'service_type' => 'postConstruction',
                'image_path' => 'gallery/sample_construction_1.jpg',
                'original_name' => 'post_construction_cleanup.jpg',
                'alt_text' => 'Post construction cleaning and debris removal',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'service_type' => 'sofa',
                'image_path' => 'gallery/sample_sofa_1.jpg',
                'original_name' => 'sofa_deep_cleaning.jpg',
                'alt_text' => 'Sofa deep cleaning and stain removal',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'service_type' => 'sofa',
                'image_path' => 'gallery/sample_sofa_2.jpg',
                'original_name' => 'mattress_cleaning.jpg',
                'alt_text' => 'Professional mattress deep cleaning service',
                'sort_order' => 2,
                'is_active' => true
            ]
        ];

        // Create sample gallery images
        foreach ($sampleImages as $imageData) {
            GalleryImage::create($imageData);
        }

        $this->command->info('Sample gallery images created successfully!');
        $this->command->info('Note: These are placeholder entries. You can upload real images through the admin panel.');
    }
}
