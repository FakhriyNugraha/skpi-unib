<x-app-layout>
    <x-slot name="title">Kelola Jurusan</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <header class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Program Studi</h1>
                    <p class="text-gray-600 mt-2">Manajemen program studi Fakultas Teknik</p>
                </div>
                <nav class="flex space-x-4" aria-label="Aksi Halaman">
                    <a href="{{ route('superadmin.create-jurusan') }}" class="btn-primary">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Program Studi
                    </a>
                    <a href="{{ route('superadmin.dashboard') }}" class="btn-outline">Kembali</a>
                </nav>
            </div>
        </header>

        <!-- Stats Cards -->
        <section aria-labelledby="ringkasan-statistik" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <h2 id="ringkasan-statistik" class="sr-only">Ringkasan Statistik</h2>

            <article class="card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $jurusans->count() }}</p>
                        <p class="text-sm text-gray-600">Total Program Studi</p>
                    </div>
                </div>
            </article>

            <article class="card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $jurusans->where('status', 'active')->count() }}</p>
                        <p class="text-sm text-gray-600">Aktif</p>
                    </div>
                </div>
            </article>

            <article class="card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 mr-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\User::where('role', 'user')->count() }}</p>
                        <p class="text-sm text-gray-600">Total Mahasiswa</p>
                    </div>
                </div>
            </article>

            <article class="card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\SkpiData::count() }}</p>
                        <p class="text-sm text-gray-600">Total SKPI</p>
                    </div>
                </div>
            </article>
        </section>

        <!-- Jurusan Grid / Empty State -->
        <section aria-labelledby="daftar-jurusan">
            <h2 id="daftar-jurusan" class="sr-only">Daftar Program Studi</h2>

            @if($jurusans->count())
                <div id="jurusanGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    @foreach($jurusans as $jurusan)
                        <article
                            class="card p-6 hover:shadow-xl transition-all duration-300 jurusan-card"
                            data-name="{{ strtolower($jurusan->nama_jurusan) }}"
                            data-code="{{ strtolower($jurusan->kode_jurusan) }}"
                            data-status="{{ $jurusan->status }}"
                            aria-label="Kartu Program Studi {{ $jurusan->nama_jurusan }}"
                        >
                            <header class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-teknik-orange-500 to-teknik-orange-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg mr-4">
                                        {{ $jurusan->kode_jurusan }}
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $jurusan->nama_jurusan }}</h3>
                                        <p class="text-sm text-gray-500">Kode: {{ $jurusan->kode_jurusan }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $jurusan->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $jurusan->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>

                                    <!-- Options -->
                                    <div class="relative">
                                        <button
                                            type="button"
                                            class="more-options text-gray-400 hover:text-gray-600 p-1"
                                            data-jurusan="{{ $jurusan->id }}"
                                            aria-haspopup="true"
                                            aria-expanded="false"
                                            aria-label="Buka menu opsi"
                                        >
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                            </svg>
                                        </button>

                                        <div
                                            class="options-menu absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 hidden"
                                            data-menu="{{ $jurusan->id }}"
                                            role="menu"
                                            aria-hidden="true"
                                        >
                                            <div class="py-1">
                                                <a href="{{ route('superadmin.edit-jurusan', $jurusan) }}"
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                   role="menuitem">
                                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit
                                                </a>

                                                <a href="#"
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 view-detail"
                                                   data-jurusan="{{ $jurusan->id }}"
                                                   role="menuitem">
                                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    Detail
                                                </a>

                                                <form action="{{ route('superadmin.toggle-jurusan-status', $jurusan) }}" method="POST" class="inline" role="none">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                            role="menuitem">
                                                        @if($jurusan->status == 'active')
                                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                      d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                                            </svg>
                                                            Nonaktifkan
                                                        @else
                                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            Aktifkan
                                                        @endif
                                                    </button>
                                                </form>

                                                <div class="border-t border-gray-100"></div>

                                                <button type="button"
                                                        class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50 delete-jurusan"
                                                        data-jurusan="{{ $jurusan->id }}"
                                                        data-name="{{ $jurusan->nama_jurusan }}"
                                                        role="menuitem">
                                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </header>

                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">{{ Str::limit($jurusan->deskripsi, 100) }}</p>

                            <!-- Statistics (tetap, tapi tanpa tombol aksi di bawahnya) -->
                            <div class="grid grid-cols-3 gap-4" aria-label="Statistik Program Studi">
                                <div class="bg-blue-50 rounded-lg p-3 text-center">
                                    <div class="text-lg font-bold text-blue-600">{{ $jurusan->users->where('role', 'user')->count() }}</div>
                                    <div class="text-xs text-gray-600">Mahasiswa</div>
                                </div>
                                <div class="bg-green-50 rounded-lg p-3 text-center">
                                    <div class="text-lg font-bold text-green-600">{{ $jurusan->skpiData->where('status', 'approved')->count() }}</div>
                                    <div class="text-xs text-gray-600">SKPI Approved</div>
                                </div>
                                <div class="bg-orange-50 rounded-lg p-3 text-center">
                                    <div class="text-lg font-bold text-orange-600">{{ $jurusan->skpiData->where('status', 'pending')->count() }}</div>
                                    <div class="text-xs text-gray-600">Menunggu</div>
                                </div>
                            </div>

                            <!-- Timestamps -->
                            <footer class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>Dibuat: {{ $jurusan->created_at->format('d M Y') }}</span>
                                    <span>Diperbarui: {{ $jurusan->updated_at->format('d M Y') }}</span>
                                </div>
                            </footer>
                        </article>
                    @endforeach
                </div>
            @else
                <div id="emptyState" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Program studi tidak ditemukan</h3>
                    <p class="mt-1 text-sm text-gray-500">Data program studi belum tersedia.</p>
                </div>
            @endif

            <!-- Pagination -->
            @if($jurusans instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-8">
                    {{ $jurusans->links() }}
                </div>
            @endif
        </section>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="modalTitle" class="text-lg font-medium text-gray-900">Detail Program Studi</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600" aria-label="Tutup modal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="modalContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <!-- Delete Jurusan Confirmation Modal -->
    <div id="deleteJurusanModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div id="deleteJurusanModalOverlay" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 text-center">
                        Hapus Program Studi
                    </h3>
                    <p class="mt-3 text-sm text-gray-600 text-center">
                        Apakah Anda yakin ingin menghapus program studi <span id="deleteJurusanName" class="font-medium"></span>? 
                        Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.
                    </p>
                    <div class="mt-6 flex flex-col sm:flex-row justify-center sm:gap-3 gap-2">
                        <button type="button" id="cancelDeleteJurusan"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batal
                        </button>
                        <button type="button" id="confirmDeleteJurusan"
                                class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                'use strict';

                const $  = (sel, ctx = document) => ctx.querySelector(sel);
                const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));
                const on = (el, evt, cb) => el.addEventListener(evt, cb, false);

                const jurusanCards  = $$('.jurusan-card');
                const optionButtons = $$('.more-options');
                const optionMenus   = $$('.options-menu');

                const detailModal   = $('#detailModal');
                const modalTitle    = $('#modalTitle');
                const modalContent  = $('#modalContent');
                const closeModalBtn = $('#closeModal');
                const viewDetailBtns= $$('.view-detail');

                const deleteModal   = $('#deleteModal');
                const deleteForm    = $('#deleteForm');
                const deleteName    = $('#deleteJurusanName');
                const cancelDelete  = $('#cancelDelete');
                const deleteBtns    = $$('.delete-jurusan');

                function toggleMenu(menuId) {
                    const menu = document.querySelector(`[data-menu="${menuId}"]`);
                    optionMenus.forEach(m => { if (m !== menu) m.classList.add('hidden'); });
                    menu.classList.toggle('hidden');
                }
                function closeAllMenus() { optionMenus.forEach(m => m.classList.add('hidden')); }

                function showToast(message, type = 'success') {
                    const el = document.createElement('div');
                    el.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${
                        type === 'success' ? 'bg-green-500' :
                        type === 'error'   ? 'bg-red-500'   :
                        type === 'warning' ? 'bg-yellow-500': 'bg-blue-500'
                    }`;
                    el.textContent = message;
                    el.style.transform = 'translateX(100%)';
                    el.style.transition = 'transform .3s ease';
                    document.body.appendChild(el);
                    requestAnimationFrame(() => el.style.transform = 'translateX(0)'));
                    setTimeout(() => {
                        el.style.transform = 'translateX(100%)';
                        setTimeout(() => el.remove(), 300);
                    }, 3000);
                }

                function openDetailModal(id) {
                    modalTitle.textContent = 'Detail Program Studi';
                    modalContent.innerHTML = '<p>Loading...</p>';
                    detailModal.classList.remove('hidden');
                    // Implement AJAX here if endpoint available
                    // fetch(`{{ route('superadmin.jurusan-detail', ':id') }}`.replace(':id', id))
                    //  .then(r => r.json())
                    //  .then(({ html }) => modalContent.innerHTML = html)
                    //  .catch(() => modalContent.innerHTML = '<p class="text-red-600">Gagal memuat detail.</p>');
                }
                function closeDetailModal() { detailModal.classList.add('hidden'); }

                function openDeleteJurusanModal(id, name) {
                    currentJurusanId = id;
                    document.getElementById('deleteJurusanName').textContent = name;
                    document.getElementById('deleteJurusanModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    document.getElementById('confirmDeleteJurusan').focus();
                }
                
                function closeDeleteJurusanModal() {
                    document.getElementById('deleteJurusanModal').classList.add('hidden');
                    document.body.style.overflow = '';
                    currentJurusanId = null;
                }
                
                let currentJurusanId = null;

                function hookStatusFormsLoading() {
                    const forms = $$('form[action*="toggle-jurusan-status"]');
                    forms.forEach(form => {
                        on(form, 'submit', () => {
                            const btn = form.querySelector('button[type="submit"]');
                            if (!btn) return;
                            const original = btn.innerHTML;
                            btn.innerHTML =
                                '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses...';
                            btn.disabled = true;
                            setTimeout(() => { btn.innerHTML = original; btn.disabled = false; }, 5000);
                        });
                    });
                }

                function hookKeyboardShortcuts() {
                    on(document, 'keydown', e => {
                        if (e.key === 'Escape') {
                            closeDetailModal();
                            closeDeleteModal();
                            closeAllMenus();
                        }
                    });
                }

                function hookCardAnimations() {
                    const observer = new IntersectionObserver(entries => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) entry.target.style.animation = 'fadeInUp 0.6s ease-out';
                        });
                    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

                    jurusanCards.forEach(card => observer.observe(card));
                }

                optionButtons.forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.stopPropagation();
                        toggleMenu(btn.dataset.jurusan);
                    });
                });
                document.addEventListener('click', () => optionMenus.forEach(m => m.classList.add('hidden')));

                viewDetailBtns.forEach(btn => {
                    on(btn, 'click', e => {
                        e.preventDefault();
                        openDetailModal(btn.dataset.jurusan);
                    });
                });
                on(closeModalBtn, 'click', closeDetailModal);
                on(detailModal, 'click', e => { if (e.target === detailModal) closeDetailModal(); });

                deleteBtns.forEach(btn => {
                    on(btn, 'click', e => {
                        e.preventDefault();
                        openDeleteJurusanModal(btn.dataset.jurusan, btn.dataset.name);
                    });
                });
                
                // Delete jurusan modal event handlers
                const deleteJurusanModalOverlay = $('#deleteJurusanModalOverlay');
                const cancelDeleteJurusan = $('#cancelDeleteJurusan');
                const confirmDeleteJurusan = $('#confirmDeleteJurusan');
                
                on(deleteJurusanModalOverlay, 'click', closeDeleteJurusanModal);
                on(cancelDeleteJurusan, 'click', closeDeleteJurusanModal);
                
                on(confirmDeleteJurusan, 'click', () => {
                    if (currentJurusanId) {
                        // Create and submit form dynamically
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/superadmin/jurusan/${currentJurusanId}`;
                        form.style.display = 'none';
                        
                        // Add CSRF token
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        form.appendChild(csrfInput);
                        
                        // Add method override
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
                
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !$('#deleteJurusanModal').classList.contains('hidden')) closeDeleteJurusanModal();
                });

                @if(session('success')) showToast(@json(session('success')), 'success'); @endif
                @if(session('error'))   showToast(@json(session('error')),   'error');   @endif
                @if(session('warning')) showToast(@json(session('warning')), 'warning'); @endif

                hookStatusFormsLoading();
                hookKeyboardShortcuts();
                hookCardAnimations();
            })();
        </script>

        <style>
            @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
            .card { animation: fadeInUp 0.6s ease-out; }
            .options-menu {
                box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
            .jurusan-card:hover { transform: translateY(-2px); }
            #modalContent { scrollbar-width: thin; scrollbar-color: #cbd5e0 #f7fafc; }
            #modalContent::-webkit-scrollbar { width: 6px; }
            #modalContent::-webkit-scrollbar-track { background: #f7fafc; }
            #modalContent::-webkit-scrollbar-thumb { background-color: #cbd5e0; border-radius: 3px; }
            @media (max-width: 768px) {
                .jurusan-card { margin-bottom: 1rem; }
                .options-menu {
                    position: fixed; bottom: 0; left: 0; right: 0; width: 100%;
                    border-radius: 1rem 1rem 0 0; transform: translateY(100%); transition: transform 0.3s ease;
                }
                .options-menu:not(.hidden) { transform: translateY(0); }
            }
        </style>
    @endpush
</x-app-layout>
