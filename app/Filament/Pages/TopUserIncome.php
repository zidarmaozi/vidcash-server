<?php

namespace App\Filament\Pages;

use App\Filament\Components\DateRangeFilter;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class TopUserIncome extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.pages.top-user-income';

    protected static ?string $title = 'Laporan Top Income User';

    protected static ?string $navigationLabel = 'Top User Income';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 10;

    public array $data = [
        'date_range' => 'today', // Default to today for relevance
        'start_date' => null,
        'end_date' => null,
        'limit' => 10,
    ];

    public function mount(): void
    {
        $this->form->fill([
            'date_range' => 'today',
            'limit' => 10,
        ]);

        // Initialize dates based on default range
        $dates = DateRangeFilter::getDateRange('today');
        $this->data['start_date'] = $dates['start'];
        $this->data['end_date'] = $dates['end'];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Laporan')
                    ->schema([
                        ...DateRangeFilter::make('date_range'),
                        Select::make('limit')
                            ->label('Jumlah Data')
                            ->options([
                                10 => 'Top 10',
                                25 => 'Top 25',
                                50 => 'Top 50',
                                100 => 'Top 100',
                            ])
                            ->default(10)
                            ->required(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),
            ])
            ->statePath('data')
            ->live(); // Enable live updates
    }

    // Handle form updates to refresh table data
    public function updatedData(): void
    {
        // Re-calculate start/end date when date_range changes
        // This is handled partly by the DateRangeFilter component logic if implemented there,
        // but often we need manual binding or ensures the array keys are set.

        // Actually, DateRangeFilter usually binds to state paths. 
        // We'll trust the $data state is updated.
        // But we might need to parse 'date_range' select value to actual dates if it's a preset.

        $dates = DateRangeFilter::getDateRange(
            $this->data['date_range'] ?? 'all',
            $this->data['start_date'] ?? null,
            $this->data['end_date'] ?? null
        );

        $this->data['start_date'] = $dates['start'];
        $this->data['end_date'] = $dates['end'];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                // Determine date range
                $dates = DateRangeFilter::getDateRange(
                    $this->data['date_range'] ?? 'all',
                    $this->data['start_date'] ?? null, // These might be populated by custom picker in DateRangeFilter
                    $this->data['end_date'] ?? null
                );

                $startDate = $dates['start'];
                $endDate = $dates['end'];
                $limit = $this->data['limit'] ?? 10;

                // Base Query using Eloquent to allow Filament Table to work its magic easily?
                // Filament tables work best with Eloquent Builders.
                // However, for aggregation like this, we either us a macro or a raw query.
                // Since user provided specific SQL grouping, let's try to adapt it to Eloquent Builder if possible,
                // OR use a read-only model/FromQuery.
    
                // Let's use User model and join views.
                return User::query()
                    ->join('videos', 'users.id', '=', 'videos.user_id')
                    ->join('views', 'videos.id', '=', 'views.video_id')
                    ->select(
                        'users.id',
                        'users.name',
                        'users.email',
                        DB::raw('SUM(views.income_amount) as total_income'),
                        DB::raw('COUNT(views.id) as total_views')
                    )
                    ->when($startDate, fn($q) => $q->where('views.created_at', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->where('views.created_at', '<=', $endDate))
                    ->groupBy('users.id', 'users.name', 'users.email')
                    ->orderByDesc('total_income')
                    ->limit($limit);
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Nama User')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('total_views')
                    ->label('Total Views')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_income')
                    ->label('Total Pendapatan')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
            ])
            ->paginated(false); // Since we use limit in query
    }
}
