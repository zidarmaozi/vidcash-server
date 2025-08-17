<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Actions\Action;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action as TableAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShowUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // User Information Section
                Section::make('Informasi User')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold'),
                                
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->copyable()
                                    ->copyMessage('Email copied!'),
                                
                                TextEntry::make('role')
                                    ->label('Role')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'admin' => 'danger',
                                        'user' => 'success',
                                    }),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('validation_level')
                                    ->label('Level Validasi Khusus')
                                    ->placeholder('Default (Menggunakan pengaturan admin)'),
                                
                                TextEntry::make('created_at')
                                    ->label('Bergabung Sejak')
                                    ->dateTime('d M Y H:i')
                                    ->color('gray'),
                            ]),
                    ])
                    ->collapsible(false)
                    ->actions([
                        Action::make('edit')
                            ->label('Edit User')
                            ->icon('heroicon-o-pencil')
                            ->url(fn () => route('filament.admin.resources.users.edit', $this->record))
                            ->color('primary'),
                        
                        Action::make('view_videos')
                            ->label('Lihat Semua Video')
                            ->icon('heroicon-o-video-camera')
                            ->url(fn () => route('filament.admin.resources.videos.index', ['tableFilters[user][value]' => $this->record->id]))
                            ->color('info')
                            ->openUrlInNewTab(),
                    ]),

                // Financial Overview Section
                Section::make('Ringkasan Keuangan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('balance')
                                    ->label('Saldo Saat Ini')
                                    ->money('IDR')
                                    ->color('success')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold'),
                                
                                TextEntry::make('total_withdrawn')
                                    ->label('Total Sudah Ditarik')
                                    ->money('IDR')
                                    ->color('danger')
                                    ->size(TextEntry\TextEntrySize::Large),
                                
                                TextEntry::make('total_income')
                                    ->label('Total Pendapatan')
                                    ->getStateUsing(function ($record) {
                                        $totalIncome = DB::table('views')
                                            ->join('videos', 'views.video_id', '=', 'videos.id')
                                            ->where('videos.user_id', $record->id)
                                            ->where('views.income_generated', true)
                                            ->sum('views.income_amount');
                                        return $totalIncome;
                                    })
                                    ->money('IDR')
                                    ->color('info')
                                    ->size(TextEntry\TextEntrySize::Large),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('pending_withdrawals')
                                    ->label('Pending Withdrawals')
                                    ->getStateUsing(function ($record) {
                                        $pendingAmount = DB::table('withdrawals')
                                            ->where('user_id', $record->id)
                                            ->where('status', 'pending')
                                            ->sum('amount');
                                        return $pendingAmount;
                                    })
                                    ->money('IDR')
                                    ->color('warning'),
                                
                                TextEntry::make('pending_event_payouts')
                                    ->label('Pending Event Payouts')
                                    ->getStateUsing(function ($record) {
                                        $pendingAmount = DB::table('event_payouts')
                                            ->where('user_id', $record->id)
                                            ->where('status', 'pending')
                                            ->sum('prize_amount');
                                        return $pendingAmount;
                                    })
                                    ->money('IDR')
                                    ->color('warning'),
                            ]),
                    ])
                    ->collapsible(false),

                // Video Performance Section
                Section::make('Performa Video')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('total_videos')
                                    ->label('Total Video')
                                    ->getStateUsing(fn ($record) => $record->videos()->count())
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->color('info'),
                                
                                TextEntry::make('total_views')
                                    ->label('Total Views')
                                    ->getStateUsing(function ($record) {
                                        return DB::table('views')
                                            ->join('videos', 'views.video_id', '=', 'videos.id')
                                            ->where('videos.user_id', $record->id)
                                            ->where('views.validation_passed', true)
                                            ->count();
                                    })
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->color('success'),
                                
                                TextEntry::make('success_rate')
                                    ->label('Success Rate')
                                    ->getStateUsing(function ($record) {
                                        $totalViews = DB::table('views')
                                            ->join('videos', 'views.video_id', '=', 'videos.id')
                                            ->where('videos.user_id', $record->id)
                                            ->count();
                                        
                                        if ($totalViews === 0) return '0%';
                                        
                                        $successfulViews = DB::table('views')
                                            ->join('videos', 'views.video_id', '=', 'videos.id')
                                            ->where('videos.user_id', $record->id)
                                            ->where('views.validation_passed', true)
                                            ->count();
                                        
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
                                
                                TextEntry::make('last_activity')
                                    ->label('Aktivitas Terakhir')
                                    ->getStateUsing(function ($record) {
                                        $lastView = DB::table('views')
                                            ->join('videos', 'views.video_id', '=', 'videos.id')
                                            ->where('videos.user_id', $record->id)
                                            ->latest('views.created_at')
                                            ->first();
                                        
                                        if (!$lastView) return 'Tidak ada aktivitas';
                                        
                                        return \Carbon\Carbon::parse($lastView->created_at)->diffForHumans();
                                    })
                                    ->color('gray'),
                            ]),
                    ])
                    ->collapsible(false),

                // Uploaded Videos Section
                Section::make('Daftar Video Upload')
                    ->schema([
                        TextEntry::make('videos_table')
                            ->label('')
                            ->getStateUsing(function ($record) {
                                return $this->getVideosTable($record);
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(false),
            ]);
    }

    private function getVideosTable($user)
    {
        $videos = $user->videos()
            ->withCount(['views' => function ($query) {
                $query->where('validation_passed', true);
            }])
            ->withSum(['views as total_income' => function ($query) {
                $query->where('income_generated', true);
            }], 'income_amount')
            ->latest()
            ->get();

        if ($videos->isEmpty()) {
            return '<div class="text-center py-8 text-gray-500">User belum mengupload video apapun.</div>';
        }

        $html = '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200">';
        $html .= '<thead class="bg-gray-50"><tr>';
        $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Video Code</th>';
        $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link</th>';
        $html .= '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Views</th>';
        $html .= '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Income</th>';
        $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>';
        $html .= '</tr></thead>';
        $html .= '<tbody class="bg-white divide-y divide-gray-200">';

        foreach ($videos as $video) {
            $html .= '<tr>';
            $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' . $video->video_code . '</td>';
            $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm">';
            $html .= '<a href="' . $video->generated_link . '" target="_blank" class="text-indigo-600 hover:underline">' . Str::limit($video->generated_link, 50) . '</a>';
            $html .= '</td>';
            $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">' . number_format($video->views_count) . '</td>';
            $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-green-600">Rp' . number_format($video->total_income, 0, ',', '.') . '</td>';
            $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . $video->created_at->format('d M Y H:i') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }
}
