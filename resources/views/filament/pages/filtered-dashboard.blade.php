<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Form --}}
        <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg shadow p-6">
            {{ $this->form }}
        </div>

        {{-- Widgets --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach ($this->getWidgets() as $widget)
                @livewire($widget, ['dateRange' => $this->getDateRange()], key($widget . '-' . ($this->data['date_range'] ?? 'all') . '-' . ($this->data['start_date'] ?? '') . '-' . ($this->data['end_date'] ?? '')))
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
