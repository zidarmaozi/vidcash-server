<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index()
    {
        // 1. Cek apakah event diaktifkan oleh admin
        $isEventEnabled = (bool) Setting::where('key', 'event_enabled')->first()?->value;

        // Jika event tidak aktif, langsung tampilkan view tanpa data
        if (!$isEventEnabled) {
            return view('leaderboard.index', ['isEventEnabled' => false]);
        }

        // 2. Ambil data hadiah dari pengaturan
        $prizes = [
            1 => Setting::where('key', 'event_prize_1')->first()?->value ?? 0,
            2 => Setting::where('key', 'event_prize_2')->first()?->value ?? 0,
            3 => Setting::where('key', 'event_prize_3')->first()?->value ?? 0,
        ];

        // 3. Query untuk mendapatkan Top 10 User bulan ini - Using STORED income amounts
        $topUsers = User::select(
            'users.name', 
            DB::raw('COUNT(views.id) as total_views'),
            DB::raw('SUM(CASE WHEN views.income_generated = 1 THEN views.income_amount ELSE 0 END) as total_earnings')
        )
            ->join('videos', 'users.id', '=', 'videos.user_id')
            ->join('views', 'videos.id', '=', 'views.video_id')
            ->whereMonth('views.created_at', now()->month)
            ->whereYear('views.created_at', now()->year)
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_earnings', 'desc')
            ->limit(10)
            ->get();

        // 4. Kirim semua data ke view
        return view('leaderboard.index', [
            'isEventEnabled' => true,
            'topUsers' => $topUsers,
            'prizes' => $prizes,
        ]);
    }
}