<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\IncomeReportWidget;
use App\Filament\Widgets\IncomeStatsWidget;
use App\Filament\Widgets\TopEarnersWidget;
use App\Filament\Widgets\TopVideosWidget;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class IncomeReportPage extends Page implements HasForms
{
    use InteractsWithForms;
    
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
        $this->form->fill([
            'timeFilter' => 'week',
            'customStartDate' => now()->startOfWeek()->format('Y-m-d'),
            'customEndDate' => now()->endOfWeek()->format('Y-m-d'),
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('timeFilter')
                    ->label('Time Period')
                    ->options([
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'week' => 'This Week',
                        'last_week' => 'Last Week',
                        'month' => 'This Month',
                        'last_month' => 'Last Month',
                        'quarter' => 'This Quarter',
                        'year' => 'This Year',
                        'custom' => 'Custom Range',
                    ])
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->updateCustomDates($state);
                    }),
                    
                DatePicker::make('customStartDate')
                    ->label('Start Date')
                    ->visible(fn ($get) => $get('timeFilter') === 'custom')
                    ->required(fn ($get) => $get('timeFilter') === 'custom'),
                    
                DatePicker::make('customEndDate')
                    ->label('End Date')
                    ->visible(fn ($get) => $get('timeFilter') === 'custom')
                    ->required(fn ($get) => $get('timeFilter') === 'custom'),
            ])
            ->statePath('data');
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
            IncomeReportWidget::class,
            TopEarnersWidget::class,
            TopVideosWidget::class,
        ];
    }
    
    private function updateCustomDates(string $filter): void
    {
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
