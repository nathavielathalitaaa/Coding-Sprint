#!/usr/bin/env python3
import re
import os

# Process sidebar.blade.php
file_path = r'resources\views\sidebar\sidebar.blade.php'
if os.path.exists(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Remove all data-key attributes
    content = re.sub(r' data-key="[^"]*"', '', content)
    
    # Replace Pages with Halaman
    content = content.replace('>Pages<', '> Halaman<')
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print('✓ sidebar.blade.php updated successfully')
else:
    print(f'File not found: {file_path}')

# Now process other view files that might have data-key
view_files = [
    r'resources\views\layouts\master.blade.php',
    r'resources\views\dashboard\home.blade.php',
    r'resources\views\pages\account-profile.blade.php',
]

for file_path in view_files:
    if os.path.exists(file_path):
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original = content
            # Remove all data-key attributes
            content = re.sub(r' data-key="[^"]*"', '', content)
            # Replace Pages with Halaman
            content = content.replace('>Pages<', '> Halaman<')
            
            if content != original:
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(content)
                print(f'✓ {file_path} updated')
        except Exception as e:
            print(f'Error processing {file_path}: {e}')

print('Done!')
