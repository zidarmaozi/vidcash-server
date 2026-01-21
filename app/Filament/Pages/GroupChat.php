<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class GroupChat extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.pages.group-chat';

    protected static ?string $navigationLabel = 'Group Chat';

    protected static ?string $title = 'Global Community Chat';

    public function getViewData(): array
    {
        return [
            'user' => Auth::user(),
            'firebaseConfig' => config('firebase'),
        ];
    }
}
