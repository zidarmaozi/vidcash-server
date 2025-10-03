<?php

namespace App\Filament\Resources\TelegramBroadcastVideoResource\Pages;

use App\Filament\Resources\TelegramBroadcastVideoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;

class ViewTelegramBroadcastVideo extends ViewRecord
{
    protected static string $resource = TelegramBroadcastVideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewFullVideo')
                ->label('View Full Video Details')
                ->icon('heroicon-o-video-camera')
                ->url(fn () => route('filament.admin.resources.videos.show', $this->record->video))
                ->openUrlInNewTab()
                ->color('info'),
        ];
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Broadcast Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('Broadcast ID')
                                    ->badge()
                                    ->color('info'),
                                
                                TextEntry::make('created_at')
                                    ->label('Broadcasted At')
                                    ->dateTime('M d, Y H:i:s')
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-clock'),
                                
                                TextEntry::make('created_at_human')
                                    ->label('Broadcasted')
                                    ->getStateUsing(fn ($record) => $record->created_at->diffForHumans())
                                    ->badge()
                                    ->color('gray'),
                                
                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('M d, Y H:i:s')
                                    ->color('gray'),
                            ]),
                    ])
                    ->collapsible(false),

                Section::make('Video Information')
                    ->schema([
                        TextEntry::make('video_thumbnail')
                            ->label('Thumbnail Preview')
                            ->getStateUsing(function ($record) {
                                if (!$record->video->thumbnail_path) {
                                    return '<div class="text-center p-8 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                                <p class="text-sm text-gray-500 dark:text-gray-400">No thumbnail available</p>
                                            </div>';
                                }
                                
                                $thumbnailUrl = $record->video->thumbnail_url;
                                return '<div class="text-center">
                                            <img src="' . $thumbnailUrl . '" 
                                                 alt="Video Thumbnail" 
                                                 class="max-w-full h-auto rounded-lg shadow-lg mx-auto"
                                                 style="max-height: 300px; object-fit: contain;" />
                                        </div>';
                            })
                            ->html()
                            ->columnSpanFull(),
                        
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('video.title')
                                    ->label('Video Title')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->columnSpanFull(),
                                
                                TextEntry::make('video.video_code')
                                    ->label('Video Code')
                                    ->copyable()
                                    ->copyMessage('Video code copied!')
                                    ->color('warning')
                                    ->badge(),
                                
                                TextEntry::make('video.user.name')
                                    ->label('Video Owner')
                                    ->badge()
                                    ->color('info'),
                                
                                TextEntry::make('video.is_active')
                                    ->label('Video Status')
                                    ->badge()
                                    ->color(fn ($record): string => $record->video->is_active ? 'success' : 'danger')
                                    ->formatStateUsing(fn ($record): string => $record->video->is_active ? 'Active' : 'Inactive')
                                    ->icon(fn ($record): string => $record->video->is_active ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                
                                TextEntry::make('video.is_safe_content')
                                    ->label('Content Safety')
                                    ->badge()
                                    ->color(fn ($record): string => $record->video->is_safe_content ? 'success' : 'warning')
                                    ->formatStateUsing(fn ($record): string => $record->video->is_safe_content ? '🛡️ Safe Content' : '⚠️ Unsafe Content')
                                    ->icon(fn ($record): string => $record->video->is_safe_content ? 'heroicon-o-shield-check' : 'heroicon-o-shield-exclamation'),
                                
                                TextEntry::make('video.generated_link')
                                    ->label('Video Link')
                                    ->copyable()
                                    ->copyMessage('Video link copied!')
                                    ->color('info')
                                    ->url(fn ($record) => $record->video->generated_link)
                                    ->openUrlInNewTab()
                                    ->columnSpanFull(),
                                
                                TextEntry::make('video.created_at')
                                    ->label('Video Created At')
                                    ->dateTime('M d, Y H:i'),
                                
                                TextEntry::make('video.updated_at')
                                    ->label('Video Updated At')
                                    ->dateTime('M d, Y H:i'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Video Statistics')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('video.views_count')
                                    ->label('Total Views')
                                    ->getStateUsing(fn ($record) => $record->video->views()->count())
                                    ->badge()
                                    ->color('info')
                                    ->icon('heroicon-o-eye'),
                                
                                TextEntry::make('video.income_generated')
                                    ->label('Income Generated')
                                    ->getStateUsing(function ($record) {
                                        $income = $record->video->views()
                                            ->where('income_generated', true)
                                            ->sum('income_amount');
                                        return 'Rp' . number_format($income, 0, ',', '.');
                                    })
                                    ->badge()
                                    ->color('success')
                                    ->icon('heroicon-o-currency-dollar'),
                                
                                TextEntry::make('video.validation_success_rate')
                                    ->label('Success Rate')
                                    ->getStateUsing(function ($record) {
                                        $totalViews = $record->video->views()->count();
                                        if ($totalViews === 0) return '0%';
                                        
                                        $successfulViews = $record->video->views()->where('validation_passed', true)->count();
                                        $rate = round(($successfulViews / $totalViews) * 100, 1);
                                        return $rate . '%';
                                    })
                                    ->badge()
                                    ->color(function ($state): string {
                                        $rate = (float) str_replace('%', '', $state);
                                        if ($rate >= 80) return 'success';
                                        if ($rate >= 60) return 'warning';
                                        return 'danger';
                                    }),
                                
                                TextEntry::make('video.reports_count')
                                    ->label('Reports')
                                    ->getStateUsing(fn ($record) => $record->video->reports()->count())
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-o-exclamation-triangle'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
