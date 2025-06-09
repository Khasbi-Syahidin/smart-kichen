<input type="text" wire:model="rfidInput" autofocus class="absolute opacity-0 -z-10 pointer-events-none" />

<script>
    Livewire.on('refreshRfidFocus', () => {
        const rfidInput = document.querySelector('[wire\\:model="rfidInput"]');
        rfidInput?.focus();
    });
</script>
