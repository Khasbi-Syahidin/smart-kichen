<?php

namespace App\Console\Commands;

use App\Services\ScheduleNotificationService;
use Illuminate\Console\Command;

class RunSchedule extends Command
{
    protected $signature = 'run:schedule';
    protected $description = 'Kirim pengingat supervisor berdasarkan sesi hari ini';

    public function handle(ScheduleNotificationService $service)
    {
        $this->info('Memulai pengiriman pesan supervisor...');
        $service->sendSupervisorNotifications();
        $this->info('Pengiriman selesai.');
    }
}
