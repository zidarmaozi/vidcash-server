<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Video;
use App\Models\Withdrawal;
use App\Models\View;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FilteredDashboardStats extends BaseWidget
{
    public ?array $dateRange = null;

    public function mount(?array $dateRange = null): void
    {
        // Initialize with parameter from parent or default values
        $this->dateRange = $dateRange ?? ['start' => null, 'end' => null];
    }

    public function updatedDateRange(): void
    {
        // This will be called when dateRange is updated from parent
    }

    protected function getStats(): array
    {
        $query = $this->buildDateQuery();
        $cacheKey = 'dashboard_stats_' . md5(serialize($query));
        
        return cache()->remember($cacheKey, 60, function () use ($query) {
            // Optimize queries with single query using conditional aggregation
            $viewStats = View::selectRaw('
                SUM(CASE WHEN income_generated = 1 THEN income_amount ELSE 0 END) as total_stored_income,
                COUNT(*) as total_views,
                SUM(CASE WHEN validation_passed = 1 THEN 1 ELSE 0 END) as total_validated_views,
                SUM(CASE WHEN validation_passed = 0 THEN 1 ELSE 0 END) as total_failed_views
            ')
            ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
            ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
            ->first();
            
            $totalStoredIncome = $viewStats->total_stored_income ?? 0;
            $totalViews = $viewStats->total_views ?? 0;
            $totalValidatedViews = $viewStats->total_validated_views ?? 0;
            $totalFailedViews = $viewStats->total_failed_views ?? 0;
            
            // Optimize withdrawal queries
            $withdrawalStats = Withdrawal::selectRaw('
                SUM(CASE WHEN status = "confirmed" THEN amount ELSE 0 END) as total_confirmed
            ')
            ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
            ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
            ->first();
            
            $totalWithdrawals = $withdrawalStats->total_confirmed ?? 0;
            
            $totalEventPayouts = \App\Models\EventPayout::where('status', 'confirmed')
                ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
                ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
                ->sum('prize_amount');
            
            // Get current CPM for comparison
            $currentCpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);
            
            // Calculate current user balances (this is always current, not filtered by date)
            $totalUserBalances = User::sum('balance');
            
            // Calculate total paid out (withdrawals + event payouts)
            $totalPaidOut = $totalWithdrawals + $totalEventPayouts;

            $dateRangeLabel = $this->getDateRangeLabel();

        return [
            Stat::make('💰 Total Pendapatan Platform', 'Rp' . number_format($totalStoredIncome, 0, ',', '.'))
                ->description("Dari {$totalValidatedViews} views yang lolos validasi ({$dateRangeLabel})")
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('📊 Total Views & Validasi', number_format($totalViews))
                ->description("Lolos: {$totalValidatedViews} | Gagal: {$totalFailedViews} ({$dateRangeLabel})")
                ->icon('heroicon-o-eye')
                ->color('info'),
            Stat::make('⚙️ CPM Saat Ini', 'Rp' . number_format($currentCpm, 0, ',', '.'))
                ->description("Pengaturan CPM terkini")
                ->icon('heroicon-o-cog-6-tooth')
                ->color('warning'),
            Stat::make('Total Pengguna', User::count())
                ->description('Total pengguna terdaftar')
                ->icon('heroicon-o-users'),
            Stat::make('Total Video', Video::count())
                ->description(Video::where('is_active', true)->count() . ' Aktif | ' . Video::where('is_active', false)->count() . ' Tidak Aktif')
                ->icon('heroicon-o-video-camera')
                ->color('info'),
            Stat::make('Video Aktif', Video::where('is_active', true)->count())
                ->description('Video yang tersedia dan dapat diakses')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Video Tidak Aktif', Video::where('is_active', false)->count())
                ->description('Video yang dinonaktifkan atau tidak tersedia')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
            Stat::make('Video ' . $dateRangeLabel, Video::query()
                ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
                ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
                ->count())
                ->description('Video yang ditambahkan dalam periode ini')
                ->icon('heroicon-o-calendar-days')
                ->color('primary'),
            Stat::make('Video dengan Views', Video::has('views')->count())
                ->description('Video yang sudah pernah dilihat')
                ->icon('heroicon-o-eye')
                ->color('success'),
            Stat::make('Video Tanpa Views', Video::doesntHave('views')->count())
                ->description('Video yang belum pernah dilihat')
                ->icon('heroicon-o-eye-slash')
                ->color('gray'),
            Stat::make('💸 Financial Overview', 'Rp' . number_format($totalStoredIncome, 0, ',', '.'))
                ->description("Pendapatan: Rp" . number_format($totalStoredIncome, 0, ',', '.') . " | Dibayar: Rp" . number_format($totalPaidOut, 0, ',', '.') . " | Saldo: Rp" . number_format($totalUserBalances, 0, ',', '.') . " ({$dateRangeLabel})")
                ->icon('heroicon-o-banknotes')
                ->color('info'),
            Stat::make('📋 Withdrawal Status', Withdrawal::count())
                ->description("Berhasil: " . Withdrawal::where('status', 'confirmed')->count() . " | Pending: " . Withdrawal::where('status', 'pending')->count() . " | Ditolak: " . Withdrawal::where('status', 'rejected')->count())
                ->icon('heroicon-o-clipboard-document-list')
                ->color('warning'),
        ];
        });
    }

    private function buildDateQuery(): array
    {
        if (!$this->dateRange) {
            return ['start' => null, 'end' => null];
        }
        
        return $this->dateRange;
    }

    private function getDateRangeLabel(): string
    {
        if (!$this->dateRange || (!$this->dateRange['start'] && !$this->dateRange['end'])) {
            return 'Semua Data';
        }

        if ($this->dateRange['start'] && $this->dateRange['end']) {
            $start = $this->dateRange['start']->format('d M Y');
            $end = $this->dateRange['end']->format('d M Y');
            return "{$start} - {$end}";
        }

        if ($this->dateRange['start']) {
            return 'Sejak ' . $this->dateRange['start']->format('d M Y');
        }

        if ($this->dateRange['end']) {
            return 'Sampai ' . $this->dateRange['end']->format('d M Y');
        }

        return 'Semua Data';
    }
}
