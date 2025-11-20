<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logounib.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logounib.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logounib.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .logo-unib-guest {
                width: 90px;
                height: 90px;
                background-image: url('/images/logounib.png'); 
                background-size: contain;
                background-repeat: no-repeat;
                background-position: center;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-2">
            
            <div class="hidden lg:flex flex-col items-center justify-center bg-gradient-to-br from-blue-800 to-blue-600 p-12 text-white text-center">
                <a href="/">
                    <div class="logo-unib-guest mb-6"></div>
                </a>
                <h1 class="text-4xl font-bold">Sistem Informasi SKPI</h1>
                <p class="text-xl mt-2 text-blue-200">Fakultas Teknik Universitas Bengkulu</p>
                <p class="mt-8 max-w-md text-blue-300">Platform digital modern untuk dokumentasi prestasi akademik dan non-akademik mahasiswa.</p>
            </div>

            <div class="flex flex-col items-center justify-center bg-gray-100 p-6 sm:p-8">
                <div class="lg:hidden mb-6">
                    <a href="/" class="flex items-center space-x-3 text-blue-800"> {{-- Menyesuaikan warna teks --}}
                         <div class="logo-unib-guest w-14 h-14"></div> <span class="text-2xl font-bold">SKPI UNIB</span>
                    </a>
                </div>

                <div class="w-full sm:max-w-md bg-white px-6 py-8 shadow-xl rounded-2xl">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>