<?php

namespace App\Filament\Resources\Gejalas\Schemas;

use Filament\Schemas\Schema;

class GejalaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Gejala (misal: Pusing, Demam)'),
            ]);
    }
}
