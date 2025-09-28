<x-app-layout>
    <x-slot name="title">Tambah User</x-slot>
    
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Tambah User Baru</h1>
                    <p class="text-gray-600 mt-2">Buat akun pengguna baru untuk sistem SKPI</p>
                </div>
                <a href="{{ route('superadmin.users') }}" class="btn-outline">
                    Kembali
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('superadmin.store-user') }}" class="space-y-8">
            @csrf

            <!-- Informasi Dasar -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-unib-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    Informasi Dasar
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                        <input type="text" name="name" id="name" class="input-field @error('name') border-red-500 @enderror" 
                               value="{{ old('name') }}" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" id="email" class="input-field @error('email') border-red-500 @enderror" 
                               value="{{ old('email') }}" required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                        <select name="role" id="role" class="input-field @error('role') border-red-500 @enderror" required onchange="toggleFields()">
                            <option value="">Pilih Role</option>
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select name="status" id="status" class="input-field @error('status') border-red-500 @enderror" required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Field NPM untuk Mahasiswa -->
                    <div id="npm-field" style="display: none;">
                        <label for="npm" class="block text-sm font-medium text-gray-700 mb-2">NPM</label>
                        <input type="text" name="npm" id="npm" class="input-field @error('npm') border-red-500 @enderror" 
                               value="{{ old('npm') }}" maxlength="9">
                        <p class="mt-1 text-sm text-gray-500">Maksimal 9 karakter</p>
                        @error('npm')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Field NIP untuk Admin/SuperAdmin -->
                    <div id="nip-field" style="display: none;">
                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                        <input type="text" name="nip" id="nip" class="input-field @error('nip') border-red-500 @enderror" 
                               value="{{ old('nip') }}" maxlength="20">
                        @error('nip')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jurusan_id" class="block text-sm font-medium text-gray-700 mb-2">Jurusan</label>
                        <select name="jurusan_id" id="jurusan_id" class="input-field @error('jurusan_id') border-red-500 @enderror">
                            <option value="">Pilih Jurusan</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                    {{ $jurusan->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                        @error('jurusan_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Kontak -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-unib-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    Informasi Kontak
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="text" name="phone" id="phone" class="input-field @error('phone') border-red-500 @enderror" 
                               value="{{ old('phone') }}">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                        <textarea name="address" id="address" rows="3" class="input-field @error('address') border-red-500 @enderror" 
                                  placeholder="Masukkan alamat lengkap">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-unib-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Password
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                        <input type="password" name="password" id="password" class="input-field @error('password') border-red-500 @enderror" required>
                        <p class="mt-1 text-sm text-gray-500">Minimal 8 karakter</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password *</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="input-field" required>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <p>* Field wajib diisi</p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="{{ route('superadmin.users') }}" class="btn-outline">
                            Batal
                        </a>
                        <button type="submit" class="btn-primary">
                            Simpan User
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function toggleFields() {
            const role = document.getElementById('role').value;
            const npmField = document.getElementById('npm-field');
            const nipField = document.getElementById('nip-field');
            const npmInput = document.getElementById('npm');
            const nipInput = document.getElementById('nip');
            
            // Reset all fields
            npmField.style.display = 'none';
            nipField.style.display = 'none';
            npmInput.required = false;
            nipInput.required = false;
            
            // Show appropriate field based on role
            if (role === 'user') {
                npmField.style.display = 'block';
                npmInput.required = true;
            } else if (role === 'admin' || role === 'superadmin') {
                nipField.style.display = 'block';
                nipInput.required = true;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleFields();
            
            // NPM formatting
            document.getElementById('npm').addEventListener('input', function(e) {
                let value = e.target.value.toUpperCase();
                value = value.replace(/[^A-Z0-9]/g, '');
                if (value.length > 9) {
                    value = value.substring(0, 9);
                }
                e.target.value = value;
            });
        });
    </script>
</x-app-layout>