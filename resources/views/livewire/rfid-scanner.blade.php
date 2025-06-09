<div>
    <div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 border rounded">
                <div class="text-xl font-bold">Total Sudah Makan</div>
                <div class="text-3xl text-green-600">{{ $consumed }}</div>
            </div>

            <div class="p-4 border rounded">
                <div class="text-xl font-bold">Total Belum Makan</div>
                <div class="text-3xl text-red-600">{{ $total - $consumed }}</div>
            </div>

            <div class="p-4 border rounded">
                <div class="text-xl font-bold">Sesi</div>
                <div>{{ $session->name ?? '-' }}</div>
            </div>
        </div>

        <input wire:model.lazy="rfid" type="text" autofocus id="rfid-input" class="opacity-0 absolute" />

        <script>
            setInterval(() => {
                document.getElementById('rfid-input')?.focus();
            }, 300);

            window.addEventListener('rfidError', event => {
                alert(event.detail.message);
            });

            window.addEventListener('rfidSuccess', () => {
                console.log('✅ RFID berhasil diproses.');
            });
        </script>

        @if ($consumer)
            <div class="p-6 border rounded text-center">
                <div class="text-lg font-bold">Selamat Makan</div>
                <div class="text-2xl mt-2">{{ $consumer->name }}</div>
                @if ($consumer->avatar)
                    <img src="{{ asset('storage/' . $consumer->avatar) }}"
                        class="mx-auto rounded-full w-32 h-32 mt-4" />
                @endif
            </div>
        @endif
    </div>
</div>
