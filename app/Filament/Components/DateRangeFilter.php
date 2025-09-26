<?php

namespace App\Filament\Components;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;
use Filament\Forms\Set;

class DateRangeFilter extends Component
{
    public static function make(string $name = 'date_range'): array
    {
        return [
            Select::make($name)
                ->label('Filter Tanggal')
                ->options([
                    'all' => 'Semua Data',
                    'today' => 'Hari Ini',
                    'yesterday' => 'Kemarin',
                    'week' => '7 Hari Terakhir',
                    'month' => 'Bulan Ini',
                    'custom' => 'Rentang Kustom',
                ])
                ->default('all')
                ->reactive()
                ->afterStateUpdated(function (Set $set, $state) {
                    if ($state !== 'custom') {
                        $set('start_date', null);
                        $set('end_date', null);
                    }
                }),
            
            DatePicker::make('start_date')
                ->label('Tanggal Mulai')
                ->visible(fn (Get $get): bool => $get($name) === 'custom')
                ->reactive(),
                
            DatePicker::make('end_date')
                ->label('Tanggal Akhir')
                ->visible(fn (Get $get): bool => $get($name) === 'custom')
                ->reactive()
                ->afterStateUpdated(function (Set $set, Get $get) {
                    $startDate = $get('start_date');
                    $endDate = $get('end_date');
                    
                    if ($startDate && $endDate && $startDate > $endDate) {
                        $set('end_date', $startDate);
                    }
                }),
        ];
    }
    
    public static function getDateRange($dateRange, $startDate = null, $endDate = null): array
    {
        $now = now();
        
        return match ($dateRange) {
            'today' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'yesterday' => [
                'start' => $now->copy()->subDay()->startOfDay(),
                'end' => $now->copy()->subDay()->endOfDay(),
            ],
            'week' => [
                'start' => $now->copy()->subDays(7)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ],
            'custom' => [
                'start' => $startDate ? $now->copy()->parse($startDate)->startOfDay() : null,
                'end' => $endDate ? $now->copy()->parse($endDate)->endOfDay() : null,
            ],
            default => [
                'start' => null,
                'end' => null,
            ],
        };
    }
}
