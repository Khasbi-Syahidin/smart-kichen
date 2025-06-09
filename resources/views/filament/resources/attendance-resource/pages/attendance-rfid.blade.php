<x-filament::page>
    <div class="flex flex-col gap-4 md:gap-6">


        {{-- <h1 class="text-2xl font-bold mt-2">Data Consumer Yang Masuk</h1> --}}
        @if ($consumer)
            <x-filament::card>
                <div class="w-full bg-green-50 rounded-lg p-6">
                    <div class="flex flex-col md:flex-row md:items-center gap-6">
                        {{-- Avatar: responsive size --}}
                        <div class="flex-shrink-0">
                            <div
                                class="w-32 h-32 md:w-48 md:h-48 rounded-full overflow-hidden border-4 border-green-300 shadow-md">
                                <img src="{{ $consumer->avatar ? asset('storage/' . $consumer->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($consumer->name) }}"
                                    alt="{{ $consumer->name }}" class="w-full h-full object-cover" />
                            </div>
                        </div>

                        {{-- Informasi Konsumen --}}
                        <div class="flex flex-col justify-center text-center md:text-left">
                            <h2 class="text-2xl font-bold text-green-800">{{ $consumer->name }}</h2>
                            <p class="text-green-700 mt-1">Berhasil absen makan</p>
                            <p class="text-sm text-green-600">Pada pukul <strong>{{ now()->format('H:i:s') }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </x-filament::card>
        @else
            <x-filament::card>
                <div class="text-center space-y-4 px-4 py-5">
                    {{-- <div class="text-white text-lg text-center font-semibold">
                        Belum ada data
                    </div> --}}
                    <div class="flex justify-center">
                        <img src="/images/rfid-tap.gif" alt="Tap RFID Card" class="w-42 h-42 mx-auto" />
                    </div>
                    <p class="text-sm text-gray-300">
                        Silakan tempelkan kartu RFID ke alat pembaca. Pastikan alat pembaca RFID telah terhubung.
                    </p>
                </div>
            </x-filament::card>
        @endforelse
    </div>
    <div class="flex flex-col gap-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <x-filament::card class="filament-card bg-success-100 text-success-600">
                <div class="text-xl font-bold">Total Sudah Makan</div>
                <div class="text-2xl">{{ $consumed }}</div>
            </x-filament::card>


            <x-filament::card>
                <div class="text-xl font-bold">Total Belum Makan</div>
                <div class="text-2xl">{{ $total - $consumed }}</div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-xl font-bold">Sesi</div>
                <div>
                    {{ match ($session->session) {
                        'breakfast' => 'Sarapan Pagi',
                        'lunch' => 'Makan Siang',
                        'dinner' => 'Makan Malam',
                        default => '-',
                    } }}
                </div>
            </x-filament::card>
        </div>
    </div>

    <input wire:model.lazy="rfid" type="text" autofocus id="rfid-input" class="opacity-0 absolute" autocomplete="off" />

    <script>
        setInterval(() => {
            document.getElementById('rfid-input')?.focus();
        }, 300);
    </script>



</x-filament::page>
