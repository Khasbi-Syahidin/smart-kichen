<x-filament-panels::page>

    {{ $this->table }}

    {{-- <div>
        {{ $this->table() }}
    </div> --}}

    {{-- Tempelkan ini di bagian paling atas dari blade Filament Page --}}
    <div class="hidden">
        <input wire:model.debounce.500ms="rfidInput" type="text" autofocus autocomplete="off" class="w-0 h-0 opacity-0">
    </div>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rfidInput = document.querySelector('input[wire\\:model]');
            rfidInput.focus();

            document.addEventListener('click', () => {
                rfidInput.focus();
            });
        });
    </script> --}}



</x-filament-panels::page>
