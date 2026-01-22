<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Form --}}
        <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg shadow p-6">
            {{ $this->form }}
        </div>

        {{-- Loading Indicator --}}
        <div wire:loading class="flex justify-center py-2">
            <div class="flex items-center space-x-2 text-gray-500 dark:text-gray-400">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary-600"></div>
                <span class="text-sm">Memproses data...</span>
            </div>
        </div>

        {{-- Table --}}
        <div>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>