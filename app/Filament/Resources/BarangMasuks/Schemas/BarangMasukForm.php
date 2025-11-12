<?php

namespace App\Filament\Resources\BarangMasuks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BarangMasukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_barang')
                    ->label('Barang')
                    ->relationship('barang', 'nama')
                    ->required(),
                Select::make('id_user')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->required(),
                DatePicker::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->required(),
            ]);
    }
}
