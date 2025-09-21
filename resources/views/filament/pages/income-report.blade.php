<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Section - Top of Page -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    📊 Income Report
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        Last updated: {{ now()->format('M d, Y H:i') }}
                    </span>
                    <button wire:click="$refresh" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <p>📈 Laporan pendapatan platform VidCash</p>
                <p>💡 Data diupdate secara real-time berdasarkan views yang menghasilkan income</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>