<?php

namespace App\Filament\Pages;

use App\Filament\Components\DateRangeFilter;
use App\Filament\Widgets\FilteredDashboardStats;
use App\Filament\Widgets\FilteredFinancialOverview;
use App\Filament\Widgets\FilteredMonthlyStatsChart;
use App\Filament\Widgets\PendingWithdrawals;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Illuminate\Contracts\Support\Htmlable;

class FilteredDashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.filtered-dashboard';
    protected static ?string $title = 'Dashboard dengan Filter';
    protected static ?string $navigationLabel = 'Dashboard Filter';
    protected static ?int $navigationSort = 1;

    public ?string $date_range = 'all';
    public ?string $start_date = null;
    public ?string $end_date = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Tanggal')
                    ->schema(DateRangeFilter::make('date_range'))
                    ->collapsible()
                    ->collapsed(false)
                    ->compact(),
            ])
            ->statePath('data');
    }

    public function getWidgets(): array
    {
        return [
            FilteredDashboardStats::class,
            FilteredFinancialOverview::class,
            FilteredMonthlyStatsChart::class,
            PendingWithdrawals::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }

    public function getDateRange(): array
    {
        return DateRangeFilter::getDateRange(
            $this->date_range,
            $this->start_date,
            $this->end_date
        );
    }
}
