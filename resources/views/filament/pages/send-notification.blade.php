<x-filament-panels::page>
    <form wire:submit.prevent="send">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                kirim pesan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>