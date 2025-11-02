<x-app-layout>
    <x-slot name="title">Dashboard Admin</x-slot>

    {{-- OPTIONAL: style ringan untuk util kelas custom (kalau sudah ada di layout-mu, bagian ini bisa dihapus) --}}
    <style>
        .card{ @apply rounded-2xl border border-gray-200 bg-white shadow-sm; }
        .btn{ @apply inline-flex items-center justify-center font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-xl; }
        .btn-primary{ @apply btn bg-unib-blue-600 text-white hover:bg-unib-blue-700 focus:ring-unib-blue-300 px-4 py-2; }
        .btn-outline{ @apply btn border border-unib-blue-600 text-unib-blue-700 hover:bg-unib-blue-50 px-4 py-2; }
        .chip{ @apply inline-flex items-center px-2 py-1 rounded-full text-xs font-medium; }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- ACTION BAR TOP -->
        <div class="pt-6 mb-6">
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">Dashboard Superadmin</h1>
                    <p class="text-gray-600">Ringkasan sistem & aktivitas terbaru SKPI</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.users') }}" class="btn-outline">Kelola Users</a>
                    <a href="{{ route('admin.jurusan') }}" class="btn-outline">Kelola Jurusan</a>
                    <a href="{{ route('admin.skpi-list') }}" class="btn-primary">Semua SKPI</a>
                </div>
            </div>
        </div>

        <!-- HERO -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-unib-blue-600 to-unib-blue-700 rounded-2xl text-white">
                <div class="p-6 sm:p-8">
                    <div class="grid gap-4 sm:gap-6 grid-cols-1 sm:grid-cols-3">
                        <div class="sm:col-span-2">
                            <h2 class="text-xl sm:text-2xl font-bold mb-2">Dashboard Admin</h2>
                            <p class="text-blue-100">Selamat datang, {{ auth()->user()->name }}</p>
                            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm mt-4">
                                <div class="flex items-center opacity-95">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ ucfirst(auth()->user()->role) }}
                                </div>
                                @if(auth()->user()->jurusan)
                                <div class="flex items-center opacity-95">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ auth()->user()->jurusan->nama_jurusan }}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex sm:justify-end sm:items-start">
                            <a href="{{ route('admin.skpi-list') }}" class="btn-primary w-full sm:w-auto">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                Review SKPI
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Pending -->
            <div class="card p-5 sm:p-6">
                <div class="flex items-start gap-4 h-full">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col justify-between min-h-[56px]">
                        <div>
                            <p class="text-2xl font-bold text-gray-900 leading-none">{{ $stats['pending'] }}</p>
                            <p class="text-sm text-gray-600 mt-1">Menunggu Review</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved -->
            <div class="card p-5 sm:p-6">
                <div class="flex items-start gap-4 h-full">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col justify-between min-h-[56px]">
                        <div>
                            <p class="text-2xl font-bold text-gray-900 leading-none">{{ $stats['approved'] }}</p>
                            <p class="text-sm text-gray-600 mt-1">Disetujui</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rejected -->
            <div class="card p-5 sm:p-6">
                <div class="flex items-start gap-4 h-full">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col justify-between min-h-[56px]">
                        <div>
                            <p class="text-2xl font-bold text-gray-900 leading-none">{{ $stats['rejected'] }}</p>
                            <p class="text-sm text-gray-600 mt-1">Ditolak</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total -->
            <div class="card p-5 sm:p-6">
                <div class="flex items-start gap-4 h-full">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col justify-between min-h-[56px]">
                        <div>
                            <p class="text-2xl font-bold text-gray-900 leading-none">{{ $stats['total'] }}</p>
                            <p class="text-sm text-gray-600 mt-1">Total SKPI</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2-COLUMN: Aksi Cepat + Pengajuan Terbaru -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Quick Actions -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.skpi-list') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate">Review SKPI</p>
                            <p class="text-sm text-gray-600">Lihat dan review semua pengajuan SKPI</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.skpi-list', ['status' => 'submitted']) }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="p-2 bg-blue-100 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate">SKPI Menunggu</p>
                            <p class="text-sm text-gray-600">{{ $stats['pending'] }} SKPI menunggu review</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.skpi-list', ['status' => 'approved']) }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="p-2 bg-green-100 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate">SKPI Disetujui</p>
                            <p class="text-sm text-gray-600">{{ $stats['approved'] }} SKPI telah disetujui</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Submissions -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengajuan Terbaru</h3>

                @if($recentSubmissions->count() > 0)
                    <ul class="space-y-3">
                        @foreach($recentSubmissions as $submission)
                            <li class="p-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 bg-unib-blue-100 text-unib-blue-700 rounded-full flex items-center justify-center text-sm font-semibold">
                                            {{ strtoupper(substr($submission->user->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $submission->nama_lengkap }}</p>
                                            <p class="text-xs text-gray-600 truncate">{{ $submission->nim }} • {{ $submission->jurusan->nama_jurusan }}</p>
                                            <p class="text-xs text-gray-500">{{ $submission->updated_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="chip"
                                              style="@switch($submission->status)
                                                  @case('approved') @default background: #DCFCE7; color: #166534; @break
                                                  @case('submitted') background:#DBEAFE; color:#1D4ED8; @break
                                                  @case('rejected') background:#FEE2E2; color:#B91C1C; @break
                                              @endswitch">
                                            {{ ucfirst($submission->status) }}
                                        </span>
                                        <a href="{{ route('admin.review-skpi', $submission) }}" class="text-unib-blue-600 hover:text-unib-blue-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.skpi-list') }}" class="text-sm text-unib-blue-700 hover:text-unib-blue-900 inline-flex items-center">
                            <span>Lihat semua</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @else
                    <div class="text-center py-10">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500">Belum ada pengajuan terbaru</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
