<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use Filament\Widgets\ChartWidget;

class DashboardChart extends ChartWidget
{
    protected static ?string $heading = 'Presense Makan';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Presense Makan',
                    'data' => [200, 100],
                    'backgroundColor' => ['#36A2EB', '#FF6384'],
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => ['Makan', 'Tidak Makan'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    public static function getChartHeight(): string
    {
        return '300px'; // atau '20rem', atau sesuai kebutuhan
    }
}
