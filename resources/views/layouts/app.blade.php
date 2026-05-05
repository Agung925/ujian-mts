<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Judul halaman: dari @section('title') atau default nama aplikasi --}}
        <title>@yield('title', config('app.name', 'MTs CBT')) — MTs CBT</title>

        {{-- Font Inter dari Google Fonts (modern & legible) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>

        {{-- Prevent dark mode flash: apply class sebelum CSS render --}}
        <script>
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        </script>

        {{-- Vite assets: Tailwind CSS + Alpine.js --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Slot untuk CSS/meta tambahan dari halaman child --}}
        @stack('head')
    </head>
    <body class="font-sans antialiased bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100">

        {{-- Navbar utama —  sticky, konsisten di semua halaman --}}
        @include('layouts.navigation')

        {{-- Konten utama halaman --}}
        <main class="min-h-[calc(100vh-4rem-73px)] bg-white dark:bg-gray-950">
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>

        {{-- ===== FOOTER ===== --}}
        <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 mt-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">

                    {{-- Kiri: brand + deskripsi --}}
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-md bg-green-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200">MTs CBT</span>
                            <span class="text-gray-300 dark:text-gray-600 dark:text-gray-400 mx-1.5">·</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Sistem Ujian Berbasis Komputer untuk MTs</span>
                        </div>
                    </div>

                    {{-- Kanan: copyright --}}
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        &copy; {{ date('Y') }} MTs &mdash; All rights reserved
                    </p>
                </div>
            </div>
        </footer>

        {{-- Slot untuk JS tambahan dari halaman child --}}
        @stack('scripts')
    </body>
</html>
