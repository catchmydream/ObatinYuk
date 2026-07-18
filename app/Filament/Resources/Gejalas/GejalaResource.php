<?php

namespace App\Filament\Resources\Gejalas;

use App\Filament\Resources\Gejalas\Pages\CreateGejala;
use App\Filament\Resources\Gejalas\Pages\EditGejala;
use App\Filament\Resources\Gejalas\Pages\ListGejalas;
use App\Filament\Resources\Gejalas\Schemas\GejalaForm;
use App\Filament\Resources\Gejalas\Tables\GejalasTable;
use App\Models\Gejala;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GejalaResource extends Resource
{
    protected static ?string $model = Gejala::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return GejalaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GejalasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGejalas::route('/'),
            'create' => CreateGejala::route('/create'),
            'edit' => EditGejala::route('/{record}/edit'),
        ];
    }
}
