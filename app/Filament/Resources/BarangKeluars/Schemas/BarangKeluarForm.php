<?php

namespace App\Filament\Resources\BarangKeluars\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BarangKeluarForm
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
                    ->label('Petugas')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->required(),
                DatePicker::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->required(),
                TextInput::make('penerima')
                    ->label('Penerima')
                    ->required(),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(2)
                    ->nullable(),
            ]);
    }
}
