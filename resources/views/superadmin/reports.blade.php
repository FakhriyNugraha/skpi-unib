{{-- resources/views/superadmin/reports.blade.php --}}
<x-app-layout>
    <x-slot name="title">Laporan & Statistik Superadmin</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- HEADER --}}
        <header class="mb-10">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-unib-blue-900">
                            Laporan & Statistik
                        </h1>
                        <p class="mt-1 text-sm text-unib-blue-800/70">
                            Visualisasi data dan statistik dari sistem SKPI
                        </p>
                    </div>
                    <nav class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('superadmin.dashboard') }}" class="btn-outline">
                            Dashboard
                        </a>
                    </nav>
                </div>

                {{-- PERIODE FILTER --}}
                <form method="GET" action="{{ route('superadmin.reports') }}" class="mt-4">
                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Periode Wisuda</label>
                            <select name="periode_wisuda" class="input-field w-full" onchange="this.form.submit()">
                                <option value="">Semua Periode (Default)</option>
                                @foreach($availablePeriods as $period)
                                    <option value="{{ $period['number'] }}" {{ request('periode_wisuda') == $period['number'] ? 'selected' : '' }}>
                                        {{ $period['title'] }} ({{ $period['number'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn-primary px-4 py-2">Filter</button>
                    </div>
                </form>
            </div>
        </header>

        {{-- STATISTIK UTAMA --}}
        <section aria-labelledby="stats-title" class="mb-10">
            <h2 id="stats-title" class="text-2xl font-bold text-unib-blue-900 mb-6">Statistik Utama</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- TOTAL USERS --}}
                <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-unib-blue-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-unib-blue-700" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path fill-rule="evenodd" d="M5 13a5 5 0 1110 0v1a2 2 0 01-2 2H7a2 2 0 01-2-2v-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Pengguna Terkait</p>
                            <p class="text-2xl font-extrabold text-unib-blue-900">{{ $stats['total_users'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-600">Mahasiswa: {{ $stats['total_mahasiswa'] ?? 0 }}</span>
                            <span class="text-gray-600">Admin: {{ $stats['total_admin'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- TOTAL SKPI TERVERIFIKASI --}}
                <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-teknik-orange-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-teknik-orange-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total SKPI Terverifikasi</p>
                            <p class="text-2xl font-extrabold text-teknik-orange-700">{{ $stats['approved_skpi'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-teknik-orange-600 h-2 rounded-full" style="width: {{ $stats['approved_percentage'] ?? 0 }}%"></div>
                        </div>
                        <div class="mt-1 text-xs text-gray-600">{{ $stats['approved_percentage'] ?? 0 }}% dari SKPI non-draft</div>
                    </div>
                </div>

                {{-- TOTAL JURUSAN --}}
                <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Jurusan Terlibat SKPI</p>
                            <p class="text-2xl font-extrabold text-green-700">{{ $stats['total_jurusan'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex justify-between text-xs">
                            <span class="text-green-600">Aktif: {{ $stats['active_jurusan'] ?? 0 }}</span>
                            <span class="text-red-600">Nonaktif: {{ $stats['inactive_jurusan'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- PERSENTASE APPROVAL --}}
                <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Tingkat Persetujuan SKPI</p>
                            <p class="text-2xl font-extrabold text-purple-700">{{ $stats['approved_percentage'] ?? 0 }}%</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center">
                            <span class="text-xs text-green-600">✓ {{ $stats['approved_skpi'] ?? 0 }} Disetujui</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- GRAFIK DASHBOARD --}}
        <section class="mb-10">
            <h2 class="text-2xl font-bold text-unib-blue-900 mb-6">Statistik Visual</h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- GRAFIK STATUS SKPI --}}
                <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-unib-blue-900 mb-4">Status Proses SKPI</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-green-700">Lulus/Terbukti</span>
                                <span class="text-sm font-medium text-green-700">{{ $stats['approved_skpi'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-600 h-3 rounded-full" style="width: {{ $stats['approved_percentage'] ?? 0 }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-blue-700">Dalam Proses</span>
                                <span class="text-sm font-medium text-blue-700">{{ $stats['pending_skpi'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $stats['pending_percentage'] ?? 0 }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-red-700">Perlu Perbaikan</span>
                                <span class="text-sm font-medium text-red-700">{{ $stats['rejected_skpi'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-red-600 h-3 rounded-full" style="width: {{ $stats['rejected_percentage'] ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ $stats['approved_percentage'] ?? 0 }}%</div>
                            <div class="text-xs text-gray-600">Lulus</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['pending_percentage'] ?? 0 }}%</div>
                            <div class="text-xs text-gray-600">Dalam Proses</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-600">{{ $stats['rejected_percentage'] ?? 0 }}%</div>
                            <div class="text-xs text-gray-600">Perlu Revisi</div>
                        </div>
                    </div>
                </div>

                {{-- GRAFIK JURUSAN --}}
                <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-unib-blue-900 mb-4">Distribusi SKPI per Jurusan</h3>
                    <div class="space-y-4">
                        @foreach($jurusanStats as $jurusan)
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-unib-blue-700">{{ $jurusan['nama_jurusan'] }}</span>
                                <span class="text-sm font-medium text-unib-blue-700">
                                    {{ $jurusan['jumlah_skpi'] }} ({{ $jurusan['jumlah_approved'] }} Terverifikasi, {{ $jurusan['jumlah_unapproved'] }} Belum)
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-unib-blue-600 h-3 rounded-full" style="width: {{ $jurusan['persentase'] }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- TABEL DATA --}}
        <section>
            <h2 class="text-2xl font-bold text-unib-blue-900 mb-6">Detail Statistik</h2>

            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-unib-blue-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-unib-blue-800 uppercase tracking-wider">Jurusan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-unib-blue-800 uppercase tracking-wider">Total SKPI</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-unib-blue-800 uppercase tracking-wider">Disetujui</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-unib-blue-800 uppercase tracking-wider">Menunggu</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-unib-blue-800 uppercase tracking-wider">Ditolak</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-unib-blue-800 uppercase tracking-wider">Approval Rate</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($detailedStats as $stat)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $stat['nama_jurusan'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $stat['total_skpi'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $stat['approved'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $stat['pending'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $stat['rejected'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-teknik-orange-100 text-teknik-orange-800">
                                                {{ $stat['approval_rate'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
