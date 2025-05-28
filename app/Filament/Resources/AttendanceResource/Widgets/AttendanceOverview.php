<?php

namespace App\Filament\Resources\AttendanceResource\Widgets;

use App\Models\AttendanceConsumer;
use App\Models\Consumer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceOverview extends BaseWidget
{
    public $attendanceSessionId;

    // Mount method untuk inisialisasi property dari query param
    public function mount(): void
    {
        // Ambil query param 'record'
        $this->attendanceSessionId = request()->query('record');
    }


    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        if (!$this->attendanceSessionId) {
            return [];
        }

        $attendanceNow = AttendanceConsumer::where('attendance_session_id', $this->attendanceSessionId)->count();
        $consumerActive = Consumer::where('is_active', 1)->count();

        $isEat = $attendanceNow;
        $isNotEat = $consumerActive - $isEat;

        return [
            Stat::make('Sudah Makan', $isEat)
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Belum Makan', $isNotEat)
                ->color('danger'),
            Stat::make('Total Consumer Aktif', $consumerActive)
                ->color('info'),
        ];
    }
}
