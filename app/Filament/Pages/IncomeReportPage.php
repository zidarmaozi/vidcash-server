<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SimpleIncomeStatsWidget;
use App\Filament\Widgets\SimpleIncomeChartWidget;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

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
    
    public function mount(): void
    {
        $this->timeFilter = 'week';
        $this->customStartDate = now()->startOfWeek()->format('Y-m-d');
        $this->customEndDate = now()->endOfWeek()->format('Y-m-d');
    }
    
    public function updatedTimeFilter($value): void
    {
        $this->updateCustomDates($value);
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
        return [
            SimpleIncomeStatsWidget::class,
        ];
    }
    
    protected function getFooterWidgets(): array
    {
        return [
            SimpleIncomeChartWidget::class,
        ];
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
