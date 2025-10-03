<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            🚨 Action Needed
        </x-slot>

        @if ($hasActions)
            <div class="space-y-3">
                @foreach ($actionItems as $item)
                    <div @class([
                        'flex items-center justify-between p-4 rounded-xl border transition-all duration-200',
                        'fi-bg-subtle border-gray-950/5 dark:border-white/10',
                        'hover:bg-gray-950/[.025] dark:hover:bg-white/5'
                    ])>
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <div class="text-3xl flex-shrink-0">
                                {{ $item['icon'] }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="text-sm font-semibold fi-color-custom">
                                        {{ $item['title'] }}
                                    </h4>
                                    @if($item['priority'] === 'danger')
                                        <x-filament::badge color="danger" size="sm">
                                            {{ $item['count'] }}
                                        </x-filament::badge>
                                    @else
                                        <x-filament::badge color="warning" size="sm">
                                            {{ $item['count'] }}
                                        </x-filament::badge>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $item['description'] }}
                                </p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            @if($item['priority'] === 'danger')
                                <x-filament::button
                                    :href="$item['url']"
                                    tag="a"
                                    color="danger"
                                    size="sm"
                                    icon="heroicon-o-arrow-right"
                                    icon-position="after"
                                >
                                    View
                                </x-filament::button>
                            @else
                                <x-filament::button
                                    :href="$item['url']"
                                    tag="a"
                                    color="warning"
                                    size="sm"
                                    icon="heroicon-o-arrow-right"
                                    icon-position="after"
                                >
                                    View
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">✅</div>
                <h3 class="text-lg font-semibold fi-header-heading">
                    All Clear!
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Tidak ada action yang diperlukan saat ini
                </p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>

