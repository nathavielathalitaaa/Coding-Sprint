<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use App\Models\User;

$user = User::where('email', 'admin@admin.com')->first();
if (!$user) {
    echo "User admin@admin.com not found\n";
    exit(1);
}

$roles = Role::pluck('name')->toArray();
if (empty($roles)) {
    echo "No roles found in DB\n";
    exit(1);
}

$user->syncRoles($roles);

echo "OK: assigned roles: " . implode(', ', $roles) . " to admin@admin.com\n";
