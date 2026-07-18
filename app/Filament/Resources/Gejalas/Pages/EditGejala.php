<?php

namespace App\Filament\Resources\Gejalas\Pages;

use App\Filament\Resources\Gejalas\GejalaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGejala extends EditRecord
{
    protected static string $resource = GejalaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
