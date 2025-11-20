<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'SKPI' }} - Universitas Bengkulu</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logounib.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logounib.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logounib.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .logo-unib {
            width: 60px;
            height: 60px;
            background-image: url('/images/logounib.png'); 
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
        [x-cloak] { display: none !important; }

        /* Custom active link style */
        .nav-link-active {
            background-color: white;
            color: #1e40af; /* blue-800 */
        }

        /* Dropdown Animation */
        .dropdown-menu {
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.2s ease-out, transform 0.2s ease-out;
            pointer-events: none;
        }
        .dropdown-menu.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        /* Avatar Glow Effect */
        .avatar-glow:hover {
            box-shadow: 0 0 12px 2px rgba(249, 115, 22, 0.5); /* teknik-orange-400 */
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col">
        
        <header class="bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 text-white shadow-xl sticky top-0 z-50">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="/" class="flex-shrink-0 flex items-center">
                        <div class="logo-unib mr-3"></div>
                        <div>
                            <h1 class="text-xl font-bold tracking-tight">SKPI UNIB</h1>
                            <p class="text-xs text-blue-300">Fakultas Teknik</p>
                        </div>
                    </a>

                    @auth
                    <div class="flex-1 flex justify-center">
                        <div class="hidden md:flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-lg">
                            @if(auth()->user()->role === 'user')
                                <a href="{{ route('skpi.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('skpi.index') ? 'nav-link-active' : 'text-blue-100 hover:bg-white/20 hover:text-white' }}">Dashboard</a>
                            @elseif(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : 'text-blue-100 hover:bg-white/20 hover:text-white' }}">Dashboard</a>
                                <a href="{{ route('admin.skpi-list') }}" class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.skpi-list') ? 'nav-link-active' : 'text-blue-100 hover:bg-white/20 hover:text-white' }}">Review SKPI</a>
                            @elseif(auth()->user()->role === 'superadmin')
                                <a href="{{ route('superadmin.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('superadmin.dashboard') ? 'nav-link-active' : 'text-blue-100 hover:bg-white/20 hover:text-white' }}">Dashboard</a>
                                <a href="{{ route('superadmin.users') }}" class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('superadmin.users') ? 'nav-link-active' : 'text-blue-100 hover:bg-white/20 hover:text-white' }}">Kelola User</a>
                                <a href="{{ route('superadmin.jurusans') }}" class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('superadmin.jurusans') ? 'nav-link-active' : 'text-blue-100 hover:bg-white/20 hover:text-white' }}">Kelola Jurusan</a>
                                <a href="{{ route('superadmin.all-skpi') }}" class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('superadmin.all-skpi') ? 'nav-link-active' : 'text-blue-100 hover:bg-white/20 hover:text-white' }}">Review SKPI</a>
                                <a href="{{ route('superadmin.reports') }}" class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('superadmin.reports') ? 'nav-link-active' : 'text-blue-100 hover:bg-white/20 hover:text-white' }}">Laporan & Statistik</a>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="flex-1"></div>
                    @endauth

                    <div class="flex items-center space-x-4">
                        <a href="https://mail.google.com/mail/?view=cm&to=ft@unib.ac.id" class="hidden md:inline-flex items-center bg-white/10 px-4 py-2 text-sm font-medium rounded-md text-blue-200 hover:bg-white/20 transition-colors" target="_blank">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Pengaduan
                        </a>

                        @auth
                            <div class="relative">
                                <button id="userMenuButton" type="button" class="flex items-center text-sm bg-gray-800 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-800 focus:ring-teknik-orange-400 avatar-glow transition-shadow">
                                    <span class="sr-only">Open user menu</span>
                                    @if(auth()->user()->avatar)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-teknik-orange-500 to-teknik-orange-600 flex items-center justify-center ring-1 ring-white/50">
                                            <span class="text-base font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </button>
                                
                                <!-- Tambah overflow-hidden agar hover background terklip radius -->
                                <div id="userMenu" class="dropdown-menu hidden origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-2xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 overflow-hidden">
                                    <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">
                                        <div class="px-4 py-3 border-b border-gray-200">
                                            <p class="text-sm font-semibold text-gray-900 truncate" role="none">{{ auth()->user()->name }}</p>
                                            <p class="text-xs text-gray-500 inline-block px-2 py-0.5 bg-gray-100 rounded-full mt-1" role="none">{{ ucfirst(auth()->user()->role) }}</p>
                                        </div>
                                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 group" role="menuitem">
                                            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-teknik-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            Profile Saya
                                        </a>
                                        <a href="https://mail.google.com/mail/?view=cm&to=ft@unib.ac.id" class="flex items-center md:hidden px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 group" role="menuitem" target="_blank">
                                            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-teknik-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            Pengaduan
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        <form method="POST" action="{{ route('logout') }}" role="none">
                                            @csrf
                                            <!-- Tambah rounded-b-xl agar mengikuti sudut container -->
                                            <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 group rounded-b-xl" role="menuitem">
                                                <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 bg-teknik-orange-500 text-sm font-semibold rounded-lg hover:bg-teknik-orange-600 transition-colors shadow-md">Login</a>
                        @endauth
                    </div>
                </div>
            </nav>
        </header>

        <main class="flex-grow">
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-lg shadow-md flex items-center" role="alert">
                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
             @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-lg shadow-md flex items-center" role="alert">
                         <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <div class="py-8">
                {{ $slot }}
            </div>
        </main>

        
        <footer class="bg-gradient-to-br from-indigo-900 via-blue-800 to-blue-900 text-white mt-auto shadow-2xl">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <div class="flex items-center mb-4"><div class="logo-unib mr-3"></div><div><h3 class="text-xl font-bold">SKPI UNIB</h3><p class="text-sm text-blue-300">Fakultas Teknik</p></div></div>
                        <p class="text-blue-200 text-sm">Sistem Informasi Surat Keterangan Pendamping Ijazah Universitas Bengkulu.</p>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold mb-4 text-teknik-orange-400">Kontak</h4>
                        <div class="space-y-2 text-sm text-blue-300">
                            <p>Jl. WR. Supratman, Kandang Limun</p>
                            <p>(0736) 344087</p>
                            <p>ft@unib.ac.id</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold mb-4 text-teknik-orange-400">Program Studi</h4>
                        <div class="grid grid-cols-2 gap-2 text-sm text-blue-300">
                           <p>Informatika</p><p>Teknik Sipil</p><p>Teknik Mesin</p><p>Teknik Elektro</p><p>Arsitektur</p><p>Sistem Informasi</p>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-blue-700/50 mt-8 pt-8 text-center text-sm text-blue-200">
                    <p>&copy; {{ date('Y') }} Universitas Bengkulu. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
    
    <script>
    (function () {
        const btn = document.getElementById('userMenuButton');
        const menu = document.getElementById('userMenu');
        if (!btn || !menu) return;

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            menu.classList.toggle('hidden');
            menu.classList.toggle('show');
        });

        document.addEventListener('click', function (e) {
            if (!menu.classList.contains('hidden') && !menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.add('hidden');
                menu.classList.remove('show');
            }
        });
        
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                menu.classList.add('hidden');
                menu.classList.remove('show');
            }
        });
    })();
    </script>
</body>
</html>
