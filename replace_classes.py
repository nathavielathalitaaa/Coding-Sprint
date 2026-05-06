import os
import re

files_to_update = [
    'resources/views/HR/employee.blade.php',
    'resources/views/HR/absensi/index.blade.php',
    'resources/views/HR/LeavesManage/leave-hr.blade.php',
    'resources/views/HR/LeavesManage/leave-employee.blade.php',
    'resources/views/surat/index.blade.php',
    'resources/views/surat/show.blade.php',
    'resources/views/surat/create.blade.php',
    'resources/views/pages/account-profile.blade.php',
    'resources/views/HR/approval-flow/index.blade.php',
    'resources/views/HR/penggajian/index.blade.php'
]

replacements = [
    (r'\bds-section\b', 'hivi-card'),
    (r'\bcard\b', 'hivi-card'),
    (r'\bds-btn btn-green\b', 'hivi-btn-primary'),
    (r'\bbtn-green\b', 'hivi-btn-primary'),
    (r'\bds-table\b', 'hivi-table'),
    (r'\bds-badge b-green\b', 'hivi-badge hivi-badge-green'),
    (r'\bds-badge b-red\b', 'hivi-badge hivi-badge-red'),
    (r'\bds-badge b-amber\b', 'hivi-badge hivi-badge-amber'),
    (r'\bds-badge b-blue\b', 'hivi-badge hivi-badge-blue'),
    (r'\bds-badge b-gray\b', 'hivi-badge hivi-badge-gray'),
    (r'\bform-input\b', 'hivi-input'),
    (r'\bds-section-title\b', 'hivi-section-title'),
    (r'\bds-btn\b', 'hivi-btn-secondary')
]

base_dir = r"c:\xampp\htdocs\Laravel-12-HR-System-Management"

for f in files_to_update:
    path = os.path.join(base_dir, f.replace('/', '\\'))
    if os.path.exists(path):
        with open(path, 'r', encoding='utf-8') as file:
            content = file.read()
        
        for old, new in replacements:
            content = re.sub(old, new, content)
            
        with open(path, 'w', encoding='utf-8') as file:
            file.write(content)
        print(f"Updated {f}")
    else:
        print(f"File not found: {f}")
