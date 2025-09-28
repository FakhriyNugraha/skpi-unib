<x-app-layout>
    <x-slot name="title">{{ $existingSkpi ? 'Edit' : 'Buat' }} SKPI</x-slot>
    
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="driveValidator()">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $existingSkpi ? 'Edit' : 'Buat' }} SKPI</h1>
                    <p class="text-gray-600 mt-2">Lengkapi semua informasi yang diperlukan untuk SKPI Anda</p>
                </div>
                <a href="{{ route('skpi.index') }}" class="btn-outline">
                    Kembali
                </a>
            </div>
        </div>

        <form method="POST"
              action="{{ $existingSkpi ? route('skpi.update', $existingSkpi) : route('skpi.store') }}"
              class="space-y-8" @submit.prevent="handleSubmit($event)">
            @csrf
            @if($existingSkpi)
                @method('PUT')
            @endif

            <!-- Informasi Pribadi -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-unib-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    Informasi Pribadi
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Lengkap read-only dari DB -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-900 border border-gray-200">
                            {{ old('nama_lengkap', $existingSkpi->nama_lengkap ?? auth()->user()->name) }}
                        </div>
                        <input type="hidden" name="nama_lengkap" value="{{ old('nama_lengkap', $existingSkpi->nama_lengkap ?? auth()->user()->name) }}">
                    </div>

                    <div>
                        <label for="npm" class="block text-sm font-medium text-gray-700 mb-2">NPM *</label>
                        <input type="text" name="npm" id="npm"
                               class="input-field @error('npm') border-red-500 @enderror" 
                               value="{{ old('npm', $existingSkpi->nim ?? (auth()->user()->npm ?? '')) }}" required>
                        @error('npm')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir *</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir"
                               class="input-field @error('tempat_lahir') border-red-500 @enderror" 
                               value="{{ old('tempat_lahir', $existingSkpi->tempat_lahir ?? '') }}" required>
                        @error('tempat_lahir')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir *</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                               class="input-field @error('tanggal_lahir') border-red-500 @enderror" 
                               value="{{ old('tanggal_lahir', isset($existingSkpi->tanggal_lahir) ? $existingSkpi->tanggal_lahir->format('Y-m-d') : '') }}" required>
                        @error('tanggal_lahir')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                        <label for="nomor_ijazah" class="block text-sm font-medium text-gray-700 mb-2">Nomor Ijazah *</label>
                        <input type="text" name="nomor_ijazah" id="nomor_ijazah"
                               class="input-field @error('nomor_ijazah') border-red-500 @enderror" 
                               value="{{ old('nomor_ijazah', $existingSkpi->nomor_ijazah ?? '') }}" required>
                        @error('nomor_ijazah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_lulus" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lulus *</label>
                        <input type="date" name="tanggal_lulus" id="tanggal_lulus"
                               class="input-field @error('tanggal_lulus') border-red-500 @enderror" 
                               value="{{ old('tanggal_lulus', isset($existingSkpi->tanggal_lulus) ? $existingSkpi->tanggal_lulus->format('Y-m-d') : '') }}" required>
                        @error('tanggal_lulus')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gelar" class="block text-sm font-medium text-gray-700 mb-2">Gelar *</label>
                        <input type="text" name="gelar" id="gelar"
                               class="input-field @error('gelar') border-red-500 @enderror" 
                               value="{{ old('gelar', $existingSkpi->gelar ?? '') }}" placeholder="contoh: S.Kom, S.T, S.Ars" required>
                        @error('gelar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dropdown Jurusan (label jadi Program Studi) -->
                    <div>
                        <label for="jurusan_id" class="block text-sm font-medium text-gray-700 mb-2">Program Studi *</label>
                        <select name="jurusan_id" id="jurusan_id"
                                class="input-field @error('jurusan_id') border-red-500 @enderror" required>
                            <option value="">Pilih Program Studi</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id }}"
                                    {{ old('jurusan_id', $existingSkpi->jurusan_id ?? auth()->user()->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                    {{ $jurusan->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                        @error('jurusan_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ipk" class="block text-sm font-medium text-gray-700 mb-2">IPK *</label>
                        <input type="number" step="0.01" min="0" max="4" name="ipk" id="ipk"
                               class="input-field @error('ipk') border-red-500 @enderror" 
                               value="{{ old('ipk', $existingSkpi->ipk ?? '') }}" required>
                        @error('ipk')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col">
                        <label for="prestasi_akademik" class="block text-sm font-medium text-gray-700 mb-2">Prestasi Akademik</label>
                        <textarea name="prestasi_akademik" id="prestasi_akademik"
                                  class="input-field min-h-[140px] resize-y @error('prestasi_akademik') border-red-500 @enderror"
                                  placeholder="Contoh: Juara 1 Lomba Programming, Beasiswa Berprestasi, dll">{{ old('prestasi_akademik', $existingSkpi->prestasi_akademik ?? '') }}</textarea>
                        @error('prestasi_akademik')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="prestasi_non_akademik" class="block text-sm font-medium text-gray-700 mb-2">Prestasi Non-Akademik</label>
                        <textarea name="prestasi_non_akademik" id="prestasi_non_akademik"
                                  class="input-field min-h-[140px] resize-y @error('prestasi_non_akademik') border-red-500 @enderror"
                                  placeholder="Contoh: Juara olahraga, kegiatan seni, dll">{{ old('prestasi_non_akademik', $existingSkpi->prestasi_non_akademik ?? '') }}</textarea>
                        @error('prestasi_non_akademik')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="organisasi" class="block text-sm font-medium text-gray-700 mb-2">Pengalaman Organisasi</label>
                        <textarea name="organisasi" id="organisasi"
                                  class="input-field min-h-[140px] resize-y @error('organisasi') border-red-500 @enderror"
                                  placeholder="Contoh: Ketua HIMASIF, Anggota UKM, dsb">{{ old('organisasi', $existingSkpi->organisasi ?? '') }}</textarea>
                        @error('organisasi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="pengalaman_kerja" class="block text-sm font-medium text-gray-700 mb-2">Pengalaman Kerja/Magang</label>
                        <textarea name="pengalaman_kerja" id="pengalaman_kerja"
                                  class="input-field min-h-[140px] resize-y @error('pengalaman_kerja') border-red-500 @enderror"
                                  placeholder="Tuliskan pengalaman kerja/magang (jika ada)">{{ old('pengalaman_kerja', $existingSkpi->pengalaman_kerja ?? '') }}</textarea>
                        @error('pengalaman_kerja')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 flex flex-col">
                        <label for="sertifikat_kompetensi" class="block text-sm font-medium text-gray-700 mb-2">Sertifikat Kompetensi</label>
                        <textarea name="sertifikat_kompetensi" id="sertifikat_kompetensi"
                                  class="input-field min-h-[120px] resize-y @error('sertifikat_kompetensi') border-red-500 @enderror"
                                  placeholder="Contoh: Sertifikat Java, Google Analytics, dsb">{{ old('sertifikat_kompetensi', $existingSkpi->sertifikat_kompetensi ?? '') }}</textarea>
                        @error('sertifikat_kompetensi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 flex flex-col">
                        <label for="catatan_khusus" class="block text-sm font-medium text-gray-700 mb-2">Catatan Khusus</label>
                        <textarea name="catatan_khusus" id="catatan_khusus"
                                  class="input-field min-h-[100px] resize-y @error('catatan_khusus') border-red-500 @enderror"
                                  placeholder="Tambahkan catatan/informasi tambahan bila diperlukan">{{ old('catatan_khusus', $existingSkpi->catatan_khusus ?? '') }}</textarea>
                        @error('catatan_khusus')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Link Google Drive (1 saja) -->
                <div class="mt-8">
                    <label for="drive_link" class="block text-sm font-medium text-gray-700 mb-2">Link Google Drive *</label>
                    <div class="flex items-center space-x-3">
                        <input type="url" name="drive_link" id="drive_link"
                               x-model="url"
                               @input="validate()"
                               class="input-field flex-1 @error('drive_link') border-red-500 @enderror"
                               placeholder="https://drive.google.com/file/d/… atau https://drive.google.com/drive/folders/…"
                               required>
                        <div x-show="status==='ok'">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div x-show="status==='bad'">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Gunakan tautan <em>Bagikan</em> dari Google Drive dan pastikan akses dapat dilihat reviewer (setidaknya “Anyone with the link, Viewer”).
                    </p>
                    @error('drive_link')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p x-show="message" class="mt-2 text-sm" :class="status==='ok' ? 'text-green-700' : 'text-red-700'">
                        <span x-text="message"></span>
                    </p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <p>* Field wajib diisi</p>
                        <p class="mt-1">Data akan disimpan sebagai draft dan dapat diedit sebelum disubmit untuk review.</p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="{{ route('skpi.index') }}" class="btn-outline">
                            Batal
                        </a>
                        <button type="submit" class="btn-primary"> {{ $existingSkpi ? 'Update SKPI' : 'Simpan SKPI' }} </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Alpine validator untuk link drive -->
    <script>
        function driveValidator() {
            return {
                url: @json(old('drive_link', $existingSkpi->drive_link ?? '')),
                status: null, // 'ok' | 'bad' | null
                message: '',
                validate() {
                    try {
                        const u = new URL(this.url);
                        const host = (u.hostname || '').toLowerCase();
                        const https = (u.protocol === 'https:');
                        const path = u.pathname || '';
                        const okPath = /^\/file\/d\/[^/]+/.test(path)
                                    || /^\/drive\/folders\/[^/]+/.test(path)
                                    || path === '/open'
                                    || path === '/uc'
                                    || path === '/drive/u/0/folders';

                        if (!https) { this.status='bad'; this.message='Link harus menggunakan HTTPS.'; return; }
                        if (host !== 'drive.google.com') { this.status='bad'; this.message='Domain harus drive.google.com'; return; }
                        if (!okPath) { this.status='bad'; this.message='Format link Drive tidak dikenali. Gunakan tautan "Bagikan".'; return; }

                        this.status='ok'; this.message='Link Google Drive valid.';
                    } catch(e) {
                        if (this.url.trim()==='') { this.status=null; this.message=''; return; }
                        this.status='bad'; this.message='Format URL tidak valid.';
                    }
                },
                handleSubmit(evt) {
                    this.validate();
                    if (this.status !== 'ok') {
                        evt.preventDefault();
                        if (!this.message) this.message = 'Periksa kembali link Google Drive Anda.';
                    }
                }
            }
        }
    </script>
</x-app-layout>
