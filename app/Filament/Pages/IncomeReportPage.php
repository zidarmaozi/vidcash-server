<?php

namespace App\Filament\Pages;

use App\Models\View;
use App\Models\Withdrawal;
use App\Models\EventPayout;
use App\Models\User;
use App\Models\Video;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class IncomeReportPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.income-report';
    protected static ?string $title = 'Income Report';
    protected static ?string $navigationLabel = 'Income Report';
    protected static ?int $navigationSort = 2;
    
    public ?string $timeFilter = 'week';
    public ?string $customStartDate = null;
    public ?string $customEndDate = null;
    
    // Computed properties for the view
    public $totalIncome = 0;
    public $totalViews = 0;
    public $validatedViews = 0;
    public $failedViews = 0;
    public $avgIncomePerView = 0;
    public $totalWithdrawals = 0;
    public $pendingWithdrawals = 0;
    public $totalEventPayouts = 0;
    public $netIncome = 0;
    public $incomeData = [];
    public $topEarners = [];
    public $topVideos = [];
    
    public function mount(): void
    {
        $this->timeFilter = 'week';
        $this->updateCustomDates('week');
        $this->loadData();
    }
    
    public function updatedTimeFilter($value): void
    {
        $this->updateCustomDates($value);
        $this->loadData();
    }
    
    public function updatedCustomStartDate($value): void
    {
        $this->loadData();
    }
    
    public function updatedCustomEndDate($value): void
    {
        $this->loadData();
    }
    
    public function loadData(): void
    {
        $dateRange = $this->getDateRange();
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        
        // Load basic stats
        $this->totalIncome = View::where('income_generated', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('income_amount');
            
        $this->totalViews = View::whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $this->validatedViews = View::where('validation_passed', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $this->failedViews = View::where('validation_passed', false)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $this->avgIncomePerView = $this->totalViews > 0 ? $this->totalIncome / $this->totalViews : 0;
        
        $this->totalWithdrawals = Withdrawal::where('status', 'confirmed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
        $this->pendingWithdrawals = Withdrawal::where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
        $this->totalEventPayouts = EventPayout::where('status', 'confirmed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('prize_amount');
            
        $this->netIncome = $this->totalIncome - $this->totalWithdrawals - $this->totalEventPayouts;
        
        // Load chart data
        $this->loadChartData($startDate, $endDate);
        
        // Load top earners
        $this->loadTopEarners($startDate, $endDate);
        
        // Load top videos
        $this->loadTopVideos($startDate, $endDate);
    }
    
    private function loadChartData($startDate, $endDate): void
    {
        $incomeData = View::where('income_generated', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(income_amount) as total_income'),
                DB::raw('COUNT(*) as total_views')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Get all dates in range
        $dates = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }
        
        $this->incomeData = [];
        foreach ($dates as $date) {
            $dayData = $incomeData->firstWhere('date', $date);
            $this->incomeData[] = [
                'date' => Carbon::parse($date)->format('M d'),
                'income' => $dayData ? (float) $dayData->total_income : 0,
                'views' => $dayData ? (int) $dayData->total_views : 0,
            ];
        }
    }
    
    private function loadTopEarners($startDate, $endDate): void
    {
        $this->topEarners = User::withCount(['videos as total_videos'])
            ->withSum(['videos as total_income' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('views', function ($q) use ($startDate, $endDate) {
                    $q->where('income_generated', true)
                      ->whereBetween('created_at', [$startDate, $endDate]);
                });
            }])
            ->orderBy('total_income', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'income' => (float) ($user->total_income ?? 0),
                    'videos' => (int) $user->total_videos,
                ];
            })
            ->toArray();
    }
    
    private function loadTopVideos($startDate, $endDate): void
    {
        $this->topVideos = Video::withCount(['views as total_views'])
            ->withSum(['views as total_income' => function ($query) use ($startDate, $endDate) {
                $query->where('income_generated', true)
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->where('is_active', true)
            ->orderBy('total_income', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($video) {
                return [
                    'title' => strlen($video->title) > 30 ? substr($video->title, 0, 30) . '...' : $video->title,
                    'income' => (float) ($video->total_income ?? 0),
                    'views' => (int) $video->total_views,
                ];
            })
            ->toArray();
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Report')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    Notification::make()
                        ->title('Export feature coming soon!')
                        ->info()
                        ->send();
                }),
                
            Action::make('refresh')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $this->dispatch('$refresh');
                    Notification::make()
                        ->title('Data refreshed successfully!')
                        ->success()
                        ->send();
                }),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [];
    }
    
    protected function getFooterWidgets(): array
    {
        return [];
    }
    
    private function updateCustomDates(string $filter): void
    {
        if ($filter === 'custom') {
            return; // Don't update custom dates when custom is selected
        }
        
        $dates = $this->getDateRange($filter);
        $this->customStartDate = $dates['start']->format('Y-m-d');
        $this->customEndDate = $dates['end']->format('Y-m-d');
    }
    
    private function getDateRange(string $filter): array
    {
        return match ($filter) {
            'today' => [
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'yesterday' => [
                'start' => now()->subDay()->startOfDay(),
                'end' => now()->subDay()->endOfDay(),
            ],
            'week' => [
                'start' => now()->startOfWeek(),
                'end' => now()->endOfWeek(),
            ],
            'last_week' => [
                'start' => now()->subWeek()->startOfWeek(),
                'end' => now()->subWeek()->endOfWeek(),
            ],
            'month' => [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
            ],
            'last_month' => [
                'start' => now()->subMonth()->startOfMonth(),
                'end' => now()->subMonth()->endOfMonth(),
            ],
            'quarter' => [
                'start' => now()->startOfQuarter(),
                'end' => now()->endOfQuarter(),
            ],
            'year' => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfYear(),
            ],
            'custom' => [
                'start' => $this->customStartDate ? Carbon::parse($this->customStartDate)->startOfDay() : now()->startOfWeek(),
                'end' => $this->customEndDate ? Carbon::parse($this->customEndDate)->endOfDay() : now()->endOfWeek(),
            ],
            default => [
                'start' => now()->startOfWeek(),
                'end' => now()->endOfWeek(),
            ],
        };
    }
}
