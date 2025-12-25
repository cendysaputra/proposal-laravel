<form wire:submit.prevent="submit">
    {{ $this->form }}

    <x-filament::button type="submit" class="mt-2">
        Add New
    </x-filament::button>
</form>
