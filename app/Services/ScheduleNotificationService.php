<?php

namespace App\Services;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ScheduleNotificationService
{
    protected $whatsappSenderServices;

    public function __construct( WhatsappSenderServices $whatsappSenderServices)
    {
        $this->whatsappSenderServices = $whatsappSenderServices;
    }
    public function sendSupervisorNotifications()
    {
        $today = now()->locale('id')->dayName; // e.g. "monday"
        $now = now();

        $sessions = config('meal_sessions');

        foreach ($sessions as $session => $time) {
            $scheduleTime = Carbon::createFromTimeString($time);

            if ($now->format('H:i') !== $scheduleTime->format('H:i')) {
                continue;
            }

            $schedule = Schedule::where('day', strtolower($today))
                ->whereJsonContains('sessions', $session)
                ->with('users')
                ->first();

            if (!$schedule) continue;

            $template = Storage::get('message_templates/supervisor_reminder.txt');

            foreach ($schedule->users as $user) {
                $message = str_replace(
                    ['{name}', '{sesi}', '{waktu}'],
                    [$user->name, ucfirst($session), $this->greetingTime($session)],
                    $template
                );

                $this->sendToWhatsApp($user->phone, $message);
            }
        }
    }

    protected function sendToWhatsApp($phone, $message)
    {
        Log::info("Sending WhatsApp to $phone: $message");
        $this->whatsappSenderServices->sendMessage($phone, $message);
    }

    protected function greetingTime($session)
    {
        return match($session) {
            'breakfast' => 'pagi',
            'lunch'     => 'siang',
            'dinner'    => 'sore',
            default     => 'hari',
        };
    }
}
