<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\AttendanceSession;
use Filament\Forms;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;


class AttendanceResource extends Resource
{
    protected static ?string $model = AttendanceSession::class;

    protected static ?string $navigationLabel = 'Absensi Makan';
    protected static ?string $navigationGroup = 'Absensi';
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    public static ?string $label = 'Absensi Makan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('session')
                    ->label('Sesi Makan')
                    ->placeholder('Pilih Sesi Makan')
                    ->required()
                    ->validationMessages([
                        'required' => 'Sesi makan harus dipilih.',
                    ])
                    ->options([
                        'breakfast' => 'Sarapan Pagi',
                        'lunch' => 'Makan Siang',
                        'dinner' => 'Makan Malam'
                    ])
                    ->native(false)
                    ->columnSpan(1)
                    ->reactive(),

                Forms\Components\Select::make('menu_id')
                    ->label('Menu Makanan')
                    ->required()
                    ->validationMessages([
                        'required' => 'Menu makanan harus dipilih.',
                    ])
                    ->placeholder('Pilih Menu Makanan')
                    ->relationship('menu', 'name')
                    ->searchable() // Tambahkan ini agar bisa mencari berdasarkan nama
                    ->preload()
                    ->native(false)
                    ->columnSpan(1),

                Forms\Components\Select::make('supervisor_id')
                    ->label('Pengawas')
                    ->default(auth()->id())
                    ->disabled()
                    ->required()
                    ->validationMessages([
                        'required' => 'Pengawas harus diisi.',
                    ])
                    ->relationship('supervisor', 'name')
                    ->preload()
                    ->native(false)
                    ->columnSpan(1),

                Forms\Components\DateTimePicker::make('date')
                    ->native(false)
                    ->label('Waktu')
                    ->required()
                    ->validationMessages([
                        'required' => 'Waktu absensi harus diisi.',
                    ])
                    ->default(now())
                    ->columnSpan(1)
                    ->reactive(),

                Forms\Components\Textarea::make('note')
                    ->label('Catatan')
                    ->autosize()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        self::validateSessionDate($get('session'), $get('date'), $get('id') ?? null);
                    })
                    ->columnSpanFull(), // Span seluruh baris
            ])
            ->columns([
                'default' => 1, // Untuk layar kecil
                'md' => 2,      // Untuk medium ke atas, jadi 2 kolom
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Waktu')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('d F Y')),

                Tables\Columns\TextColumn::make('session')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'breakfast' => 'success',
                        'lunch' => 'danger',
                        'dinner' => 'info',
                    })
                    ->label('Sesi Makan')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'breakfast' => 'Sarapan Pagi',
                        'lunch' => 'Makan Siang',
                        'dinner' => 'Makan Malam',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('menu.name')
                    ->label('Menu Makanan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('consumers_count')
                    ->label('Jumlah Consumer')
                    ->counts('consumers'),
                Tables\Columns\TextColumn::make('supervisor.name')
                    ->label('Pengawas')
                    ->searchable(),

            ])
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('date')
                            ->label('Tanggal Makan')
                            ->default(now()->toDateString()), // ✅ default untuk hari ini
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['date']) {
                            return $query;
                        }

                        return $query->whereDate('date', Carbon::parse($data['date'])->toDateString());
                    }),
            ])


            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('read-consumer')
                        ->label('Lihat Consumer Absensi')
                        ->icon('heroicon-o-tag')
                        ->url(fn($record) => route(
                            'filament.admin.resources.attendances.detail_attendance',
                            ['record' => $record->id]
                        ))
                        ->openUrlInNewTab(false),
                    Tables\Actions\Action::make('tap-rfid')
                        ->label('Lakukan Absensi RFID')
                        ->icon('heroicon-o-credit-card')
                        ->url(fn($record) => route(
                            'filament.admin.resources.attendances.atendance_rfid',
                            ['record' => $record->id]
                        ))
                        ->openUrlInNewTab(false),
                    Tables\Actions\Action::make('qr')
                        ->label('Lakukan Absensi QR')
                        ->icon('heroicon-o-qr-code')
                        ->url(fn($record) => route(
                            'filament.admin.resources.attendances.atendance_camera',
                            ['record' => $record->id]
                        ))
                        ->openUrlInNewTab(false),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function validateSessionDate($session, $date, $recordId = null)
    {
        if (!$session || !$date) return;

        $exists = \App\Models\AttendanceSession::whereDate('date', $date)
            ->where('session', $session)
            ->when($recordId, fn($q) => $q->where('id', '!=', $recordId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'session' => ["Absensi untuk sesi '$session' pada tanggal $date sudah tersedia."],
            ]);
        }
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $dateOnly = Carbon::parse($data['date'])->toDateString();

        $exists = AttendanceSession::whereDate('date', $dateOnly)
            ->where('session', $data['session'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'session' => ["Absensi untuk sesi '{$data['session']}' pada tanggal {$dateOnly} sudah tersedia."],
            ]);
        }

        $data['date'] = $dateOnly;
        return $data;
    }


    public static function mutateFormDataBeforeUpdate(array $data, $record): array
    {
        $dateOnly = Carbon::parse($data['date'])->toDateString();

        $exists = AttendanceSession::whereDate('date', $dateOnly)
            ->where('session', $data['session'])
            ->where('id', '!=', $record->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'session' => ["Absensi untuk sesi '{$data['session']}' pada tanggal {$dateOnly} sudah tersedia."],
            ]);
        }

        $data['date'] = $dateOnly;
        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAttendances::route('/'),
            'detail_attendance' => Pages\AttendanceAction::route('/detail_attendance'),
            'atendance_rfid' => Pages\AttendanceRfid::route('/attendance_rfid'),
            'atendance_camera' => Pages\AttendanceCamera::route('/attendance_camera'),
        ];
    }
}
