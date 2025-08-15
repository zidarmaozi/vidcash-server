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
    $cpm = Setting::where('key', 'cpm')->first()->value ?? 10;

    // --- Data Hari Ini ---
    $viewsToday = View::whereIn('video_id', $videoIds)->whereDate('created_at', today())->count();
    $earningsToday = $viewsToday * $cpm;

    // --- Data Kemarin---
    $viewsYesterday = View::whereIn('video_id', $videoIds)->whereDate('created_at', today()->subDay())->count();
    $earningsYesterday = $viewsYesterday * $cpm;
    $viewsDayBeforeYesterday = View::whereIn('video_id', $videoIds)->whereDate('created_at', today()->subDays(2))->count();


    // --- Data 7 Hari Terakhir ---
    $viewsLast7Days = View::whereIn('video_id', $videoIds)->where('created_at', '>=', now()->subDays(6))->count();
    $earningsLast7Days = $viewsLast7Days * $cpm;
    
    // --- Data Bulan Ini ---
    $viewsThisMonth = View::whereIn('video_id', $videoIds)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
    $earningsThisMonth = $viewsThisMonth * $cpm;

    // --- DATA UNTUK PERBANDINGAN ---
    $earningsSameDayLastWeek = (View::whereIn('video_id', $videoIds)->whereDate('created_at', today()->subWeek())->count()) * $cpm;
    $earningsPrevious7Days = (View::whereIn('video_id', $videoIds)->whereBetween('created_at', [today()->subDays(13), today()->subDays(7)])->count()) * $cpm;
    $earningsLastMonth = (View::whereIn('video_id', $videoIds)->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year)->count()) * $cpm;

    // --- LOGIKA BARU UNTUK APEXCHARTS ---
        $viewsData = View::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as views')
            )
            ->whereIn('video_id', $videoIds)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Ubah data menjadi format yang dimengerti ApexCharts (logika chart)
        $chartViewsData = $viewsData->map(function ($item) {
            return [Carbon::parse($item->date)->timestamp * 1000, $item->views];
        });

        $chartEarningsData = $viewsData->map(function ($item) use ($cpm) {
            return [Carbon::parse($item->date)->timestamp * 1000, $item->views * $cpm];
        });
        // --- AKHIR LOGIKA BARU ---

        // --- LOGIKA BARU UNTUK VIDEO TERPOPULER ---
    $topVideos = $user->videos()
        ->withCount(['views' => function ($query) {
            // Hanya hitung view dalam 7 hari terakhir
            $query->where('created_at', '>=', now()->subDays(7));
        }])
        ->orderBy('views_count', 'desc') // Urutkan berdasarkan jumlah view
        ->limit(7) // Ambil 7 teratas
        ->get();
    // --- AKHIR LOGIKA BARU ---

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
    // Variabel baru untuk ApexCharts
    'chartViewsData' => $chartViewsData,
    'chartEarningsData' => $chartEarningsData,

    // untuk video terpopuler
    'topVideos' => $topVideos,

    'isEventActive' => $isEventActive, // Kirim status event ke view
]);
}
}