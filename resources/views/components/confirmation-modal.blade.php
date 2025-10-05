@props([
    'name' => '',
    'show' => false,
    'maxWidth' => 'lg',
    'action' => '',
    'title' => 'Konfirmasi Aksi',
    'content' => 'Apakah Anda yakin ingin melakukan aksi ini?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'confirmClass' => 'bg-red-600 text-white',
    'requirePassword' => false,
    'passwordPlaceholder' => 'Password'
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{
        show: false,
        action: '',
        requirePassword: @js($requirePassword),
        password: '',
        title: @js($title),
        content: @js($content),
        confirmText: @js($confirmText),
        cancelText: @js($cancelText),
        confirmClass: @js($confirmClass),
        passwordPlaceholder: @js($passwordPlaceholder),
        init() {
            // Listen for open modal events
            document.addEventListener('open-confirmation-modal', (e) => {
                if (e.detail.name === '{{ $name }}') {
                    this.show = true;
                    this.title = e.detail.title || @js($title);
                    this.content = e.detail.content || @js($content);
                    this.confirmText = e.detail.confirmText || @js($confirmText);
                    this.cancelText = e.detail.cancelText || @js($cancelText);
                    this.confirmClass = e.detail.confirmClass || @js($confirmClass);
                    this.requirePassword = e.detail.requirePassword || @js($requirePassword);
                    this.passwordPlaceholder = e.detail.passwordPlaceholder || @js($passwordPlaceholder);
                    this.action = e.detail.action || '';
                    document.body.classList.add('overflow-y-hidden');
                    
                    // Focus password input if required
                    if (this.requirePassword) {
                        this.$nextTick(() => {
                            const passwordInput = this.$refs.passwordInput;
                            if (passwordInput) {
                                passwordInput.focus();
                            }
                        });
                    }
                }
            });
            
            // Listen for close modal events
            document.addEventListener('close-confirmation-modal', (e) => {
                if (e.detail.name === '{{ $name }}') {
                    this.close();
                }
            });
        },
        close() {
            this.show = false;
            this.password = '';
            document.body.classList.remove('overflow-y-hidden');
        },
        submit() {
            if (this.requirePassword && !this.password.trim()) {
                alert('Silakan masukkan password untuk konfirmasi.');
                return;
            }
            
            if (this.action) {
                // Handle form with password if required
                const form = document.getElementById(this.action);
                if (form) {
                    if (this.requirePassword) {
                        let passwordField = form.querySelector('input[name=\"password\"]');
                        if (!passwordField) {
                            // Create password field if it doesn't exist
                            passwordField = document.createElement('input');
                            passwordField.type = 'hidden';
                            passwordField.name = 'password';
                            form.appendChild(passwordField);
                        }
                        passwordField.value = this.password;
                    }
                    form.submit();
                }
            }
            this.close();
        }
    }"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-[100]"
    style="display: {{ $show ? 'block' : 'none' }};"
    x-cloak
>
    <!-- Background overlay -->
    <div 
        x-show="show" 
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 transform transition-all"
        x-on:click="close"
    >
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <!-- Modal content -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="mb-6 bg-white rounded-xl shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto overflow-hidden"
    >
        <div class="p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <!-- Warning icon -->
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="title">Konfirmasi Aksi</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-600" x-text="content">Apakah Anda yakin ingin melakukan aksi ini?</p>
                    </div>
                    
                    <!-- Password field if required -->
                    <template x-if="requirePassword">
                        <div class="mt-4">
                            <label for="confirm-password-{{ $name }}" class="block text-sm font-medium text-gray-700">Password</label>
                            <input 
                                x-model="password" 
                                id="confirm-password-{{ $name }}" 
                                type="password" 
                                class="mt-1 block w-full input-field" 
                                :placeholder="passwordPlaceholder"
                                x-ref="passwordInput"
                                @keydown.enter.prevent="submit"
                            />
                        </div>
                    </template>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button 
                    x-on:click="close"
                    type="button" 
                    class="btn-outline"
                >
                    <span x-text="cancelText">Batal</span>
                </button>
                <button 
                    x-on:click="submit"
                    type="button" 
                    :class="confirmClass + ' px-4 py-2 rounded-lg font-medium hover:opacity-90 transition-opacity'"
                >
                    <span x-text="confirmText">Ya, Lanjutkan</span>
                </button>
            </div>
        </div>
    </div>
</div>