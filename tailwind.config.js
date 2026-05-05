import forms from '@tailwindcss/forms';
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // Safelist: class yang dibuat secara dinamis oleh PHP (tidak bisa di-scan Tailwind)
    safelist: [
        // Warna badge kesulitan soal (dari accessor BankSoal::warna_badge_kesulitan)
        'bg-green-100', 'text-green-700',
        'bg-yellow-100', 'text-yellow-700',
        'bg-red-100', 'text-red-700',
        'bg-gray-100', 'text-gray-700',
        // Warna badge tipe soal
        'bg-blue-100', 'text-blue-700',
        'bg-purple-100', 'text-purple-700',
        'bg-orange-100', 'text-orange-700',
        // Warna badge role (navbar & mobile menu)
        'bg-emerald-100', 'text-emerald-700',
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
