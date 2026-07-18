<?php

namespace App\Filament\Resources\Obats\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;

class ObatForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                ->required(),

            \Filament\Forms\Components\Select::make('category')
                ->label('Kategori Produk')
                ->options([
                    'obat' => '💊 Obat',
                    'vitamin' => '✨ Vitamin',
                    'suplemen' => '🔋 Suplemen',
                    'herbal' => '🌿 Obat Herbal',
                ])
                ->default('obat')
                ->required()
                ->native(false),

            Textarea::make('description'),

            Textarea::make('dosage')
                ->label('Dosis & Penggunaan')
                ->placeholder('Contoh: Dewasa 3x1 tablet, Anak 2x1/2 tablet'),

            \Filament\Forms\Components\Select::make('aturan_pakai')
                ->label('Aturan Pakai (Kaitan dengan Makan)')
                ->options([
                    'sebelum_makan' => '🍽️ Sebelum Makan',
                    'sesudah_makan' => '🍕 Sesudah Makan',
                    'saat_makan' => '🍲 Bersamaan Makan',
                    'bebas' => '✅ Bebas (Tidak terikat makan)',
                ])
                ->placeholder('Pilih aturan pakai...')
                ->native(false),

            FileUpload::make('image')
                ->image()
                ->directory('obat-images')
                ->disk('public'),

            \Filament\Forms\Components\Select::make('classification')
                ->label('Golongan Obat (Khusus Kategori Obat)')
                ->options([
                    'bebas' => 'Obat Bebas (Hijau)',
                    'terbatas' => 'Obat Bebas Terbatas (Biru)',
                    'keras' => 'Obat Keras (Merah / K)',
                ])
                ->required()
                ->native(false),
            
            \Filament\Forms\Components\Select::make('gejalas')
                ->relationship('gejalas', 'name')
                ->multiple()
                ->preload()
                ->label('Gejala Penyakit (Bisa pilih lebih dari satu)')
                ->createOptionForm([
                    TextInput::make('name')
                        ->required()
                        ->label('Nama Gejala')
                        ->placeholder('Contoh: Sakit Kepala, Demam, Batuk')
                ]),
                
            TextInput::make('price')
                ->required()
                ->numeric()
                ->prefix('Rp')
                ->default(15000),
                
            TextInput::make('stock')
                ->required()
                ->numeric()
                ->default(50),
            ]);
    }
}
