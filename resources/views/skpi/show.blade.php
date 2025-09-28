<x-app-layout>
    <x-slot name="title">Detail SKPI</x-slot>
    
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Detail SKPI</h1>
                    <p class="text-gray-600 mt-2">{{ $skpi->nama_lengkap }} - {{ $skpi->npm }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($skpi->status === 'draft') bg-yellow-100 text-yellow-800
                        @elseif($skpi->status === 'submitted') bg-blue-100 text-blue-800
                        @elseif($skpi->status === 'approved') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($skpi->status) }}
                    </span>
                    <a href="{{ route('skpi.index') }}" class="btn-outline">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Informasi Pribadi -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-unib-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    Informasi Pribadi
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->nama_lengkap }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NPM</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->npm }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->tempat_lahir }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->tanggal_lahir->format('d F Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Akademik -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-unib-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                    </svg>
                    Informasi Akademik
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor Ijazah</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->nomor_ijazah }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Lulus</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->tanggal_lulus->format('d F Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Gelar</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->gelar }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Program Studi</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->program_studi }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jurusan</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->jurusan->nama_jurusan }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">IPK</label>
                        <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg font-semibold">{{ $skpi->ipk }}</p>
                    </div>
                </div>
            </div>

            <!-- Prestasi & Aktivitas -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teknik-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Prestasi & Aktivitas
                </h2>
                
                <div class="space-y-4">
                    @if($skpi->prestasi_akademik)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Prestasi Akademik</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->prestasi_akademik }}</p>
                        </div>
                    @endif
                    @if($skpi->prestasi_non_akademik)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Prestasi Non-Akademik</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->prestasi_non_akademik }}</p>
                        </div>
                    @endif
                    @if($skpi->organisasi)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Organisasi</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->organisasi }}</p>
                        </div>
                    @endif
                    @if($skpi->pengalaman_kerja)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pengalaman Kerja/Magang</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->pengalaman_kerja }}</p>
                        </div>
                    @endif
                    @if($skpi->sertifikat_kompetensi)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sertifikat Kompetensi</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->sertifikat_kompetensi }}</p>
                        </div>
                    @endif
                    @if($skpi->drive_link)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Link Google Drive</label>
                            <p class="mt-1 text-sm">
                                <a href="{{ $skpi->drive_link }}" target="_blank" rel="noopener" class="text-unib-blue-600 hover:underline">
                                    Buka Link Drive
                                </a>
                            </p>
                        </div>
                    @endif

                    @if($skpi->catatan_khusus)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan Khusus</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $skpi->catatan_khusus }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Dokumen Pendukung -->
            @if($skpi->documents && $skpi->documents->count() > 0)
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-unib-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Dokumen Pendukung
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($skpi->documents as $doc)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $doc->file_name }}</p>
                                <p class="text-xs text-gray-500">{{ $doc->file_size_human }}</p>
                            </div>
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-unib-blue-600 hover:underline text-sm">
                                Lihat
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Informasi Update -->
            <div class="card p-6 text-sm text-gray-500">
                <p>Dibuat: {{ $skpi->created_at->format('d M Y H:i') }}</p>
                <p>Terakhir diupdate: {{ $skpi->updated_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
