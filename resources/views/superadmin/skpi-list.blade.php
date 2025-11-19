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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="input-field w-full min-h-[42px]">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                            <option value="submitted" {{ request('status')=='submitted'?'selected':'' }}>Submitted</option>
                            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                            <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Program Studi</label>
                        <select name="jurusan" class="input-field w-full min-h-[42px]">
                            <option value="">Semua Prodi</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id }}" {{ (string)request('jurusan')===(string)$j->id ? 'selected' : '' }}>
                                    {{ $j->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Periode Wisuda</label>
                        <select name="periode_wisuda" class="input-field w-full min-h-[42px]">
                            <option value="">Semua Periode</option>
                            @foreach($availablePeriods as $period)
                                <option value="{{ $period['number'] }}" {{ request('periode_wisuda') == $period['number'] ? 'selected' : '' }}>
                                    {{ $period['title'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Reviewer</label>
                        <input type="text" name="reviewer" value="{{ request('reviewer') }}" class="input-field w-full min-h-[42px]" placeholder="Cari nama reviewer..." maxlength="50">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="btn-primary px-3 py-3 w-full">Filter</button>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian Umum</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="input-field w-full"
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
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
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

                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                    <a href="{{ route('superadmin.skpi.show', $row) }}"
                                       class="inline-flex items-center px-3 py-1 rounded bg-blue-600 text-white border-2 border-blue-600 hover:bg-blue-700"
                                       title="Lihat">
                                        Lihat
                                    </a>

                                    @if($status === 'submitted')
                                        <button type="button" class="inline-flex items-center px-3 py-1 rounded bg-green-600 text-white border-2 border-green-600 hover:bg-green-700 approve-btn" data-skpi-id="{{ $row->id }}" data-action="approve">
                                            Setujui
                                        </button>

                                        <button type="button" class="inline-flex items-center px-3 py-1 rounded bg-red-600 text-white border-2 border-red-600 hover:bg-red-700 reject-btn" data-skpi-id="{{ $row->id }}" data-action="reject">
                                            Tolak
                                        </button>
                                    @endif

                                    @if($status === 'approved')
                                        <a href="{{ route('superadmin.skpi.print', $row) }}" target="_blank"
                                           class="inline-flex items-center px-3 py-1 rounded bg-amber-500 text-white border-2 border-amber-500 hover:bg-amber-600"
                                           title="Cetak">
                                            Cetak
                                        </a>
                                    @endif
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

    <!-- Approve/Reject Confirmation Modal -->
    <div id="skpiReviewModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div id="skpiReviewModalOverlay" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-gray-200">
                <div class="p-6">
                    <h3 id="skpiReviewModalTitle" class="text-lg font-semibold text-gray-900 text-center">
                        Konfirmasi Aksi
                    </h3>
                    <p id="skpiReviewModalContent" class="mt-3 text-sm text-gray-600 text-center">
                        Apakah Anda yakin ingin melakukan aksi ini?
                    </p>
                    <div class="mt-6 flex flex-col sm:flex-row justify-center sm:gap-3 gap-2">
                        <button type="button" id="cancelSkpiReview"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batal
                        </button>
                        <button type="button" id="confirmSkpiReview"
                                class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span id="confirmSkpiReviewText">Konfirmasi</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const approveButtons = document.querySelectorAll('.approve-btn');
            const rejectButtons = document.querySelectorAll('.reject-btn');
            const skpiReviewModal = document.getElementById('skpiReviewModal');
            const skpiReviewModalOverlay = document.getElementById('skpiReviewModalOverlay');
            const cancelSkpiReview = document.getElementById('cancelSkpiReview');
            const confirmSkpiReview = document.getElementById('confirmSkpiReview');
            const skpiReviewModalTitle = document.getElementById('skpiReviewModalTitle');
            const skpiReviewModalContent = document.getElementById('skpiReviewModalContent');
            const confirmSkpiReviewText = document.getElementById('confirmSkpiReviewText');
            
            let currentSkpiId = null;
            let currentAction = null;
            
            // Approve button event handlers
            approveButtons.forEach(button => {
                button.addEventListener('click', function() {
                    currentSkpiId = this.dataset.skpiId;
                    currentAction = 'approve';
                    skpiReviewModalTitle.textContent = 'Setujui SKPI';
                    skpiReviewModalContent.textContent = 'Apakah Anda yakin ingin menyetujui SKPI ini? Tindakan ini tidak dapat dibatalkan.';
                    confirmSkpiReviewText.textContent = 'Setujui';
                    confirmSkpiReview.className = 'px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 shadow-sm inline-flex items-center justify-center';
                    
                    skpiReviewModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    confirmSkpiReview.focus();
                });
            });
            
            // Reject button event handlers
            rejectButtons.forEach(button => {
                button.addEventListener('click', function() {
                    currentSkpiId = this.dataset.skpiId;
                    currentAction = 'reject';
                    skpiReviewModalTitle.textContent = 'Tolak SKPI';
                    skpiReviewModalContent.textContent = 'Apakah Anda yakin ingin menolak SKPI ini? Tindakan ini tidak dapat dibatalkan.';
                    confirmSkpiReviewText.textContent = 'Tolak';
                    confirmSkpiReview.className = 'px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm inline-flex items-center justify-center';
                    
                    skpiReviewModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    confirmSkpiReview.focus();
                });
            });
            
            // Close modal
            function closeSkpiReviewModal() {
                skpiReviewModal.classList.add('hidden');
                document.body.style.overflow = '';
                currentSkpiId = null;
                currentAction = null;
            }
            
            // Confirm action
            confirmSkpiReview.addEventListener('click', function() {
                if (currentSkpiId && currentAction) {
                    // Create and submit form dynamically based on action
                    const form = document.createElement('form');
                    form.method = 'POST';
                    
                    if (currentAction === 'approve') {
                        form.action = `/superadmin/skpi/${currentSkpiId}/approve`;
                    } else {
                        form.action = `/superadmin/skpi/${currentSkpiId}/reject`;
                    }
                    
                    form.style.display = 'none';
                    
                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfInput);
                    
                    // For reject action, we might want to add a note, but for now just submit
                    if (currentAction === 'reject') {
                        const catatanInput = document.createElement('input');
                        catatanInput.type = 'hidden';
                        catatanInput.name = 'catatan_reviewer';
                        catatanInput.value = '';
                        form.appendChild(catatanInput);
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
            
            // Event listeners for closing modal
            skpiReviewModalOverlay.addEventListener('click', closeSkpiReviewModal);
            cancelSkpiReview.addEventListener('click', closeSkpiReviewModal);
            
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !skpiReviewModal.classList.contains('hidden')) closeSkpiReviewModal();
            });
        });
    </script>
</x-app-layout>
