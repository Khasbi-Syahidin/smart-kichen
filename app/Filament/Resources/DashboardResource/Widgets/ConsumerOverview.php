<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Consumer;
use App\Models\AttendanceSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConsumerOverview extends BaseWidget
{
    protected static ?int $sort = 3;

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(function () {
                $startDate = Carbon::now()->subDays(30)->toDateString();

                // Ambil sesi absensi dalam 30 hari terakhir
                $sessions = AttendanceSession::whereDate('date', '>=', $startDate)
                    ->get(['id', 'date']);

                // Ambil semua consumer yang aktif
                $consumers = Consumer::where('is_active', true)->get();

                // Hitung kehadiran masing-masing consumer berdasarkan valid session
                $consumerStats = $consumers->map(function ($consumer) use ($sessions) {
                    $relevantSessions = $sessions->filter(fn($session) => $consumer->created_at->lte($session->date));
                    $relevantSessionIds = $relevantSessions->pluck('id');

                    $attendanceCount = DB::table('attendance_consumers')
                        ->where('consumer_id', $consumer->id)
                        ->whereIn('attendance_session_id', $relevantSessionIds)
                        ->count();

                    return (object) [
                        'id' => $consumer->id,
                        'name' => $consumer->name,
                        'created_at' => $consumer->created_at,
                        'attendance_count' => $attendanceCount,
                    ];
                });

                // Urutkan dari yang paling sedikit hadir
                $sorted = $consumerStats->sortBy('attendance_count')->take(5);

                // Ambil ulang model Consumer untuk digunakan di table (agar compatible)
                return Consumer::query()
                ->whereIn('id', $sorted->pluck('id'))
                ->orderByRaw("FIELD(id, {$sorted->pluck('id')->implode(',')})");
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Consumer'),
                Tables\Columns\TextColumn::make('attendance_count')
                    ->label('Jumlah Makan (30 Hari)')
                    ->getStateUsing(function ($record) {
                        $startDate = Carbon::now()->subDays(30)->toDateString();
                        $sessions = AttendanceSession::whereDate('date', '>=', $startDate)
                            ->get(['id', 'date']);

                        $relevantSessions = $sessions->filter(
                            fn($session) => $record->created_at->lte($session->date)
                        );

                        $relevantSessionIds = $relevantSessions->pluck('id');

                        return DB::table('attendance_consumers')
                            ->where('consumer_id', $record->id)
                            ->whereIn('attendance_session_id', $relevantSessionIds)
                            ->count();
                    }),
            ])
            ->paginated(false);
    }
}
