<x-app-layout>
    <x-slot name="title">Dashboard Admin</x-slot>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-unib-blue-600 to-unib-blue-700 rounded-2xl text-white p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">Dashboard Admin</h1>
                        <p class="text-blue-100">Selamat datang, {{ auth()->user()->name }}</p>
                        <div class="flex items-center mt-4 space-x-4 text-sm">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ ucfirst(auth()->user()->role) }}
                            </div>
                            @if(auth()->user()->jurusan)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ auth()->user()->jurusan->nama_jurusan }}
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <a href="{{ route('admin.skpi-list') }}" class="btn-primary inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            Review SKPI
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                        <p class="text-sm text-gray-600">Menunggu Review</p>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['approved'] }}</p>
                        <p class="text-sm text-gray-600">Disetujui</p>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['rejected'] }}</p>
                        <p class="text-sm text-gray-600">Ditolak</p>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                        <p class="text-sm text-gray-600">Total SKPI</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Quick Actions -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.skpi-list') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Review SKPI</p>
                            <p class="text-sm text-gray-600">Lihat dan review semua pengajuan SKPI</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.skpi-list', ['status' => 'submitted']) }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="p-2 bg-blue-100 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">SKPI Menunggu</p>
                            <p class="text-sm text-gray-600">{{ $stats['pending'] }} SKPI menunggu review</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.skpi-list', ['status' => 'approved']) }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="p-2 bg-green-100 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">SKPI Disetujui</p>
                            <p class="text-sm text-gray-600">{{ $stats['approved'] }} SKPI telah disetujui</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Submissions -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengajuan Terbaru</h3>
                @if($recentSubmissions->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentSubmissions as $submission)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-unib-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-sm font-medium text-unib-blue-600">
                                        {{ substr($submission->user->name, 0, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $submission->nama_lengkap }}</p>
                                    <p class="text-xs text-gray-600">{{ $submission->nim }} - {{ $submission->jurusan->nama_jurusan }}</p>
                                    <p class="text-xs text-gray-500">{{ $submission->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($submission->status) }}
                                </span>
                                <a href="{{ route('admin.review-skpi', $submission) }}" class="text-unib-blue-600 hover:text-unib-blue-800 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.skpi-list') }}" class="text-sm text-unib-blue-600 hover:text-unib-blue-800 inline-flex items-center">
                            <span>Lihat semua</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500">Belum ada pengajuan terbaru</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>