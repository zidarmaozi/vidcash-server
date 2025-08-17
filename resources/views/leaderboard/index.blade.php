<x-app-layout>
    <main class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Tampilkan konten hanya jika event aktif --}}
            @if ($isEventEnabled)
                <!-- Header & Countdown Timer -->
                <div class="text-center mb-8 p-6 bg-white rounded-lg">
                    <h1 class="text-3xl font-bold text-gray-800">Event {{ Carbon\Carbon::now()->translatedFormat('F Y') }}</h1>
                    <p class="text-gray-600 mt-2">Event akan berakhir pada:</p>
                    <div id="countdown" class="flex justify-center items-center space-x-4 mt-4 text-gray-700">
                        <div><span id="days" class="text-3xl font-bold">0</span><span class="text-xs block">Hari</span></div>
                        <div><span id="hours" class="text-3xl font-bold">0</span><span class="text-xs block">Jam</span></div>
                        <div><span id="minutes" class="text-3xl font-bold">0</span><span class="text-xs block">Menit</span></div>
                        <div><span id="seconds" class="text-3xl font-bold">0</span><span class="text-xs block">Detik</span></div>
                    </div>
                </div>
                
                <!-- Tabel Leaderboard -->
                <div class="bg-white rounded-lg overflow-hidden">
                    <div class="p-4 border-b">
                        <h3 class="text-lg font-semibold">🏆 Top 10 Peringkat Bulan Ini</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">Rank</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hadiah</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Views</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Earnings</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($topUsers as $rank => $user)
                                    @php $currentRank = $rank + 1; @endphp
                                    <tr class="@if($currentRank <= 3) bg-gradient-to-r from-yellow-50 to-white @endif">
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full font-bold 
                                                @if($currentRank == 1) bg-yellow-400 text-white @elseif($currentRank == 2) bg-gray-400 text-white @elseif($currentRank == 3) bg-yellow-600 text-white @else bg-gray-200 text-gray-700 @endif">
                                                {{ $currentRank }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-800">{{ Str::mask($user->name, '*', 2, -2) }}</td>
                                        <td class="px-6 py-4 font-semibold text-green-600">
                                            @if (isset($prizes[$currentRank]) && $prizes[$currentRank] > 0)
                                                Rp{{ number_format($prizes[$currentRank], 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-indigo-600">{{ number_format($user->total_views) }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-green-600">Rp{{ number_format($user->total_earnings, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-10 text-gray-500">Belum ada data untuk bulan ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @else
                {{-- Tampilkan ini jika event dinonaktifkan --}}
                <div class="text-center py-20">
                    <h1 class="text-2xl font-bold text-gray-700">Event Telah Berakhir</h1>
                    <p class="text-gray-500 mt-2">Saat ini tidak ada event yang sedang berlangsung. Nantikan event selanjutnya!</p>
                </div>
            @endif
        </div>
    </main>

    <script>
        // Countdown Logic
        const countDownDate = new Date("{{ Carbon\Carbon::now()->endOfMonth()->toIso8601String() }}").getTime();
        
        const x = setInterval(function() {
            const now = new Date().getTime();
            const distance = countDownDate - now;
            
            document.getElementById("days").innerText = Math.floor(distance / (1000 * 60 * 60 * 24));
            document.getElementById("hours").innerText = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            document.getElementById("minutes").innerText = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            document.getElementById("seconds").innerText = Math.floor((distance % (1000 * 60)) / 1000);
            
            if (distance < 0) {
                clearInterval(x);
                document.getElementById("countdown").innerHTML = "<div class='text-2xl font-bold text-red-600'>EVENT BERAKHIR</div>";
            }
        }, 1000);
    </script>
</x-app-layout>