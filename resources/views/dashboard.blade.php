<x-app-layout>
    <!-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> -->

    <!-- <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div> -->

    <main class="py-10 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- === TAMBAHKAN BLOK INFO EVENT DI SINI === --}}
            @if($isEventActive)

                <!-- <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white p-4 rounded-lg shadow-lg mb-6 flex items-center justify-between">
                <div><img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEilPx9JPOAKwBFj0kWuvD6CQ4tnIhS1d2qawMEqOp0YK9UPOogYfTRSRJ_gTLCUp4JjR9dRKTF7CXNp_qKKL6jh5L4c99X7b8RX19ngzo6KiJ8__pQJORLMOKoJCD43LGYgfUThToS1Qcmauhn3cy9CBW-IKzhevoeVOItUMWLrIKohmqm_DSIc1IeOQl0/s1600/82947d6cd0b450cb4d0d6ba95e1b95c3-removebg-preview.png"/></div>    
                <div>
                        <p class="font-bold">Event Peringkat Bulanan Sedang Berlangsung!</p>
                        <p class="text-sm opacity-90">Jadilah yang teratas dan menangkan hadiah saldo tunai.</p>
                    </div>
                    <a href="{{ route('leaderboard.index') }}" class="px-4 py-2 bg-white text-yellow-600 font-semibold rounded-md text-sm hover:bg-gray-100">
                        Lihat Peringkat
                    </a>
                </div> -->
                <div id="event-banner"
                    class="relative bg-gray-900/80 backdrop-blur-md rounded-lg shadow-lg p-4 flex items-center gap-4 mb-6">
                    <!-- Tombol Close -->
                    <button onclick="document.getElementById('event-banner').remove()"
                        class="absolute top-2 right-2 text-gray-400 hover:text-white">
                        ✕
                    </button>

                    <!-- Gambar piala -->
                    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEilPx9JPOAKwBFj0kWuvD6CQ4tnIhS1d2qawMEqOp0YK9UPOogYfTRSRJ_gTLCUp4JjR9dRKTF7CXNp_qKKL6jh5L4c99X7b8RX19ngzo6KiJ8__pQJORLMOKoJCD43LGYgfUThToS1Qcmauhn3cy9CBW-IKzhevoeVOItUMWLrIKohmqm_DSIc1IeOQl0/s1600/82947d6cd0b450cb4d0d6ba95e1b95c3-removebg-preview.png"
                        alt="Trophy" class="w-20 h-auto drop-shadow-lg">

                    <!-- Teks -->
                    <div class="flex flex-col">
                        <!-- Baris logo sponsor -->
                        <div class="flex items-center gap-2">
                            <span class="bg-white text-black px-2 py-0.5 rounded-md text-sm font-bold">Vidcash</span>
                            <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                            <span class="text-blue-400 font-bold">Event Bulanan</span>
                        </div>
                        <!-- Deskripsi -->
                        <p class="text-sm text-gray-300">Event Peringkat Bulanan Sedang Berlangsung! Jadilah yang teratas
                            dan menangkan hadiah saldo tunai.<a href="{{ route('leaderboard.index') }}"
                                class=" text-yellow-600 font-semibold text-sm hover:bg-gray-100">Lihat Peringkat</a>
                        </p>
                    </div>
                </div>

            @endif
            {{-- ========================================== --}}

            {{-- Helper function untuk menampilkan perbandingan +/- --}}
            @php
                function formatComparison($value)
                {
                    $formatted = 'Rp' . number_format(abs($value), 0, ',', '.');
                    return ($value >= 0 ? '+' : '-') . $formatted;
                }
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-gradient-to-br from-indigo-500 to-blue-600 text-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-lg font-semibold mb-4">Estimasi penghasilan</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-8">
                            <div>
                                <p class="text-sm opacity-80">Hari ini hingga saat ini</p>
                                <p class="text-2xl font-semibold">Rp{{ number_format($earningsToday, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm opacity-80">Kemarin</p>
                                <p class="text-2xl font-semibold">Rp{{ number_format($earningsYesterday, 0, ',', '.') }}
                                </p>
                                <p class="text-xs opacity-80">{{ formatComparison($comparisonTodayVsLastWeek) }} vs hari
                                    yang sama pekan lalu</p>
                            </div>
                            <div>
                                <p class="text-sm opacity-80">7 hari terakhir</p>
                                <p class="text-2xl font-semibold">Rp{{ number_format($earningsLast7Days, 0, ',', '.') }}
                                </p>
                                <p class="text-xs opacity-80">{{ formatComparison($comparisonLast7Days) }} vs 7 hari
                                    sebelumnya</p>
                            </div>
                            <div>
                                <p class="text-sm opacity-80">Bulan ini</p>
                                <p class="text-2xl font-semibold">Rp{{ number_format($earningsThisMonth, 0, ',', '.') }}
                                </p>
                                <p class="text-xs opacity-80">{{ formatComparison($comparisonThisMonth) }} vs periode
                                    yang sama bulan lalu</p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="space-y-6">
                    <div class="bg-gradient-to-br from-indigo-500 to-blue-600 text-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-lg font-semibold">Saldo</h3>
                        <p class="mt-2 text-3xl font-semibold">Rp{{ number_format($balance, 0, ',', '.') }}</p>
                        @if($lastWithdrawal)
                            <p class="text-base opacity-80 mt-2">Pembayaran terakhir</p>
                            <p class="text-base font-semibold"> Rp{{ number_format($lastWithdrawal->amount) }}</p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 w-full lg:col-span-2">
                    <div class="bg-white p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Performa</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                            <div>
                                <p class="text-sm text-gray-500">Klik Hari Ini</p>
                                <p class="text-2xl font-medium text-gray-900">{{ number_format($clicksToday) }}</p>
                                <p class="text-xs text-gray-500">Total klik hari ini</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Klik Kemarin</p>
                                <p class="text-2xl font-medium text-gray-900">{{ number_format($viewsYesterday) }}</p>
                                @php
                                    $comparison = $comparisonYesterdayViews;
                                    $comparisonText = ($comparison >= 0 ? '+' : '') . number_format(abs($comparison));
                                    $colorClass = $comparison >= 0 ? 'text-green-600' : 'text-red-600';
                                @endphp
                                <p class="text-xs {{ $colorClass }} font-semibold">
                                    {{ $comparisonText }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Klik Bulan Ini</p>
                                <p class="text-2xl font-medium text-gray-900">{{ number_format($viewsThisMonth) }}</p>
                                <p class="text-xs text-gray-500">Total klik bulan ini</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Total Video</p>
                                <p class="text-2xl font-medium text-gray-900">
                                    {{ number_format(Auth::user()->videos()->count()) }}</p>
                                <p class="text-xs text-gray-500">Aktif</p>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Statistik -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div
                            class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
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
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden h-fit">
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
                            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition flex items-center gap-4">
                                <!-- Rank Icon/Number -->
                                <div class="flex-shrink-0 w-8 flex justify-center">
                                    @if ($loop->iteration == 1)
                                        <svg class="w-8 h-8 text-yellow-400 drop-shadow-sm" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @elseif ($loop->iteration == 2)
                                        <svg class="w-8 h-8 text-gray-400 drop-shadow-sm" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @elseif ($loop->iteration == 3)
                                        <svg class="w-8 h-8 text-amber-600 drop-shadow-sm" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @else
                                        <span class="text-lg font-bold text-gray-400 font-mono">#{{ $loop->iteration }}</span>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <a href="{{ $video->generated_link }}" target="_blank"
                                        class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline truncate block transition">
                                        {{ Str::limit($video->generated_link, 60) }}
                                    </a>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        ID: {{ $video->video_code }}
                                    </p>
                                </div>

                                <!-- Views -->
                                <div class="text-right">
                                    <span class="block text-sm font-bold text-gray-900 dark:text-white">
                                        {{ number_format($video->views_count) }}
                                    </span>
                                    <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Views</span>
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



    {{-- Memuat library ApexCharts.js dari CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartElement = document.getElementById('dashboardChart');

            if (chartElement) {
                // Opsi konfigurasi untuk ApexCharts
                const options = {
                    series: [{
                        name: 'Views',
                        data: @json($chartViewsData)
                    }, {
                        name: 'Pendapatan (Rp)',
                        data: @json($chartEarningsData)
                    }],
                    chart: {
                        type: 'area', // Ubah ke 'line' atau 'bar' jika diinginkan
                        height: 380,
                        fontFamily: 'Instrument Sans, sans-serif',
                        toolbar: {
                            show: false // Sembunyikan toolbar untuk tampilan lebih bersih
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    dataLabels: {
                        enabled: false // Matikan angka di setiap titik agar tidak penuh
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    xaxis: {
                        type: 'datetime',
                        tooltip: {
                            enabled: false
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: [
                        {
                            title: {
                                text: "Views",
                                style: { color: '#6366f1' }
                            },
                            labels: {
                                style: { colors: '#6366f1' }
                            }
                        },
                        {
                            opposite: true,
                            title: {
                                text: "Pendapatan",
                                style: { color: '#10b981' }
                            },
                            labels: {
                                style: { colors: '#10b981' },
                                formatter: (value) => { return 'Rp ' + new Intl.NumberFormat('id-ID').format(value) }
                            }
                        }
                    ],
                    theme: {
                        mode: 'light' // atau 'dark'
                    },
                    tooltip: {
                        theme: 'light',
                        x: {
                            format: 'dd MMMM yyyy'
                        },
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
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [50, 100, 100]
                        }
                    },
                    colors: ['#6366f1', '#10b981'], // Indigo-500 & Emerald-500
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4,
                        yaxis: {
                            lines: {
                                show: true
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right'
                    }
                };

                const chart = new ApexCharts(chartElement, options);
                chart.render();
            }
        });
    </script>

</x-app-layout>