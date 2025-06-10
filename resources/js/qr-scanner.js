import { Html5QrcodeScanner } from "html5-qrcode";

window.initQrScanner = function (elementId, onSuccessCallback) {
    let isProcessing = false;

    const html5QrcodeScanner = new Html5QrcodeScanner(
        elementId,
        {
            fps: 10,
            qrbox: { width: 250, height: 250 }, // tetap responsif
            aspectRatio: 1.0, // bisa ditambahkan agar menyesuaikan
        },
        false
    );


    html5QrcodeScanner.render((decodedText, decodedResult) => {
        if (isProcessing) return;

        isProcessing = true;

        onSuccessCallback(decodedText, decodedResult);

        // Biarkan kamera tetap menyala, hanya tunda scan berikutnya
        setTimeout(() => {
            isProcessing = false;
        }, 2000); // tunggu 2 detik sebelum bisa scan QR lagi
    });
};
