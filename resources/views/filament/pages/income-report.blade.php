<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Widgets -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($this->getHeaderWidgets() as $widget)
                @livewire($widget)
            @endforeach
        </div>
        
        <!-- Footer Widgets -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($this->getFooterWidgets() as $widget)
                @livewire($widget)
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
