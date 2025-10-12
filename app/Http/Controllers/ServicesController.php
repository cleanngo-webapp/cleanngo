<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    /**
     * Display the public services page with pricing and details
     * This page shows all available services without requiring authentication
     */
    public function index()
    {
        // Get all active services from the database, excluding General, Mattress Deep Cleaning, and Enhanced Disinfection services
        $services = Service::where('is_active', true)
            ->whereNotIn('name', ['General', 'Mattress Deep Cleaning', 'Enhanced Disinfection'])
            ->orderBy('name')
            ->get()
            ->map(function ($service) {
                $hasTieredPricing = $this->hasTieredPricing($service);
                $pricingTiers = $this->getPricingTiers($service);
                
                // Get the starting price - lowest price for tiered services, base price for others
                $startingPrice = $hasTieredPricing && !empty($pricingTiers) 
                    ? $this->getLowestPrice($pricingTiers)
                    : $service->base_price_cents;
                
                return [
                    'id' => $service->id,
                    'name' => $service->name === 'Sofa Mattress Deep Cleaning' ? 'Sofa / Mattress Deep Cleaning' : $service->name,
                    'description' => $service->description,
                    'base_price_cents' => $service->base_price_cents,
                    'base_price_formatted' => '₱' . number_format($startingPrice / 100, 2),
                    'duration_minutes' => $service->duration_minutes,
                    'duration_formatted' => $this->formatDuration($service->duration_minutes),
                    'pricing_type' => $this->getPricingType($service),
                    'image' => $this->getServiceImage($service->name),
                    'has_tiered_pricing' => $hasTieredPricing,
                    'pricing_tiers' => $pricingTiers
                ];
            });

        return view('services', compact('services'));
    }

    /**
     * Format duration in minutes to human readable format (without "minutes")
     */
    private function formatDuration($minutes)
    {
        if ($minutes < 60) {
            return $minutes . ' min';
        } elseif ($minutes < 1440) { // Less than 24 hours
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;
            if ($remainingMinutes > 0) {
                return $hours . 'h ' . $remainingMinutes . 'min';
            }
            return $hours . 'h';
        } else {
            $days = floor($minutes / 1440);
            return $days . ' day' . ($days > 1 ? 's' : '');
        }
    }

    /**
     * Determine the pricing type based on service name and price structure
     */
    private function getPricingType($service)
    {
        $name = strtolower($service->name);
        
        // Services with per-unit pricing
        if (strpos($name, 'carpet') !== false) {
            return 'per square foot';
        } elseif (strpos($name, 'curtain') !== false) {
            return 'per yard';
        } elseif (strpos($name, 'house cleaning') !== false) {
            return 'per square meter';
        } elseif (strpos($name, 'car') !== false) {
            return 'varies on vehicle type';
        } elseif (strpos($name, 'sofa') !== false) {
            return 'varies on sofa type';
        } elseif (strpos($name, 'mattress') !== false) {
            return 'varies on mattress type';
        } elseif (strpos($name, 'post construction') !== false) {
            return 'per square meter';
        } elseif (strpos($name, 'glass') !== false) {
            return 'per square foot';
        } elseif (strpos($name, 'disinfection') !== false) {
            return 'per square meter';
        } else {
            return 'fixed price';
        }
    }

    /**
     * Get the appropriate image for each service
     */
    private function getServiceImage($serviceName)
    {
        $name = strtolower($serviceName);
        
        $imageMap = [
            'carpet' => 'cs-dashboard-carpet-cleaning.webp',
            'disinfection' => 'cs-dashboard-home-dis.webp',
            'sofa' => 'cs-services-sofa-mattress-cleaning.webp',
            'mattress' => 'cs-services-sofa-mattress-cleaning.webp',
            'car' => 'cs-dashboard-car-detailing.webp',
            'glass' => 'cs-services-glass-cleaning.webp',
            'post construction' => 'cs-services-post-cons-cleaning.webp',
            'house cleaning' => 'home-cleaning.webp',
            'curtain' => 'curtain-cleaning.webp'
        ];

        foreach ($imageMap as $keyword => $image) {
            if (strpos($name, $keyword) !== false) {
                return $image;
            }
        }

        return 'cs-dashboard-home-dis.webp'; // Default image
    }

    /**
     * Check if a service has tiered pricing based on different types
     */
    private function hasTieredPricing($service)
    {
        $name = strtolower($service->name);
        
        // Services with tiered pricing - use more specific checks to avoid false matches
        if (strpos($name, 'sofa mattress deep cleaning') !== false) {
            return true;
        } elseif (strpos($name, 'sofa') !== false) {
            return true;
        } elseif (strpos($name, 'mattress') !== false) {
            return true;
        } elseif (strpos($name, 'home service car') !== false || strpos($name, 'car interior') !== false) {
            // More specific check for car services to avoid matching "carpet"
            return true;
        }

        return false;
    }

    /**
     * Get pricing tiers for services with different types
     */
    private function getPricingTiers($service)
    {
        $name = strtolower($service->name);
        
        if (strpos($name, 'sofa mattress deep cleaning') !== false) {
            // Combined pricing for both sofa and mattress deep cleaning
            return [
                // Sofa Deep Cleaning section
                ['type' => 'Sofa Deep Cleaning', 'price' => ''],
                ['type' => 'Single chair', 'price' => '₱750'],
                ['type' => '2-seater', 'price' => '₱1,250'],
                ['type' => '3-seater', 'price' => '₱1,750'],
                ['type' => '4-seater', 'price' => '₱2,250'],
                ['type' => '5-seater', 'price' => '₱2,750'],
                ['type' => '6-seater', 'price' => '₱3,250'],
                ['type' => '7-seater', 'price' => '₱3,750'],
                ['type' => '8-seater', 'price' => '₱4,250'],
                // Mattress Deep Cleaning section
                ['type' => 'Mattress Deep Cleaning', 'price' => ''],
                ['type' => 'Single', 'price' => '₱950'],
                ['type' => 'Double', 'price' => '₱1,100'],
                ['type' => 'Queen', 'price' => '₱1,350'],
                ['type' => 'King', 'price' => '₱1,450']
            ];
        } elseif (strpos($name, 'sofa') !== false) {
            return [
                ['type' => 'Single chair', 'price' => '₱750'],
                ['type' => '2-seater', 'price' => '₱1,250'],
                ['type' => '3-seater', 'price' => '₱1,750'],
                ['type' => '4-seater', 'price' => '₱2,250'],
                ['type' => '5-seater', 'price' => '₱2,750'],
                ['type' => '6-seater', 'price' => '₱3,250'],
                ['type' => '7-seater', 'price' => '₱3,750'],
                ['type' => '8-seater', 'price' => '₱4,250']
            ];
        } elseif (strpos($name, 'mattress') !== false) {
            return [
                ['type' => 'Single', 'price' => '₱950'],
                ['type' => 'Double', 'price' => '₱1,100'],
                ['type' => 'Queen', 'price' => '₱1,350'],
                ['type' => 'King', 'price' => '₱1,450']
            ];
        } elseif (strpos($name, 'home service car') !== false || strpos($name, 'car interior') !== false) {
            // More specific check for car services to avoid matching "carpet"
            return [
                ['type' => 'Sedan', 'price' => '₱2,900'],
                ['type' => 'Hatchback', 'price' => '₱3,000'],
                ['type' => 'SUV', 'price' => '₱3,900'],
                ['type' => 'Van', 'price' => '₱6,900']
            ];
        }

        return [];
    }

    /**
     * Get the lowest price from pricing tiers
     */
    private function getLowestPrice($pricingTiers)
    {
        $lowestPrice = PHP_INT_MAX;
        
        foreach ($pricingTiers as $tier) {
            // Skip section headers (empty prices)
            if (empty($tier['price'])) {
                continue;
            }
            
            // Extract numeric value from price string (e.g., "₱750" -> 750)
            $price = (int) str_replace(['₱', ','], '', $tier['price']);
            if ($price > 0 && $price < $lowestPrice) {
                $lowestPrice = $price;
            }
        }
        
        return $lowestPrice * 100; // Convert to cents
    }
}
