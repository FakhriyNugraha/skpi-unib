<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Cetak Banyak SKPI</h1>
            <p class="text-gray-600 mt-1">Pilih SKPI yang ingin dicetak atau cetak semua SKPI approved</p>
        </div>

        <!-- Filter Section -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="periode_wisuda_filter" class="block text-sm font-medium text-gray-700 mb-2">Periode Wisuda</label>
                    <select id="periode_wisuda_filter" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-unib-blue-300 focus:ring focus:ring-unib-blue-200 focus:ring-opacity-50">
                        <option value="">Semua Periode</option>
                        @foreach($availablePeriods as $period)
                            <option value="{{ $period['number'] }}">{{ $period['title'] }} ({{ $period['number'] }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="npm_filter" class="block text-sm font-medium text-gray-700 mb-2">Cari NPM</label>
                    <input type="text" id="npm_filter" placeholder="Masukkan NPM..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-unib-blue-300 focus:ring focus:ring-unib-blue-200 focus:ring-opacity-50">
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <div class="flex flex-wrap gap-3">
                    <button type="button" id="select-all-btn" class="btn bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 text-sm transition-all duration-200 shadow-md rounded-lg">
                        Pilih Semua
                    </button>
                    <button type="button" id="deselect-all-btn" class="btn bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 text-sm transition-all duration-200 shadow-md rounded-lg">
                        Batal Pilih Semua
                    </button>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="button" id="print-selected-btn" class="btn bg-teknik-orange-500 hover:bg-teknik-orange-600 text-white px-4 py-2 text-sm transition-all duration-200 shadow-md flex items-center rounded-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak SKPI Terpilih
                    </button>
                    <button type="button" id="print-all-btn" class="btn bg-unib-blue-600 hover:bg-unib-blue-700 text-white px-4 py-2 text-sm transition-all duration-200 shadow-md flex items-center rounded-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m4 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak Semua SKPI (Approved)
                    </button>
                </div>
            </div>

            <div id="selected-count" class="text-sm font-medium text-gray-700 bg-blue-50 px-4 py-2 rounded-lg border border-blue-200 mb-4">
                <span id="count-number">0</span> SKPI dipilih
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table id="skpi-table" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                <input type="checkbox" id="master-checkbox" class="rounded border-gray-300 text-unib-blue-600 shadow-sm focus:border-unib-blue-300 focus:ring focus:ring-unib-blue-200 focus:ring-opacity-50">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mahasiswa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                NPM
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jurusan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Periode Wisuda
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($skpis as $skpi)
                        <tr data-periode="{{ $skpi->periode_wisuda ?? '' }}" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="skpi_ids[]" value="{{ $skpi->id }}" class="skpi-checkbox rounded border-gray-300 text-unib-blue-600 shadow-sm focus:border-unib-blue-300 focus:ring focus:ring-unib-blue-200 focus:ring-opacity-50">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $skpi->nama_lengkap }}</div>
                                <div class="text-sm text-gray-500">{{ $skpi->tempat_lahir }}, {{ \Carbon\Carbon::parse($skpi->tanggal_lahir)->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $skpi->npm }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $skpi->jurusan->nama_jurusan ?? 'Tidak ada jurusan' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $skpi->periode_wisuda ? 'Periode '.$skpi->periode_wisuda : 'Approved' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada SKPI approved yang ditemukan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($skpis->count() > 0)
            <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
                <div class="text-sm text-gray-700">
                    Menampilkan <span class="font-medium">{{ $skpis->count() }}</span> SKPI approved
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const masterCheckbox = document.getElementById('master-checkbox');
            const checkboxes = document.querySelectorAll('.skpi-checkbox');
            const selectAllBtn = document.getElementById('select-all-btn');
            const deselectAllBtn = document.getElementById('deselect-all-btn');
            const printSelectedBtn = document.getElementById('print-selected-btn');
            const printAllBtn = document.getElementById('print-all-btn');
            const selectedCount = document.getElementById('selected-count');
            const countNumber = document.getElementById('count-number');

            // Update counter
            function updateCounter() {
                const checkedCount = document.querySelectorAll('.skpi-checkbox:checked').length;
                countNumber.textContent = checkedCount;
            }

            // Master checkbox event
            masterCheckbox.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateCounter();
            });

            // Individual checkbox events
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateCounter);
            });

            // Select all button
            selectAllBtn.addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                masterCheckbox.checked = true;
                updateCounter();
            });

            // Deselect all button
            deselectAllBtn.addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                masterCheckbox.checked = false;
                updateCounter();
            });

            // Print selected button
            printSelectedBtn.addEventListener('click', function() {
                const selectedIds = Array.from(checkboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);

                if (selectedIds.length === 0) {
                    alert('Silakan pilih setidaknya satu SKPI untuk dicetak.');
                    return;
                }

                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.print-bulk') }}';

                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Add selected IDs
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'skpi_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            });

            // Print all button
            printAllBtn.addEventListener('click', function() {
                window.dispatchEvent(new CustomEvent('open-generic-confirmation', {
                    detail: {
                        title: 'Konfirmasi Cetak Semua',
                        content: 'Apakah Anda yakin ingin mencetak semua SKPI yang approved?',
                        actionType: 'save', // Using save color since it's a print action
                        confirmAction: 'window.location.href = \'{{ route('admin.print-bulk-all') }}\''
                    }
                }));
            });

            // Initialize counter
            updateCounter();
        });

        // Add filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const periodeFilter = document.getElementById('periode_wisuda_filter');
            const npmFilter = document.getElementById('npm_filter');
            const tableRows = document.querySelectorAll('#skpi-table tbody tr');

            function applyFilters() {
                const selectedPeriode = periodeFilter.value;
                const npmInput = npmFilter.value.toLowerCase().trim();

                tableRows.forEach(row => {
                    // Find NPM data in the row (in the third column)
                    const rowNpmElement = row.querySelector('td:nth-child(3) .text-sm.text-gray-900');
                    const rowNpm = rowNpmElement ? rowNpmElement.textContent.toLowerCase().trim() : '';

                    // Check if row has data attribute for periode (we'll add this in PHP part later)
                    const dataPeriode = row.getAttribute('data-periode') || '';

                    // Apply filters
                    const matchesNpm = !npmInput || rowNpm.includes(npmInput);
                    const matchesPeriode = !selectedPeriode || dataPeriode.includes(selectedPeriode);

                    // Show row if all active filters match
                    if (matchesNpm && matchesPeriode) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Add event listeners for filters
            periodeFilter.addEventListener('change', applyFilters);
            npmFilter.addEventListener('input', applyFilters);
        });
    </script>
</x-app-layout>