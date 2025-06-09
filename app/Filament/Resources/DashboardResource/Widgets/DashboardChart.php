<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Consumer;
use App\Models\AttendanceSession;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardChart extends ChartWidget
{
    protected static ?string $heading = 'Persentase Makan';
    // protected static ?string $heading = 'Presense Makan';

    // protected static ?string $heading = 'Ilustrasi Kehadiran Makan';
    protected static string $color = 'success';

    protected function getData(): array
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $sessions = AttendanceSession::whereBetween('date', [$startDate, $endDate])->get();

        $totalExpected = 0;
        $totalActual = 0;

        foreach ($sessions as $session) {
            $eligibleConsumers = Consumer::where('is_active', true)
                ->whereDate('created_at', '<=', $session->date)
                ->get();

            $totalExpected += $eligibleConsumers->count();

            $actual = DB::table('attendance_consumers')
                ->where('attendance_session_id', $session->id)
                ->whereIn('consumer_id', $eligibleConsumers->pluck('id'))
                ->count();

            $totalActual += $actual;
        }

        $totalNotPresent = max(0, $totalExpected - $totalActual);

        return [
            'datasets' => [
                [
                    'label' => 'Presensi Makan',
                    'data' => [$totalActual, $totalNotPresent],
                    'backgroundColor' => ['#36A2EB', '#FF6384'],
                    'borderColor' => '#FFFFFF',
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
        return '300px';
    }
}
