<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Group;
use Illuminate\Support\Facades\DB;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return "Detail User: {$this->record->name}";
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\EditAction::make()
                ->label('Edit User')
                ->icon('heroicon-o-pencil-square'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserResource\Widgets\UserStatsWidget::class,
        ];
    }
    
    protected function getFooterWidgets(): array
    {
        return [
            UserResource\Widgets\UserEarningsChartWidget::class,
            UserResource\Widgets\UserVideosWidget::class,
            UserResource\Widgets\UserWithdrawalsWidget::class,
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Akun')
                    ->icon('heroicon-o-user-circle')
                    ->description('Informasi dasar user dan akun')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama Lengkap')
                                    ->icon('heroicon-m-user')
                                    ->weight('bold')
                                    ->size('lg'),
                                
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->icon('heroicon-m-envelope')
                                    ->copyable()
                                    ->copyMessage('Email disalin!')
                                    ->copyMessageDuration(1500),
                                
                                TextEntry::make('role')
                                    ->label('Role')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'admin' => 'danger',
                                        'user' => 'success',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                            ]),
                    ])
                    ->collapsible(),
                
                Section::make('Pengaturan & Statistik')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->description('Level validasi dan statistik akun')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('validation_level')
                                    ->label('Level Validasi')
                                    ->default('Default (dari setting)')
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-m-shield-check'),
                                
                                TextEntry::make('created_at')
                                    ->label('Bergabung Sejak')
                                    ->dateTime('d M Y')
                                    ->icon('heroicon-m-calendar')
                                    ->badge()
                                    ->color('success'),
                                
                                TextEntry::make('days_active')
                                    ->label('Hari Aktif')
                                    ->state(fn ($record) => now()->diffInDays($record->created_at) . ' hari')
                                    ->icon('heroicon-m-clock')
                                    ->badge()
                                    ->color('primary'),
                            ]),
                    ])
                    ->collapsible(),
                
                Section::make('Keuangan')
                    ->icon('heroicon-o-currency-dollar')
                    ->description('Detail keuangan dan transaksi')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('balance')
                                    ->label('Saldo Saat Ini')
                                    ->money('IDR')
                                    ->weight('bold')
                                    ->color('success')
                                    ->icon('heroicon-m-wallet'),
                                
                                TextEntry::make('total_withdrawn')
                                    ->label('Total Penarikan')
                                    ->money('IDR')
                                    ->weight('bold')
                                    ->color('danger')
                                    ->icon('heroicon-m-arrow-up-tray'),
                                
                                TextEntry::make('lifetime_earnings')
                                    ->label('Total Penghasilan')
                                    ->state(function ($record) {
                                        $totalIncome = DB::table('views')
                                            ->join('videos', 'views.video_id', '=', 'videos.id')
                                            ->where('videos.user_id', $record->id)
                                            ->where('views.income_generated', true)
                                            ->sum('views.income_amount');
                                        return 'Rp' . number_format($totalIncome, 0, ',', '.');
                                    })
                                    ->weight('bold')
                                    ->color('info')
                                    ->icon('heroicon-m-chart-bar'),
                            ]),
                    ])
                    ->collapsible(),
                
                Section::make('Statistik Konten')
                    ->icon('heroicon-o-video-camera')
                    ->description('Statistik video dan performa')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('total_videos')
                                    ->label('Total Video')
                                    ->state(fn ($record) => $record->videos()->count())
                                    ->icon('heroicon-m-video-camera')
                                    ->badge()
                                    ->color('primary'),
                                
                                TextEntry::make('active_videos')
                                    ->label('Video Aktif')
                                    ->state(fn ($record) => $record->videos()->where('is_active', true)->count())
                                    ->icon('heroicon-m-check-circle')
                                    ->badge()
                                    ->color('success'),
                                
                                TextEntry::make('total_views')
                                    ->label('Total Views')
                                    ->state(function ($record) {
                                        return DB::table('views')
                                            ->join('videos', 'views.video_id', '=', 'videos.id')
                                            ->where('videos.user_id', $record->id)
                                            ->where('views.validation_passed', true)
                                            ->count();
                                    })
                                    ->icon('heroicon-m-eye')
                                    ->badge()
                                    ->color('info'),
                                
                                TextEntry::make('success_rate')
                                    ->label('Success Rate')
                                    ->state(function ($record) {
                                        $totalViews = DB::table('views')
                                            ->join('videos', 'views.video_id', '=', 'videos.id')
                                            ->where('videos.user_id', $record->id)
                                            ->count();
                                        
                                        $validViews = DB::table('views')
                                            ->join('videos', 'views.video_id', '=', 'videos.id')
                                            ->where('videos.user_id', $record->id)
                                            ->where('views.validation_passed', true)
                                            ->count();
                                        
                                        if ($totalViews == 0) return '0%';
                                        return round(($validViews / $totalViews) * 100, 1) . '%';
                                    })
                                    ->icon('heroicon-m-arrow-trending-up')
                                    ->badge()
                                    ->color('warning'),
                            ]),
                    ])
                    ->collapsible(),
                
                Section::make('Akun Pembayaran')
                    ->icon('heroicon-o-credit-card')
                    ->description('Daftar akun pembayaran terdaftar')
                    ->schema([
                        TextEntry::make('payment_accounts_list')
                            ->label('')
                            ->state(function ($record) {
                                $accounts = $record->paymentAccounts;
                                if ($accounts->isEmpty()) {
                                    return '❌ Belum ada akun pembayaran terdaftar';
                                }
                                return $accounts->map(function ($account) {
                                    $icon = $account->type === 'bank' ? '🏦' : '💳';
                                    return "{$icon} {$account->type}: {$account->account_name} ({$account->bank_name}) - {$account->account_number}";
                                })->join("\n\n");
                            })
                            ->columnSpanFull()
                            ->markdown(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}

