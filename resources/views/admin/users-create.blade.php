<x-app-layout>
    <x-slot name="title">Tambah Mahasiswa Baru</x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Tambah Mahasiswa Baru</h1>
                    <p class="text-gray-600 mt-2">Buat akun mahasiswa baru untuk jurusan {{ auth()->user()->jurusan->nama_jurusan ?? '' }}</p>
                </div>
                <a href="{{ route('admin.users-jurusan.index') }}" class="btn-outline">
                    Kembali
                </a>
            </div>
        </div>

        <form action="{{ route('admin.users-jurusan.store') }}" method="POST">
            @csrf

            <div class="card p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Informasi Akun</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               class="input-field @error('name') border-red-500 @enderror"
                               placeholder="Masukkan nama lengkap mahasiswa">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               class="input-field @error('email') border-red-500 @enderror"
                               placeholder="contoh@email.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" id="password"
                               class="input-field @error('password') border-red-500 @enderror"
                               placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="input-field"
                               placeholder="Ulangi password">
                    </div>
                </div>

                <h2 class="text-lg font-semibold text-gray-900 mb-6">Informasi Mahasiswa</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="npm" class="block text-sm font-medium text-gray-700 mb-2">NPM</label>
                        <input type="text" name="npm" id="npm" value="{{ old('npm') }}"
                               class="input-field @error('npm') border-red-500 @enderror"
                               placeholder="Nomor Pokok Mahasiswa" maxlength="9">
                        @error('npm')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jurusan_id" class="block text-sm font-medium text-gray-700 mb-2">Program Studi</label>
                        <select name="jurusan_id" id="jurusan_id"
                                class="input-field @error('jurusan_id') border-red-500 @enderror">
                            <option value="">Pilih Program Studi</option>
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

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                               class="input-field @error('phone') border-red-500 @enderror"
                               placeholder="Contoh: 081234567890">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                        <textarea name="address" id="address" rows="3"
                                  class="input-field @error('address') border-red-500 @enderror"
                                  placeholder="Alamat lengkap mahasiswa">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.users-jurusan.index') }}" class="btn-outline">
                        Batal
                    </a>
                    <button type="submit" class="btn-primary">
                        Simpan Mahasiswa
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>