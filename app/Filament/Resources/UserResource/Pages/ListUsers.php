<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol "New user" yang sudah ada
            Actions\CreateAction::make(), 
            
            // Tombol Aksi Baru untuk Kirim Notifikasi
            Actions\Action::make('sendNotification')
                ->label('Kirim Notifikasi')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('primary')
                ->form([
                    Toggle::make('send_to_all')
                        ->label('Kirim ke Semua Pengguna')
                        ->live(),
                    Select::make('user_id')
                        ->label('Pilih Pengguna Spesifik')
                        ->options(User::where('role', 'user')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->hidden(fn ($get) => $get('send_to_all')),
                    RichEditor::make('message')
                        ->label('Isi Pesan')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $message = $data['message'];

                    if ($data['send_to_all']) {
                        $users = User::where('role', 'user')->get();
                        foreach ($users as $user) {
                            $user->customNotifications()->create(['type' => 'info', 'message' => $message]);
                        }
                        Notification::make()->title("Notifikasi berhasil dikirim ke {$users->count()} pengguna.")->success()->send();
                    } else {
                        $user = User::find($data['user_id']);
                        if ($user) {
                            $user->customNotifications()->create(['type' => 'info', 'message' => $message]);
                            Notification::make()->title("Notifikasi berhasil dikirim ke {$user->name}.")->success()->send();
                        } else {
                            Notification::make()->title("Gagal! Pengguna tidak ditemukan.")->danger()->send();
                        }
                    }
                }),
        ];
    }
}