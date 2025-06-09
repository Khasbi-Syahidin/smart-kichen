<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Consumer;
use App\Models\AttendanceSession;
use App\Models\Menu;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $consumer = Consumer::where('is_active', 1)->count();
        $user = User::count();
        $menu = Menu::count();
        return [
            Stat::make('Tingkat Kehadiran Consumer', function () {
                $startDate = Carbon::now()->subDays(30);
                $endDate = Carbon::now();

                $sessions = AttendanceSession::whereBetween('date', [$startDate, $endDate])->get();

                $totalExpectedAttendances = 0;
                $totalActualAttendances = 0;

                foreach ($sessions as $session) {
                    // Ambil konsumen aktif yang sudah bergabung sebelum sesi ini
                    $eligibleConsumers = Consumer::where('is_active', true)
                        ->whereDate('created_at', '<=', $session->date)
                        ->get();

                    // Tambah ke total yang seharusnya hadir
                    $totalExpectedAttendances += $eligibleConsumers->count();

                    // Hitung kehadiran aktual untuk sesi ini hanya dari eligible consumers
                    $actual = DB::table('attendance_consumers')
                        ->where('attendance_session_id', $session->id)
                        ->whereIn('consumer_id', $eligibleConsumers->pluck('id'))
                        ->count();

                    $totalActualAttendances += $actual;
                }

                $percentage = $totalExpectedAttendances > 0
                    ? round(($totalActualAttendances / $totalExpectedAttendances) * 100)
                    : 0;

                return "{$percentage}%";
            })->description('Dalam 30 hari terakhir'),
            Stat::make('Consumer Aktif', $consumer),
            Stat::make('Supervisor', $user),
            Stat::make('Menu Makanan', $menu),
        ];
    }
}
