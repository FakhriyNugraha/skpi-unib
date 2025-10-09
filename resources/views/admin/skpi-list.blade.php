<x-app-layout>
    <x-slot name="title">Daftar SKPI</x-slot>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Daftar SKPI</h1>
                    <p class="text-gray-600 mt-2">Kelola dan review pengajuan SKPI mahasiswa</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn-outline">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card p-6 mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status-filter" class="input-field w-full">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Menunggu Review</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" id="search" placeholder="Nama atau NIM..." class="input-field w-full">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="applyFilters()" class="btn-primary px-4 py-2 text-sm h-[40px]">Filter</button>
                </div>
            </div>
        </div>

        <!-- SKPI List -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mahasiswa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jurusan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IPK
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Submit
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($skpiList as $skpi)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-unib-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-sm font-medium text-unib-blue-600">
                                            {{ substr($skpi->nama_lengkap, 0, 2) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $skpi->nama_lengkap }}</div>
                                        <div class="text-sm text-gray-500">{{ $skpi->npm }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $skpi->jurusan->nama_jurusan }}</div>
                                <div class="text-sm text-gray-500">{{ $skpi->jurusan->kode_jurusan }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ $skpi->ipk }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($skpi->status === 'draft') bg-yellow-100 text-yellow-800
                                    @elseif($skpi->status === 'submitted') bg-blue-100 text-blue-800
                                    @elseif($skpi->status === 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($skpi->status) }}
                                </span>
                            </td>
                            <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $skpi->updated_at->format('d M Y') }}
                                <div class="text-xs text-gray-400">{{ $skpi->updated_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
                                @if($skpi->status === 'submitted')
                                    <a href="{{ route('admin.review-skpi', $skpi) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-unib-blue-600 hover:bg-unib-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-unib-blue-500">
                                        Review
                                    </a>
                                @elseif($skpi->status === 'approved')
                                    <a href="{{ route('admin.print-skpi', $skpi) }}" target="_blank" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Cetak
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500">Belum ada data SKPI</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($skpiList->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
                {{ $skpiList->links() }}
            </div>
            @endif
        </div>
    </div>

    <script>
        function applyFilters() {
            const status = document.getElementById('status-filter').value;
            const search = document.getElementById('search').value;
            
            const params = new URLSearchParams(window.location.search);
            
            if (status) params.set('status', status);
            else params.delete('status');
            
            if (search) params.set('search', search);
            else params.delete('search');
            
            window.location.search = params.toString();
        }
        
        // Set filter values from URL
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            
            if (params.get('status')) {
                document.getElementById('status-filter').value = params.get('status');
            }
            if (params.get('search')) {
                document.getElementById('search').value = params.get('search');
            }
        });
    </script>
</x-app-layout>