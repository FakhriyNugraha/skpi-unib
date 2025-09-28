<x-app-layout>
    <x-slot name="title">Semua SKPI</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- HEADER --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Semua SKPI</h1>
                <p class="text-gray-600 mt-2">Daftar SKPI lintas program studi untuk pengelolaan Superadmin</p>
            </div>
            <nav class="flex gap-2">
                <a href="{{ route('superadmin.users') }}" class="btn-outline px-3 py-2 text-sm">Kelola Users</a>
                <a href="{{ route('superadmin.jurusans') }}" class="btn-outline px-3 py-2 text-sm">Kelola Jurusan</a>
                <a href="{{ route('superadmin.all-skpi') }}" class="btn-primary px-3 py-2 text-sm">Semua SKPI</a>
            </nav>
        </div>

        {{-- FLASH --}}
        @foreach (['success'=>'green','error'=>'red','warning'=>'yellow'] as $key=>$color)
            @if(session($key))
                <div class="mb-4 rounded-xl bg-{{ $color }}-50 border border-{{ $color }}-200 px-4 py-3 text-{{ $color }}-800">
                    {{ session($key) }}
                </div>
            @endif
        @endforeach

        {{-- FILTERS (server-side) --}}
        <div class="card p-6 mb-8">
            <form method="GET" action="{{ route('superadmin.all-skpi') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="input-field">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                            <option value="submitted" {{ request('status')=='submitted'?'selected':'' }}>Submitted</option>
                            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                            <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Program Studi</label>
                        <select name="jurusan" class="input-field">
                            <option value="">Semua Prodi</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id }}" {{ (string)request('jurusan')===(string)$j->id ? 'selected' : '' }}>
                                    {{ $j->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reviewer</label>
                        <input type="text" name="reviewer" value="{{ request('reviewer') }}" class="input-field" placeholder="Nama reviewer">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full px-3 py-2 text-sm">Filter</button>
                    </div>
                </div>

                <div class="mt-4">
                    <input type="text" name="search" value="{{ request('search') }}" class="input-field"
                           placeholder="Cari nama, NIM, email, atau nama prodi…">
                </div>
            </form>
        </div>

        {{-- TABEL --}}
        <div class="card">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="font-semibold text-gray-900">Daftar SKPI</div>
                <div class="text-sm text-gray-500">
                    Halaman ini: {{ $skpiList->count() }} • Total: {{ $skpiList->total() }}
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program Studi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approver</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diupdate</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($skpiList as $row)
                            @php
                                $status = strtolower($row->status ?? 'draft');
                                $badgeClass = $status === 'approved'
                                    ? 'bg-green-100 text-green-800'
                                    : ($status === 'submitted'
                                        ? 'bg-blue-100 text-blue-800'
                                        : ($status === 'rejected'
                                            ? 'bg-red-100 text-red-800'
                                            : 'bg-yellow-100 text-yellow-800'));
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ data_get($row,'nama_lengkap') ?? data_get($row,'user.name','—') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ data_get($row,'user.email','') }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ data_get($row,'npm') ?? data_get($row,'user.npm','—') }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ data_get($row,'program_studi') ?? data_get($row,'jurusan.nama_jurusan','—') }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium {{ $badgeClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ data_get($row,'reviewer.name','—') }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ data_get($row,'approver.name','—') }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ optional($row->updated_at)->format('d M Y H:i') }}
                                </td>

                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('superadmin.skpi.show', $row) }}"
                                           class="btn-outline px-2 py-1 text-xs"
                                           title="Lihat">
                                            Lihat
                                        </a>

                                        @if($status === 'submitted')
                                            <form method="POST" action="{{ route('superadmin.approve-skpi', $row) }}" class="inline"
                                                  onsubmit="return confirm('Setujui SKPI ini?');">
                                                @csrf
                                                <button type="submit" class="btn-primary px-2 py-1 text-xs">Approve</button>
                                            </form>

                                            <form method="POST" action="{{ route('superadmin.reject-skpi', $row) }}" class="inline"
                                                  onsubmit="return confirm('Tolak SKPI ini?');">
                                                @csrf
                                                <input type="hidden" name="catatan_reviewer" value="">
                                                <button type="submit" class="btn-outline px-2 py-1 text-xs">Reject</button>
                                            </form>
                                        @endif

                                        @if($status === 'approved')
                                            <a href="{{ route('superadmin.skpi.print', $row) }}" target="_blank"
                                               class="btn-outline px-2 py-1 text-xs"
                                               title="Cetak">
                                                Cetak
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">Belum ada data SKPI.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($skpiList->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $skpiList->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
