<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
   public function index()
{
    $user = Auth::user();
    $videoIds = $user->videos()->pluck('id');
    
    // Get current CPM for comparison (but use stored amounts for actual earnings)
    $currentCpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);

    // --- Data Hari Ini - Using STORED income amounts ---
    $viewsToday = View::whereIn('video_id', $videoIds)->whereDate('created_at', today())->count();
    $earningsToday = View::whereIn('video_id', $videoIds)
        ->whereDate('created_at', today())
        ->where('income_generated', true)
        ->sum('income_amount');

    // --- Data Kemarin - Using STORED income amounts ---
    $viewsYesterday = View::whereIn('video_id', $videoIds)->whereDate('created_at', today()->subDay())->count();
    $earningsYesterday = View::whereIn('video_id', $videoIds)
        ->whereDate('created_at', today()->subDay())
        ->where('income_generated', true)
        ->sum('income_amount');
    
    $viewsDayBeforeYesterday = View::whereIn('video_id', $videoIds)->whereDate('created_at', today()->subDays(2))->count();

    // --- Data 7 Hari Terakhir - Using STORED income amounts ---
    $viewsLast7Days = View::whereIn('video_id', $videoIds)->where('created_at', '>=', now()->subDays(6))->count();
    $earningsLast7Days = View::whereIn('video_id', $videoIds)
        ->where('created_at', '>=', now()->subDays(6))
        ->where('income_generated', true)
        ->sum('income_amount');
    
    // --- Data Bulan Ini - Using STORED income amounts ---
    $viewsThisMonth = View::whereIn('video_id', $videoIds)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
    $earningsThisMonth = View::whereIn('video_id', $videoIds)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('income_generated', true)
        ->sum('income_amount');

    // --- DATA UNTUK PERBANDINGAN - Using STORED income amounts ---
    $earningsSameDayLastWeek = View::whereIn('video_id', $videoIds)
        ->whereDate('created_at', today()->subWeek())
        ->where('income_generated', true)
        ->sum('income_amount');
    
    $earningsPrevious7Days = View::whereIn('video_id', $videoIds)
        ->whereBetween('created_at', [today()->subDays(13), today()->subDays(7)])
        ->where('income_generated', true)
        ->sum('income_amount');
    
    $earningsLastMonth = View::whereIn('video_id', $videoIds)
        ->whereMonth('created_at', now()->subMonth()->month)
        ->whereYear('created_at', now()->subMonth()->year)
        ->where('income_generated', true)
        ->sum('income_amount');

    // --- LOGIKA BARU UNTUK APEXCHARTS - Using STORED income amounts ---
    $viewsData = View::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as views'),
            DB::raw('SUM(CASE WHEN income_generated = 1 THEN income_amount ELSE 0 END) as total_income')
        )
        ->whereIn('video_id', $videoIds)
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

    // Ubah data menjadi format yang dimengerti ApexCharts
    $chartViewsData = $viewsData->map(function ($item) {
        return [Carbon::parse($item->date)->timestamp * 1000, $item->views];
    });

    $chartEarningsData = $viewsData->map(function ($item) {
        return [Carbon::parse($item->date)->timestamp * 1000, $item->total_income];
    });

    // --- LOGIKA BARU UNTUK VIDEO TERPOPULER ---
    $topVideos = $user->videos()
        ->withCount(['views' => function ($query) {
            // Hanya hitung view dalam 7 hari terakhir
            $query->where('created_at', '>=', now()->subDays(7));
        }])
        ->orderBy('views_count', 'desc')
        ->limit(7)
        ->get();

    // Cek status event dari database
    $isEventActive = (bool) Setting::where('key', 'event_enabled')->first()?->value;

    return view('dashboard', [
        'balance' => $user->balance,
        'lastWithdrawal' => $user->withdrawals()->latest()->first(),

        'earningsToday' => $earningsToday,
        'earningsYesterday' => $earningsYesterday,
        'earningsLast7Days' => $earningsLast7Days,
        'earningsThisMonth' => $earningsThisMonth,

        'comparisonTodayVsLastWeek' => $earningsToday - $earningsSameDayLastWeek,
        'comparisonLast7Days' => $earningsLast7Days - $earningsPrevious7Days,
        'comparisonThisMonth' => $earningsThisMonth - $earningsLastMonth,

        'viewsYesterday' => $viewsYesterday,
        'comparisonYesterdayViews' => $viewsYesterday - $viewsDayBeforeYesterday,

        'clicksToday' => $viewsToday,
        'viewsThisMonth' => $viewsThisMonth,

        // untuk chart
        'chartViewsData' => $chartViewsData,
        'chartEarningsData' => $chartEarningsData,

        // untuk video terpopuler
        'topVideos' => $topVideos,

        'isEventActive' => $isEventActive,
    ]);
}
}