<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WhatsappSenderServices
{
    public function sendMessage($phone, $message): string
    {
        $response = Http::post(env('WHATSAPP_URL'), [
            'api_key' => env('WHATSAPP_API_KEY'),
            'sender' => env('WHATSAPP_NUMBER_SEEDER'),
            'number' => $phone,
            'message' => $message,
        ]);
    }
}
