#!/usr/bin/env python3
"""
Script untuk menambahkan dark mode support ke semua Blade views
Mengganti class-class yang perlu dark variant
"""

import os
import re
from pathlib import Path

# Mapping dari light classes ke dark classes
DARK_MODE_MAPPING = {
    # Background colors
    'bg-white(["\' ])': r'bg-white dark:bg-gray-800\1',
    'bg-gray-50(["\' ])': r'bg-gray-50 dark:bg-gray-900\1',
    'bg-gray-100(["\' ])': r'bg-gray-100 dark:bg-gray-700\1',
    
    # Text colors
    'text-gray-900(["\' ])': r'text-gray-900 dark:text-white\1',
    'text-gray-800(["\' ])': r'text-gray-800 dark:text-gray-100\1',
    'text-gray-700(["\' ])': r'text-gray-700 dark:text-gray-300\1',
    'text-gray-600(["\' ])': r'text-gray-600 dark:text-gray-400\1',
    'text-gray-500(["\' ])': r'text-gray-500 dark:text-gray-400\1',
    'text-gray-400(["\' ])': r'text-gray-400 dark:text-gray-500\1',
    
    # Borders
    'border-gray-200(["\' ])': r'border-gray-200 dark:border-gray-700\1',
    'border-gray-100(["\' ])': r'border-gray-100 dark:border-gray-700\1',
    
    # Special replacements untuk nav/dropdown
    'bg-gray-50 border-b border-gray-100': r'bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600',
}

def add_dark_mode_to_file(file_path):
    """Tambahkan dark mode classes ke file"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        
        # Apply all mappings
        for light_pattern, dark_replacement in DARK_MODE_MAPPING.items():
            # Skip jika sudah ada dark: variant
            if 'dark:' in dark_replacement:
                # Cek apakah pattern sudah di-apply
                pattern_with_dark = dark_replacement.replace(r'\1', '').split(' dark:')[0] + '.*dark:'
                if not re.search(pattern_with_dark, content):
                    content = re.sub(light_pattern, dark_replacement, content)
        
        if content != original_content:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
            return True
        return False
    except Exception as e:
        print(f"Error processing {file_path}: {e}")
        return False

def main():
    """Main function - process all blade files"""
    views_dir = Path('/workdir/www/ujian-mts/resources/views')
    
    # Extensions to process
    extensions = ['*.blade.php']
    
    updated_files = []
    
    # Find dan update semua blade files
    for ext in extensions:
        for file_path in views_dir.rglob(ext):
            # Skip auth views (sudah punya dark mode)
            if 'auth' in str(file_path):
                continue
            
            if add_dark_mode_to_file(file_path):
                updated_files.append(str(file_path))
                print(f"✓ Updated: {file_path.relative_to(views_dir)}")
    
    print(f"\nTotal files updated: {len(updated_files)}")
    return len(updated_files) > 0

if __name__ == '__main__':
    success = main()
    exit(0 if success else 1)
