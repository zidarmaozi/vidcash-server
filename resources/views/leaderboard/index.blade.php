<x-app-layout>
    <main class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Tampilkan konten hanya jika event aktif --}}
            @if ($isEventEnabled)
                <!-- Header & Countdown Timer -->
                <div class="text-center mb-8 p-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors">
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        Event <span class="text-indigo-600 dark:text-indigo-400">{{ Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">Berlomba menjadi yang terbaik sebelum event berakhir.</p>
                    
                    <div id="countdown" class="grid grid-cols-4 gap-4 max-w-lg mx-auto mt-8">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 flex flex-col items-center border border-gray-100 dark:border-gray-600">
                            <span id="days" class="text-3xl font-black text-gray-900 dark:text-white">0</span>
                            <span class="text-xs uppercase tracking-wider font-bold text-gray-400 dark:text-gray-500">Hari</span>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 flex flex-col items-center border border-gray-100 dark:border-gray-600">
                            <span id="hours" class="text-3xl font-black text-gray-900 dark:text-white">0</span>
                            <span class="text-xs uppercase tracking-wider font-bold text-gray-400 dark:text-gray-500">Jam</span>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 flex flex-col items-center border border-gray-100 dark:border-gray-600">
                            <span id="minutes" class="text-3xl font-black text-gray-900 dark:text-white">0</span>
                            <span class="text-xs uppercase tracking-wider font-bold text-gray-400 dark:text-gray-500">Menit</span>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 flex flex-col items-center border border-gray-100 dark:border-gray-600">
                            <span id="seconds" class="text-3xl font-black text-indigo-600 dark:text-indigo-400">0</span>
                            <span class="text-xs uppercase tracking-wider font-bold text-gray-400 dark:text-gray-500">Detik</span>
                        </div>
                    </div>
                </div>
                
                <!-- Tabel Leaderboard -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center gap-3">
                        <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg text-yellow-600 dark:text-yellow-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top 10 Peringkat Bulan Ini</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Peringkat diperbarui secara otomatis.</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">Rank</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Username</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hadiah</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Views</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Earnings</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($topUsers as $rank => $user)
                                    @php $currentRank = $rank + 1; @endphp
                                    <tr class="transition-colors group hover:bg-gray-50 dark:hover:bg-gray-700/30 
                                        @if($currentRank <= 3) bg-gradient-to-r from-yellow-50/50 to-transparent dark:from-yellow-900/10 dark:to-transparent @endif">
                                        <td class="px-6 py-4 text-center">
                                            @if($currentRank == 1) 
                                                <div class="relative w-8 h-8 mx-auto flex items-center justify-center bg-yellow-400 text-white font-bold rounded-full ring-4 ring-yellow-100 dark:ring-yellow-900/30 shadow-sm">1
                                                    <svg class="absolute -top-3 text-yellow-500 w-5 h-5 dropped-shadow" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11c-1.1 0-2 .9-2 2v2c0 1.1.9 2 2 2h2c1.1 0 2-.9 2-2v-2c0-1.1-.9-2-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13c0-1.1.9-2 2-2h2c1.1 0 2 .9 2 2v2c0 1.1-.9 2-2 2h-2c-1.1 0-2-.9-2-2v-2z"></path></svg>
                                                </div>
                                            @elseif($currentRank == 2)
                                                <div class="w-8 h-8 mx-auto flex items-center justify-center bg-gray-300 text-gray-800 font-bold rounded-full ring-4 ring-gray-100 dark:ring-gray-700 shadow-sm">2</div>
                                            @elseif($currentRank == 3)
                                                <div class="w-8 h-8 mx-auto flex items-center justify-center bg-amber-600 text-white font-bold rounded-full ring-4 ring-amber-100 dark:ring-orange-900/30 shadow-sm">3</div>
                                            @else
                                                <span class="text-gray-500 dark:text-gray-400 font-bold font-mono">#{{ $currentRank }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900 dark:text-white">{{ Str::mask($user->name, '*', 2, -2) }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ Str::limit(md5($user->id), 6, '') }}...</div>
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-green-600 dark:text-green-400">
                                            @if (isset($prizes[$currentRank]) && $prizes[$currentRank] > 0)
                                                <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2.5 py-0.5 rounded text-sm font-bold border border-green-200 dark:border-green-800">
                                                    Rp{{ number_format($prizes[$currentRank], 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="text-gray-300 dark:text-gray-600">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-indigo-600 dark:text-indigo-400 font-mono">
                                            {{ number_format($user->total_views) }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">
                                            Rp{{ number_format($user->total_earnings, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-12">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                                <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada data untuk periode ini.</p>
                                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Jadilah yang pertama masuk ke leaderboard!</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @else
                {{-- Tampilkan ini jika event dinonaktifkan --}}
                <div class="flex flex-col items-center justify-center py-24 text-center">
                    <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-6 shadow-inner">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Event Telah Berakhir</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-md mx-auto">Untuk saat ini tidak ada kompetisi yang sedang berlangsung. Terus tingkatkan performa Anda dan nantikan event menarik selanjutnya!</p>
                    <a href="{{ route('dashboard') }}" class="mt-8 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition-colors">
                        Kembali ke Dashboard
                    </a>
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
                document.getElementById("countdown").innerHTML = "<div class='col-span-4 text-2xl font-bold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 py-4 rounded-xl'>EVENT BERAKHIR</div>";
            }
        }, 1000);
    </script>
</x-app-layout>