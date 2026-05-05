import forms from '@tailwindcss/forms';
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // Safelist: class yang dibuat secara dinamis oleh PHP (tidak bisa di-scan Tailwind)
    safelist: [
        // Warna badge kesulitan soal (dari accessor BankSoal::warna_badge_kesulitan)
        'bg-green-100', 'text-green-700', 'dark:bg-green-900', 'dark:text-green-300',
        'bg-yellow-100', 'text-yellow-700', 'dark:bg-yellow-900', 'dark:text-yellow-300',
        'bg-red-100', 'text-red-700', 'dark:bg-red-900', 'dark:text-red-300',
        'bg-gray-100', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300',
        // Warna badge tipe soal
        'bg-blue-100', 'text-blue-700', 'dark:bg-blue-900', 'dark:text-blue-300',
        'bg-purple-100', 'text-purple-700', 'dark:bg-purple-900', 'dark:text-purple-300',
        'bg-orange-100', 'text-orange-700', 'dark:bg-orange-900', 'dark:text-orange-300',
        // Warna badge role (navbar & mobile menu)
        'bg-emerald-100', 'text-emerald-700', 'dark:bg-emerald-900', 'dark:text-emerald-300',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            // Warna tema utama hijau sesuai identitas project ujian-mts
            colors: {
                primary: {
                    50:  '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e',
                    600: '#16a34a', // ← Warna utama tema
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                },
            },
        },
    },

    plugins: [forms],
};
