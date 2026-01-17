<x-app-layout>
    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- === TAMBAHKAN BLOK INFO EVENT DI SINI === --}}
            @if($isEventActive)
                <div id="event-banner"
                    class="relative bg-gray-900/90 dark:bg-gray-800/90 backdrop-blur-md rounded-2xl shadow-lg p-6 flex items-center gap-6 mb-8 border border-white/10 overflow-hidden group">
                    <!-- Background Glow -->
                    <div
                        class="absolute -top-20 -right-20 w-64 h-64 bg-orange-500/30 rounded-full blur-3xl group-hover:bg-orange-500/40 transition-all duration-700">
                    </div>
                    <div
                        class="absolute -bottom-20 -left-20 w-64 h-64 bg-blue-600/30 rounded-full blur-3xl group-hover:bg-blue-600/40 transition-all duration-700">
                    </div>

                    <!-- Tombol Close -->
                    <button onclick="document.getElementById('event-banner').remove()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-white transition-colors z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>

                    <!-- Gambar piala -->
                    <div class="relative z-10 shrink-0">
                        <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEilPx9JPOAKwBFj0kWuvD6CQ4tnIhS1d2qawMEqOp0YK9UPOogYfTRSRJ_gTLCUp4JjR9dRKTF7CXNp_qKKL6jh5L4c99X7b8RX19ngzo6KiJ8__pQJORLMOKoJCD43LGYgfUThToS1Qcmauhn3cy9CBW-IKzhevoeVOItUMWLrIKohmqm_DSIc1IeOQl0/s1600/82947d6cd0b450cb4d0d6ba95e1b95c3-removebg-preview.png"
                            alt="Trophy" class="w-24 h-auto drop-shadow-2xl floating-animation">
                    </div>

                    <!-- Teks -->
                    <div class="flex flex-col relative z-10">
                        <!-- Baris logo sponsor -->
                        <div class="flex items-center gap-3 mb-2">
                            <span
                                class="bg-white/10 text-white px-2 py-0.5 rounded text-xs font-bold border border-white/20 backdrop-blur-sm">Vidcash</span>
                            <span class="w-1.5 h-1.5 bg-orange-500 rounded-full animate-pulse"></span>
                            <span class="text-blue-400 font-bold tracking-wide uppercase text-xs">Event Bulanan</span>
                        </div>
                        <!-- Judul & Deskripsi -->
                        <h3 class="text-xl font-bold text-white mb-1">Event Peringkat Bulanan</h3>
                        <p class="text-sm text-gray-300 mb-3 max-w-xl">Jadilah konten kreator teratas bulan ini dan
                            menangkan total hadiah saldo tunai jutaan rupiah!</p>

                        <a href="{{ route('leaderboard.index') }}"
                            class="inline-flex items-center gap-2 w-fit px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-400 hover:to-orange-500 text-white font-bold text-sm rounded-lg shadow-lg hover:shadow-orange-500/20 transition-all transform hover:-translate-y-0.5">
                            Lihat Peringkat
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Helper function untuk menampilkan perbandingan +/- --}}
            @php
                function formatComparison($value)
                {
                    $formatted = 'Rp' . number_format(abs($value), 0, ',', '.');
                    return ($value >= 0 ? '+' : '-') . $formatted;
                }
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Highlight Cards (Pendapatan) -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="relative overflow-hidden bg-gradient-to-br from-indigo-600 to-blue-700 dark:from-indigo-900 dark:to-blue-900 text-white p-6 rounded-2xl shadow-xl">
                        <!-- Background Pattern -->
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-2xl">
                        </div>
                        <div
                            class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-indigo-500/30 rounded-full blur-2xl">
                        </div>

                        <h3 class="relative text-lg font-bold mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            Estimasi Penghasilan
                        </h3>

                        <div class="relative grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-8">
                            <div class="group">
                                <p class="text-xs font-medium text-indigo-200 mb-1 uppercase tracking-wider">Hari Ini
                                </p>
                                <p
                                    class="text-2xl font-extrabold tracking-tight group-hover:scale-105 transition-transform origin-left">
                                    Rp{{ number_format($earningsToday, 0, ',', '.') }}</p>
                            </div>
                            <div class="group">
                                <p class="text-xs font-medium text-indigo-200 mb-1 uppercase tracking-wider">Kemarin</p>
                                <p
                                    class="text-2xl font-extrabold tracking-tight group-hover:scale-105 transition-transform origin-left">
                                    Rp{{ number_format($earningsYesterday, 0, ',', '.') }}</p>
                                <div class="mt-1 flex items-center gap-1 text-xs text-indigo-100/80">
                                    <span
                                        class="{{ $comparisonTodayVsLastWeek >= 0 ? 'text-green-300' : 'text-red-300' }} font-bold bg-white/10 px-1.5 py-0.5 rounded">
                                        {{ $comparisonTodayVsLastWeek >= 0 ? '↑' : '↓' }}
                                        {{ formatComparison($comparisonTodayVsLastWeek) }}
                                    </span>
                                </div>
                            </div>
                            <div class="group">
                                <p class="text-xs font-medium text-indigo-200 mb-1 uppercase tracking-wider">7 Hari
                                    Terakhir</p>
                                <p
                                    class="text-2xl font-extrabold tracking-tight group-hover:scale-105 transition-transform origin-left">
                                    Rp{{ number_format($earningsLast7Days, 0, ',', '.') }}</p>
                                <div class="mt-1 flex items-center gap-1 text-xs text-indigo-100/80">
                                    <span
                                        class="{{ $comparisonLast7Days >= 0 ? 'text-green-300' : 'text-red-300' }} font-bold bg-white/10 px-1.5 py-0.5 rounded">
                                        {{ $comparisonLast7Days >= 0 ? '↑' : '↓' }}
                                        {{ formatComparison($comparisonLast7Days) }}
                                    </span>
                                </div>
                            </div>
                            <div class="group">
                                <p class="text-xs font-medium text-indigo-200 mb-1 uppercase tracking-wider">Bulan Ini
                                </p>
                                <p
                                    class="text-2xl font-extrabold tracking-tight group-hover:scale-105 transition-transform origin-left">
                                    Rp{{ number_format($earningsThisMonth, 0, ',', '.') }}</p>
                                <div class="mt-1 flex items-center gap-1 text-xs text-indigo-100/80">
                                    <span
                                        class="{{ $comparisonThisMonth >= 0 ? 'text-green-300' : 'text-red-300' }} font-bold bg-white/10 px-1.5 py-0.5 rounded">
                                        {{ $comparisonThisMonth >= 0 ? '↑' : '↓' }}
                                        {{ formatComparison($comparisonThisMonth) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Balance Card -->
                <div class="space-y-6">
                    <div
                        class="relative overflow-hidden bg-gradient-to-br from-violet-600 to-purple-700 dark:from-violet-900 dark:to-purple-900 text-white p-6 rounded-2xl shadow-xl flex flex-col justify-between h-full min-h-[220px]">
                        <!-- Deco -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -mr-10 -mt-10">
                        </div>

                        <div>
                            <h3 class="text-lg font-bold flex items-center gap-2 mb-6">
                                <svg class="w-5 h-5 text-violet-200" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>
                                Saldo Dompet
                            </h3>
                            <p class="text-4xl font-black tracking-tight mb-2">
                                Rp{{ number_format($balance, 0, ',', '.') }}</p>
                            @if($lastWithdrawal)
                                <div
                                    class="inline-flex items-center gap-2 bg-black/20 px-3 py-1.5 rounded-lg border border-white/10">
                                    <span class="text-xs text-violet-200">Terakhir cair:</span>
                                    <span
                                        class="text-sm font-bold text-white">Rp{{ number_format($lastWithdrawal->amount) }}</span>
                                </div>
                            @else
                                <span class="text-xs text-violet-200 bg-black/20 px-3 py-1.5 rounded-lg">Belum ada
                                    penarikan</span>
                            @endif
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('withdrawals.index') }}"
                                class="block w-full py-2.5 bg-white text-violet-700 hover:bg-violet-50 font-bold text-center rounded-xl shadow-lg transition-colors">
                                Tarik Saldo
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Section: Performa & Charts -->
                <div class="grid grid-cols-1 gap-6 w-full lg:col-span-2">
                    <!-- Cards Grid for Clicks/Video -->
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            Ringkasan Performa
                        </h3>
                        <div
                            class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-x divide-gray-100 dark:divide-gray-700">
                            <div class="px-2">
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold mb-1">
                                    Klik Hari Ini</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($clicksToday) }}</p>
                            </div>

                            <div class="px-2">
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold mb-1">
                                    Klik Kemarin</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($viewsYesterday) }}</p>
                                @php
                                    $comparison = $comparisonYesterdayViews;
                                    $comparisonText = ($comparison >= 0 ? '+' : '') . number_format(abs($comparison));
                                    $colorClass = $comparison >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
                                @endphp
                                <p
                                    class="text-xs {{ $colorClass }} font-bold bg-gray-50 dark:bg-gray-700/50 inline-block px-1.5 py-0.5 rounded mt-1">
                                    {{ $comparisonText }}
                                </p>
                            </div>

                            <div class="px-2">
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold mb-1">
                                    Klik Bulan Ini</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($viewsThisMonth) }}</p>
                            </div>

                            <div class="px-2 border-r-0">
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold mb-1">
                                    Total Link</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format(Auth::user()->videos()->count()) }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Aktif</p>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Card -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors">
                        <div
                            class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z">
                                    </path>
                                </svg>
                                Statistik 30 Hari Terakhir
                            </h3>
                        </div>
                        <div class="p-6">
                            <div id="dashboardChart"></div>
                        </div>
                    </div>
                </div>

                <!-- Video Terpopuler Cards -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden h-fit transition-colors">
                    <div
                        class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                </path>
                            </svg>
                            Top Video (7 Hari)
                        </h3>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($topVideos as $video)
                            <div
                                class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition flex items-center gap-4 group">
                                <!-- Rank Icon/Number -->
                                <div class="flex-shrink-0 w-8 flex justify-center">
                                    @if ($loop->iteration == 1)
                                        <svg class="w-8 h-8 text-yellow-400 drop-shadow-sm transform group-hover:scale-110 transition-transform"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @elseif ($loop->iteration == 2)
                                        <svg class="w-8 h-8 text-gray-400 drop-shadow-sm transform group-hover:scale-110 transition-transform"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @elseif ($loop->iteration == 3)
                                        <svg class="w-8 h-8 text-amber-600 drop-shadow-sm transform group-hover:scale-110 transition-transform"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @else
                                        <span
                                            class="text-lg font-bold text-gray-400 dark:text-gray-500 font-mono">#{{ $loop->iteration }}</span>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <a href="{{ $video->generated_link }}" target="_blank"
                                        class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline truncate block transition">
                                        {{ Str::limit($video->title ?: $video->generated_link, 50) }}
                                    </a>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-mono">
                                        ID: {{ $video->video_code }}
                                    </p>
                                </div>

                                <!-- Views -->
                                <div class="text-right">
                                    <span class="block text-sm font-bold text-gray-900 dark:text-white">
                                        {{ number_format($video->views_count) }}
                                    </span>
                                    <span
                                        class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Views</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Belum ada data view dalam 7 hari terakhir.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartElement = document.getElementById('dashboardChart');

            // Check for dark mode preference
            const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (chartElement) {
                const options = {
                    series: [{
                        name: 'Views',
                        data: @json($chartViewsData)
                    }, {
                        name: 'Pendapatan (Rp)',
                        data: @json($chartEarningsData)
                    }],
                    chart: {
                        type: 'area', // Mixed chart
                        height: 380,
                        fontFamily: 'Instrument Sans, sans-serif',
                        background: 'transparent',
                        toolbar: { show: false },
                        zoom: { enabled: false }
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            style: {
                                colors: isDarkMode ? '#9ca3af' : '#64748b'
                            }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        tooltip: { enabled: false }
                    },
                    yaxis: [
                        {
                            title: {
                                text: "Views",
                                style: { color: '#6366f1' }
                            },
                            labels: {
                                style: { colors: isDarkMode ? '#9ca3af' : '#64748b' }
                            }
                        },
                        {
                            opposite: true,
                            title: {
                                text: "Pendapatan",
                                style: { color: '#10b981' }
                            },
                            labels: {
                                style: { colors: isDarkMode ? '#9ca3af' : '#64748b' },
                                formatter: (value) => { return 'Rp ' + new Intl.NumberFormat('id-ID').format(value) }
                            }
                        }
                    ],
                    theme: {
                        mode: isDarkMode ? 'dark' : 'light'
                    },
                    tooltip: {
                        theme: isDarkMode ? 'dark' : 'light',
                        x: { format: 'dd MMMM yyyy' },
                        y: {
                            formatter: function (val, { seriesIndex }) {
                                if (seriesIndex === 1) return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                                return val;
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.6,
                            opacityTo: 0.1,
                            stops: [0, 90, 100]
                        }
                    },
                    colors: ['#6366f1', '#10b981'],
                    grid: {
                        borderColor: isDarkMode ? '#374151' : '#f1f5f9',
                        strokeDashArray: 4
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        labels: {
                            colors: isDarkMode ? '#e5e7eb' : '#374151'
                        }
                    }
                };

                const chart = new ApexCharts(chartElement, options);
                chart.render();

                // Listen for dark mode toggle (if any)
                // This assumes standard CSS media query listener for simplicity
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
                    const newMode = event.matches ? 'dark' : 'light';
                    chart.updateOptions({
                        theme: { mode: newMode },
                        xaxis: { labels: { style: { colors: event.matches ? '#9ca3af' : '#64748b' } } },
                        yaxis: [
                            { labels: { style: { colors: event.matches ? '#9ca3af' : '#64748b' } } },
                            { labels: { style: { colors: event.matches ? '#9ca3af' : '#64748b' } } }
                        ],
                        grid: { borderColor: event.matches ? '#374151' : '#f1f5f9' },
                        tooltip: { theme: newMode },
                        legend: { labels: { colors: event.matches ? '#e5e7eb' : '#374151' } }
                    });
                });
            }
        });
    </script>
</x-app-layout>