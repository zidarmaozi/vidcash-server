<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Filament\Resources\VideoResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class ShowVideo extends ViewRecord
{
    protected static string $resource = VideoResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Video Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Title')
                                    ->size(TextEntry\TextEntrySize::Large),
                                
                                TextEntry::make('user.name')
                                    ->label('Owner')
                                    ->size(TextEntry\TextEntrySize::Large),
                                
                                TextEntry::make('video_code')
                                    ->label('Video Code')
                                    ->copyable()
                                    ->copyMessage('Video code copied!'),
                                
                                TextEntry::make('generated_link')
                                    ->label('Generated Link')
                                    ->copyable()
                                    ->copyMessage('Link copied!')
                                    ->url(fn ($record) => $record->generated_link)
                                    ->openUrlInNewTab(),
                                
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),
                                
                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime(),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Performance Metrics')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('total_views')
                                    ->label('Total Views')
                                    ->getStateUsing(fn ($record) => $record->views()->count())
                                    ->badge()
                                    ->color('info'),
                                
                                TextEntry::make('income_generated')
                                    ->label('Income Generated')
                                    ->getStateUsing(function ($record) {
                                        $income = $record->views()
                                            ->where('income_generated', true)
                                            ->sum('income_amount');
                                        return 'Rp' . number_format($income, 0, ',', '.');
                                    })
                                    ->badge()
                                    ->color('success'),
                                
                                TextEntry::make('validation_success_rate')
                                    ->label('Success Rate')
                                    ->getStateUsing(function ($record) {
                                        $totalViews = $record->views()->count();
                                        if ($totalViews === 0) return '0%';
                                        
                                        $successfulViews = $record->views()->where('validation_passed', true)->count();
                                        $rate = round(($successfulViews / $totalViews) * 100, 1);
                                        return $rate . '%';
                                    })
                                    ->badge()
                                    ->color(function (string $state): string {
                                        $rate = (float) str_replace('%', '', $state);
                                        if ($rate >= 80) return 'success';
                                        if ($rate >= 60) return 'warning';
                                        return 'danger';
                                    }),
                                
                                TextEntry::make('last_view')
                                    ->label('Last View')
                                    ->getStateUsing(function ($record) {
                                        $lastView = $record->views()->latest()->first();
                                        return $lastView ? $lastView->created_at->diffForHumans() : 'Never';
                                    })
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Views History')
                    ->schema([
                        $this->getViewsTable(),
                    ])
                    ->collapsible(),
            ]);
    }

    protected function getViewsTable(): \Filament\Tables\Table
    {
        return \Filament\Tables\Table::make()
            ->query(
                $this->record->views()->getQuery()
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('View Time')
                    ->dateTime()
                    ->sortable(),
                
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable(),
                
                TextColumn::make('validation_passed')
                    ->label('Validation')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Passed' : 'Failed'),
                
                TextColumn::make('income_generated')
                    ->label('Income Generated')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                
                TextColumn::make('income_amount')
                    ->label('Income Amount')
                    ->money('IDR')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'Rp' . number_format($state, 0, ',', '.') : '-'),
                
                TextColumn::make('cpm_at_time')
                    ->label('CPM at Time')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'Rp' . number_format($state, 0, ',', '.') : '-'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100])
            ->searchable()
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('validation_passed')
                    ->label('Validation Status')
                    ->options([
                        '1' => 'Passed',
                        '0' => 'Failed',
                    ]),
                
                \Filament\Tables\Filters\SelectFilter::make('income_generated')
                    ->label('Income Generated')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ]),
            ]);
    }
}
