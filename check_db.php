<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Users count: " . \App\Models\User::count() . "\n";
echo "Customers count: " . \App\Models\Customer::count() . "\n";

$users = \App\Models\User::all(['id', 'first_name', 'last_name', 'role']);
echo "Users:\n";
foreach ($users as $user) {
    echo "  ID: {$user->id}, Name: {$user->first_name} {$user->last_name}, Role: {$user->role}\n";
}

$customers = \App\Models\Customer::all(['id', 'first_name', 'last_name']);
echo "Customers:\n";
foreach ($customers as $customer) {
    echo "  ID: {$customer->id}, Name: {$customer->first_name} {$customer->last_name}\n";
}
