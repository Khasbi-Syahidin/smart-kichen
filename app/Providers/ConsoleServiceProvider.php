<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;


class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    public function schedule(Schedule $schedule): void
    {
        foreach (config('meal_sessions') as $session => $time) {
            [$hour, $minute] = explode(':', $time);
            $schedule->command('run:schedule')->dailyAt("$hour:$minute");
        }
    }

    public function commands($commands): void
    {
        $this->load(__DIR__ . '/../Console/Commands');
    }
}
