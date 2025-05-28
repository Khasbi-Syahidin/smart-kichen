<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Tingkat kehadiran consumer', '30%')->description(' dalam 30 hari terakhir'),
            Stat::make('Consumer Aktif', '200'),
            Stat::make('Supervisor Aktif', '12'),
            Stat::make('Menu Makanan', '24'),
        ];
    }
}
