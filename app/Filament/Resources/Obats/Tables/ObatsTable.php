<?php

namespace App\Filament\Resources\Obats\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ObatsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('public'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'vitamin' => 'warning',
                        'suplemen' => 'info',
                        'herbal' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('classification')
                    ->label('Golongan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bebas' => 'success',
                        'terbatas' => 'info',
                        'keras' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bebas' => 'Obat Bebas',
                        'terbatas' => 'Bebas Terbatas',
                        'keras' => 'Obat Keras (K)',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('aturan_pakai')
                    ->label('Aturan Pakai')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'sebelum_makan' => 'Sebelum Makan',
                        'sesudah_makan' => 'Sesudah Makan',
                        'saat_makan' => 'Bersamaan Makan',
                        'bebas' => 'Bebas',
                        default => '-',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
