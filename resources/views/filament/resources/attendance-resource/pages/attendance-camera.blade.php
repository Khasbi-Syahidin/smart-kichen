@vite([ 'resources/css/app.css', 'resources/js/app.js'])

<x-filament::page>
    <div class="space-y-4">
        <h2 class="text-xl font-bold">Scan QR Code Konsumen</h2>

        <x-filament::button color="gray" onclick="setupScanner()">
            Belum muncul kamera? Klik di sini
        </x-filament::button>

        <div class="w-full md:w-1/2 mx-auto relative">
            <div id="reader" class="w-full"></div>
        </div>


        <div id="scan-result" class="mt-4 p-4 bg-green-100 text-green-800 rounded hidden">
            Scan berhasil: <span id="scan-name"></span>
        </div>
    </div>

    <script>
        function setupScanner() {
            if (typeof initQrScanner === 'function') {
                initQrScanner("reader", function(decodedText) {
                    document.getElementById('scan-name').textContent = decodedText;
                    document.getElementById('scan-result').classList.remove('hidden');

                    @this.set('scannedName', decodedText);
                    @this.call('markAttendance');
                });
            } else {
                console.warn("initQrScanner tidak ditemukan");
            }
        }

        setupScanner()

        document.addEventListener("DOMContentLoaded", setupScanner);
        window.addEventListener("livewire:load", setupScanner);
    </script>
</x-filament::page>
