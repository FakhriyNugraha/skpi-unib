<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - SKPI UNIB</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logounib.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logounib.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logounib.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900">
<div class="min-h-screen flex flex-col">

    <!-- MAIN -->
    <main class="flex-1 grid lg:grid-cols-2">

        <!-- Left: Login Form -->
        <section class="flex items-center justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24">
            <div class="w-full max-w-sm lg:w-96">

                <!-- Brand -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-6">
                        <div class="w-20 h-20">
                            <img src="{{ asset('images/logounib.png') }}" alt="Logo UNIB" class="w-full h-full object-contain">
                        </div>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
                    <p class="text-sm text-gray-600">Masuk ke Sistem SKPI Universitas Bengkulu</p>
                </div>

                <!-- Flash Status -->
                @if (session('status'))
                    <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Global Error -->
                @if (session('error'))
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Login Form -->
                <form class="space-y-6" method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <input
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="email"
                                required
                                autofocus
                                value="{{ old('email') }}"
                                placeholder="Masukkan email Anda"
                                class="appearance-none relative block w-full pl-10 pr-3 py-3 bg-blue-50 border placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-unib-blue-500 focus:border-unib-blue-500 sm:text-sm @error('email') border-red-500 bg-red-50 @else border-blue-100 @enderror"
                            >
                            <!-- Left icon: person -->
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                        </div>
                        @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                placeholder="Masukkan password Anda"
                                class="appearance-none relative block w-full pl-10 pr-10 py-3 bg-blue-50 border placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-unib-blue-500 focus:border-unib-blue-500 sm:text-sm @error('password') border-red-500 bg-red-50 @else border-blue-100 @enderror"
                            >
                            <!-- Left icon: lock -->
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>

                            <!-- Right icon (toggle show/hide) -->
                            <button
                                id="togglePassword"
                                type="button"
                                aria-label="Tampilkan/sembunyikan password"
                                title="Tampilkan/sembunyikan password"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none"
                            >
                                <!-- eye closed (default) -->
                                <svg id="icon-eye-closed" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 3l18 18M10.477 10.477A3 3 0 0112 9c3 0 5.5 1.5 7 3-.398.398-.83.768-1.29 1.104M6.7 6.7C5.074 7.646 3.735 8.83 3 12c1.5 1.5 4 3 7 3 1.046 0 2.042-.162 2.963-.462" />
                                </svg>
                                <!-- eye open -->
                                <svg id="icon-eye-open" class="h-5 w-5 hidden text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    <circle cx="12" cy="12" r="3" stroke-width="2" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember -->
                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center">
                            <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-unib-blue-600 focus:ring-unib-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <div>
                        <button type="submit"
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-unib-blue-600 hover:bg-unib-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-unib-blue-500 transition-all duration-200 transform hover:scale-[1.02]">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-unib-blue-200 group-hover:text-unib-blue-100 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </span>
                            Masuk
                        </button>
                    </div>

                    <!-- Register -->
                    <p class="text-center text-sm text-gray-600">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="font-medium text-teknik-orange-600 hover:text-teknik-orange-500 transition-colors">
                            Daftar di sini
                        </a>
                    </p>
                </form>

                <!-- Password Reminder Card -->
                <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-800">Lupa Password?</h3>
                            <p class="mt-1 text-xs text-amber-700">
                                Jika Anda lupa password, silakan hubungi admin jurusan terkait Anda untuk bantuan pengaturan ulang password.
                                Untuk admin jurusan, hubungi super admin. Untuk mahasiswa, hubungi admin jurusan masing-masing.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Default Login Info (opsional) -->
                <div class="mt-8 rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <h3 class="mb-2 text-sm font-medium text-blue-800">Info Login Default:</h3>
                    <div class="space-y-1 text-xs text-blue-700">
                        <p><strong>Super Admin:</strong> superadmin@unib.ac.id / password123</p>
                        <p><strong>Admin:</strong> admin.if@unib.ac.id / password123</p>
                        <p><strong>Mahasiswa:</strong> mahasiswa1@student.unib.ac.id / password123</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Right: Hero -->
        <aside class="relative hidden lg:block">
            <div class="absolute inset-0 bg-gradient-to-br from-unib-blue-600 to-unib-blue-800">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="relative flex h-full flex-col items-center justify-start text-white pt-16 pb-12 px-12">
                    <div class="max-w-md text-center mt-12">
                        <img src="{{ asset('images/logounib.png') }}" alt="Logo UNIB" class="mx-auto mb-4 h-32 w-32">
                        <h1 class="mb-1 text-4xl font-bold">SKPI UNIB</h1>
                        <h2 class="mb-4 text-2xl font-semibold">Fakultas Teknik</h2>
                        <p class="mb-6 text-lg text-blue-100 leading-relaxed">
                            Sistem Informasi Surat Keterangan Pendamping Ijazah
                            Universitas Bengkulu
                        </p>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="rounded-lg bg-white/10 p-4 backdrop-blur-sm">
                                <div class="text-2xl font-bold text-teknik-orange-400">{{ $stats['total_jurusan'] ?? 0 }}</div>
                                <div class="text-sm">Program Studi</div>
                            </div>
                            <div class="rounded-lg bg-white/10 p-4 backdrop-blur-sm">
                                <div class="text-2xl font-bold text-teknik-orange-400">{{ $stats['total_mahasiswa'] ?? 0 }}</div>
                                <div class="text-sm">Mahasiswa</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </main>

    <!-- Footer -->
    <footer class="py-4 text-center text-sm text-gray-500 lg:text-white bg-white lg:bg-transparent">
        © {{ date('Y') }} Universitas Bengkulu. All rights reserved.
    </footer>
</div>

<!-- Toggle password (vanilla JS, tanpa Alpine) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pwd = document.getElementById('password');
    const btn = document.getElementById('togglePassword');
    const eyeOpen = document.getElementById('icon-eye-open');   // terlihat saat text
    const eyeClosed = document.getElementById('icon-eye-closed'); // terlihat saat password

    if (btn && pwd && eyeOpen && eyeClosed) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const isHidden = pwd.getAttribute('type') === 'password';
            pwd.setAttribute('type', isHidden ? 'text' : 'password');
            // toggle ikon
            eyeOpen.classList.toggle('hidden', !isHidden);   // jika sekarang text, eyeOpen tampil
            eyeClosed.classList.toggle('hidden', isHidden);  // jika sekarang text, eyeClosed sembunyi
        });
    }
});
</script>
</body>
</html>
