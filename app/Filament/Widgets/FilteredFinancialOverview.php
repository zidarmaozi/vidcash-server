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
        $this->dateRange = $dateRange ?? ['start' => null, 'end' => null];
    }

    protected function getStats(): array
    {
        $query = $this->buildDateQuery();
        
        // Get current CPM setting for comparison
        $currentCpm = (int) (Setting::where('key', 'cpm')->first()->value ?? 10);
        
        // Calculate total platform income from STORED income amounts
        $totalStoredIncome = View::where('income_generated', true)
            ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
            ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
            ->sum('income_amount');
            
        $totalViews = View::query()
            ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
            ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
            ->count();
            
        $totalValidatedViews = View::where('validation_passed', true)
            ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
            ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
            ->count();
            
        $totalFailedViews = View::where('validation_passed', false)
            ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
            ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
            ->count();
        
        // Calculate current user balances (this is always current, not filtered by date)
        $totalUserBalances = User::sum('balance');
        
        // Calculate total paid out (withdrawals + event payouts)
        $totalWithdrawals = Withdrawal::where('status', 'confirmed')
            ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
            ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
            ->sum('amount');
            
        $totalEventPayouts = EventPayout::where('status', 'confirmed')
            ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
            ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
            ->sum('prize_amount');
            
        $totalPaidOut = $totalWithdrawals + $totalEventPayouts;
        
        // Calculate pending amounts
        $pendingWithdrawals = Withdrawal::where('status', 'pending')
            ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
            ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
            ->sum('amount');
            
        $pendingEventPayouts = EventPayout::where('status', 'pending')
            ->when($query['start'], fn($q) => $q->where('created_at', '>=', $query['start']))
            ->when($query['end'], fn($q) => $q->where('created_at', '<=', $query['end']))
            ->sum('prize_amount');
        
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
            
            Stat::make('Pending (Withdrawal + Event)', 'Rp' . number_format($pendingWithdrawals + $pendingEventPayouts, 0, ',', '.'))
                ->description("Penarikan: Rp" . number_format($pendingWithdrawals, 0, ',', '.') . " + Event: Rp" . number_format($pendingEventPayouts, 0, ',', '.') . " ({$dateRangeLabel})")
                ->icon('heroicon-o-clock')
                ->color('info'),
            
            Stat::make('Net Platform Profit', 'Rp' . number_format($netPlatformProfit, 0, ',', '.'))
                ->description('Pendapatan - Dibayar - Saldo User')
                ->icon('heroicon-o-chart-bar')
                ->color($netPlatformProfit >= 0 ? 'success' : 'danger'),
        ];
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
