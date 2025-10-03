<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Filament\Resources\VideoResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Actions;
use Illuminate\Support\Str;

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
                    ? 'Apakah Anda yakin ingin menonaktifkan video ini? Video yang dinonaktifkan tidak akan dapat diakses dan safe content akan di-set ke false.' 
                    : 'Apakah Anda yakin ingin mengaktifkan video ini? Video yang diaktifkan akan dapat diakses kembali.')
                ->action(function () {
                    $isActivating = !$this->record->is_active;
                    
                    if ($isActivating) {
                        // Jika mengaktifkan video
                        $this->record->update(['is_active' => true]);
                    } else {
                        // Jika menonaktifkan video, set is_safe_content ke false
                        $this->record->update([
                            'is_active' => false,
                            'is_safe_content' => false
                        ]);
                    }
                    
                    $status = $this->record->is_active ? 'diaktifkan' : 'dinonaktifkan';
                    $message = $isActivating 
                        ? "Video berhasil {$status}" 
                        : "Video berhasil {$status} dan safe content di-set ke false";
                    
                    \Filament\Notifications\Notification::make()
                        ->title($message)
                        ->success()
                        ->send();
                }),
            
            Actions\Action::make('toggleSafeContent')
                ->label(fn (): string => $this->record->is_safe_content ? 'Tandai Unsafe' : 'Tandai Safe')
                ->icon(fn (): string => $this->record->is_safe_content ? 'heroicon-o-shield-exclamation' : 'heroicon-o-shield-check')
                ->color(fn (): string => $this->record->is_safe_content ? 'warning' : 'success')
                ->disabled(fn (): bool => !$this->record->is_active)
                ->requiresConfirmation()
                ->modalHeading(fn (): string => $this->record->is_safe_content ? 'Tandai sebagai Unsafe Content' : 'Tandai sebagai Safe Content')
                ->modalDescription(fn (): string => $this->record->is_safe_content 
                    ? 'Apakah Anda yakin ingin menandai video ini sebagai konten tidak aman?' 
                    : 'Apakah Anda yakin ingin menandai video ini sebagai konten aman?')
                ->action(function () {
                    if (!$this->record->is_active) {
                        \Filament\Notifications\Notification::make()
                            ->title('Video harus aktif terlebih dahulu untuk mengubah status safe content')
                            ->warning()
                            ->send();
                        return;
                    }
                    
                    $this->record->update(['is_safe_content' => !$this->record->is_safe_content]);
                    
                    $status = $this->record->is_safe_content ? 'safe content' : 'unsafe content';
                    \Filament\Notifications\Notification::make()
                        ->title("Video berhasil ditandai sebagai {$status}")
                        ->success()
                        ->send();
                }),
            
            Actions\Action::make('viewReports')
                ->label(fn () => 'View All Reports (' . $this->record->reports()->count() . ')')
                ->icon('heroicon-o-exclamation-triangle')
                ->url(fn () => route('filament.admin.resources.video-reports.index', ['tableFilters' => ['video_id' => ['value' => $this->record->id]]]))
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->reports()->count() > 0)
                ->color('warning'),
            
            Actions\Action::make('markReportsReviewed')
                ->label(fn () => 'Mark All Pending Reports as Reviewed (' . $this->record->reports()->where('status', 'pending')->count() . ')')
                ->icon('heroicon-o-check-circle')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Mark All Pending Reports as Reviewed')
                ->modalDescription('Are you sure you want to mark all pending reports for this video as reviewed?')
                ->visible(fn () => $this->record->reports()->where('status', 'pending')->count() > 0)
                ->action(function () {
                    $pendingCount = $this->record->reports()->where('status', 'pending')->count();
                    $this->record->reports()->where('status', 'pending')->update(['status' => 'reviewed']);
                    
                    \Filament\Notifications\Notification::make()
                        ->title("{$pendingCount} pending reports marked as reviewed")
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

                Section::make('Thumbnail Preview')
                    ->schema([
                        TextEntry::make('thumbnail_preview')
                            ->label('')
                            ->getStateUsing(function ($record) {
                                if (!$record->thumbnail_path) {
                                    return '<div class="text-center p-8 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No thumbnail available</p>
                                            </div>';
                                }
                                
                                $thumbnailUrl = $record->thumbnail_url;
                                return '<div class="text-center">
                                            <img src="' . $thumbnailUrl . '" 
                                                 alt="Video Thumbnail" 
                                                 class="max-w-full h-auto rounded-lg shadow-lg mx-auto"
                                                 style="max-height: 400px; object-fit: contain;" />
                                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Thumbnail Path: ' . $record->thumbnail_path . '</p>
                                        </div>';
                            })
                            ->html()
                            ->columnSpanFull(),
                        
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('thumbnail_status')
                                    ->label('Thumbnail Status')
                                    ->getStateUsing(fn ($record) => $record->thumbnail_path ? 'Available' : 'Not Available')
                                    ->badge()
                                    ->color(fn ($record) => $record->thumbnail_path ? 'success' : 'gray')
                                    ->icon(fn ($record) => $record->thumbnail_path ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),
                                
                                TextEntry::make('thumbnail_url')
                                    ->label('Thumbnail URL')
                                    ->getStateUsing(fn ($record) => $record->thumbnail_url ?? 'N/A')
                                    ->copyable()
                                    ->copyMessage('Thumbnail URL copied!')
                                    ->visible(fn ($record) => $record->thumbnail_path !== null)
                                    ->color('info'),
                            ]),
                    ])
                    ->collapsible()
                    ->visible(fn ($record) => true),

                Section::make('Video Status')
                    ->schema([
                        Grid::make(2)
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
                                
                                TextEntry::make('safe_content_info')
                                    ->label('Content Safety')
                                    ->getStateUsing(function ($record) {
                                        $status = $record->is_safe_content ? 'Safe Content' : 'Unsafe Content';
                                        $icon = $record->is_safe_content ? '🛡️' : '⚠️';
                                        $description = $record->is_safe_content 
                                            ? 'Video ini ditandai sebagai konten yang aman untuk ditonton.'
                                            : 'Video ini ditandai sebagai konten yang tidak aman.';
                                        
                                        return "{$icon} **{$status}**\n\n{$description}";
                                    })
                                    ->markdown()
                                    ->prose()
                                    ->color(fn ($record) => $record->is_safe_content ? 'success' : 'warning'),
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
                                
                                TextEntry::make('is_safe_content')
                                    ->label('Content Safety')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'success' : 'warning')
                                    ->formatStateUsing(fn (bool $state): string => $state ? '🛡️ Safe' : '⚠️ Unsafe')
                                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-shield-check' : 'heroicon-o-shield-exclamation'),
                                
                                TextEntry::make('video_code')
                                    ->label('Video Code')
                                    ->copyable()
                                    ->copyMessage('Video code copied!'),
                                
                                TextEntry::make('video_code')
                                    ->label('Video Code')
                                    ->copyable()
                                    ->copyMessage('Video code copied!')
                                    ->color('warning'),
                                
                                TextEntry::make('cdn_urls')
                                    ->label('CDN URLs')
                                    ->getStateUsing(function ($record) {
                                        $mp4Url = "https://cdn.videy.co/{$record->video_code}.mp4";
                                        $movUrl = "https://cdn.videy.co/{$record->video_code}.mov";
                                        
                                        return "**MP4:** {$mp4Url}\n\n**MOV:** {$movUrl}";
                                    })
                                    ->markdown()
                                    ->prose()
                                    ->copyable()
                                    ->copyMessage('CDN URLs copied!'),
                                
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

                Section::make('Video Reports')
                    ->schema([
                        TextEntry::make('reports_summary')
                            ->label('Reports Summary')
                            ->getStateUsing(function ($record) {
                                $totalReports = $record->reports()->count();
                                $pendingReports = $record->reports()->where('status', 'pending')->count();
                                $reviewedReports = $record->reports()->where('status', 'reviewed')->count();
                                $resolvedReports = $record->reports()->where('status', 'resolved')->count();
                                
                                if ($totalReports === 0) {
                                    return 'No reports yet';
                                }
                                
                                return "Total: {$totalReports} | Pending: {$pendingReports} | Reviewed: {$reviewedReports} | Resolved: {$resolvedReports}";
                            })
                            ->markdown()
                            ->prose()
                            ->color(fn ($record) => $record->reports()->where('status', 'pending')->count() > 0 ? 'warning' : 'success'),
                        
                        TextEntry::make('recent_reports')
                            ->label('Recent Reports (Last 10)')
                            ->getStateUsing(function ($record) {
                                $recentReports = $record->reports()
                                    ->latest()
                                    ->limit(10)
                                    ->get();
                                
                                if ($recentReports->isEmpty()) {
                                    return 'No reports yet';
                                }
                                
                                $reportList = $recentReports->map(function ($report) {
                                    $statusIcon = match($report->status) {
                                        'pending' => '🟡',
                                        'reviewed' => '🔵',
                                        'resolved' => '🟢',
                                        default => '⚪'
                                    };
                                    
                                    $time = $report->created_at->format('M d, H:i');
                                    $ip = $report->reporter_ip;
                                    $description = $report->description ? Str::limit($report->description, 50) : 'No description';
                                    
                                    return "- {$statusIcon} **{$time}** - IP: `{$ip}` - {$description}";
                                })->join("\n");
                                
                                return $reportList;
                            })
                            ->markdown()
                            ->prose(),
                    ])
                    ->collapsible()
                    ->visible(fn ($record) => $record->reports()->count() > 0),
            ]);
    }
}
