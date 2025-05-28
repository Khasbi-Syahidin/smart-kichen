<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Consumer;
use App\Models\AttendanceSession;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;


class ConsumerOverview extends BaseWidget
{
    protected static ?int $sort = 3;

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        $startDate = Carbon::now()->subDays(30)->toDateString();
        $attendanceSessionIds = AttendanceSession::where('date', '>=', $startDate)->pluck('id');

        return $table
            ->query(function () use ($attendanceSessionIds) {
                return Consumer::withCount([
                    'attendanceSessions as attendance_count' => fn($query) =>
                    $query->whereIn('attendance_sessions.id', $attendanceSessionIds),
                ])
                    ->orderBy('attendance_count', 'asc'); // Default: paling sedikit makan
                // ->limit(5);
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Consumer'),
                Tables\Columns\TextColumn::make('attendance_count')->label('Jumlah Makan (30 Hari)'),
            ])
            ->defaultPaginationPageOption(5);
    }
}
