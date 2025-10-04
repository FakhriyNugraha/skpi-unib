<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Daftar Akun - SKPI UNIB</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased min-h-screen flex flex-col">
    <!-- Page Wrapper -->
    <main class="flex-1">
        <div class="min-h-[calc(100vh-56px)] lg:min-h-[calc(100vh-64px)] flex">
            <!-- Left Side - Register Form -->
            <section class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24">
                <div class="mx-auto w-full max-w-lg lg:w-96">
                    <!-- Logo and Header -->
                    <div class="text-center mb-8">
                        <div class="flex justify-center mb-6">
                            <div class="w-20 h-20">
                                <img src="{{ asset('images/logounib.png') }}" alt="Logo UNIB" class="w-full h-full object-contain">
                            </div>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Daftar Akun Baru</h1>
                        <p class="text-sm text-gray-600">Buat akun untuk mengakses Sistem SKPI</p>
                    </div>

                    <!-- Register Form -->
                    <form class="space-y-6" method="POST" action="{{ route('register') }}" novalidate>
                        @csrf

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input id="name" name="name" type="text" autocomplete="name" required
                                       class="appearance-none relative block w-full pl-10 pr-3 py-3 border placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-unib-blue-500 focus:border-unib-blue-500 focus:z-10 sm:text-sm border-gray-300 @error('name') border-red-500 @enderror"
                                       placeholder="Masukkan nama lengkap" value="{{ old('name') }}">
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jurusan (DIPINDAH ke atas sebelum NPM) -->
                        <div>
                            <label for="jurusan_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Program Studi
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <select id="jurusan_id" name="jurusan_id" required
                                        class="appearance-none relative block w-full pl-10 pr-10 py-3 border placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-unib-blue-500 focus:border-unib-blue-500 focus:z-10 sm:text-sm border-gray-300 @error('jurusan_id') border-red-500 @enderror">
                                    <option value="">Pilih Program Studi</option>
                                    @php
                                        $prefMap = [
                                            'Informatika'      => 'G1A0',
                                            'Teknik Sipil'     => 'G1B0',
                                            'Teknik Elektro'   => 'G1C0',
                                            'Teknik Mesin'     => 'G1D0',
                                            'Arsitektur'       => 'G1E0',
                                            'Sistem Informasi' => 'G1F0',
                                        ];
                                    @endphp
                                    @foreach(\App\Models\Jurusan::active()->orderBy('nama_jurusan')->get() as $jurusan)
                                        <option value="{{ $jurusan->id }}"
                                                data-prefix="{{ $prefMap[$jurusan->nama_jurusan] ?? '' }}"
                                                {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                            {{ $jurusan->nama_jurusan }} ({{ $jurusan->kode_jurusan }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('jurusan_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- NPM (auto prefix) -->
                        <div>
                            <label for="npm" class="block text-sm font-medium text-gray-700 mb-2">
                                NPM (Nomor Pokok Mahasiswa)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4zM9 6v10h6V6H9z"></path>
                                    </svg>
                                </div>
                                <input id="npm" name="npm" type="text" autocomplete="off" required maxlength="9"
                                       class="appearance-none relative block w-full pl-10 pr-3 py-3 border placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-unib-blue-500 focus:border-unib-blue-500 focus:z-10 sm:text-sm border-gray-300 tracking-wider @error('npm') border-red-500 @enderror"
                                       placeholder="Pilih Program Studi untuk mengisi prefix"
                                       value="{{ old('npm') }}">
                            </div>
                           
                            @error('npm')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required
                                       class="appearance-none relative block w-full pl-10 pr-3 py-3 border placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-unib-blue-500 focus:border-unib-blue-500 focus:z-10 sm:text-sm border-gray-300 @error('email') border-red-500 @enderror"
                                       placeholder="Masukkan email Anda" value="{{ old('email') }}">
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input id="password" name="password" type="password" autocomplete="new-password" required
                                       class="appearance-none relative block w-full pl-10 pr-3 py-3 border placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-unib-blue-500 focus:border-unib-blue-500 focus:z-10 sm:text-sm border-gray-300 @error('password') border-red-500 @enderror"
                                       placeholder="Masukkan password (min. 8 karakter)">
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                       class="appearance-none relative block w-full pl-10 pr-3 py-3 border placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-unib-blue-500 focus:border-unib-blue-500 focus:z-10 sm:text-sm border-gray-300 @error('password_confirmation') border-red-500 @enderror"
                                       placeholder="Ulangi password Anda">
                            </div>
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit"
                                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-unib-blue-600 hover:bg-unib-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-unib-blue-500 transition-all duration-200 transform hover:scale-[1.02]">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-unib-blue-500 group-hover:text-unib-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                </span>
                                Daftar Akun
                            </button>
                        </div>

                        <!-- Login Link -->
                        <p class="text-center text-sm">
                            <span class="text-gray-600">Sudah punya akun? </span>
                            <a href="{{ route('login') }}" class="font-medium text-teknik-orange-600 hover:text-teknik-orange-500 transition-colors">
                                Login di sini
                            </a>
                        </p>
                    </form>

                    <!-- Info -->
                    <aside class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h2 class="text-sm font-medium text-blue-800 mb-2">Informasi Penting:</h2>
                        <ul class="text-xs text-blue-700 space-y-1 list-disc pl-5">
                            <li>Pastikan NPM yang Anda masukkan benar dan sesuai data resmi kampus.</li>
                            <li>Pilih program studi yang sesuai dengan jurusan Anda.</li>
                            <li>Gunakan email yang aktif untuk notifikasi sistem.</li>
                            <li>Password minimal 8 karakter untuk keamanan akun.</li>
                        </ul>
                    </aside>
                </div>
            </section>

            <!-- Right Side - Hero Image -->
            <aside class="hidden lg:block relative w-0 flex-1">
                <div class="absolute inset-0 bg-gradient-to-br from-teknik-orange-600 to-teknik-orange-800">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="relative h-full flex flex-col justify-start items-center text-white pt-16 pb-12 px-12">
                        <div class="text-center max-w-md mt-12">
                            <div class="mb-6">
                                <img src="{{ asset('images/logounib.png') }}" alt="Logo UNIB" class="w-32 h-32 mx-auto mb-4">
                            </div>
                            <h2 class="text-4xl font-bold mb-2">Bergabung dengan</h2>
                            <h3 class="text-2xl font-semibold mb-4">Fakultas Teknik UNIB</h3>
                            <p class="text-lg text-orange-100 mb-6 leading-relaxed">
                                Daftarkan diri Anda untuk mengakses sistem SKPI dan kelola prestasi akademik Anda dengan mudah
                            </p>
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                                    <div class="text-2xl font-bold text-white">6</div>
                                    <div class="text-sm">Program Studi</div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                                    <div class="text-2xl font-bold text-white">1000+</div>
                                    <div class="text-sm">Mahasiswa</div>
                                </div>
                                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                                    <div class="text-2xl font-bold text-white">24/7</div>
                                    <div class="text-sm">Akses Online</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-4 text-sm text-gray-500 bg-white lg:bg-transparent lg:text-white mt-auto">
        © {{ date('Y') }} Universitas Bengkulu. All rights reserved.
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const npmInput = document.getElementById('npm');
            const jurusanSelect = document.getElementById('jurusan_id');

            // Peta prefix sesuai permintaan
            const prefixMap = {
                'G1A0': ['Informatika'],
                'G1B0': ['Teknik Sipil'],
                'G1C0': ['Teknik Elektro'],
                'G1D0': ['Teknik Mesin'],
                'G1E0': ['Arsitektur'],
                'G1F0': ['Sistem Informasi'],
            };

            // Helper: ambil prefix dari option terpilih
            function getSelectedPrefix() {
                const opt = jurusanSelect.options[jurusanSelect.selectedIndex];
                return opt ? (opt.getAttribute('data-prefix') || '') : '';
            }

            // Set prefix saat prodi dipilih
            function applyPrefixFromJurusan() {
                const prefix = getSelectedPrefix();
                if (!prefix) return;

                // Jika npm kosong atau tidak cocok prefix saat ini, set prefix baru
                if (!npmInput.value || !npmInput.value.startsWith(prefix)) {
                    npmInput.value = prefix;
                } else {
                    // Jika sudah ada, pastikan tetap uppercase dan terpangkas 9
                    npmInput.value = npmInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 9);
                }
                placeCursorAtEnd(npmInput); // fokus isi 5 karakter terakhir
            }

            // Kunci prefix: cegah penghapusan/penyisipan sebelum prefix length
            function lockPrefixOnInput(e) {
                const prefix = getSelectedPrefix();
                const prefixLen = prefix.length;

                // Normalisasi input: uppercase, alfanumerik, max 9
                let v = npmInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                if (v.length > 9) v = v.slice(0, 9);

                // Pastikan prefix selalu diawal
                if (prefix && !v.startsWith(prefix)) {
                    // Jika user menghapus/melewati prefix, kembalikan prefix + sisa (tanpa prefix lama)
                    const suffix = v.replace(new RegExp('^' + prefix.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')), '');
                    v = prefix + suffix;
                }
                npmInput.value = v;

                // Cegah cursor masuk ke area prefix
                if (npmInput.selectionStart < prefixLen) {
                    setSelection(npmInput, prefixLen);
                }
            }

            // Saat keydown: blok Backspace/Delete di dalam prefix
            function guardPrefixKeys(e) {
                const prefix = getSelectedPrefix();
                const prefixLen = prefix.length;
                const start = npmInput.selectionStart;
                const end = npmInput.selectionEnd;

                // Backspace di posisi prefix boundary
                if (e.key === 'Backspace' && start <= prefixLen) {
                    e.preventDefault();
                    setSelection(npmInput, prefixLen);
                }
                // Delete di dalam prefix
                if (e.key === 'Delete' && start < prefixLen) {
                    e.preventDefault();
                    setSelection(npmInput, prefixLen);
                }
                // Home: lompat ke setelah prefix
                if (e.key === 'Home') {
                    e.preventDefault();
                    setSelection(npmInput, prefixLen);
                }
                // Kunci navigasi panah kiri ke batas prefix
                if (e.key === 'ArrowLeft' && start <= prefixLen) {
                    e.preventDefault();
                    setSelection(npmInput, prefixLen);
                }
            }

            function setSelection(el, pos) {
                el.setSelectionRange(pos, pos);
            }
            function placeCursorAtEnd(el) {
                const len = el.value.length;
                el.focus();
                setSelection(el, len);
            }

            // Validasi submit front-end
            const form = document.querySelector('form');
            function validateBeforeSubmit(e) {
                const jurusan = jurusanSelect.value || '';
                const prefix = getSelectedPrefix();
                let npm = (npmInput.value || '').toUpperCase().replace(/[^A-Z0-9]/g, '');

                if (!jurusan) {
                    e.preventDefault();
                    alert('Silakan pilih Program Studi terlebih dahulu.');
                    jurusanSelect.focus();
                    return;
                }
                if (!prefix) {
                    e.preventDefault();
                    alert('Prefix NPM untuk Program Studi ini belum diatur.');
                    jurusanSelect.focus();
                    return;
                }
                if (!npm.startsWith(prefix)) {
                    e.preventDefault();
                    alert('NPM harus diawali dengan prefix ' + prefix + ' sesuai Program Studi.');
                    npmInput.focus();
                    return;
                }
                if (npm.length !== 9) {
                    e.preventDefault();
                    alert('NPM harus berjumlah 9 karakter (prefix + 5 karakter terakhir).');
                    npmInput.focus();
                    return;
                }
                // Tulis balik value yang sudah dinormalisasi
                npmInput.value = npm;
            }

            // Event bindings
            if (jurusanSelect) {
                jurusanSelect.addEventListener('change', applyPrefixFromJurusan);
            }
            if (npmInput) {
                npmInput.addEventListener('keydown', guardPrefixKeys);
                npmInput.addEventListener('input', lockPrefixOnInput);
            }
            if (form) {
                form.addEventListener('submit', validateBeforeSubmit);
            }

            // Inisialisasi saat load:
            // - Jika jurusan sudah terpilih (old value) dan NPM kosong, isi prefix
            // - Jika NPM ada tapi salah format, normalisasi
            (function init() {
                const prefix = getSelectedPrefix();
                if (prefix) {
                    if (!npmInput.value) {
                        npmInput.value = prefix;
                        placeCursorAtEnd(npmInput);
                    } else {
                        npmInput.value = npmInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 9);
                        // Pastikan tetap diawali prefix yang benar
                        if (!npmInput.value.startsWith(prefix)) {
                            const suffix = npmInput.value.replace(/^[A-Z0-9]{0,4}/, '');
                            npmInput.value = (prefix + suffix).slice(0,9);
                        }
                        placeCursorAtEnd(npmInput);
                    }
                } else {
                    // Tanpa jurusan: tetap normalisasi kalau user sudah isi NPM
                    if (npmInput.value) {
                        npmInput.value = npmInput.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 9);
                    }
                }
            })();
        });
    </script>
</body>
</html>
