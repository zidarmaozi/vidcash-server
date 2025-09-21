<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\IncomeStatsWidget;
use App\Filament\Widgets\IncomeChartWidget;
use App\Filament\Widgets\TopEarnersWidget;
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

    public ?string $dateRange = 'today';
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount(): void
    {
        $this->dateRange = 'today';
    }

    public function updatedDateRange($value): void
    {
        if ($value !== 'custom') {
            $this->startDate = null;
            $this->endDate = null;
        }
        $this->dispatch('$refresh');
    }

    public function updatedStartDate($value): void
    {
        $this->dispatch('$refresh');
    }

    public function updatedEndDate($value): void
    {
        $this->dispatch('$refresh');
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
    
    public function getHeaderWidgets(): array
    {
        return [
            IncomeStatsWidget::class,
        ];
    }
    
    public function getFooterWidgets(): array
    {
        return [
            IncomeChartWidget::class,
            TopEarnersWidget::class,
        ];
    }

    public function getDateRange(): array
    {
        switch ($this->dateRange) {
            case 'today':
                return [
                    'start' => Carbon::today(),
                    'end' => Carbon::today()->endOfDay(),
                ];
            case 'yesterday':
                return [
                    'start' => Carbon::yesterday(),
                    'end' => Carbon::yesterday()->endOfDay(),
                ];
            case 'week':
                return [
                    'start' => Carbon::now()->subDays(7),
                    'end' => Carbon::now(),
                ];
            case 'month':
                return [
                    'start' => Carbon::now()->subDays(30),
                    'end' => Carbon::now(),
                ];
            case 'quarter':
                return [
                    'start' => Carbon::now()->subMonths(3),
                    'end' => Carbon::now(),
                ];
            case 'year':
                return [
                    'start' => Carbon::now()->subYear(),
                    'end' => Carbon::now(),
                ];
            case 'custom':
                return [
                    'start' => Carbon::parse($this->startDate ?? now()->subDays(7)),
                    'end' => Carbon::parse($this->endDate ?? now()),
                ];
            default:
                return [
                    'start' => Carbon::today(),
                    'end' => Carbon::today()->endOfDay(),
                ];
        }
    }
}