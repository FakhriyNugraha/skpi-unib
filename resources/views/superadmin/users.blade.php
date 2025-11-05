<x-app-layout>
    <x-slot name="title">Kelola Users</x-slot>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Users</h1>
                    <p class="text-gray-600 mt-2">Manajemen pengguna sistem SKPI</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('superadmin.create-user') }}" class="btn-primary">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah User
                    </a>
                    <a href="{{ route('superadmin.dashboard') }}" class="btn-outline">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $users->where('role', 'user')->count() }}</p>
                        <p class="text-sm text-gray-600">Mahasiswa</p>
                    </div>
                </div>
            </div>
            <div class="card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.5-1.5a11 11 0 00-1.5-5.5m1.5 5.5a11 11 0 01-1.5 5.5m1.5-5.5L20 12"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $users->where('role', 'admin')->count() }}</p>
                        <p class="text-sm text-gray-600">Admin</p>
                    </div>
                </div>
            </div>
            <div class="card p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $users->where('role', 'superadmin')->count() }}</p>
                        <p class="text-sm text-gray-600">Super Admin</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card p-6 mb-8">
            <form method="GET" action="{{ route('superadmin.users') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select name="role" class="input-field">
                            <option value="">Semua Role</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan</label>
                        <select name="jurusan" class="input-field">
                            <option value="">Semua Jurusan</option>
                            @foreach(\App\Models\Jurusan::all() as $jurusan)
                                <option value="{{ $jurusan->id }}" {{ request('jurusan') == $jurusan->id ? 'selected' : '' }}>
                                    {{ $jurusan->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="input-field">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full">Filter</button>
                    </div>
                </div>
                <div class="mt-4">
                    <input type="text" name="search" placeholder="Cari nama, email, NPM..." class="input-field" 
                           value="{{ request('search') }}">
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="card">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jurusan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($user->avatar)
                                        <img class="h-10 w-10 rounded-full object-cover mr-3" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-unib-blue-100 flex items-center justify-center mr-3">
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
                                        @elseif($user->nip)
                                            <div class="text-xs text-gray-400">NIP: {{ $user->nip }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($user->role == 'superadmin') bg-purple-100 text-purple-800
                                    @elseif($user->role == 'admin') bg-green-100 text-green-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->jurusan->nama_jurusan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $user->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                <a href="{{ route('superadmin.edit-user', $user) }}" class="inline-flex items-center px-3 py-1 rounded bg-amber-500 text-white border-2 border-amber-500 hover:bg-amber-600">
                                    Edit
                                </a>
                                @if($user->id != auth()->id())
                                <button 
                                    type="button"
                                    class="inline-flex items-center px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700 delete-user-btn"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}">
                                    Hapus
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <p class="text-gray-500">Tidak ada data user</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $users->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <x-confirmation-modal name="delete-user" />

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to all delete user buttons
            const deleteButtons = document.querySelectorAll('.delete-user-btn');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const userId = this.dataset.userId;
                    const userName = this.dataset.userName;
                    
                    // Open confirmation modal
                    window.dispatchEvent(new CustomEvent('open-confirmation-modal', {
                        detail: {
                            name: 'delete-user',
                            title: 'Hapus User',
                            content: `Apakah Anda yakin ingin menghapus user <span class="font-medium">${userName}</span>? Tindakan ini tidak dapat dibatalkan.`,
                            confirmText: 'Hapus',
                            cancelText: 'Batal',
                            confirmClass: 'bg-red-600 text-white',
                            action: `delete-user-${userId}-form`
                        }
                    }));
                    
                    // Create and add the hidden delete form if it doesn't exist yet
                    let deleteForm = document.querySelector(`#delete-user-${userId}-form`);
                    if (!deleteForm) {
                        deleteForm = document.createElement('form');
                        deleteForm.id = `delete-user-${userId}-form`;
                        deleteForm.method = 'POST';
                        deleteForm.action = `/superadmin/users/${userId}`;
                        deleteForm.style.display = 'none';
                        
                        // Add CSRF token and method
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken;
                        deleteForm.appendChild(csrfInput);
                        
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        deleteForm.appendChild(methodInput);
                        
                        document.body.appendChild(deleteForm);
                    }
                });
            });
        });
    </script>

    <!-- Delete User Confirmation Modal -->
    <div id="deleteUserModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div id="deleteUserModalOverlay" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 text-center">
                        Hapus User
                    </h3>
                    <p class="mt-3 text-sm text-gray-600 text-center">
                        Apakah Anda yakin ingin menghapus user <span id="deleteUserName" class="font-medium"></span>? 
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div class="mt-6 flex flex-col sm:flex-row justify-center sm:gap-3 gap-2">
                        <button type="button" id="cancelDeleteUser"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batal
                        </button>
                        <button type="button" id="confirmDeleteUser"
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete User Modal
            const deleteButtons = document.querySelectorAll('.delete-user-btn');
            const deleteUserModal = document.getElementById('deleteUserModal');
            const deleteUserModalOverlay = document.getElementById('deleteUserModalOverlay');
            const btnCancelDeleteUser = document.getElementById('cancelDeleteUser');
            const btnConfirmDeleteUser = document.getElementById('confirmDeleteUser');
            const deleteUserName = document.getElementById('deleteUserName');
            let currentUserId = null;

            const openDeleteUser = (userId, userName) => {
                currentUserId = userId;
                deleteUserName.textContent = userName;
                deleteUserModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                btnConfirmDeleteUser.focus();
            };
            
            const closeDeleteUser = () => {
                deleteUserModal.classList.add('hidden');
                document.body.style.overflow = '';
                currentUserId = null;
            };

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const userId = this.dataset.userId;
                    const userName = this.dataset.userName;
                    openDeleteUser(userId, userName);
                });
            });

            deleteUserModalOverlay?.addEventListener('click', closeDeleteUser);
            btnCancelDeleteUser?.addEventListener('click', closeDeleteUser);
            
            btnConfirmDeleteUser?.addEventListener('click', () => {
                if (currentUserId) {
                    // Create and submit form dynamically
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/superadmin/users/${currentUserId}`;
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

            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !deleteUserModal.classList.contains('hidden')) closeDeleteUser();
            });
        });
    </script>

</x-app-layout>