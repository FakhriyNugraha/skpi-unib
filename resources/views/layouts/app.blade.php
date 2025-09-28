<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'SKPI' }} - Universitas Bengkulu</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .logo-unib {
            width: 50px;
            height: 50px;
            background-image: url('/images/logounib.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
                    <!-- Navigation -->
            <nav class="bg-gradient-to-r from-gray-200 via-gray-100 to-blue-50 shadow-md border-b border-gray-300">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <div class="logo-unib mr-3"></div>
                                <div>
                                    <h1 class="text-xl font-bold text-unib-blue-900">SKPI UNIB</h1>
                                    <p class="text-xs text-gray-700">Fakultas Teknik</p>
                                </div>
                            </div>

                            <!-- Navigation Links -->
                            @auth
                            <div class="hidden space-x-8 sm:ml-10 sm:flex">
                                @if(auth()->user()->role === 'user')
                                    <a href="{{ route('skpi.index') }}" class="text-gray-700 hover:text-unib-blue-700 whitespace-nowrap py-2 px-1 font-medium text-sm transition-colors">
                                        Dashboard
                                    </a>
                                @elseif(auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-unib-blue-700 whitespace-nowrap py-2 px-1 font-medium text-sm transition-colors">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('admin.skpi-list') }}" class="text-gray-700 hover:text-unib-blue-700 whitespace-nowrap py-2 px-1 font-medium text-sm transition-colors">
                                        Review SKPI
                                    </a>
                                @elseif(auth()->user()->role === 'superadmin')
                                    <a href="{{ route('superadmin.dashboard') }}" class="text-gray-700 hover:text-unib-blue-700 whitespace-nowrap py-2 px-1 font-medium text-sm transition-colors">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('superadmin.users') }}" class="text-gray-700 hover:text-unib-blue-700 whitespace-nowrap py-2 px-1 font-medium text-sm transition-colors">
                                        Users
                                    </a>
                                    <a href="{{ route('superadmin.all-skpi') }}" class="text-gray-700 hover:text-unib-blue-700 whitespace-nowrap py-2 px-1 font-medium text-sm transition-colors">
                                        All SKPI
                                    </a>
                                @endif
                            </div>
                            @endauth
                        </div>

                        <!-- User Menu -->
                        <div class="flex items-center space-x-4">
                            <!-- Email Pengaduan -->
                            <a href="mailto:ft@unib.ac.id" class="flex items-center text-sm text-gray-700 hover:text-unib-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="hidden md:inline">Pengaduan</span>
                            </a>

                            @auth
                                <div class="relative">
                                    <button
                                        id="userMenuButton"
                                        type="button"
                                        class="flex items-center text-sm bg-white rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-unib-blue-500"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                    >
                                        @if(auth()->user()->avatar)
                                            <img class="h-8 w-8 rounded-full object-cover" src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-unib-blue-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ substr(auth()->user()->name, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                    </button>

                                    <!-- Dropdown -->
                                    <div
                                        id="userMenu"
                                        class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                    >
                                        <div class="py-1">
                                            <div class="px-4 py-2 border-b border-gray-100">
                                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                                <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</p>
                                            </div>
                                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Profile
                                            </a>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Logout
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-unib-blue-700 px-3 py-2 text-sm font-medium">Login</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>


        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Page Content -->
        <main class="py-8">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-unib-blue-900 text-white mt-16">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="logo-unib mr-3"></div>
                            <div>
                                <h3 class="text-lg font-bold">SKPI UNIB</h3>
                                <p class="text-sm text-gray-300">Fakultas Teknik</p>
                            </div>
                        </div>
                        <p class="text-gray-300 text-sm">
                            Sistem Informasi Surat Keterangan Pendamping Ijazah
                            Universitas Bengkulu Fakultas Teknik
                        </p>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Kontak</h4>
                        <div class="space-y-2 text-sm text-gray-300">
                            <p>Jl. WR. Supratman, Kandang Limun</p>
                            <p>Bengkulu 38371</p>
                            <p>Telp: (0736) 344087</p>
                            <p>Email: ft@unib.ac.id</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Program Studi</h4>
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-300">
                            <p>Informatika</p>
                            <p>Teknik Sipil</p>
                            <p>Teknik Mesin</p>
                            <p>Teknik Elektro</p>
                            <p>Arsitektur</p>
                            <p>Sistem Informasi</p>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-300">
                    <p>&copy; {{ date('Y') }} Universitas Bengkulu. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.10.2/cdn.min.js" defer></script>

    <script>
    (function () {
        const btn  = document.getElementById('userMenuButton');
        const menu = document.getElementById('userMenu');
        if (!btn || !menu) return;
        const openMenu  = () => { menu.classList.remove('hidden'); btn.setAttribute('aria-expanded', 'true'); };
        const closeMenu = () => { menu.classList.add('hidden'); btn.setAttribute('aria-expanded', 'false'); };
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (menu.classList.contains('hidden')) { openMenu(); } else { closeMenu(); }
        });
        document.addEventListener('click', function (e) {
            if (!menu.classList.contains('hidden')) {
                if (!menu.contains(e.target) && e.target !== btn && !btn.contains(e.target)) {
                    closeMenu();
                }
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeMenu();
        });
    })();
    </script>
</body>
</html>
