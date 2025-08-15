<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Video;
use App\Models\Withdrawal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalWithdrawals = Withdrawal::where('status', 'confirmed')->sum('amount');

        return [
            Stat::make('Total Pengguna', User::count())
                ->icon('heroicon-o-users'),
            Stat::make('Total Video', Video::count())
                ->icon('heroicon-o-video-camera'),
            Stat::make('Total Penarikan (Rp)', 'Rp' . number_format($totalWithdrawals, 0, ',', '.'))
                ->description('Jumlah semua penarikan yang berhasil')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            Stat::make('Penarikan Berhasil', Withdrawal::where('status', 'confirmed')->count()),
            Stat::make('Penarikan Pending', Withdrawal::where('status', 'pending')->count()),
            Stat::make('Penarikan Ditolak', Withdrawal::where('status', 'rejected')->count()),
        ];
    }
}