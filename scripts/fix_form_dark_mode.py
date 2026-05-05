#!/usr/bin/env python3
"""
Script untuk menambahkan dark mode support ke form elements (input, select, textarea)
Mengganti class-class yang perlu dark variant
"""

import os
import re
from pathlib import Path

def fix_form_elements(file_path):
    """Fix dark mode untuk form elements"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        
        # Pattern untuk mendeteksi form elements yang perlu di-fix
        # Input, select, textarea dengan border-gray-300 tanpa dark variant
        
        # Pattern 1: <input ... border-gray-300 ... tanpa dark:border
        pattern1 = r'(<(?:input|textarea)[^>]*?)border-gray-300(?![^ \n>]*dark:border)([^>]*?>)'
        replacement1 = r'\1border-gray-300 dark:border-gray-600\2'
        content = re.sub(pattern1, replacement1, content)
        
        # Pattern 2: Tambahkan bg dan text color untuk input/select/textarea yang hanya punya border
        # Cari yang punya border-gray-300 dark:border-gray-600 tapi belum ada bg/text dark variant
        pattern2 = r'(<(?:input|textarea)[^>]*border-gray-300 dark:border-gray-600)(?![^>]*dark:bg)([^>]*?)(rounded-lg)'
        replacement2 = r'\1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100\2\3'
        content = re.sub(pattern2, replacement2, content)
        
        # Pattern 3: Untuk select elements
        pattern3 = r'(<select[^>]*?)border-gray-300(?![^ \n>]*dark:border)([^>]*?>)'
        replacement3 = r'\1border-gray-300 dark:border-gray-600\2'
        content = re.sub(pattern3, replacement3, content)
        
        # Pattern 4: Tambahkan bg dan text color untuk select
        pattern4 = r'(<select[^>]*border-gray-300 dark:border-gray-600)(?![^>]*dark:bg)([^>]*?)(rounded-lg[^>]*>)'
        replacement4 = r'\1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100\2\3'
        content = re.sub(pattern4, replacement4, content)
        
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
    
    updated_files = []
    
    # Find dan update semua blade files dengan form elements
    for file_path in views_dir.rglob('*.blade.php'):
        if fix_form_elements(file_path):
            updated_files.append(str(file_path))
            print(f"✓ Updated: {file_path.relative_to(views_dir)}")
    
    print(f"\nTotal files updated: {len(updated_files)}")
    return len(updated_files) > 0

if __name__ == '__main__':
    success = main()
    exit(0 if success else 1)
