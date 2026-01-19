<?php

namespace App\Filament\Resources\FolderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VideosRelationManager extends RelationManager
{
    protected static string $relationship = 'videos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_path')
                    ->label('Thumbnail')
                    ->disk('public')
                    ->height(40)
                    ->width(60)
                    ->defaultImageUrl('https://via.placeholder.com/60x40/e5e7eb/6b7280?text=No+Img')
                    ->extraImgAttributes(['class' => 'rounded object-cover']),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('is_active')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive'),

                Tables\Columns\IconColumn::make('is_safe_content')
                    ->boolean()
                    ->label('Safe'),

                Tables\Columns\TextColumn::make('views_count')
                    ->counts('views')
                    ->label('Views')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->query(fn($query) => $query->where('is_active', true)),
                Tables\Filters\Filter::make('inactive')
                    ->query(fn($query) => $query->where('is_active', false)),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(), // Usually videos are uploaded separately, not created in relation
                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => \App\Filament\Resources\VideoResource::getUrl('show', ['record' => $record]))
                    ->openUrlInNewTab(),

                Tables\Actions\DissociateAction::make()
                    ->label('Remove from Folder'),

                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
