<?php

namespace App\Filament\Widgets;

use App\Models\View;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\EventPayout;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FilteredFinancialOverview extends BaseWidget
{
    public ?array $dateRange = null;

    protected ?string $heading = 'Ringkasan Keuangan Platform';

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
        $cacheKey = 'financial_overview_' . md5(serialize($query));
        
        return cache()->remember($cacheKey, 60, function () use ($query) {
            // Get current CPM setting for comparison
            $currentCpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);
            
            // Optimize View queries with single query using conditional aggregation
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
            
            // Calculate current user balances (this is always current, not filtered by date)
            $totalUserBalances = User::sum('balance');
            
            // Calculate users registered in the date range
            $usersInRange = User::query()
                ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
                ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
                ->count();
            
            // Optimize withdrawal queries with single query
            $withdrawalStats = Withdrawal::selectRaw('
                SUM(CASE WHEN status = "confirmed" THEN amount ELSE 0 END) as total_confirmed,
                SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as total_pending
            ')
            ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
            ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
            ->first();
            
            $totalWithdrawals = $withdrawalStats->total_confirmed ?? 0;
            $pendingWithdrawals = $withdrawalStats->total_pending ?? 0;
            
            // Optimize event payout queries with single query
            $eventPayoutStats = EventPayout::selectRaw('
                SUM(CASE WHEN status = "confirmed" THEN prize_amount ELSE 0 END) as total_confirmed,
                SUM(CASE WHEN status = "pending" THEN prize_amount ELSE 0 END) as total_pending
            ')
            ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
            ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
            ->first();
            
            $totalEventPayouts = $eventPayoutStats->total_confirmed ?? 0;
            $pendingEventPayouts = $eventPayoutStats->total_pending ?? 0;
            
            $totalPaidOut = $totalWithdrawals + $totalEventPayouts;
            
            // Calculate net platform profit (if any)
            $netPlatformProfit = $totalStoredIncome - $totalPaidOut - $totalUserBalances;

            $dateRangeLabel = $this->getDateRangeLabel();

        return [
            Stat::make('💰 TOTAL PENDAPATAN PLATFORM (STORED)', 'Rp' . number_format($totalStoredIncome, 0, ',', '.'))
                ->description("Dari {$totalValidatedViews} views yang lolos validasi - {$dateRangeLabel}")
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
            
            Stat::make('Total Sudah Dibayar', 'Rp' . number_format($totalPaidOut, 0, ',', '.'))
                ->description("Penarikan: Rp" . number_format($totalWithdrawals, 0, ',', '.') . " + Event: Rp" . number_format($totalEventPayouts, 0, ',', '.') . " ({$dateRangeLabel})")
                ->icon('heroicon-o-banknotes')
                ->color('danger'),
            
            Stat::make('Saldo User Saat Ini', 'Rp' . number_format($totalUserBalances, 0, ',', '.'))
                ->description('Belum ditarik (selalu current)')
                ->icon('heroicon-o-wallet')
                ->color('warning'),
            
            Stat::make('Pengguna ' . $dateRangeLabel, number_format($usersInRange))
                ->description('Pengguna yang terdaftar dalam periode ini')
                ->icon('heroicon-o-user-plus')
                ->color('primary'),
            
            Stat::make('Pending (Withdrawal + Event)', 'Rp' . number_format($pendingWithdrawals + $pendingEventPayouts, 0, ',', '.'))
                ->description("Penarikan: Rp" . number_format($pendingWithdrawals, 0, ',', '.') . " + Event: Rp" . number_format($pendingEventPayouts, 0, ',', '.') . " ({$dateRangeLabel})")
                ->icon('heroicon-o-clock')
                ->color('info'),
            
            Stat::make('Net Platform Profit', 'Rp' . number_format($netPlatformProfit, 0, ',', '.'))
                ->description('Pendapatan - Dibayar - Saldo User')
                ->icon('heroicon-o-chart-bar')
                ->color($netPlatformProfit >= 0 ? 'success' : 'danger'),
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
