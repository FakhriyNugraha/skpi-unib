<x-app-layout>
    <x-slot name="title">Review SKPI</x-slot>
    
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Review SKPI</h1>
                    <p class="text-gray-600 mt-2">{{ $skpi->nama_lengkap }} - {{ $skpi->npm }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($skpi->status === 'draft') bg-yellow-100 text-yellow-800
                        @elseif($skpi->status === 'submitted') bg-blue-100 text-blue-800
                        @elseif($skpi->status === 'approved') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($skpi->status) }}
                    </span>
                    <a href="{{ route('admin.skpi-list') }}" class="btn-outline">
                        Kembali
                    </a>
                </div>
            </div>

            @if($skpi->catatan_khusus)
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Khusus</label>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $skpi->catatan_khusus }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- SKPI Data (2/3 width) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Informasi Pribadi -->
                <div class="card p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-unib-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        Informasi Pribadi
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $skpi->nama_lengkap }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NPM</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $skpi->npm }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $skpi->tempat_lahir }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $skpi->tanggal_lahir->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Akademik -->
                <div class="card p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-unib-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01-.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                        Informasi Akademik
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Program Studi</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $skpi->program_studi }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jurusan</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $skpi->jurusan->nama_jurusan }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">IPK</label>
                            <p class="mt-1 text-sm text-gray-900 font-bold text-lg">{{ $skpi->ipk }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Lulus</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $skpi->tanggal_lulus->format('d F Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Ijazah</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $skpi->nomor_ijazah }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gelar</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $skpi->gelar }}</p>
                        </div>
                    </div>
                </div>

                <!-- Prestasi dan Aktivitas -->
                @if($skpi->prestasi_akademik || $skpi->prestasi_non_akademik || $skpi->organisasi || $skpi->pengalaman_kerja || $skpi->sertifikat_kompetensi || $skpi->catatan_khusus)
                <div class="card p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-teknik-orange-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Prestasi dan Aktivitas
                    </h2>
                    
                    <div class="space-y-6">
                        @if($skpi->prestasi_akademik)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prestasi Akademik</label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $skpi->prestasi_akademik }}</p>
                            </div>
                        </div>
                        @endif

                        @if($skpi->prestasi_non_akademik)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prestasi Non-Akademik</label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $skpi->prestasi_non_akademik }}</p>
                            </div>
                        </div>
                        @endif

                        @if($skpi->organisasi)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pengalaman Organisasi</label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $skpi->organisasi }}</p>
                            </div>
                        </div>
                        @endif

                        @if($skpi->pengalaman_kerja)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pengalaman Kerja/Magang</label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $skpi->pengalaman_kerja }}</p>
                            </div>
                        </div>
                        @endif

                        @if($skpi->sertifikat_kompetensi)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sertifikat Kompetensi</label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $skpi->sertifikat_kompetensi }}</p>
                            </div>
                        </div>
                        @endif

                        @if($skpi->catatan_khusus)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Khusus</label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $skpi->catatan_khusus }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($skpi->drive_link)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dokumen</label>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ $skpi->drive_link }}" target="_blank" class="btn-primary text-sm px-4 py-2 inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Lihat Sertifikat
                                    </a>
                                    
                                    <!-- Button "Periksa" warna hijau -->
                                    <button 
                                        type="button" 
                                        id="checkDriveBtn"
                                        class="btn-primary bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 inline-flex items-center"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            class="w-4 h-4 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.6"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <!-- Pin chip atas -->
                                            <path d="M9 2.5V4.5" />
                                            <path d="M12 2.5V4.5" />
                                            <path d="M15 2.5V4.5" />

                                            <!-- Pin chip bawah -->
                                            <path d="M9 19.5V21.5" />
                                            <path d="M12 19.5V21.5" />
                                            <path d="M15 19.5V21.5" />

                                            <!-- Pin chip kiri -->
                                            <path d="M3 9H5" />
                                            <path d="M3 12H5" />
                                            <path d="M3 15H5" />

                                            <!-- Pin chip kanan -->
                                            <path d="M19 9H21" />
                                            <path d="M19 12H21" />
                                            <path d="M19 15H21" />

                                            <!-- Body chip -->
                                            <rect x="6" y="5" width="12" height="14" rx="2" />

                                            <!-- Otak di dalam chip -->
                                            <path d="
                                                M11 8.5
                                                C11 7.7 11.6 7 12.4 7
                                                C13.2 7 13.8 7.7 13.8 8.5
                                                C14.6 8.6 15.2 9.3 15.2 10.1
                                                C15.2 10.6 15 11 14.7 11.3
                                                C15.1 11.7 15.3 12.2 15.3 12.8
                                                C15.3 13.8 14.5 14.6 13.5 14.6
                                                L13.1 14.6
                                                C12.9 15.1 12.5 15.4 12 15.4
                                                C11.5 15.4 11.1 15.1 10.9 14.6
                                                L10.5 14.6
                                                C9.5 14.6 8.7 13.8 8.7 12.8
                                                C8.7 12.2 8.9 11.7 9.3 11.3
                                                C9 11 8.8 10.6 8.8 10.1
                                                C8.8 9.3 9.4 8.6 10.2 8.5
                                                C10.2 7.7 10.7 7 11.5 7
                                                C11.6 7 11.8 7 11.9 7.1
                                            " />
                                        </svg>
                                        <span>Periksa</span>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Scan Results -->
                            <div id="scanResults" class="mt-3 hidden">
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Hasil Pemeriksaan:</h4>
                                    <div id="scanResultsContent" class="text-sm text-gray-700"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Review Panel (1/3 width) -->
            <div class="space-y-6">
                <!-- Review Form -->
                @if($skpi->status === 'submitted')
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Review SKPI</h3>
                    
                    <form method="POST" action="{{ route('admin.approve-skpi', $skpi) }}">
                        @csrf
                        
                        <div class="mb-6">
                            <label for="catatan_reviewer" class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan Review
                            </label>
                            <textarea name="catatan_reviewer" id="catatan_reviewer" rows="4" 
                                      class="input-field w-full @error('catatan_reviewer') border-red-500 @enderror"
                                      placeholder="Berikan catatan untuk mahasiswa...">{{ old('catatan_reviewer') }}</textarea>
                            @error('catatan_reviewer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="space-y-3">
                            <button type="button" id="approveBtn"
                                    class="w-full bg-green-600 text-white px-4 py-3 rounded-lg font-medium hover:bg-green-700 transition-colors flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Setujui SKPI
                            </button>
                            
                            <button type="button" id="rejectBtn"
                                    class="w-full bg-red-600 text-white px-4 py-3 rounded-lg font-medium hover:bg-red-700 transition-colors flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Tolak SKPI
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <!-- Status Information -->
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Status</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Status Saat Ini:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($skpi->status === 'draft') bg-yellow-100 text-yellow-800
                                @elseif($skpi->status === 'submitted') bg-blue-100 text-blue-800
                                @elseif($skpi->status === 'approved') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($skpi->status) }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Dibuat:</span>
                            <span class="text-sm text-gray-900">{{ $skpi->created_at->format('d M Y H:i') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Disubmit:</span>
                            <span class="text-sm text-gray-900">{{ $skpi->updated_at->format('d M Y H:i') }}</span>
                        </div>
                        
                        @if($skpi->reviewed_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Direview:</span>
                            <span class="text-sm text-gray-900">{{ $skpi->reviewed_at->format('d M Y H:i') }}</span>
                        </div>
                        @endif
                        
                        @if($skpi->reviewer)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Reviewer:</span>
                            <span class="text-sm text-gray-900">{{ $skpi->reviewer->name }}</span>
                        </div>
                        @endif
                        
                        @if($skpi->catatan_reviewer)
                        <div class="pt-4 border-t border-gray-200">
                            <span class="text-sm font-medium text-gray-700">Catatan Review Sebelumnya:</span>
                            <p class="text-sm text-gray-600 mt-2 bg-gray-50 p-3 rounded-lg whitespace-pre-wrap">{{ $skpi->catatan_reviewer }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Student Information -->
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Mahasiswa</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-unib-blue-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-sm font-medium text-unib-blue-600">
                                    {{ substr($skpi->user->name, 0, 2) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $skpi->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $skpi->user->email }}</p>
                            </div>
                        </div>
                        
                        @if($skpi->user->phone)
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            {{ $skpi->user->phone }}
                        </div>
                        @endif
                        
                        @if($skpi->user->address)
                        <div class="flex items-start text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ $skpi->user->address }}</span>
                        </div>
                        @endif
                    </div>
                </div>


            </div>
        </div>
    </div>

    <!-- Approve/Reject Confirmation Modal -->
    <div id="reviewModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div id="reviewModalOverlay" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-gray-200">
                <div class="p-6">
                    <h3 id="reviewModalTitle" class="text-lg font-semibold text-gray-900 text-center">
                        Konfirmasi Aksi
                    </h3>
                    <p id="reviewModalContent" class="mt-3 text-sm text-gray-600 text-center">
                        Apakah Anda yakin ingin melakukan aksi ini?
                    </p>
                    <div class="mt-6 flex flex-col sm:flex-row justify-center sm:gap-3 gap-2">
                        <button type="button" id="cancelReview"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batal
                        </button>
                        <button type="button" id="confirmReview"
                                class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span id="confirmReviewText">Konfirmasi</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const approveBtn = document.getElementById('approveBtn');
            const rejectBtn = document.getElementById('rejectBtn');
            const reviewModal = document.getElementById('reviewModal');
            const reviewModalOverlay = document.getElementById('reviewModalOverlay');
            const cancelReview = document.getElementById('cancelReview');
            const confirmReview = document.getElementById('confirmReview');
            const reviewModalTitle = document.getElementById('reviewModalTitle');
            const reviewModalContent = document.getElementById('reviewModalContent');
            const confirmReviewText = document.getElementById('confirmReviewText');
            
            let currentAction = null;
            
            // Approve button event handler
            approveBtn.addEventListener('click', function() {
                currentAction = 'approve';
                reviewModalTitle.textContent = 'Setujui SKPI';
                reviewModalContent.textContent = 'Apakah Anda yakin ingin menyetujui SKPI ini? Tindakan ini tidak dapat dibatalkan.';
                confirmReviewText.textContent = 'Setujui';
                confirmReview.className = 'px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 shadow-sm inline-flex items-center justify-center';
                
                reviewModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                confirmReview.focus();
            });
            
            // Reject button event handler
            rejectBtn.addEventListener('click', function() {
                currentAction = 'reject';
                reviewModalTitle.textContent = 'Tolak SKPI';
                reviewModalContent.textContent = 'Apakah Anda yakin ingin menolak SKPI ini? Pastikan Anda telah memberikan catatan yang jelas.';
                confirmReviewText.textContent = 'Tolak';
                confirmReview.className = 'px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm inline-flex items-center justify-center';
                
                reviewModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                confirmReview.focus();
            });
            
            // Close modal
            function closeReviewModal() {
                reviewModal.classList.add('hidden');
                document.body.style.overflow = '';
                currentAction = null;
            }
            
            // Confirm action
            confirmReview.addEventListener('click', function() {
                if (currentAction) {
                    // Create and submit form dynamically
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.approve-skpi', $skpi) }}';
                    form.style.display = 'none';
                    
                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfInput);
                    
                    // Add action value
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = currentAction;
                    form.appendChild(actionInput);
                    
                    // Add catatan_reviewer if exists
                    const textarea = document.getElementById('catatan_reviewer');
                    if (textarea && textarea.value) {
                        const catatanInput = document.createElement('input');
                        catatanInput.type = 'hidden';
                        catatanInput.name = 'catatan_reviewer';
                        catatanInput.value = textarea.value;
                        form.appendChild(catatanInput);
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
            
            // Event listeners for closing modal
            reviewModalOverlay.addEventListener('click', closeReviewModal);
            cancelReview.addEventListener('click', closeReviewModal);
            
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !reviewModal.classList.contains('hidden')) closeReviewModal();
            });
        });
        
        // Handle Drive Link Check
        const checkDriveBtn = document.getElementById('checkDriveBtn');
        if (checkDriveBtn) {
            checkDriveBtn.addEventListener('click', function() {
                const scanResults = document.getElementById('scanResults');
                const scanResultsContent = document.getElementById('scanResultsContent');
                
                // Show loading state
                scanResultsContent.innerHTML = 'Memeriksa isi folder Google Drive...';
                scanResults.classList.remove('hidden');
                
                // Make AJAX request to backend
                fetch(`{{ route('admin.verify-drive', $skpi) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const results = data.data;
                        
                        let html = '<div class="space-y-4">';
                        
                        // Overall validation score
                        if (results.validation_score) {
                            let scoreColor = 'text-gray-700 bg-gray-100';
                            if (results.validation_score.status === 'excellent') scoreColor = 'text-green-700 bg-green-100';
                            else if (results.validation_score.status === 'good') scoreColor = 'text-blue-700 bg-blue-100';
                            else if (results.validation_score.status === 'average') scoreColor = 'text-yellow-700 bg-yellow-100';
                            else if (results.validation_score.status === 'below_average') scoreColor = 'text-orange-700 bg-orange-100';
                            else if (results.validation_score.status === 'poor') scoreColor = 'text-red-700 bg-red-100';
                            
                            html += `<div class="p-4 rounded-lg border ${scoreColor.replace('text', 'border')}">`;
                            html += `<h3 class="font-bold text-lg ${scoreColor.split(' ')[0]}">Tingkat Validasi: ${results.validation_score.percentage}%</h3>`;
                            html += `<p class="${scoreColor.split(' ')[0]}">${results.validation_score.message}</p>`;
                            html += '</div>';
                        }
                        
                        // Detailed document matching with percentages and content analysis
                        if (results.validation_score && results.validation_score.details) {
                            html += '<div class="mt-4 space-y-3">';
                            html += '<h4 class="font-medium text-gray-800">Kesesuaian Dokumen per Jenis (Berdasarkan Analisis Isi Konten):</h4>';
                            html += '<p class="text-xs text-gray-600">Kesesuaian dihitung berdasarkan analisis isi konten dokumen terhadap data yang diinput mahasiswa</p>';
                            
                            results.validation_score.details.forEach(detail => {
                                let statusColor = 'text-gray-600 bg-gray-100';
                                let confidenceColor = 'text-gray-500';
                                if (detail.status === 'tersedia') {
                                    statusColor = 'text-green-600 bg-green-100';
                                    // Color based on integrity level
                                    if (detail.confidence >= 80) confidenceColor = 'text-green-600';
                                    else if (detail.confidence >= 60) confidenceColor = 'text-blue-600';
                                    else if (detail.confidence >= 40) confidenceColor = 'text-yellow-600';
                                    else confidenceColor = 'text-red-600';
                                } else {
                                    statusColor = 'text-red-600 bg-red-100';
                                }
                                
                                html += '<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">';
                                html += `<span class="text-sm font-medium">${detail.type}</span>`;
                                html += `<div class="flex items-center space-x-2">`;
                                html += `<span class="px-2 py-1 rounded text-xs ${statusColor}">Kesesuaian: ${detail.percentage}%</span>`;
                                if (detail.status === 'tersedia') {
                                    html += `<span class="px-2 py-1 rounded text-xs ${confidenceColor}" title="Tingkat integritas berdasarkan analisis isi konten">Integritas: ${detail.confidence}%</span>`;
                                }
                                html += `<span class="text-xs ${detail.status === 'tersedia' ? 'text-green-600' : 'text-red-600'}">${detail.status}</span>`;
                                if (detail.file_name) {
                                    html += `<span class="text-xs text-gray-500 ml-2 truncate max-w-xs" title="${detail.file_name}">${detail.file_name.length > 30 ? detail.file_name.substring(0, 30) + '...' : ''}</span>`;
                                }
                                html += '</div>';
                                html += '</div>';
                            });
                            
                            html += '</div>';
                        }
                        
                        // Scan summary
                        if (results.scan_summary) {
                            html += '<div class="bg-blue-50 p-3 rounded-lg mt-4">';
                            html += '<h4 class="font-medium text-blue-800">Ringkasan Pemindaian:</h4>';
                            html += `<p class="text-sm">Total dokumen ditemukan: ${results.total_files_scanned}</p>`;
                            html += `<p class="text-sm">PDF: ${results.scan_summary.pdf_count} | Gambar: ${results.scan_summary.image_count} | Dokumen: ${results.scan_summary.document_count}</p>`;
                            html += '</div>';
                        }
                        
                        // Missing items
                        if (results.missing_items && results.missing_items.length > 0) {
                            html += '<div class="border-l-4 border-red-500 pl-4 mt-4">';
                            html += '<h4 class="font-medium text-red-700">Kekurangan Dokumen:</h4>';
                            results.missing_items.forEach(item => {
                                html += `<p class="text-sm text-red-600 mt-1">• ${item.type}: ${item.content.substring(0, 100)}${item.content.length > 100 ? '...' : ''}</p>`;
                            });
                            html += '</div>';
                        }
                        
                        // Matched items - only show document types without similarity scores
                        if (results.matched_items && results.matched_items.length > 0) {
                            html += '<div class="border-l-4 border-green-500 pl-4 mt-4">';
                            html += '<h4 class="font-medium text-green-700">Dokumen Terpenuhi:</h4>';
                            // Only show document types, not file names and similarity scores
                            const uniqueMatchedTypes = [...new Set(results.matched_items.map(item => item.type))];
                            uniqueMatchedTypes.forEach(type => {
                                html += `<p class="text-sm text-green-600 mt-1">• ${type}</p>`;
                            });
                            html += '</div>';
                        }
                        
                        // Extra items
                        if (results.extra_items && results.extra_items.length > 0) {
                            html += '<div class="border-l-4 border-yellow-500 pl-4 mt-4">';
                            html += '<h4 class="font-medium text-yellow-700">Dokumen Tambahan:</h4>';
                            results.extra_items.forEach(item => {
                                html += `<p class="text-sm text-yellow-600 mt-1">• ${item.file_name} (${item.mimeType})</p>`;
                            });
                            html += '</div>';
                        }
                        
                        html += '</div>';
                        scanResultsContent.innerHTML = html;
                    } else {
                        scanResultsContent.innerHTML = `<span class="text-red-600">Gagal: ${data.message}</span>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (error.message && error.message.includes('Class \\"Google\\\\Client\\" not found')) {
                        scanResultsContent.innerHTML = '<span class="text-red-600">Google API Client belum diinstall. Jalankan: composer require google/apiclient</span>';
                    } else {
                        scanResultsContent.innerHTML = '<span class="text-red-600">Terjadi kesalahan saat memeriksa folder Google Drive: ' + error.message + '</span>';
                    }
                });
            });
        }
    </script>
</x-app-layout>
