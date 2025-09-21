<x-filament-panels::page>
    <x-filament-widgets::widgets
        :widgets="$this->getCachedHeaderWidgets()"
        :columns="$this->getHeaderWidgetsColumns()"
    />
    
    <x-filament-widgets::widgets
        :widgets="$this->getCachedFooterWidgets()"
        :columns="$this->getFooterWidgetsColumns()"
    />
</x-filament-panels::page>
