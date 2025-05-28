<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\AttendanceConsumer;
use App\Models\AttendanceSession;
use App\Models\Consumer;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class AttendanceAction extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = AttendanceResource::class;

    protected static string $view = 'filament.resources.attendance-resource.pages.attendance-action';

    protected static string $name = 'Absensi';

    public $attendanceSessionId;

    public $attendanceSession;

    public function mount(): void
    {
        $this->attendanceSessionId = request()->query('record');

        $this->attendanceSession = AttendanceSession::find($this->attendanceSessionId);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('absen-manual')
                ->label('Absensi Manual')
                ->outlined()
                ->form([
                    Select::make('consumer_ids')
                        ->label('Nama Consumer')
                        ->required()
                        ->placeholder('Pilih Consumer')
                        ->multiple()
                        ->options(
                            Consumer::pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                ])
                ->modalHeading('Tambah Absensi')
                ->modalWidth('md')
                ->action(function (array $data) {
                    $sessionId = $this->attendanceSessionId;

                    // Hindari duplikasi
                    $existingConsumerIds = AttendanceConsumer::where('attendance_session_id', $sessionId)
                        ->pluck('consumer_id')
                        ->toArray();

                    $newConsumerIds = array_diff($data['consumer_ids'], $existingConsumerIds);

                    foreach ($newConsumerIds as $consumerId) {
                        AttendanceConsumer::create([
                            'attendance_session_id' => $sessionId,
                            'consumer_id' => $consumerId,
                        ]);
                    }

                    if (count($newConsumerIds) > 0) {
                        Notification::make()
                            ->title('Berhasil menambahkan absensi.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Consumer sudah tercatat.')
                            ->warning()
                            ->send();
                    }
                })
                ->color('primary'),

            Action::make('back')
                ->label('Akhiri Absensi')
                ->url(route('filament.admin.resources.attendances.index')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\AttendanceResource\Widgets\AttendanceOverview::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 3;
    }

    protected function getDefaultTableFilters(): array
    {
        return [
            'attendance_status' => 'sudah',
        ];
    }


    public function table(Table $table): Table
    {
        $sessionId = $this->attendanceSessionId;

        return $table
            ->query(
                fn() => Consumer::query()
                    ->whereDate('created_at', '<=', $this->attendanceSession->date)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Consumer')->searchable(),

                Tables\Columns\BadgeColumn::make('attendance_status')
                    ->label('Status Makan')
                    ->getStateUsing(function (Consumer $record) use ($sessionId) {
                        return $record->attendanceSessions->contains('id', $sessionId)
                            ? 'Sudah Makan'
                            : 'Belum Makan';
                    })
                    ->colors([
                        'primary' => 'Belum Makan',
                        'success' => 'Sudah Makan',
                    ]),
            ])
            ->filters([
                SelectFilter::make('attendance_status')
                    ->label('Status Makan')
                    ->options([
                        'sudah' => 'Sudah Makan',
                        'belum' => 'Belum Makan',
                    ])
                    ->default($this->getDefaultTableFilters()['attendance_status'])
                    ->query(function (Builder $query, array $data) use ($sessionId) {
                        $value = $data['value'] ?? null;

                        if ($value === 'sudah') {
                            $query->whereHas('attendanceSessions', function ($q) use ($sessionId) {
                                $q->where('attendance_sessions.id', $sessionId);
                            });
                        } elseif ($value === 'belum') {
                            $query->whereDoesntHave('attendanceSessions', function ($q) use ($sessionId) {
                                $q->where('attendance_sessions.id', $sessionId);
                            });
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }




    public string $rfidInput = '';

    public function updatedRfidInput($value)
    {
        $sessionId = $this->attendanceSessionId;

        $consumer = Consumer::where('rfid', $value)->first();

        if (!$consumer) {
            Notification::make()
                ->title("RFID tidak ditemukan.")
                ->danger()
                ->send();

            return;
        }

        $alreadyExists = AttendanceConsumer::where('attendance_session_id', $sessionId)
            ->where('consumer_id', $consumer->id)
            ->exists();

        if ($alreadyExists) {
            Notification::make()
                ->title("{$consumer->name} sudah absen.")
                ->warning()
                ->send();
        } else {
            AttendanceConsumer::create([
                'attendance_session_id' => $sessionId,
                'consumer_id' => $consumer->id,
            ]);

            Notification::make()
                ->title("Absensi berhasil untuk {$consumer->name}.")
                ->success()
                ->send();
        }

        // Reset untuk menerima input berikutnya
        $this->rfidInput = '';
    }
}
