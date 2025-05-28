<?php

namespace App\Filament\Resources\ConsumerResource\Pages;

use App\Filament\Resources\ConsumerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageConsumers extends ManageRecords
{
    protected static string $resource = ConsumerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
