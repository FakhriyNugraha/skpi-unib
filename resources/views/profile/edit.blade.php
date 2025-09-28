{{-- resources/views/profile/edit.blade.php --}}
<x-app-layout>
    <x-slot name="title">Edit Profile</x-slot>

    {{-- Toasts (popup, anti-duplikasi) --}}
    <div x-data="toastComp()" x-init="init()" class="fixed top-4 right-4 z-50 space-y-3 pointer-events-none">
        <template x-for="t in toasts" :key="t.id">
            <div
                x-show="t.show"
                x-transition.opacity.scale.80
                class="pointer-events-auto w-80 rounded-xl shadow-lg text-white overflow-hidden"
                :class="{
                    'bg-green-600': t.type === 'success',
                    'bg-red-600': t.type === 'error',
                    'bg-blue-600': t.type === 'info',
                    'bg-gray-800': !['success','error','info'].includes(t.type)
                }"
            >
                <div class="p-4 flex">
                    <div class="flex-shrink-0 mt-0.5">
                        <template x-if="t.type === 'success'">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <template x-if="t.type === 'error'">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </template>
                        <template x-if="t.type === 'info'">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 18a6 6 0 100-12 6 6 0 000 12z"/>
                            </svg>
                        </template>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-semibold" x-text="t.title"></p>
                        <p class="mt-1 text-sm" x-text="t.message"></p>
                    </div>
                    <button class="ml-4 text-white/80 hover:text-white" @click="close(t.id)">✕</button>
                </div>
            </div>
        </template>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit Profile</h1>
            <p class="text-gray-600 mt-2">Kelola informasi profil dan pengaturan akun Anda</p>
        </div>

        <div class="space-y-8">
            {{-- PROFILE INFO --}}
            <div class="card p-6">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-unib-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900">Informasi Profile</h2>
                </div>

                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Avatar --}}
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-4">Foto Profile</label>
                            <div class="flex flex-col items-center">
                                <div class="relative" id="avatarPreviewWrapper">
                                    @if($user->avatar)
                                        <img id="avatarPreview"
                                             class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg"
                                             src="{{ Storage::url($user->avatar) }}"
                                             alt="{{ $user->name }}">
                                    @else
                                        <div id="avatarFallback"
                                             class="h-24 w-24 rounded-full bg-unib-blue-600 flex items-center justify-center border-4 border-white shadow-lg">
                                            <span class="text-2xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    @endif

                                    <label for="avatarInput"
                                           class="absolute bottom-0 right-0 bg-teknik-orange-500 rounded-full p-2 cursor-pointer hover:bg-teknik-orange-600 transition-colors"
                                           title="Ganti foto">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </label>
                                </div>

                                <input type="file" id="avatarInput" name="avatar" class="hidden" accept="image/*">
                                <p class="mt-2 text-xs text-gray-500 text-center">JPG/PNG, maks 2MB</p>
                                @error('avatar')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Fields --}}
                        <div class="md:col-span-2 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                    <input id="name" name="name" type="text"
                                           class="input-field @error('name') border-red-500 @enderror"
                                           value="{{ old('name', $user->name) }}" required autofocus>
                                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input id="email" name="email" type="email"
                                           class="input-field @error('email') border-red-500 @enderror"
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-800">
                                                Email Anda belum diverifikasi.
                                                <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900">
                                                    Klik disini untuk kirim ulang verifikasi.
                                                </button>
                                            </p>
                                            @if (session('status') === 'verification-link-sent')
                                                <p class="mt-2 font-medium text-sm text-green-600">
                                                    Link verifikasi baru telah dikirim ke email Anda.
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                @if($user->role === 'user')
                                    <div>
                                        <label for="npm" class="block text-sm font-medium text-gray-700 mb-2">NPM</label>
                                        <input id="npm" name="npm" type="text"
                                               class="input-field @error('npm') border-red-500 @enderror"
                                               value="{{ old('npm', $user->npm) }}">
                                        @error('npm') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                @elseif(in_array($user->role, ['admin', 'superadmin']))
                                    <div>
                                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">NIP</label>
                                        <input id="nip" name="nip" type="text"
                                               class="input-field @error('nip') border-red-500 @enderror"
                                               value="{{ old('nip', $user->nip) }}">
                                        @error('nip') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                @endif

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                                    <input id="phone" name="phone" type="text"
                                           class="input-field @error('phone') border-red-500 @enderror"
                                           value="{{ old('phone', $user->phone) }}"
                                           placeholder="Isi nomor telepon aktif">
                                    @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                                    {{-- Info kecil jika kosong --}}
                                    @if(empty($user->phone))
                                        <p class="mt-1 text-xs text-teknik-orange-600">
                                            ⚠️ Nomor telepon belum ada. Harap diisi untuk kebutuhan kontak.
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                <textarea id="address" name="address" rows="3"
                                          class="input-field @error('address') border-red-500 @enderror"
                                          placeholder="Masukkan alamat lengkap">{{ old('address', $user->address) }}</textarea>
                                @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200">
                        <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            {{-- UPDATE PASSWORD --}}
            <div class="card p-6">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-unib-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900">Update Password</h2>
                </div>

                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                            <input id="update_password_current_password" name="current_password" type="password"
                                   class="input-field @error('current_password', 'updatePassword') border-red-500 @enderror"
                                   autocomplete="current-password">
                            @error('current_password', 'updatePassword') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                            <input id="update_password_password" name="password" type="password"
                                   class="input-field @error('password', 'updatePassword') border-red-500 @enderror"
                                   autocomplete="new-password">
                            @error('password', 'updatePassword') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                                   class="input-field @error('password_confirmation', 'updatePassword') border-red-500 @enderror"
                                   autocomplete="new-password">
                            @error('password_confirmation', 'updatePassword') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 pt-6 border-t border-gray-200">
                        <button type="submit" class="btn-primary">Update Password</button>
                    </div>
                </form>
            </div>

            {{-- DELETE ACCOUNT --}}
            <div class="card p-6 border-red-200 bg-red-50">
                <div class="flex items-center mb-6">
                    <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <h2 class="text-xl font-semibold text-red-900">Hapus Akun</h2>
                </div>

                <p class="text-sm text-red-700 mb-6">
                    Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen.
                    Sebelum menghapus akun, harap unduh data atau informasi apa pun yang ingin Anda simpan.
                </p>

                <button
                    x-data
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                    class="bg-red-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-red-700 transition-colors">
                    Hapus Akun
                </button>

                {{-- Modal Jetstream --}}
                <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                    <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                        @csrf
                        @method('delete')

                        <h2 class="text-lg font-medium text-gray-900">Apakah Anda yakin ingin menghapus akun?</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen.
                            Masukkan password untuk konfirmasi penghapusan akun.
                        </p>

                        <div class="mt-6">
                            <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                            <input id="password" name="password" type="password" class="input-field" placeholder="Password">
                            @error('password', 'userDeletion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="button" x-on:click="$dispatch('close')" class="btn-outline mr-3">Batal</button>
                            <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-red-700 transition-colors">
                                Hapus Akun
                            </button>
                        </div>
                    </form>
                </x-modal>
            </div>
        </div>
    </div>

    {{-- form verifikasi email untuk tombol di atas --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- Script: Toast & Preview avatar --}}
    <script>
        // ===== Global helper agar bisa dipanggil: window.showToast('pesan','success','Judul');
        window.showToast = function(message, type = 'info', title = null) {
            window.dispatchEvent(new CustomEvent('app:toast', { detail: { message, type, title } }));
        };

        // ===== Alpine toast component (anti-duplikasi)
        function toastComp() {
            return {
                toasts: [],
                _id: 0,
                push(title, message, type = 'info', timeout = 4000) {
                    const id = ++this._id;
                    const t = { id, title, message, type, show: true };
                    this.toasts.push(t);
                    setTimeout(() => this.close(id), timeout);
                },
                close(id) {
                    const t = this.toasts.find(x => x.id === id);
                    if (t) t.show = false;
                },
                init() {
                    if (window.__TOAST_INIT_DONE__) return;
                    window.__TOAST_INIT_DONE__ = true;

                    const handler = (e) => {
                        const d = e.detail || {};
                        const type  = d.type  || 'info';
                        const title = d.title || (type === 'success' ? 'Berhasil' : type === 'error' ? 'Gagal' : 'Info');
                        const msg   = d.message || '';
                        this.push(title, msg, type);
                    };
                    window.addEventListener('app:toast', handler);

                    @if(session()->has('success'))
                        this.push('Berhasil', @json(session('success')), 'success');
                    @elseif(session()->has('error'))
                        this.push('Gagal', @json(session('error')), 'error');
                    @elseif(session()->has('info'))
                        this.push('Info', @json(session('info')), 'info');
                    @elseif(session('status') === 'profile-updated')
                        this.push('Berhasil', 'Profil berhasil diperbarui.', 'success');
                    @endif

                    const errors = @json($errors->all());
                    if (Array.isArray(errors) && errors.length) {
                        const extra = errors.length > 1 ? ` (+${errors.length - 1} lainnya)` : '';
                        this.push('Validasi gagal', `${errors[0]}${extra}`, 'error');
                    }
                }
            }
        }

        // ===== Preview avatar
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('avatarInput');
            const wrapper = document.getElementById('avatarPreviewWrapper');
            let img = document.getElementById('avatarPreview');
            const fallback = document.getElementById('avatarFallback');

            if (!input) return;

            input.addEventListener('change', function (e) {
                const file = e.target.files && e.target.files[0];
                if (!file) return;

                if (!file.type || !file.type.startsWith('image/')) {
                    showToast('Tipe file tidak valid. Pilih gambar JPG/PNG.', 'error', 'Gagal');
                    input.value = '';
                    return;
                }
                const maxBytes = 2 * 1024 * 1024;
                if (file.size > maxBytes) {
                    showToast('Ukuran gambar maksimum 2MB.', 'error', 'Gagal');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (ev) {
                    if (!img) {
                        img = document.createElement('img');
                        img.id = 'avatarPreview';
                        img.className = 'h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg';
                        wrapper.insertBefore(img, wrapper.firstChild);
                    }
                    img.src = ev.target.result;
                    if (fallback) fallback.classList.add('hidden');
                    showToast('Preview foto diperbarui (belum disimpan).', 'info', 'Preview');
                }
                reader.readAsDataURL(file);
            });
        });
    </script>
</x-app-layout>
