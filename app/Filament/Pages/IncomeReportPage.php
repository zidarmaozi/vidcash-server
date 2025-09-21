<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\IncomeStatsWidget;
use App\Filament\Widgets\IncomeChartWidget;
use App\Filament\Widgets\TopEarnersWidget;
use App\Filament\Widgets\TopVideosWidget;
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
    
    public function mount(): void
    {
        // Page initialization if needed
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
            IncomeStatsWidget::class,
        ];
    }
    
    protected function getFooterWidgets(): array
    {
        return [
            IncomeChartWidget::class,
            TopEarnersWidget::class,
        ];
    }
    
}
