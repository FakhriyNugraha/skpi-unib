{{-- resources/views/superadmin/skpi-show.blade.php --}}
<x-app-layout>
    <x-slot name="title">Detail SKPI</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail SKPI</h1>
                <p class="text-gray-600 mt-2">Tinjau ringkasan data SKPI mahasiswa</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('superadmin.all-skpi') }}" class="btn-outline px-3 py-2 text-sm">Kembali</a>
                @if($skpi->status === 'approved')
                    <a href="{{ route('superadmin.skpi.print', $skpi) }}" target="_blank" class="btn-primary px-3 py-2 text-sm">Cetak</a>
                @endif
            </div>
        </div>

        {{-- Flash --}}
        @foreach (['success'=>'green','error'=>'red','warning'=>'yellow'] as $key=>$color)
            @if(session($key))
                <div class="mb-4 rounded-xl bg-{{ $color }}-50 border border-{{ $color }}-200 px-4 py-3 text-{{ $color }}-800">
                    {{ session($key) }}
                </div>
            @endif
        @endforeach

        {{-- Identitas + Status --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="card p-6 lg:col-span-2">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Identitas Mahasiswa</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Nama Lengkap</dt>
                        <dd class="font-medium text-gray-900">{{ $skpi->nama_lengkap ?? ($skpi->user->name ?? '—') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">NIM</dt>
                        <dd class="font-medium text-gray-900">{{ $skpi->nim ?? ($skpi->user->npm ?? '—') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Program Studi</dt>
                        <dd class="font-medium text-gray-900">{{ $skpi->program_studi ?? ($skpi->jurusan->nama_jurusan ?? '—') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Gelar</dt>
                        <dd class="font-medium text-gray-900">{{ $skpi->gelar ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Nomor Ijazah</dt>
                        <dd class="font-medium text-gray-900">{{ $skpi->nomor_ijazah ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Tanggal Lulus</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $skpi->tanggal_lulus ? \Carbon\Carbon::parse($skpi->tanggal_lulus)->format('d M Y') : '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Tempat/Tanggal Lahir</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $skpi->tempat_lahir ? $skpi->tempat_lahir : '—' }}
                            @if($skpi->tanggal_lahir)
                                , {{ \Carbon\Carbon::parse($skpi->tanggal_lahir)->format('d M Y') }}
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">IPK</dt>
                        <dd class="font-medium text-gray-900">{{ $skpi->ipk ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Status SKPI</h2>
                @php
                    $status = strtolower($skpi->status ?? 'draft');
                    $badge  = match ($status) {
                        'approved'  => 'bg-green-100 text-green-800',
                        'submitted' => 'bg-blue-100 text-blue-800',
                        'rejected'  => 'bg-red-100 text-red-800',
                        default     => 'bg-yellow-100 text-yellow-800',
                    };
                @endphp
                <p class="mb-4">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium {{ $badge }}">
                        {{ ucfirst($status) }}
                    </span>
                </p>
                <dl class="text-sm space-y-2">
                    <div>
                        <dt class="text-gray-500">Reviewer</dt>
                        <dd class="font-medium text-gray-900">{{ $skpi->reviewer->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Approver</dt>
                        <dd class="font-medium text-gray-900">{{ $skpi->approver->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Diperbarui</dt>
                        <dd class="font-medium text-gray-900">{{ optional($skpi->updated_at)->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Ringkasan Konten --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Prestasi</h3>
                <dl class="text-sm space-y-3">
                    <div>
                        <dt class="text-gray-500 mb-1">Prestasi Akademik</dt>
                        <dd class="whitespace-pre-line">{{ $skpi->prestasi_akademik ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">Prestasi Non-Akademik</dt>
                        <dd class="whitespace-pre-line">{{ $skpi->prestasi_non_akademik ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Aktivitas</h3>
                <dl class="text-sm space-y-3">
                    <div>
                        <dt class="text-gray-500 mb-1">Organisasi</dt>
                        <dd class="whitespace-pre-line">{{ $skpi->organisasi ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">Pengalaman Kerja</dt>
                        <dd class="whitespace-pre-line">{{ $skpi->pengalaman_kerja ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">Catatan Khusus</dt>
                        <dd class="whitespace-pre-line">{{ $skpi->catatan_khusus ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Section Dokumen dan Sertifikat -->
            @if($skpi->drive_link)
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Dokumen</h3>
                <div class="flex items-center">
                    <a href="{{ $skpi->drive_link }}" target="_blank" class="btn-primary text-sm px-4 py-2 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Lihat Sertifikat
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Script untuk tombol periksa -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkDriveBtn = document.getElementById('checkDriveBtn');
            if (checkDriveBtn) {
                checkDriveBtn.addEventListener('click', async function() {
                    const driveLink = "{{ $skpi->drive_link }}";
                    const scanResults = document.getElementById('scanResults');
                    const scanResultsContent = document.getElementById('scanResultsContent');

                    if (!driveLink) {
                        showToast('Tidak ada link drive yang tersedia.', 'error', 'Gagal');
                        return;
                    }

                    // Tampilkan pesan memproses
                    scanResultsContent.innerHTML = 'Memproses pemeriksaan...';
                    scanResults.classList.remove('hidden');

                    try {
                        // Lakukan permintaan ke server untuk memeriksa link drive
                        const response = await fetch(`/superadmin/skpi/${encodeURIComponent({{ $skpi->id }})}/verify-drive`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (response.ok) {
                            let resultHtml = '<ul class="space-y-2">';

                            if (result.type === 'folder') {
                                resultHtml += `<li class="flex items-start"><span class="text-green-500 mr-2">✓</span> Tipe: Folder Google Drive</li>`;
                                resultHtml += `<li class="flex items-start"><span class="text-green-500 mr-2">✓</span> Jumlah file: ${result.file_count}</li>`;
                                if (result.files && result.files.length > 0) {
                                    resultHtml += `<li class="ml-6"><strong>File dalam folder:</strong></li>`;
                                    result.files.forEach(file => {
                                        resultHtml += `<li class="ml-8 flex items-start"><span class="text-green-500 mr-2">✓</span> ${file.name} (${file.type})</li>`;
                                    });
                                }
                            } else {
                                resultHtml += `<li class="flex items-start"><span class="text-green-500 mr-2">✓</span> Tipe: File ${result.type}</li>`;
                                resultHtml += `<li class="flex items-start"><span class="text-green-500 mr-2">✓</span> Ukuran: ${result.size || 'Tidak diketahui'}</li>`;
                            }

                            resultHtml += `<li class="flex items-start"><span class="text-green-500 mr-2">✓</span> Akses: ${result.accessible ? 'Dapat diakses' : 'Tidak dapat diakses'}</li>`;
                            resultHtml += '</ul>';

                            scanResultsContent.innerHTML = resultHtml;
                        } else {
                            scanResultsContent.innerHTML = `<span class="text-red-600">${result.message || 'Gagal memeriksa link drive'}</span>`;
                        }
                    } catch (error) {
                        console.error('Error checking drive link:', error);
                        scanResultsContent.innerHTML = '<span class="text-red-600">Terjadi kesalahan saat memeriksa link drive.</span>';
                    }
                });
            }
        });

        // Fungsi untuk menampilkan toast (jika belum ada)
        if (typeof showToast === 'undefined') {
            window.showToast = function(message, type = 'info', title = null) {
                // Gunakan fungsi toast yang sama seperti di halaman edit profil jika ada
                if (window.dispatchEvent && CustomEvent) {
                    const detail = { message, type, title };
                    window.dispatchEvent(new CustomEvent('app:toast', { detail }));
                } else {
                    alert(message); // Fallback jika tidak ada toast
                }
            };
        }
    </script>
</x-app-layout>
