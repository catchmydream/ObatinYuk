<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label('Pelanggan'),
                Select::make('obat_id')
                    ->relationship('obat', 'name')
                    ->required()
                    ->label('Obat'),
                Textarea::make('shipping_address')
                    ->label('Alamat Pengiriman')
                    ->disabled()
                    ->columnSpanFull(),
                TextInput::make('phone_number')
                    ->label('Nomor Telepon')
                    ->disabled()
                    ->tel(),
                TextInput::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->disabled(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->label('Jumlah'),
                TextInput::make('total_price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->label('Total Harga'),
                Select::make('status')
                    ->options([
                        'Menunggu Pembayaran' => '🕐 Menunggu Pembayaran',
                        'Menunggu Verifikasi' => '🔍 Menunggu Verifikasi',
                        'Diproses'           => '⚙️ Diproses',
                        'Dikirim'            => '🚚 Dikirim',
                        'Selesai'            => '🎉 Selesai',
                        'Dibatalkan'         => '❌ Dibatalkan',
                    ])
                    ->default('Menunggu Pembayaran')
                    ->required()
                    ->label('Status Pesanan'),
                DateTimePicker::make('payment_deadline')
                    ->label('Batas Waktu Pembayaran'),
                FileUpload::make('payment_proof')
                    ->image()
                    ->directory('payment-proofs')
                    ->disk('public')
                    ->label('Bukti Transfer'),
            ]);
    }
}
