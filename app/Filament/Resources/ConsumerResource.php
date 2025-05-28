<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsumerResource\Pages;
use App\Filament\Resources\ConsumerResource\RelationManagers;
use App\Models\Consumer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Hidden;

class ConsumerResource extends Resource
{
    protected static ?string $model = Consumer::class;

    protected static ?string $navigationLabel = 'Consumers';
    protected static ?string $navigationGroup = 'Management';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('avatar')
                    ->directory('consumers')
                    ->columnSpan(2),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->minLength(2)
                    ->maxLength(255),
                Forms\Components\TextInput::make('rfid'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->width(40)
                    ->height(40)
                    ->getStateUsing(fn($record) => $record->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_active')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Tidak Aktif' => 'danger',
                    })
                    ->label('Status')
                    ->getStateUsing(function (Consumer $record) {
                        return $record->is_active ? 'Aktif' : 'Tidak Aktif';
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('add-rfid')
                        ->label('Tambah RFID')
                        ->icon('heroicon-o-identification')
                        ->form(fn($record) => [
                            Forms\Components\TextInput::make('rfid')
                                ->label('RFID')
                                ->required()
                                ->rules(['min:10', 'max:10'])
                                ->default($record->rfid)
                                ->extraAttributes(['autofocus' => true])

                        ])
                        ->modalHeading('Tambah RFID')
                        ->modalWidth('md')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup')
                        ->modalDescription(new HtmlString(view('components.rfid-instruction')->render()))
                        ->action(function (array $data, $record) {
                            $record->update([
                                'rfid' => $data['rfid'],
                            ]);
                        })
                        ->modalButton('Simpan')
                        ->color('primary'),

                    Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ManageConsumers::route('/'),
        ];
    }
}
