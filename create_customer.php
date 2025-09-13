<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get user ID 24 (the one that's logged in)
$user = \App\Models\User::find(24);

if (!$user) {
    echo "User ID 24 not found!\n";
    exit;
}

echo "Found user: {$user->first_name} {$user->last_name} (ID: {$user->id})\n";

// Check if customer already exists
$existingCustomer = \App\Models\Customer::where('user_id', 24)->first();

if ($existingCustomer) {
    echo "Customer profile already exists for this user (ID: {$existingCustomer->id})\n";
} else {
    // Create customer profile
    $customer = \App\Models\Customer::create([
        'user_id' => 24,
        'default_address_id' => null,
        'notes' => 'Auto-created customer profile'
    ]);
    
    echo "Created customer profile with ID: {$customer->id}\n";
}

echo "Done!\n";
