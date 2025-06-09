<x-filament-panels::page>

    {{ $this->table }}

    {{-- <div class="mt-6 space-y-4">
        <input
            type="text"
            wire:model.debounce.500ms="rfidInput"
            placeholder="Scan Kartu..."
            class="text-black bg-yellow-200 p-2 rounded w-full"
            id="rfidInput"
            autocomplete="off"
        />

        <p>Input Masuk: {{ $rfidInput }}</p>
    </div>

    <script>
        document.addEventListener('livewire:load', () => {
            const input = document.getElementById('rfidInput');
            input?.focus();

            // Fokus kembali jika user klik sembarang tempat
            document.addEventListener('click', () => {
                input?.focus();
            });

            // Saat trigger dari backend
            Livewire.on('refreshRfidFocus', () => {
                input?.focus();
            });
        });
    </script> --}}
    <script>
        document.addEventListener('livewire:load', () => {
            Livewire.on('reset-rfid', () => {
                const input = document.getElementById('rfidInputField');
                if (input) {
                    input.value = '';
                    input.dispatchEvent(new Event('input', {
                        bubbles: true
                    })); // sinkronisasi ke Livewire
                    input.focus();
                }
            });
        });
    </script>


</x-filament-panels::page>
