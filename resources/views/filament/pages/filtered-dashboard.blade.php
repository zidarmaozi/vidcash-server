<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Form --}}
        <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg shadow p-6">
            {{ $this->form }}
        </div>

        {{-- Loading Indicator - Simple and Non-intrusive --}}
        <div wire:loading class="flex justify-center py-2">
            <div class="flex items-center space-x-2 text-gray-500 dark:text-gray-400">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary-600"></div>
                <span class="text-sm">Memproses filter...</span>
            </div>
        </div>

        {{-- Widgets - Always Visible --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach ($this->getWidgets() as $widget)
                @livewire($widget, ['dateRange' => $this->getDateRange()], key($widget . '-' . ($this->data['date_range'] ?? 'all') . '-' . ($this->data['start_date'] ?? '') . '-' . ($this->data['end_date'] ?? '')))
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
