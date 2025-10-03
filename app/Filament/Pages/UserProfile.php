<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Filament\Resources\UserResource\Widgets\UserStatsWidget;
use App\Models\User;

class UserProfile extends Page
{
    protected static string $view = 'filament.pages.user-profile';

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $title = 'User Profile';
    
    // Hide from navigation - use ViewUser instead
    protected static bool $shouldRegisterNavigation = false;

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
            Action::make('view_user_detail')
                ->label('Lihat Detail Lengkap')
                ->icon('heroicon-o-eye')
                ->url(fn () => route('filament.admin.resources.users.view', $this->record))
                ->color('info'),
                
            Action::make('edit')
                ->label('Edit User')
                ->icon('heroicon-o-pencil')
                ->url(fn () => route('filament.admin.resources.users.edit', $this->record))
                ->color('primary'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Add footer widgets here if needed
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
