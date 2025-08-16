<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Setting;
use App\Models\EventPayout;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AwardMonthlyPrizes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:award-monthly-prizes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

public function handle()
{
    // 1. Cek apakah event diaktifkan oleh admin
    $isEventEnabled = (bool) Setting::where('key', 'event_enabled')->first()?->value;
    if (!$isEventEnabled) {
        $this->info('Event bulanan tidak aktif.');
        return 0;
    }

    // 2. Tentukan periode (bulan lalu)
    $period = Carbon::now()->subMonth();
    $periodString = $period->format('Y-m');

    // 3. Cek apakah hadiah untuk periode ini sudah pernah diproses
    if (EventPayout::where('period', $periodString)->exists()) {
        $this->info("Hadiah untuk periode $periodString sudah pernah diproses.");
        return 0;
    }

    // 4. Ambil pengaturan hadiah
    $prizes = [
        1 => Setting::where('key', 'event_prize_1')->first()?->value ?? 75000,
        2 => Setting::where('key', 'event_prize_2')->first()?->value ?? 50000,
        3 => Setting::where('key', 'event_prize_3')->first()?->value ?? 25000,
    ];

    // 5. Query untuk mencari Top 3 Pemenang bulan lalu - Using STORED income amounts
    $winners = User::select('users.id as user_id', DB::raw('SUM(CASE WHEN views.income_generated = 1 THEN views.income_amount ELSE 0 END) as total_earnings'))
        ->join('videos', 'users.id', '=', 'videos.user_id')
        ->join('views', 'videos.id', '=', 'views.video_id')
        ->whereMonth('views.created_at', $period->month)
        ->whereYear('views.created_at', $period->year)
        ->groupBy('users.id')
        ->orderBy('total_earnings', 'desc')
        ->limit(3)
        ->get();

    // 6. Simpan calon pemenang ke database dengan status "pending"
    if ($winners->isEmpty()) {
        $this->info('Tidak ditemukan pemenang untuk periode ' . $periodString);
        return 0;
    }

    foreach ($winners as $rank => $winner) {
        $currentRank = $rank + 1;
        EventPayout::create([
            'user_id' => $winner->user_id,
            'period' => $periodString,
            'rank' => $currentRank,
            'total_views' => $winner->total_earnings, // Use total_earnings from the query
            'prize_amount' => $prizes[$currentRank],
            'status' => 'pending', // Statusnya pending
        ]);

        // Di dalam `handle()` setelah EventPayout::create([...])
$winnerUser = User::find($winner->user_id);
$winnerUser->notifications()->create([
    'type' => 'event',
    'message' => 'Selamat! Anda memenangkan hadiah event peringkat ke-' . $currentRank . ' sebesar Rp' . number_format($prizes[$currentRank]) . '. Saldo telah ditambahkan.'
]);
    }

    
    $this->info("Berhasil menemukan {$winners->count()} calon pemenang untuk periode {$periodString}. Silakan konfirmasi di panel admin.");
    return 0;
}
}

