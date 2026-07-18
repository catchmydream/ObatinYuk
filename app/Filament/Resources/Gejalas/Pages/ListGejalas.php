<?php

namespace App\Filament\Resources\Gejalas\Pages;

use App\Filament\Resources\Gejalas\GejalaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGejalas extends ListRecords
{
    protected static string $resource = GejalaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
