@php
    $role = Auth::user()->role ?? '';

    // Tentukan URL dashboard berdasarkan role pengguna
    $dashboardRoute = match($role) {
        'super_admin' => route('admin.dashboard'),
        'guru'        => route('guru.dashboard'),
        'siswa'       => route('siswa.dashboard'),
        default       => '/',
    };

    // Definisi menu navigasi per role beserta path icon SVG-nya
    $menuItems = match($role) {
        'super_admin' => [
            ['label' => 'Dashboard',      'route' => 'admin.dashboard',           'pattern' => 'admin.dashboard',         'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['label' => 'Pengguna',       'route' => 'admin.users.index',         'pattern' => 'admin.users.*',           'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['label' => 'Kelas',          'route' => 'admin.kelas.index',         'pattern' => 'admin.kelas.*',           'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ['label' => 'Mata Pelajaran', 'route' => 'admin.mata-pelajaran.index','pattern' => 'admin.mata-pelajaran.*', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
            ['label' => 'Tahun Ajaran',   'route' => 'admin.tahun-ajaran.index',  'pattern' => 'admin.tahun-ajaran.*',   'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label' => 'Bank Soal',      'route' => 'admin.bank-soal.index',     'pattern' => 'admin.bank-soal.*',      'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
        ],
        'guru' => [
            ['label' => 'Dashboard', 'route' => 'guru.dashboard',       'pattern' => 'guru.dashboard',   'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['label' => 'Bank Soal', 'route' => 'guru.bank-soal.index', 'pattern' => 'guru.bank-soal.*', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
            ['label' => 'Ujian',     'route' => 'guru.ujian.index',     'pattern' => 'guru.ujian.*',     'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ],
        'siswa' => [
            ['label' => 'Dashboard', 'route' => 'siswa.dashboard', 'pattern' => 'siswa.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ],
        default => [],
    };

    // Label dan warna badge role
    $roleLabel      = match($role) { 'super_admin' => 'Admin', 'guru' => 'Guru', 'siswa' => 'Siswa', default => '-' };
    $roleBadgeClass = match($role) {
        'super_admin' => 'bg-purple-100 text-purple-700',
        'guru'        => 'bg-blue-100 text-blue-700',
        'siswa'       => 'bg-emerald-100 text-emerald-700',
        default       => 'bg-gray-100 text-gray-500',
    };

    // Inisial nama user untuk avatar circle (maks 2 karakter)
    $initials = collect(explode(' ', Auth::user()->name))
                    ->map(fn($w) => strtoupper($w[0] ?? ''))
                    ->take(2)->join('');
@endphp

{{-- ===== NAVBAR UTAMA ===== --}}
<nav x-data="{ mobileOpen: false, darkMode: $el.parentElement.darkMode }"
     class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- BRAND / LOGO --}}
            <a href="{{ $dashboardRoute }}"
               class="flex items-center gap-3 shrink-0 group">
                {{-- Logo MTs Al-Hidayah Tamansari (Optimized Display) --}}
                <div class="w-10 h-10 rounded-md p-1 shadow-md bg-white dark:bg-gray-800 flex items-center justify-center shrink-0 border border-gray-200 dark:border-gray-700
                            group-hover:shadow-lg transition-shadow">
                    <img src="{{ asset('images/mts-al-hidayah-logo.png') }}" 
                         alt="Logo {{ config('app.name') }}"
                         class="w-full h-full object-contain"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                    <svg class="w-5 h-5 text-green-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="hidden sm:block leading-tight">
                    <p class="text-sm font-bold text-gray-900 dark:text-white tracking-tight">{{ config('app.name') }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400 font-medium">CBT System</p>
                </div>
            </a>

            {{-- NAVIGATION LINKS: Icon-Only with Tooltip on Hover --}}
            <div class="hidden md:flex items-center gap-1">
                @foreach($menuItems as $item)
                    @php $isActive = request()->routeIs($item['pattern']); @endphp
                    {{-- Icon-Only Link dengan Tooltip Hover --}}
                    <div class="relative group">
                        <a href="{{ route($item['route']) }}"
                           class="relative p-2 rounded-lg transition-all duration-150
                                  {{ $isActive ? 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800' }}"
                           title="{{ $item['label'] }}">
                            <svg class="w-5 h-5 {{ $isActive ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                            </svg>
                            @if($isActive)
                                <span class="absolute bottom-1 left-2 right-2 h-0.5 rounded-full bg-green-500"></span>
                            @endif
                        </a>
                        {{-- Hover Tooltip --}}
                        <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 px-3 py-1.5 text-xs font-semibold text-white bg-gray-900 dark:bg-gray-950 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-40">
                            {{ $item['label'] }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- USER DROPDOWN + DARK MODE TOGGLE (desktop) --}}
            <div class="hidden md:flex items-center gap-3">
                {{-- Badge role --}}
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $roleBadgeClass }} dark:{{ str_replace(['bg-', 'text-'], ['dark:bg-', 'dark:text-'], $roleBadgeClass) }}-800 dark:{{ str_replace('text-', 'dark:text-', $roleBadgeClass) }}-200">
                    {{ $roleLabel }}
                </span>

                {{-- Dark mode toggle button --}}
                <button @click="
                    darkMode = !darkMode;
                    localStorage.setItem('darkMode', darkMode);
                    document.documentElement.classList.toggle('dark', darkMode);
                "
                        class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        title="Toggle dark mode">
                    <svg x-show="!darkMode" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>

                {{-- Avatar + Dropdown --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                            class="flex items-center gap-2 rounded-xl px-2.5 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-800
                                   border border-transparent hover:border-gray-200 dark:hover:border-gray-700 transition-all duration-150">
                        {{-- Avatar: foto profil jika ada, fallback ke inisial --}}
                        <div class="w-8 h-8 rounded-full overflow-hidden bg-gradient-to-br from-green-500 to-green-700
                                    flex items-center justify-center text-white text-xs font-bold shadow-sm shrink-0">
                            @if(Auth::user()->foto)
                                <img src="{{ Storage::url(Auth::user()->foto) }}" alt="Foto" class="w-8 h-8 object-cover">
                            @else
                                {{ $initials }}
                            @endif
                        </div>
                        <span class="hidden lg:block text-sm font-medium text-gray-800 dark:text-gray-200 max-w-[120px] truncate">
                            {{ Auth::user()->name }}
                        </span>
                        <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 transition-transform duration-200"
                             :class="open ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Panel dropdown --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute right-0 top-full mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg
                                ring-1 ring-black/5 dark:ring-white/10 overflow-hidden z-50"
                         style="display: none;">

                        {{-- Info user di dalam dropdown --}}
                        <div class="px-4 py-3.5 bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-full overflow-hidden bg-gradient-to-br from-green-500 to-green-700
                                            flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    @if(Auth::user()->foto)
                                        <img src="{{ Storage::url(Auth::user()->foto) }}" alt="Foto" class="w-9 h-9 object-cover">
                                    @else
                                        {{ $initials }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profil Saya
                            </a>
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-600 py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HAMBURGER BUTTON (mobile) --}}
            <button @click="mobileOpen = !mobileOpen"
                    class="md:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display:none"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ===== MOBILE MENU ===== --}}
    <div x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900"
         style="display: none;">

        {{-- Info user di atas menu mobile --}}
        <div class="flex items-center gap-3 px-4 py-4 bg-gray-50 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="w-10 h-10 rounded-full overflow-hidden bg-gradient-to-br from-green-500 to-green-700
                        flex items-center justify-center text-white text-sm font-bold shrink-0">
                @if(Auth::user()->foto)
                    <img src="{{ Storage::url(Auth::user()->foto) }}" alt="Foto" class="w-10 h-10 object-cover">
                @else
                    {{ $initials }}
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">{{ Auth::user()->name }}</p>
                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full mt-0.5 {{ $roleBadgeClass }}">
                    {{ $roleLabel }}
                </span>
            </div>
        </div>

        {{-- Link navigasi (mobile) --}}
        <div class="px-3 py-3 space-y-0.5">
            @foreach($menuItems as $item)
                @php $isActive = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ $isActive ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 shrink-0 {{ $isActive ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                    @if($isActive)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Dark mode toggle + Profil & logout (mobile) --}}
        <div class="px-3 py-3 border-t border-gray-100 dark:border-gray-800 space-y-0.5">
            {{-- Dark mode toggle button (mobile) --}}
            <button @click="
                darkMode = !darkMode;
                localStorage.setItem('darkMode', darkMode);
                document.documentElement.classList.toggle('dark', darkMode);
            "
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <svg x-show="!darkMode" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg x-show="darkMode" class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24" style="display:none;">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
                <span>{{ __('Dark Mode') }}</span>
                <svg class="w-4 h-4 ml-auto text-gray-400 dark:text-gray-500 shrink-0" :class="darkMode ? '' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profil Saya
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </div>
</nav>
