<?php

namespace App\Filament\Pages;

use App\Models\Video;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Livewire\WithPagination;

class VideoGallery extends Page implements HasForms
{
    use InteractsWithForms, WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static string $view = 'filament.pages.video-gallery';
    protected static ?string $title = 'Video Gallery';
    protected static ?string $navigationLabel = 'Video Gallery';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationGroup = 'Videos';

    public array $data = [
        'search' => '',
        'status' => 'all',
    ];

    public int $perPage = 12;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('search')
                    ->label('Search by title or video code')
                    ->placeholder('Search videos...')
                    ->live()
                    ->debounce(500),
                
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'all' => 'All Videos',
                        'active' => 'Active Only',
                        'inactive' => 'Inactive Only',
                    ])
                    ->default('all')
                    ->live(),
            ])
            ->statePath('data')
            ->columns(2);
    }

    public function getVideos()
    {
        $query = Video::whereNotNull('thumbnail_path')
            ->with('user');

        // Apply search filter
        if (!empty($this->data['search'])) {
            $search = $this->data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('video_code', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (!empty($this->data['status']) && $this->data['status'] !== 'all') {
            if ($this->data['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($this->data['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }
    
    public function updatedData(): void
    {
        // Reset to first page when filters change
        $this->resetPage();
    }

    public function getStats(): array
    {
        $total = Video::whereNotNull('thumbnail_path')->count();
        $active = Video::whereNotNull('thumbnail_path')->where('is_active', true)->count();
        $inactive = Video::whereNotNull('thumbnail_path')->where('is_active', false)->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
        ];
    }
}
