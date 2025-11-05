{{-- resources/views/superadmin/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="title">Dashboard Superadmin</x-slot>

    {{-- Utility ringan untuk komponen --}}
    <style>
        .card{ @apply rounded-2xl border border-gray-200 bg-white shadow-sm; }
        .btn{ @apply inline-flex items-center justify-center font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-xl; }
        .btn-primary{ @apply btn bg-unib-blue-600 text-white hover:bg-unib-blue-700 focus:ring-unib-blue-300 px-4 py-2; }
        .btn-outline{ @apply btn border border-unib-blue-600 text-unib-blue-700 hover:bg-unib-blue-50 px-4 py-2; }
        .chip{ @apply inline-flex items-center px-2 py-1 rounded-full text-[11px] font-semibold; }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-3">
        <!-- HERO (spacing & font seperti referensi) -->
        <div class="mb-4">
            <div class="bg-gradient-to-r from-unib-blue-600 to-unib-blue-700 rounded-2xl text-white shadow-sm">
                <div class="px-5 py-5 sm:px-6 sm:py-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                        <div class="sm:col-span-2">
                            <!-- Judul lebih besar & tebal, tracking rapat -->
                            <h2 class="text-2xl font-extrabold tracking-tight leading-tight">
                                Dashboard Superadmin
                            </h2>

                            <!-- Subjudul sedikit lebih besar -->
                            <p class="text-blue-100 text-sm mt-1">
                                Ringkasan sistem & aktivitas terbaru SKPI
                            </p>

                            <!-- Baris info, icon + teks rapat -->
                            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs mt-3">
                                <div class="flex items-center opacity-95">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ ucfirst(auth()->user()->role) }}
                                </div>

                                @if(auth()->user()->jurusan)
                                <div class="flex items-center opacity-95">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ auth()->user()->jurusan->nama_jurusan }}
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Slot kanan dibiarkan kosong (di referensi untuk badge), tidak mengubah konten -->
                        <div class="flex sm:justify-end items-start">
                            {{-- tempatkan badge/aksi jika diperlukan --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATS --}}
        <section aria-labelledby="stats-title" class="mb-10">
            <h2 id="stats-title" class="sr-only">Statistik</h2>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- TOTAL USERS (7 kolom dari 12 = sekitar 58%) --}}
                <article class="card p-5 hover:shadow-md transition-shadow flex flex-col lg:col-span-7">
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

                {{-- TOTAL SKPI (5 kolom dari 12 = sekitar 42%) --}}
                <article class="card p-5 hover:shadow-md transition-shadow flex flex-col lg:col-span-5">
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
                                    <th class="px-3 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-unib-blue-800/80">NPM</th>
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
                                            {{ $item->npm ?? ($item->user->npm ?? '—') }}
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
                                                   class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-600 text-white border-2 border-blue-600 hover:bg-blue-700 text-sm">Lihat</a>
                                                @if($item->status === 'approved')
                                                    <span class="text-gray-300">•</span>
                                                    <a href="{{ route('admin.print-skpi', $item) }}" target="_blank"
                                                       class="inline-flex items-center px-2.5 py-1 rounded-lg bg-green-600 text-white border-2 border-green-600 hover:bg-green-700 text-sm">Cetak</a>
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
