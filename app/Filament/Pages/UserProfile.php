<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Filament\Widgets\UserStatsOverview;
use App\Filament\Widgets\UserVideosTable;
use App\Filament\Widgets\UserEarningsChart;
use App\Filament\Widgets\UserViewsChart;
use App\Models\User;

class UserProfile extends Page
{
    protected static string $view = 'filament.pages.user-profile';

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $title = 'User Profile';

    public $record;

    public function mount(): void
    {
        // Get user ID from query parameter
        $id = request()->query('user_id');
        if (!$id) {
            abort(404, 'User ID not provided');
        }
        $this->record = User::findOrFail($id);
    }

    public function getTitle(): string
    {
        if (!$this->record) {
            return 'User Profile';
        }
        return "User Profile: {$this->record->name}";
    }

    protected function getHeaderActions(): array
    {
        if (!$this->record) {
            return [];
        }
        
        return [
            Action::make('edit')
                ->label('Edit User')
                ->icon('heroicon-o-pencil')
                ->url(fn () => route('filament.admin.resources.users.edit', $this->record))
                ->color('primary'),
            
            Action::make('view_videos')
                ->label('View All Videos')
                ->icon('heroicon-o-video-camera')
                ->url(fn () => route('filament.admin.resources.videos.index', ['tableFilters[user][value]' => $this->record->id]))
                ->color('info')
                ->openUrlInNewTab(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            UserVideosTable::class,
            UserEarningsChart::class,
            UserViewsChart::class,
        ];
    }

    public function hasHeaderWidgets(): bool
    {
        return !empty($this->getHeaderWidgets());
    }

    public function hasFooterWidgets(): bool
    {
        return !empty($this->getFooterWidgets());
    }

    // Pass data to widgets
    protected function getHeaderWidgetsData(): array
    {
        return [
            'record' => $this->record,
        ];
    }

    protected function getFooterWidgetsData(): array
    {
        return [
            'record' => $this->record,
        ];
    }
}
