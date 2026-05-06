<?php
$supRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'supervisor']);

// Direktur
$dir = \App\Models\User::firstOrCreate(
    ['email' => 'direktur@company.com'],
    [
        'user_id' => 'DIR-001',
        'name' => 'Direktur',
        'password' => bcrypt('password'),
        'role_name' => 'supervisor',
        'status' => 'aktif'
    ]
);
$dir->assignRole($supRole);
\App\Models\EmployeeProfile::updateOrCreate(
    ['user_id' => $dir->id],
    ['jabatan' => 'Direktur']
);

// HOD
$hod = \App\Models\User::firstOrCreate(
    ['email' => 'hod@company.com'],
    [
        'user_id' => 'HOD-001',
        'name' => 'HOD',
        'password' => bcrypt('password'),
        'role_name' => 'supervisor',
        'status' => 'aktif'
    ]
);
$hod->assignRole($supRole);
\App\Models\EmployeeProfile::updateOrCreate(
    ['user_id' => $hod->id],
    ['jabatan' => 'HOD']
);

// Owner Rep
$owner = \App\Models\User::firstOrCreate(
    ['email' => 'ownerrep@company.com'],
    [
        'user_id' => 'OWN-001',
        'name' => 'Owner Rep',
        'password' => bcrypt('password'),
        'role_name' => 'supervisor',
        'status' => 'aktif'
    ]
);
$owner->assignRole($supRole);
\App\Models\EmployeeProfile::updateOrCreate(
    ['user_id' => $owner->id],
    ['jabatan' => 'Owner Rep']
);

echo "Users Created!\n";
