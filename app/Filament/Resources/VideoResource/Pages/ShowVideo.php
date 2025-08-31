<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Filament\Resources\VideoResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Actions;

class ShowVideo extends ViewRecord
{
    protected static string $resource = VideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('toggleStatus')
                ->label(fn (): string => $this->record->is_active ? 'Nonaktifkan Video' : 'Aktifkan Video')
                ->icon(fn (): string => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn (): string => $this->record->is_active ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn (): string => $this->record->is_active ? 'Nonaktifkan Video' : 'Aktifkan Video')
                ->modalDescription(fn (): string => $this->record->is_active 
                    ? 'Apakah Anda yakin ingin menonaktifkan video ini? Video yang dinonaktifkan tidak akan dapat diakses.' 
                    : 'Apakah Anda yakin ingin mengaktifkan video ini? Video yang diaktifkan akan dapat diakses kembali.')
                ->action(function () {
                    $this->record->update(['is_active' => !$this->record->is_active]);
                    
                    $status = $this->record->is_active ? 'diaktifkan' : 'dinonaktifkan';
                    \Filament\Notifications\Notification::make()
                        ->title("Video berhasil {$status}")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Video Preview')
                    ->schema([
                        TextEntry::make('video_player')
                            ->label('')
                            ->getStateUsing(function ($record) {
                                $videoUrl = 'https://cdn.videy.co/' . $record->video_code . '.mp4';
                                return view('components.video-player', ['videoUrl' => $videoUrl])->render();
                            })
                            ->html()
                            ->columnSpanFull(),
                        
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('video_url')
                                    ->label('CDN URL')
                                    ->getStateUsing(function ($record) {
                                        return 'https://cdn.videy.co/' . $record->video_code . '.mp4';
                                    })
                                    ->copyable()
                                    ->copyMessage('Video URL copied!')
                                    ->color('info'),
                                
                                TextEntry::make('video_format')
                                    ->label('Format')
                                    ->getStateUsing('MP4')
                                    ->badge()
                                    ->color('success'),
                                
                                TextEntry::make('video_code_display')
                                    ->label('Video Code')
                                    ->getStateUsing(fn ($record) => $record->video_code)
                                    ->copyable()
                                    ->copyMessage('Video code copied!')
                                    ->color('warning'),
                            ]),
                    ])
                    ->collapsible(false),

                Section::make('Video Status')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextEntry::make('status_info')
                                    ->label('Status Video')
                                    ->getStateUsing(function ($record) {
                                        $status = $record->is_active ? 'Aktif' : 'Tidak Aktif';
                                        $description = $record->is_active 
                                            ? 'Video ini sedang aktif dan dapat diakses oleh pengguna.'
                                            : 'Video ini sedang dinonaktifkan dan tidak dapat diakses oleh pengguna.';
                                        
                                        return "**{$status}**\n\n{$description}";
                                    })
                                    ->markdown()
                                    ->prose()
                                    ->color(fn ($record) => $record->is_active ? 'success' : 'danger'),
                            ]),
                    ])
                    ->collapsible(false),

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
                                
                                TextEntry::make('is_active')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Tidak Aktif'),
                                
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
                        TextEntry::make('views_summary')
                            ->label('Views Summary')
                            ->getStateUsing(function ($record) {
                                $totalViews = $record->views()->count();
                                $successfulViews = $record->views()->where('validation_passed', true)->count();
                                $failedViews = $record->views()->where('validation_passed', false)->count();
                                $totalIncome = $record->views()->where('income_generated', true)->sum('income_amount');
                                
                                if ($totalViews === 0) {
                                    return 'No views yet';
                                }
                                
                                return "Total: {$totalViews} | Success: {$successfulViews} | Failed: {$failedViews} | Income: Rp" . number_format($totalIncome, 0, ',', '.');
                            })
                            ->markdown()
                            ->prose(),
                        
                        TextEntry::make('recent_views')
                            ->label('Recent Views (Last 10)')
                            ->getStateUsing(function ($record) {
                                $recentViews = $record->views()
                                    ->latest()
                                    ->limit(10)
                                    ->get();
                                
                                if ($recentViews->isEmpty()) {
                                    return 'No views yet';
                                }
                                
                                $viewList = $recentViews->map(function ($view) {
                                    $status = $view->validation_passed ? '✅' : '❌';
                                    $income = $view->income_generated ? '💰' : '💸';
                                    $time = $view->created_at->format('M d, H:i');
                                    $ip = $view->ip_address;
                                    
                                    return "- {$status} {$income} **{$time}** - IP: `{$ip}` - " . 
                                           ($view->income_amount > 0 ? 'Rp' . number_format($view->income_amount, 0, ',', '.') : 'No income');
                                })->join("\n");
                                
                                return $viewList;
                            })
                            ->markdown()
                            ->prose(),
                    ])
                    ->collapsible(),
            ]);
    }
}
