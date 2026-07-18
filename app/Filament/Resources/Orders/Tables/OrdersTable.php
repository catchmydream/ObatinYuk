<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('obat.name')
                    ->label('Obat')
                    ->sortable(),
                TextColumn::make('shipping_address')
                    ->label('Alamat Kirim')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->label('No. Telepon')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Qty')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                ImageColumn::make('payment_proof')
                    ->label('Bukti Bayar')
                    ->disk('public')
                    ->height(60)
                    ->width(80),
                TextColumn::make('payment_method')
                    ->label('Metode Bayar')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'Menunggu Pembayaran' => 'warning',
                        'Menunggu Verifikasi' => 'info',
                        'Diproses'           => 'primary',
                        'Dikirim'            => 'success',
                        'Selesai'            => 'success',
                        'Dibatalkan'         => 'danger',
                        default              => 'gray',
                    }),
                TextColumn::make('payment_deadline')
                    ->label('Batas Bayar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Order')
                    ->dateTime('d M Y, H:i')
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
            ])
            ->defaultSort('created_at', 'desc');
    }
}
