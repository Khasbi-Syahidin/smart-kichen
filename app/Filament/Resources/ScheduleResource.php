<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Filament\Resources\ScheduleResource\RelationManagers;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationLabel = 'Jadwal Pengawas';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    public static ?string $label = 'Jadwal Pengawas';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('day')
                    ->label('Hari')
                    ->required()
                    ->options([
                        'sunday' => 'Minggu',
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                    ])
                    ->unique(
                        table: 'schedules',
                        column: 'day',
                        ignorable: fn($record) => $record,
                    )
                    ->validationMessages([
                        'unique' => 'Hari tersebut sudah digunakan.',
                        'required' => 'Harap pilih hari terlebih dahulu.',
                    ])
                    ->native(false),
                    Forms\Components\CheckboxList::make('sessions')
                    ->label('Sesi Makan')
                    ->options([
                        'breakfast' => 'Sarapan Pagi',
                        'lunch' => 'Makan Siang',
                        'dinner' => 'Makan Malam',
                    ])
                    ->columns(3) // tampil horizontal
                    ->required()
                    ->validationMessages([
                        'required' => 'Minimal satu sesi makan harus dipilih.',
                    ]),

                Forms\Components\Select::make('supervisor_id')
                    ->label('Pengawas')
                    ->required()
                    ->placeholder('Pilih Pengawas')
                    ->multiple()
                    ->relationship('users', 'name')
                    ->preload()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('day')
                    ->searchable()
                    ->label('Hari')
                    ->getStateUsing(function (Schedule $record) {
                        return match ($record->day) {
                            'sunday' => 'Minggu',
                            'monday' => 'Senin',
                            'tuesday' => 'Selasa',
                            'wednesday' => 'Rabu',
                            'thursday' => 'Kamis',
                            'friday' => 'Jumat',
                            'saturday' => 'Sabtu',
                        };
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSchedules::route('/'),
        ];
    }
}
