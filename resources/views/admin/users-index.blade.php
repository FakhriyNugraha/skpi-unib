<x-app-layout>
    <x-slot name="title">Kelola Mahasiswa</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Mahasiswa</h1>
                    <p class="text-gray-600 mt-2">Manajemen mahasiswa jurusan {{ auth()->user()->jurusan->nama_jurusan ?? '' }}</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.users-jurusan.create') }}" class="btn-primary">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Mahasiswa
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-6">
            <div class="card p-4">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-900">{{ $totalMahasiswa }}</p>
                        <p class="text-xs text-gray-600">Mahasiswa</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card p-4 mb-6">
            <form method="GET" action="{{ route('admin.users-jurusan.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NPM</label>
                        <input type="text" name="npm" placeholder="Cari berdasarkan NPM" class="input-field"
                               value="{{ request('npm') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Periode Wisuda</label>
                        <select name="periode_wisuda" class="input-field">
                            <option value="">Semua Periode</option>
                            @foreach($availablePeriods as $period)
                                <option value="{{ $period['number'] }}" {{ request('periode_wisuda') == $period['number'] ? 'selected' : '' }}>
                                    {{ $period['number'] }} - {{ $period['title'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary px-5 py-2.5 text-sm">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="card p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jurusan</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($user->avatar)
                                        <img class="h-8 w-8 rounded-full object-cover mr-2" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-unib-blue-100 flex items-center justify-center mr-2">
                                            <span class="text-sm font-medium text-unib-blue-600">
                                                {{ substr($user->name, 0, 2) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        @if($user->npm)
                                            <div class="text-xs text-gray-400">NPM: {{ $user->npm }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-sm font-medium
                                    bg-blue-100 text-blue-800">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->jurusan->nama_jurusan ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-sm font-medium
                                    {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-xs font-medium space-x-2">
                                <a href="{{ route('admin.users-jurusan.edit', $user) }}" class="inline-flex items-center px-3 py-1.5 rounded bg-amber-500 text-white border-2 border-amber-500 hover:bg-amber-600 text-sm">
                                    Edit
                                </a>
                                <form action="{{ route('admin.users-jurusan.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 rounded bg-red-600 text-white hover:bg-red-700 text-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <p class="text-sm text-gray-500">Tidak ada data mahasiswa</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
            <div class="px-4 py-2 border-t border-gray-200">
                {{ $users->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>