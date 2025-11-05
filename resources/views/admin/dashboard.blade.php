<x-app-layout>
    <x-slot name="title">Admin Dashboard</x-slot>

    {{-- Utility ringan untuk komponen --}}
    <style>
        .card{ @apply rounded-2xl border border-gray-200 bg-white shadow-sm; }
        .btn{ @apply inline-flex items-center justify-center font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-xl; }
        .btn-primary{ @apply btn bg-unib-blue-600 text-white hover:bg-unib-blue-700 focus:ring-unib-blue-300 px-4 py-2; }
        .btn-outline{ @apply btn border border-unib-blue-600 text-unib-blue-700 hover:bg-unib-blue-50 px-4 py-2; }
        .chip{ @apply inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold; }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-3">

        <!-- HERO (spacing & font seperti referensi) -->
        <div class="mb-4">
            <div class="bg-gradient-to-r from-unib-blue-600 to-unib-blue-700 rounded-3xl text-white shadow-sm">
                <div class="px-6 py-7 sm:px-8 sm:py-8">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                        <div class="sm:col-span-2">
                            <!-- Judul lebih besar & tebal, tracking rapat -->
                            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight leading-tight">
                                Dashboard Admin
                            </h2>

                            <!-- Subjudul sedikit lebih besar -->
                            <p class="text-blue-100 text-sm sm:text-base mt-2">
                                Selamat datang, {{ auth()->user()->name }}
                            </p>

                            <!-- Baris info, icon + teks rapat -->
                            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-[13px] sm:text-sm mt-4">
                                <div class="flex items-center opacity-95">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ ucfirst(auth()->user()->role) }}
                                </div>

                                @if(auth()->user()->jurusan)
                                <div class="flex items-center opacity-95">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ auth()->user()->jurusan->nama_jurusan }}
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Slot kanan dibiarkan kosong (di referensi untuk badge), tidak mengubah konten -->
                        <div class="flex sm:justify-end items-start">
                            {{-- tempatkan badge/aksi jika diperlukan --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STAT CARDS (font & jarak diseragamkan) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Pending -->
            <div class="card p-5">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[28px] leading-none font-extrabold tracking-tight">{{ $stats['pending'] }}</p>
                        <p class="text-[13px] text-gray-600 mt-1">Menunggu Review</p>
                    </div>
                </div>
            </div>

            <!-- Approved -->
            <div class="card p-5">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[28px] leading-none font-extrabold tracking-tight">{{ $stats['approved'] }}</p>
                        <p class="text-[13px] text-gray-600 mt-1">Disetujui</p>
                    </div>
                </div>
            </div>

            <!-- Rejected -->
            <div class="card p-5">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[28px] leading-none font-extrabold tracking-tight">{{ $stats['rejected'] }}</p>
                        <p class="text-[13px] text-gray-600 mt-1">Ditolak</p>
                    </div>
                </div>
            </div>

            <!-- Total -->
            <div class="card p-5">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[28px] leading-none font-extrabold tracking-tight">{{ $stats['total'] }}</p>
                        <p class="text-[13px] text-gray-600 mt-1">Total SKPI</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2-COLUMN -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Statistik link -->
            <div class="card p-5">
                <h3 class="text-[16px] font-semibold text-gray-900 mb-3 tracking-tight">Statistik</h3>

                <a href="{{ route('admin.skpi-list', ['status' => 'submitted']) }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50">
                    <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium text-[15px] text-gray-900 truncate tracking-tight">Pengajuan Menunggu</p>
                        <p class="text-[13px] text-gray-600">{{ $stats['pending'] }} SKPI menunggu review</p>
                    </div>
                </a>

                <a href="{{ route('admin.skpi-list', ['status' => 'approved']) }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium text-[15px] text-gray-900 truncate tracking-tight">Pengajuan Disetujui</p>
                        <p class="text-[13px] text-gray-600">{{ $stats['approved'] }} SKPI telah disetujui</p>
                    </div>
                </a>

                <a href="{{ route('admin.skpi-list') }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50">
                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium text-[15px] text-gray-900 truncate tracking-tight">Total Pengajuan</p>
                        <p class="text-[13px] text-gray-600">{{ $stats['total'] }} SKPI diajukan</p>
                    </div>
                </a>
            </div>

            <!-- Pengajuan terbaru -->
            <div class="card p-5">
                <h3 class="text-[16px] font-semibold text-gray-900 mb-3 tracking-tight">Pengajuan Terbaru</h3>

                @if($recentSubmissions->count() > 0)
                    <ul class="space-y-2">
                        @foreach($recentSubmissions as $submission)
                            <li class="p-3 rounded-xl border border-gray-200 bg-gray-50">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 bg-unib-blue-100 text-unib-blue-700 rounded-full flex items-center justify-center text-[15px] font-bold">
                                            {{ strtoupper(substr($submission->user->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[16px] font-medium text-gray-900 truncate tracking-tight">{{ $submission->nama_lengkap }}</p>
                                            <p class="text-[14px] text-gray-600 truncate">{{ $submission->nim }} • {{ $submission->jurusan->nama_jurusan }}</p>
                                            <p class="text-[14px] text-gray-500">{{ $submission->updated_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="shrink-0">
                                        <span class="chip"
                                              style="@switch($submission->status)
                                                  @case('approved') background:#DCFCE7; color:#166534; @break
                                                  @case('submitted') background:#DBEAFE; color:#1D4ED8; @break
                                                  @case('rejected') background:#FEE2E2; color:#B91C1C; @break
                                                  @default background:#E5E7EB; color:#111827;
                                              @endswitch">
                                            @switch($submission->status)
                                                @case('submitted')
                                                    Diajukan
                                                    @break
                                                @case('approved')
                                                    Disetujui
                                                    @break
                                                @case('rejected')
                                                    Ditolak
                                                    @break
                                                @default
                                                    {{ ucfirst($submission->status) }}
                                            @endswitch
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-6">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 text-[15px]">Belum ada pengajuan terbaru</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
