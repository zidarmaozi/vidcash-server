<?php

namespace App\Filament\Widgets;

use App\Models\Withdrawal;
use App\Models\VideoReport;
use App\Models\Video;
use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 2;

    protected function getViewData(): array
    {
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        $pendingReports = VideoReport::where('status', 'pending')->count();
        $videosWithoutThumbnail = Video::where('is_active', true)
            ->whereNull('thumbnail_path')
            ->count();
        $unsafeActiveVideos = Video::where('is_active', true)
            ->where('is_safe_content', false)
            ->count();

        $actionItems = [];
        
        if ($pendingWithdrawals > 0) {
            $actionItems[] = [
                'priority' => 'danger',
                'icon' => '💸',
                'title' => 'Pending Withdrawals',
                'count' => $pendingWithdrawals,
                'description' => 'Permintaan penarikan menunggu approval',
                'url' => '/admin/withdrawals',
            ];
        }
        
        if ($pendingReports > 0) {
            $actionItems[] = [
                'priority' => 'danger',
                'icon' => '⚠️',
                'title' => 'Pending Reports',
                'count' => $pendingReports,
                'description' => 'Video reports perlu direview',
                'url' => '/admin/video-reports',
            ];
        }
        
        if ($videosWithoutThumbnail > 0) {
            $actionItems[] = [
                'priority' => 'warning',
                'icon' => '📷',
                'title' => 'Videos Without Thumbnail',
                'count' => $videosWithoutThumbnail,
                'description' => 'Video aktif tanpa thumbnail',
                'url' => '/admin/videos',
            ];
        }
        
        if ($unsafeActiveVideos > 0) {
            $actionItems[] = [
                'priority' => 'danger',
                'icon' => '🚫',
                'title' => 'Unsafe Active Videos',
                'count' => $unsafeActiveVideos,
                'description' => 'Video unsafe yang masih aktif',
                'url' => '/admin/videos',
            ];
        }

        return [
            'actionItems' => $actionItems,
            'hasActions' => !empty($actionItems),
        ];
    }
}

