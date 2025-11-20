<x-app-layout>
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-unib-blue-900 via-unib-blue-800 to-unib-blue-700 text-white relative overflow-hidden -mt-16">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-6">
                        Sistem Informasi<br>
                        <span class="text-teknik-orange-400 animate-pulse">SKPI</span><br>
                        <span class="text-3xl md:text-4xl">Universitas Bengkulu</span>
                    </h1>
                    <p class="text-xl text-gray-200 mb-8 leading-relaxed">
                        Surat Keterangan Pendamping Ijazah (SKPI) Fakultas Teknik - 
                        Platform digital modern untuk dokumentasi prestasi akademik dan non-akademik mahasiswa.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 justify-center lg:justify-start">
                        @auth
                            {{-- BUTTON DASHBOARD SAYA --}}
                            <a href="{{ route('dashboard') }}" class="btn-secondary text-center transform transition duration-300 ease-out hover:scale-105 hover:-translate-y-1 hover:shadow-2xl">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Dashboard Saya
                            </a>
                        @else
                            {{-- BUTTON LOGIN HERO --}}
                            <a href="{{ route('login') }}" class="btn-secondary text-center transform transition duration-300 ease-out hover:scale-110 hover:-translate-y-1 hover:shadow-2xl">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Login
                            </a>
                            {{-- BUTTON DAFTAR AKUN HERO --}}
                            <a href="{{ route('register') }}" class="bg-white text-teknik-orange-600 px-6 py-3 rounded-xl font-semibold text-center hover:bg-gray-100 transform transition duration-300 ease-out hover:scale-110 hover:-translate-y-1 hover:shadow-2xl flex items-center justify-center">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                Daftar Akun
                            </a>
                        @endauth
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 border border-white border-opacity-20 text-center btn-animated">
                        <div class="text-4xl font-bold text-teknik-orange-400 mb-2 animate-bounce">{{ $stats['total_jurusan'] }}</div>
                        <div class="text-sm text-gray-300">Program Studi</div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 border border-white border-opacity-20 text-center btn-animated">
                        <div class="text-4xl font-bold text-teknik-orange-400 mb-2 animate-bounce" style="animation-delay: 0.1s">{{ $stats['total_skpi_approved'] }}</div>
                        <div class="text-sm text-gray-300">SKPI Disetujui</div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 border border-white border-opacity-20 text-center btn-animated">
                        <div class="text-4xl font-bold text-teknik-orange-400 mb-2 animate-bounce" style="animation-delay: 0.2s">{{ $stats['total_mahasiswa'] }}</div>
                        <div class="text-sm text-gray-300">Mahasiswa Terdaftar</div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-6 border border-white border-opacity-20 text-center btn-animated">
                        <div class="text-4xl font-bold text-teknik-orange-400 mb-2 animate-bounce" style="animation-delay: 0.3s">{{ $stats['total_skpi'] }}</div>
                        <div class="text-sm text-gray-300">Total SKPI</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics & Charts Section -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Statistik SKPI Per Program Studi</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-6">
                    Visualisasi data SKPI terkini untuk memantau progress dan perkembangan di setiap program studi
                </p>

                {{-- PERIODE FILTER --}}
                <form method="GET" action="{{ route('home') }}" class="max-w-md mx-auto">
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Periode Wisuda</label>
                            <select name="periode_wisuda" class="input-field w-full" onchange="this.form.submit()">
                                <option value="">Semua Periode (Default)</option>
                                @foreach($availablePeriods as $period)
                                    <option value="{{ $period['number'] }}" {{ request('periode_wisuda') == $period['number'] ? 'selected' : '' }}>
                                        {{ $period['title'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Charts Container -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                <!-- Bar Chart - SKPI per Prodi -->
                <div class="lg:col-span-2">
                    <div class="card p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Status SKPI per Program Studi</h3>
                        <div class="relative h-80">
                            <canvas id="skpiChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Pie Chart - Distribusi Mahasiswa -->
                <div class="lg:col-span-1">
                    <div class="card p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Mahasiswa</h3>
                        <div class="relative h-80">
                            <canvas id="mahasiswaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trend Line Chart -->
            <div class="card p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tren SKPI 6 Bulan Terakhir</h3>
                <div class="relative h-64">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Program Studi Grid -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Program Studi Fakultas Teknik</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Fakultas Teknik Universitas Bengkulu memiliki 6 program studi yang berkualitas dan terakreditasi
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($jurusans as $jurusan)
                <div class="card p-6 btn-animated border-l-4 border-teknik-orange-500 transform hover:-translate-y-2 hover:shadow-2xl">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-teknik-orange-500 to-teknik-orange-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            {{ $jurusan->kode_jurusan }}
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $jurusan->nama_jurusan }}</h3>
                            <p class="text-sm text-gray-500">{{ $jurusan->kode_jurusan }}</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm mb-4 leading-relaxed">{{ $jurusan->deskripsi }}</p>
                    
                    <!-- Mini Statistics -->
                    <div class="bg-gradient-to-r from-blue-50 to-orange-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-lg font-bold text-unib-blue-600">
                                    {{ $jurusan->skpiData->where('status', 'approved')->sum('total') ?? 0 }}
                                </div>
                                <div class="text-xs text-gray-600">SKPI Approved</div>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-teknik-orange-600">
                                    {{ $jurusan->skpiData->whereIn('status', ['submitted'])->sum('total') ?? 0 }}
                                </div>
                                <div class="text-xs text-gray-600">Dalam Review</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($jurusan->kaprodi)
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 mb-1">Ketua Program Studi:</p>
                        <p class="text-sm font-medium text-gray-900">{{ $jurusan->kaprodi }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-16 bg-gradient-to-r from-gray-50 to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Fitur Unggulan Sistem SKPI</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Platform modern yang memudahkan pengelolaan dan penerbitan SKPI secara digital
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center btn-animated">
                    <div class="w-20 h-20 bg-gradient-to-br from-unib-blue-500 to-unib-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg hover:shadow-xl transition-shadow">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Input Data Mudah</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Form yang user-friendly dengan validasi otomatis dan upload dokumen pendukung</p>
                </div>
                
                <div class="text-center btn-animated">
                    <div class="w-20 h-20 bg-gradient-to-br from-teknik-orange-500 to-teknik-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg hover:shadow-xl transition-shadow">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Verifikasi Berlapis</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Sistem review multi-level dari admin jurusan untuk memastikan kualitas data</p>
                </div>
                
                <div class="text-center btn-animated">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg hover:shadow-xl transition-shadow">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Cetak Digital</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Output PDF profesional dengan format resmi dan logo universitas</p>
                </div>
                
                <div class="text-center btn-animated">
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg hover:shadow-xl transition-shadow">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Keamanan Terjamin</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Sistem keamanan berlapis dengan enkripsi data dan akses terkontrol</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-16 bg-gradient-to-r from-teknik-orange-600 to-teknik-orange-500 text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: linear-gradient(45deg, transparent 40%, white 50%, transparent 60%);"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
            <h2 class="text-4xl font-bold mb-4">Siap Mengajukan SKPI Anda?</h2>
            <p class="text-xl text-orange-100 mb-8 max-w-3xl mx-auto leading-relaxed">
                Bergabung dengan mahasiswa Fakultas Teknik lainnya dan dapatkan SKPI digital Anda hari ini juga!
            </p>
            @guest
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                {{-- BUTTON DAFTAR SEKARANG CTA --}}
                <a href="{{ route('register') }}" class="bg-white text-teknik-orange-600 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-100 transform transition duration-300 ease-out hover:scale-110 hover:-translate-y-1 hover:shadow-2xl">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Daftar Sekarang
                </a>
                {{-- BUTTON LOGIN CTA --}}
                <a href="{{ route('login') }}" class="btn-cta flex items-center justify-center transform transition duration-300 ease-out hover:scale-110 hover:-translate-y-1 hover:shadow-2xl">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Login
                </a>
            </div>
            @endguest
        </div>
    </div>

    <!-- JavaScript untuk Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart.js Configuration
        Chart.defaults.font.family = 'Inter, sans-serif';
        Chart.defaults.color = '#374151';

        // Bar Chart - SKPI per Prodi
        const ctxBar = document.getElementById('skpiChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {!! json_encode($chartData) !!},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, padding: 20 }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { weight: '500' } } },
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.1)' }, ticks: { stepSize: 1 } }
                },
                interaction: { intersect: false, mode: 'index' },
                animation: { duration: 2000, easing: 'easeInOutQuart' }
            }
        });

        // Pie Chart - Distribusi Mahasiswa
        const ctxPie = document.getElementById('mahasiswaChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {!! json_encode($pieData) !!},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        return {
                                            text: `${label}: ${value}`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            strokeStyle: data.datasets[0].backgroundColor[i],
                                            pointStyle: 'circle'
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} mahasiswa (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%',
                animation: { animateRotate: true, animateScale: true, duration: 2000, easing: 'easeInOutQuart' }
            }
        });

        // Line Chart - Trend
        const ctxLine = document.getElementById('trendChart').getContext('2d');
        const monthlyTrendData = {!! json_encode($monthlyTrend) !!};
        
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: monthlyTrendData.map(item => item.month),
                datasets: [
                    {
                        label: 'SKPI Disetujui',
                        data: monthlyTrendData.map(item => item.approved),
                        borderColor: 'rgba(25, 135, 84, 1)',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(25, 135, 84, 1)',
                        pointBorderColor: 'white',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    },
                    {
                        label: 'SKPI Disubmit',
                        data: monthlyTrendData.map(item => item.submitted),
                        borderColor: 'rgba(13, 110, 253, 1)',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                        pointBorderColor: 'white',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, padding: 20 } },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: { title: (items) => `Bulan: ${items[0].label}` }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { weight: '500' } } },
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.1)' }, ticks: { stepSize: 1 } }
                },
                interaction: { intersect: false, mode: 'index' },
                animation: { duration: 2000, easing: 'easeInOutQuart' }
            }
        });

        // Counter Animation for Statistics
        function animateCounters() {
            const counters = document.querySelectorAll('.text-4xl.font-bold.text-teknik-orange-400');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent);
                const increment = target / 50;
                let current = 0;
                const updateCounter = () => {
                    if (current < target) {
                        current += increment;
                        counter.textContent = Math.ceil(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };
                updateCounter();
            });
        }

        // Intersection Observer for animations
        const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('animate-fadeInUp'); });
        }, observerOptions);

        document.addEventListener('DOMContentLoaded', () => {
            const elementsToObserve = document.querySelectorAll('.card, .transform');
            elementsToObserve.forEach(el => observer.observe(el));
            setTimeout(animateCounters, 1000);
        });
    </script>

    <!-- Custom CSS for animations -->
    <style>
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px);} to { opacity: 1; transform: translateY(0);} }
        .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
        @keyframes float { 0%,100%{ transform: translateY(0);} 50%{ transform: translateY(-10px);} }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-gradient {
            background: linear-gradient(-45deg, #1e3a8a, #f97316, #1e40af, #ea580c);
            background-size: 400% 400%;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            animation: gradientShift 4s ease infinite;
        }
        @keyframes gradientShift { 0%{background-position:0% 50%;} 50%{background-position:100% 50%;} 100%{background-position:0% 50%;} }
        .card:hover { box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .chart-loading { display:flex; justify-content:center; align-items:center; height:320px; }
        .chart-loading::after {
            content:''; width:40px; height:40px; border:4px solid #f3f4f6; border-top:4px solid #3b82f6; border-radius:50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0%{ transform: rotate(0);} 100%{ transform: rotate(360deg);} }
    </style>
</x-app-layout>
