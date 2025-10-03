<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            🚨 Action Needed
        </x-slot>

        @if ($hasActions)
            <div class="space-y-3">
                @foreach ($actionItems as $item)
                    <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <div class="flex items-center space-x-4 flex-1">
                            <div class="text-3xl">
                                {{ $item['icon'] }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $item['title'] }}
                                    </h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($item['priority'] === 'danger') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @endif">
                                        {{ $item['count'] }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $item['description'] }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ $item['url'] }}" 
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white 
                                      @if($item['priority'] === 'danger') bg-red-600 hover:bg-red-700
                                      @else bg-yellow-600 hover:bg-yellow-700
                                      @endif
                                      focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                                View
                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-6xl mb-4">✅</div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    All Clear!
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Tidak ada action yang diperlukan saat ini
                </p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>

