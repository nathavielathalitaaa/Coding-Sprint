<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap the kernel so facades and container work
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\EmployeeProfile;

// Ensure role exists
Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

// Create or update user
$user = User::updateOrCreate(
    ['email' => 'admin@admin.com'],
    [
        'name' => 'Super Admin',
        'password' => bcrypt('admin123'),
        'user_id' => 'ADM-0001',
        'status' => 'aktif',
        'role_name' => 'super-admin',
        'must_change_password' => false,
    ]
);

// Assign role
$user->syncRoles(['super-admin']);

// Create profile
EmployeeProfile::firstOrCreate(
    ['user_id' => $user->id],
    ['jabatan' => 'Super Administrator', 'status_pernikahan' => 'belum_menikah']
);

echo "OK: Super Admin created or updated (admin@admin.com)\n";
