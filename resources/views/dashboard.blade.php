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
            <div id="event-banner" class="relative bg-gray-900/80 backdrop-blur-md rounded-lg shadow-lg p-4 flex items-center gap-4 mb-6">
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
        <p class="text-sm text-gray-300">Event Peringkat Bulanan Sedang Berlangsung! Jadilah yang teratas dan menangkan hadiah saldo tunai.<a href="{{ route('leaderboard.index') }}" class=" text-yellow-600 font-semibold text-sm hover:bg-gray-100">Lihat Peringkat</a>
        </p>
        </div>
</div>

        @endif
        {{-- ========================================== --}}

        {{-- Helper function untuk menampilkan perbandingan +/- --}}
        @php
            function formatComparison($value) {
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
                            <p class="text-2xl font-semibold">Rp{{ number_format($earningsYesterday, 0, ',', '.') }}</p>
                            <p class="text-xs opacity-80">{{ formatComparison($comparisonTodayVsLastWeek) }} vs hari yang sama pekan lalu</p>
                        </div>
                        <div>
                            <p class="text-sm opacity-80">7 hari terakhir</p>
                            <p class="text-2xl font-semibold">Rp{{ number_format($earningsLast7Days, 0, ',', '.') }}</p>
                            <p class="text-xs opacity-80">{{ formatComparison($comparisonLast7Days) }} vs 7 hari sebelumnya</p>
                        </div>
                        <div>
                            <p class="text-sm opacity-80">Bulan ini</p>
                            <p class="text-2xl font-semibold">Rp{{ number_format($earningsThisMonth, 0, ',', '.') }}</p>
                            <p class="text-xs opacity-80">{{ formatComparison($comparisonThisMonth) }} vs periode yang sama bulan lalu</p>
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
            <p class="text-2xl font-medium text-gray-900">{{ number_format(Auth::user()->videos()->count()) }}</p>
            <p class="text-xs text-gray-500">Aktif</p>
        </div>
     </div>
</div>
                
   <!-- Kartu Statistik -->
<div class="bg-white p-6 rounded-lg">
    <h3 class="text-lg font-semibold mb-4">Statistik Views & Pendapatan 30 Hari Terakhir</h3>
    <div id="dashboardChart"></div>
</div>
</div>

<!-- Tabel Video Terpopuler -->
<div class="bg-white p-6 rounded-lg">
    <h3 class="text-lg font-semibold mb-4">Top Videos - Views (Last 7 Days)</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="border-b">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Link</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Views</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($topVideos as $video)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <a href="{{ $video->generated_link }}" target="_blank" class="text-indigo-600 hover:underline">
                                {{ Str::limit($video->generated_link, 50) }}
                            </a>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">{{ number_format($video->views_count) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500">Belum ada data view dalam 7 hari terakhir.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
                type: 'area',
                height: 350,
                zoom: {
                    enabled: true
                },
                toolbar: {
                    show: true
                }
            },
            dataLabels: {
                enabled: true
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                type: 'datetime',
                labels: {
                    datetimeUTC: true, // Tampilkan dalam zona waktu lokal
                }
            },
            // yaxis: [
            //     {
            //         title: {
            //             text: "Jumlah Views",
            //         },
            //     },
            //     {
            //         opposite: true,
            //         title: {
            //             text: "Pendapatan (Rp)"
            //         },
            //         labels: {
            //             formatter: function (value) {
            //                 return 'Rp' + value.toLocaleString('id-ID');
            //             }
            //         }
            //     }
            // ],
            tooltip: {
                x: {
                    format: 'dd MMMM yyyy'
                },
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            colors: ['#4F46E5', '#16A34A'], // Warna Indigo dan Hijau
        };

        const chart = new ApexCharts(chartElement, options);
        chart.render();
    }
});
</script>

</x-app-layout>
