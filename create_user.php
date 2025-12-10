<?php
// create_user.php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// User data
$userData = [
    'name' => 'aze',
    'email' => 'aze@gmail.com',
    'mobile' => '056561',
    'password' => bcrypt('123456'),
    'role_id' => 2,
    'status' => 'active'
];

try {
    $user = User::create($userData);
    echo "User created successfully!\n";
    echo "ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Password: password123\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}