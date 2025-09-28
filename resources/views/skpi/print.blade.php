<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKPI - {{ $skpi->nama_lengkap }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { print-color-adjust: exact; }
            .no-print { display: none; }
            .page-break { page-break-before: always; }
        }
        .logo-unib { width: 80px; height: 80px; background: url('{{ asset('images/logounib.png') }}') center/cover no-repeat; }
    </style>
</head>
<body class="bg-white">
    <!-- Tombol Cetak -->
    <div class="no-print fixed top-4 right-4 z-50">
        <button onclick="window.print()" class="bg-unib-blue-600 hover:bg-unib-blue-700 text-white px-6 py-2 rounded-lg shadow-lg">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Cetak
        </button>
    </div>

    <div class="max-w-4xl mx-auto p-8 bg-white">
        <!-- Header -->
        <div class="text-center border-b-2 border-unib-blue-900 pb-6 mb-8">
            <div class="flex items-center justify-center mb-4">
                <div class="logo-unib mr-6"></div>
                <div class="text-left">
                    <h1 class="text-2xl font-bold text-unib-blue-900 mb-1">UNIVERSITAS BENGKULU</h1>
                    <h2 class="text-xl font-semibold text-teknik-orange-600">FAKULTAS TEKNIK</h2>
                    <p class="text-sm text-gray-600 mt-1">Jl. WR. Supratman, Kandang Limun, Bengkulu 38371</p>
                    <p class="text-sm text-gray-600">Telp: (0736) 344087 | Email: ft@unib.ac.id</p>
                </div>
            </div>
            <div class="mt-6">
                <h3 class="text-xl font-bold text-gray-800 uppercase tracking-wide">SURAT KETERANGAN PENDAMPING IJAZAH</h3>
                <h4 class="text-lg font-semibold text-gray-700">(DIPLOMA SUPPLEMENT)</h4>
            </div>
        </div>

        <!-- Konten -->
        <div class="space-y-8">
            <!-- 1. Informasi Pemegang Ijazah -->
            <div>
                <h4 class="text-lg font-bold text-unib-blue-900 border-b border-gray-300 pb-2 mb-4">
                    1. INFORMASI PEMEGANG IJAZAH
                </h4>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <div class="mb-3">
                            <span class="font-semibold">1.1 Nama Lengkap:</span>
                            <p class="text-gray-800">{{ $skpi->nama_lengkap }}</p>
                        </div>
                        <div class="mb-3">
                            <span class="font-semibold">1.2 NPM:</span>
                            <p class="text-gray-800">{{ $skpi->npm }}</p>
                        </div>
                        <div class="mb-3">
                            <span class="font-semibold">1.3 Tempat, Tanggal Lahir:</span>
                            <p class="text-gray-800">{{ $skpi->tempat_lahir }}, {{ $skpi->tanggal_lahir->format('d F Y') }}</p>
                        </div>
                    </div>
                    <div>
                        <div class="mb-3">
                            <span class="font-semibold">1.4 Tanggal Lulus:</span>
                            <p class="text-gray-800">{{ $skpi->tanggal_lulus->format('d F Y') }}</p>
                        </div>
                        <div class="mb-3">
                            <span class="font-semibold">1.5 Nomor Ijazah:</span>
                            <p class="text-gray-800">{{ $skpi->nomor_ijazah }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Informasi Kualifikasi -->
            <div>
                <h4 class="text-lg font-bold text-unib-blue-900 border-b border-gray-300 pb-2 mb-4">
                    2. INFORMASI KUALIFIKASI
                </h4>
                <div class="space-y-3">
                    <div>
                        <span class="font-semibold">2.1 Nama Kualifikasi:</span>
                        <p class="text-gray-800">{{ $skpi->gelar }} - {{ $skpi->program_studi }}</p>
                    </div>
                    <div>
                        <span class="font-semibold">2.2 Bidang Studi:</span>
                        <p class="text-gray-800">{{ $skpi->jurusan->nama_jurusan }}</p>
                    </div>
                    <div>
                        <span class="font-semibold">2.3 Nama dan Status Institusi:</span>
                        <p class="text-gray-800">Universitas Bengkulu - Perguruan Tinggi Negeri</p>
                    </div>
                    <div>
                        <span class="font-semibold">2.4 Institusi Penyelenggara Studi:</span>
                        <p class="text-gray-800">Fakultas Teknik, Universitas Bengkulu</p>
                    </div>
                    <div>
                        <span class="font-semibold">2.5 Bahasa Pengantar Perkuliahan:</span>
                        <p class="text-gray-800">Bahasa Indonesia</p>
                    </div>
                </div>
            </div>

            <!-- 3. Informasi Tingkat Kualifikasi -->
            <div>
                <h4 class="text-lg font-bold text-unib-blue-900 border-b border-gray-300 pb-2 mb-4">
                    3. INFORMASI TINGKAT KUALIFIKASI
                </h4>
                <div class="space-y-3">
                    <div>
                        <span class="font-semibold">3.1 Tingkat Kualifikasi:</span>
                        <p class="text-gray-800">Strata 1 (S1) - Sarjana</p>
                    </div>
                    <div>
                        <span class="font-semibold">3.2 Lama Studi Resmi:</span>
                        <p class="text-gray-800">8 semester / 4 tahun</p>
                    </div>
                    <div>
                        <span class="font-semibold">3.3 Persyaratan Penerimaan:</span>
                        <p class="text-gray-800">Lulusan SMA/SMK/MA sederajat dan lulus seleksi masuk PT</p>
                    </div>
                </div>
            </div>

            <!-- 4. Isi Program dan Hasil -->
            <div>
                <h4 class="text-lg font-bold text-unib-blue-900 border-b border-gray-300 pb-2 mb-4">
                    4. INFORMASI ISI PROGRAM DAN HASIL YANG DICAPAI
                </h4>
                <div class="space-y-3">
                    <div><span class="font-semibold">4.1 Mode Studi:</span> <p class="text-gray-800">Penuh waktu (full time)</p></div>
                    <div><span class="font-semibold">4.2 Persyaratan Program:</span> <p class="text-gray-800">Minimal 144 SKS, IPK ≥ 2.00</p></div>
                    <div><span class="font-semibold">4.3 Rincian Program:</span> <p class="text-gray-800">Integrasi teori & praktik bidang {{ $skpi->jurusan->nama_jurusan }}</p></div>
                    <div><span class="font-semibold">4.4 IPK:</span> <p class="text-gray-800 font-bold text-lg">{{ $skpi->ipk }}</p></div>
                    <div><span class="font-semibold">4.5 Sistem Penilaian:</span> <p class="text-gray-800">Skala 0.00–4.00</p></div>
                </div>
            </div>

            <!-- 5–9 (tetap sama dengan template kamu, tidak ada NPM/NIM di sini) -->
            {{-- ... Seluruh bagian lain tetap, tidak perlu perubahan selain jika ada referensi NIM -> NPM --}}
        </div>

        <!-- Footer -->
        <div class="mt-12 pt-6 border-t-2 border-unib-blue-900 text-center">
            <p class="text-sm text-gray-600">Dokumen ini dikeluarkan secara resmi oleh Fakultas Teknik Universitas Bengkulu</p>
            <p class="text-sm text-gray-600 mt-2">Tanggal Cetak: {{ now()->format('d F Y H:i') }} WIB</p>
            <div class="mt-4 text-xs text-gray-500">
                <p>SKPI ID: SKPI-{{ str_pad($skpi->id, 6, '0', STR_PAD_LEFT) }}-{{ $skpi->created_at->format('Y') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
