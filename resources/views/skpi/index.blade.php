<x-app-layout>
    <x-slot name="title">Dashboard SKPI</x-slot>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-unib-blue-600 to-unib-blue-700 rounded-2xl text-white p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">Dashboard SKPI</h1>
                        <p class="text-blue-100">Selamat datang, {{ auth()->user()->name }}</p>
                        <div class="flex items-center mt-4 space-x-4 text-sm">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                NPM: {{ auth()->user()->npm ?? '—' }}
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
                    <div class="text-right">
                        @if($skpiData)
                            <div class="mb-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($skpiData->status === 'draft') bg-yellow-100 text-yellow-800
                                    @elseif($skpiData->status === 'submitted') bg-blue-100 text-blue-800
                                    @elseif($skpiData->status === 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($skpiData->status) }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Peringatan nomor telepon belum ada (DISISIPKAN, desain tetap konsisten) --}}
        @if(empty(auth()->user()->phone))
            <div class="mb-6 rounded-2xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-yellow-900">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mt-0.5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold">Lengkapi Nomor Telepon Anda</p>
                        <p class="text-sm mt-1">
                            Kami belum menemukan nomor telepon pada profil Anda. Mohon lengkapi agar admin dapat menghubungi Anda jika diperlukan.
                        </p>
                        <div class="mt-3">
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium bg-yellow-600 text-white hover:bg-yellow-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 min-w-[140px] text-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Lengkapi Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(!$skpiData)
        <!-- Belum ada data SKPI -->
        <div class="card p-8 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Belum Ada Data SKPI</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                Anda belum memiliki data SKPI. Mulai buat SKPI Anda dengan mengisi informasi yang diperlukan.
            </p>
            <a href="{{ route('skpi.create') }}" class="btn-primary inline-flex items-center justify-center min-w-[160px]">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat SKPI Baru
            </a>
        </div>
        
        @else
        <!-- Ada data SKPI -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Status Card -->
            <div class="lg:col-span-1">
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status SKPI</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Status:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($skpiData->status === 'draft') bg-yellow-100 text-yellow-800
                                @elseif($skpiData->status === 'submitted') bg-blue-100 text-blue-800
                                @elseif($skpiData->status === 'approved') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($skpiData->status) }}
                            </span>
                        </div>
                        
                        @if($skpiData->reviewed_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Direview pada:</span>
                            <p class="text-sm font-medium">{{ $skpiData->reviewed_at->format('d M Y H:i') }}</p>
                        </div>
                        @endif
                        
                        @if($skpiData->approved_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Disetujui pada:</span>
                            <p class="text-sm font-medium">{{ $skpiData->approved_at->format('d M Y H:i') }}</p>
                        </div>
                        @endif
                        
                        @if($skpiData->catatan_reviewer)
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <span class="text-sm text-gray-600">Catatan Reviewer:</span>
                                <p class="text-sm bg-gray-50 p-3 rounded-lg mt-2">{{ $skpiData->catatan_reviewer }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-6 flex flex-col gap-3">
                        <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center sm:justify-center gap-3 lg:gap-4">
                            @if($skpiData->canBeEdited())
                                <a href="{{ route('skpi.edit', $skpiData) }}" class="btn-primary min-w-[140px] flex items-center justify-center transition-all duration-300 hover:shadow-xl hover:translate-y-[-2px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit SKPI
                                </a>
                            @endif
                            
                            @if($skpiData->status === 'approved')
                                <a href="{{ route('skpi.print', $skpiData) }}" target="_blank" class="btn-outline min-w-[140px] flex items-center justify-center transition-all duration-300 hover:shadow-xl hover:translate-y-[-2px] focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 rounded-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    Cetak SKPI
                                </a>
                            @endif
                            
                            <a href="{{ route('skpi.show', $skpiData) }}" class="btn-outline min-w-[140px] flex items-center justify-center transition-all duration-300 hover:shadow-xl hover:translate-y-[-2px] focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 rounded-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat Detail
                            </a>
                        </div>
                        
                        @if($skpiData->canBeSubmitted())
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex justify-center">
                                <form method="POST" action="{{ route('skpi.submit', $skpiData) }}" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin submit SKPI? Data tidak dapat diedit setelah disubmit.')">
                                    @csrf
                                    <button type="submit" class="btn-primary w-full sm:w-auto min-w-[160px] flex items-center justify-center transition-all duration-300 hover:shadow-xl hover:translate-y-[-2px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg border-2 border-blue-500 bg-blue-600 hover:bg-blue-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Submit untuk Review
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Data Preview -->
            <div class="lg:col-span-2">
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Ringkasan Data SKPI</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">Informasi Pribadi</h4>
                            <div class="space-y-2 text-sm">
                                <div><span class="text-gray-600">Nama:</span> {{ $skpiData->nama_lengkap }}</div>
                                <div><span class="text-gray-600">NPM:</span> {{ $skpiData->npm ?? auth()->user()->npm ?? '—' }}</div>
                                <div><span class="text-gray-600">Tempat, Tanggal Lahir:</span> {{ $skpiData->tempat_lahir }}, {{ $skpiData->tanggal_lahir->format('d M Y') }}</div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">Informasi Akademik</h4>
                            <div class="space-y-2 text-sm">
                                <div><span class="text-gray-600">Program Studi:</span> {{ $skpiData->program_studi }}</div>
                                <div><span class="text-gray-600">IPK:</span> {{ $skpiData->ipk }}</div>
                                <div><span class="text-gray-600">Tanggal Lulus:</span> {{ $skpiData->tanggal_lulus->format('d M Y') }}</div>
                            </div>
                        </div>
                        
                        @if($skpiData->prestasi_akademik)
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">Prestasi Akademik</h4>
                            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ Str::limit($skpiData->prestasi_akademik, 200) }}</p>
                        </div>
                        @endif
                        
                        @if($skpiData->prestasi_non_akademik)
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">Prestasi Non-Akademik</h4>
                            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ Str::limit($skpiData->prestasi_non_akademik, 200) }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            <p>Terakhir diupdate: {{ $skpiData->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Help Section -->
        <div class="mt-8">
            <div class="card p-6 bg-blue-50 border-l-4 border-blue-500">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informasi Penting</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Pastikan semua data yang diisi sudah benar sebelum submit</li>
                                <li>SKPI yang sudah disubmit tidak dapat diedit kecuali ditolak oleh reviewer</li>
                                <li>Proses review biasanya memakan waktu 3-5 hari kerja</li>
                                <li>Anda akan mendapat notifikasi via email ketika status SKPI berubah</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
