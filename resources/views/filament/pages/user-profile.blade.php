<x-filament-panels::page>
    {{-- User Profile Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                    <span class="text-white text-2xl font-bold">{{ substr($this->record->name, 0, 1) }}</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $this->record->name }}</h1>
                    <p class="text-gray-600">{{ $this->record->email }}</p>
                    <div class="flex items-center space-x-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $this->record->role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($this->record->role) }}
                        </span>
                        @if($this->record->validation_level)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Level {{ $this->record->validation_level }}
                            </span>
                        @endif
                        <span class="text-sm text-gray-500">
                            Member since {{ $this->record->created_at->format('M Y') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-3">
                @foreach($this->getHeaderActions() as $action)
                    {{ $action }}
                @endforeach
            </div>
        </div>
    </div>

    {{-- Stats Overview Widget --}}
    @if($this->hasHeaderWidgets())
        <div class="mb-6">
            @foreach ($this->getHeaderWidgets() as $widget)
                @livewire($widget, ['record' => $this->record])
            @endforeach
        </div>
    @endif

    {{-- Footer Widgets --}}
    @if($this->hasFooterWidgets())
        <div class="space-y-6">
            @foreach ($this->getFooterWidgets() as $widget)
                @livewire($widget, ['record' => $this->record])
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
