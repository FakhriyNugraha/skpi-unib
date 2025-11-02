{{-- resources/views/superadmin/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="title">Dashboard Superadmin</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- HEADER --}}
        <header class="mb-10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-unib-blue-900">
                        Dashboard Superadmin
                    </h1>
                    <p class="mt-1 text-sm text-unib-blue-800/70">
                        Ringkasan sistem & aktivitas terbaru SKPI
                    </p>
                </div>
            </div>
        </header>

        {{-- STATS --}}
        <section aria-labelledby="stats-title" class="mb-10">
            <h2 id="stats-title" class="sr-only">Statistik</h2>

            <div class="flex flex-col lg:flex-row gap-6">
                {{-- TOTAL USERS (45%) --}}
                <article class="card p-5 hover:shadow-md transition-shadow flex flex-col" style="flex: 0 0 45%;">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-unib-blue-800/70">Total Users</p>
                            <p class="mt-1 text-3xl font-extrabold text-unib-blue-900">
                                {{ $stats['total_users'] }}
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-unib-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-unib-blue-700" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M13 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path fill-rule="evenodd" d="M5 13a5 5 0 1110 0v1a2 2 0 01-2 2H7a2 2 0 01-2-2v-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="rounded-lg border border-unib-blue-100 bg-unib-blue-50 p-3">
                            <p class="text-sm font-medium text-unib-blue-800/90">Mahasiswa</p>
                            <p class="text-xl font-semibold text-unib-blue-900">
                                {{ $stats['total_mahasiswa'] }}
                            </p>
                        </div>
                        <div class="rounded-lg border border-unib-blue-100 bg-unib-blue-50 p-3">
                            <p class="text-sm font-medium text-unib-blue-800/90">Admin Jurusan</p>
                            <p class="text-xl font-semibold text-unib-blue-900">
                                {{ $stats['total_admin'] }}
                            </p>
                        </div>
                    </div>
                </article>

                {{-- TOTAL SKPI (40%) --}}
                <article class="card p-5 hover:shadow-md transition-shadow flex flex-col" style="flex: 0 0 40%;">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-unib-blue-800/70">Total SKPI</p>
                            <p class="mt-1 text-3xl font-extrabold text-unib-blue-900">
                                {{ $stats['total_skpi'] }}
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-teknik-orange-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-teknik-orange-700" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="rounded-lg border border-teknik-orange-100 bg-teknik-orange-50 p-3">
                            <p class="text-sm font-medium text-unib-blue-800/90">Disetujui</p>
                            <p class="text-xl font-semibold text-teknik-orange-700">
                                {{ $stats['approved_skpi'] }}
                            </p>
                        </div>
                        <div class="rounded-lg border border-teknik-orange-100 bg-teknik-orange-50 p-3">
                            <p class="text-sm font-medium text-unib-blue-800/90">Persentase</p>
                            @php
                                $approved = (int) $stats['approved_skpi'];
                                $total = max(1, (int) $stats['total_skpi']);
                                $approvedPct = min(100, round(($approved / $total) * 100));
                            @endphp
                            <p class="text-xl font-semibold text-teknik-orange-700">
                                {{ $approvedPct }}%
                            </p>
                        </div>
                    </div>
                </article>

                {{-- AKSI CEPAT (15%) --}}
                <article class="card p-4 hover:shadow-md transition-shadow flex flex-col" style="flex: 0 0 15%;">
                    <p class="mb-3 text-[11px] uppercase tracking-wide text-unib-blue-800/70">Aksi Cepat</p>

                    <div class="flex-1 space-y-2">
                        <a href="{{ route('superadmin.create-user') }}"
                           class="btn-primary block w-full text-center text-sm">
                            Buat User
                        </a>
                        <a href="{{ route('superadmin.create-jurusan') }}"
                           class="btn-outline block w-full text-center text-sm">
                            Tambah Jurusan
                        </a>
                        <a href="{{ route('superadmin.reports') }}"
                           class="btn-outline block w-full text-center text-sm">
                            Laporan & Statistik
                        </a>
                    </div>
                </article>
            </div>
        </section>

        {{-- RECENT ACTIVITY --}}
        <section aria-labelledby="activity-title" class="card p-6">
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h2 id="activity-title" class="text-xl font-semibold text-unib-blue-900">
                    Aktivitas Terbaru
                </h2>
                <a href="{{ route('superadmin.all-skpi') }}"
                   class="text-sm text-unib-blue-700 hover:underline self-start sm:self-auto">
                    Lihat semua
                </a>
            </div>

            @if($recentActivity->isEmpty())
                <div class="py-16 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-unib-blue-100">
                        <svg class="h-6 w-6 text-unib-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 17v-6a2 2 0 114 0v6m-6 4h8a2 2 0 002-2V7a2 2 0 00-2-2h-3l-2-2H9L7 5H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="mt-3 text-unib-blue-800/70">Belum ada aktivitas terbaru.</p>
                </div>
            @else
                <div class="-mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full align-middle px-4 sm:px-6 lg:px-8">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-unib-blue-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-unib-blue-800/80">Mahasiswa</th>
                                    <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-unib-blue-800/80">NIM</th>
                                    <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-unib-blue-800/80">Program Studi</th>
                                    <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-unib-blue-800/80">Status</th>
                                    <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-unib-blue-800/80">Reviewer</th>
                                    <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-unib-blue-800/80">Diupdate</th>
                                    <th class="px-3 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($recentActivity as $item)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-3 py-3">
                                            <div class="flex items-center">
                                                <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-unib-blue-100">
                                                    <span class="text-[11px] font-semibold text-unib-blue-700">
                                                        {{ strtoupper(substr($item->user->name ?? 'U', 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $item->user->name ?? '—' }}</p>
                                                    <p class="text-xs text-gray-500">{{ $item->user->email ?? '' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-sm text-gray-900">
                                            {{ $item->nim ?? ($item->user->nim ?? '—') }}
                                        </td>
                                        <td class="px-3 py-3 text-sm text-gray-900">
                                            {{ $item->jurusan->nama_jurusan ?? '—' }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                                @if($item->status === 'draft') bg-yellow-100 text-yellow-800
                                                @elseif($item->status === 'submitted') bg-blue-100 text-blue-800
                                                @elseif($item->status === 'approved') bg-green-100 text-green-800
                                                @elseif($item->status === 'rejected') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-sm text-gray-900">
                                            {{ $item->reviewer->name ?? '—' }}
                                        </td>
                                        <td class="px-3 py-3 text-sm text-gray-500">
                                            {{ $item->updated_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-3 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('superadmin.all-skpi') }}?highlight={{ $item->id }}"
                                                   class="text-sm text-unib-blue-700 hover:underline">Lihat</a>
                                                @if($item->status === 'approved')
                                                    <span class="text-gray-300">•</span>
                                                    <a href="{{ route('admin.print-skpi', $item) }}" target="_blank"
                                                       class="text-sm text-teknik-orange-700 hover:underline">Cetak</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>    
                    </div>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
