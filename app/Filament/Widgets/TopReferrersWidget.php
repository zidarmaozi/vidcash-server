<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopReferrersWidget extends BaseWidget
{
    protected static ?string $heading = '🏆 Top Referrals (Growth Drivers)';

    protected static ?int $sort = 7;

    // Accept date range if needed, though referrals are often tracked all-time or strictly by user creation date
    public ?array $dateRange = null;

    public function mount(?array $dateRange = null): void
    {
        $this->dateRange = $dateRange ?? ['start' => null, 'end' => null];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->has('referrals')
                    ->withCount([
                        'referrals as referral_count' => function ($query) {
                            // Optional: Filter referrals by the selected date range
                            if ($this->dateRange['start']) {
                                $query->where('created_at', '>=', $this->dateRange['start']);
                            }
                            if ($this->dateRange['end']) {
                                $query->where('created_at', '<=', $this->dateRange['end']);
                            }
                        }
                    ])
                    ->orderByDesc('referral_count')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('User')
                    ->description(fn(User $record): string => $record->email)
                    ->searchable(),
                Tables\Columns\TextColumn::make('referral_count')
                    ->label('Recruits')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->money('IDR'),
            ])
            ->paginated(false);
    }
}
