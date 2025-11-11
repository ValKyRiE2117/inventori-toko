<?php

namespace App\Filament\Resources\Barangs\Schemas;

use Filament\Schemas\Schema;
use Ramsey\Uuid\Guid\Fields;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Fieldset;

class BarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)
                    ->schema([
                        Section::make('Detail Supplier')
                            ->columns(1)
                            ->schema([
                                Select::make('supplier_id')
                                    ->label('Supplier')
                                    ->relationship('supplier', 'nama')
                                    ->required(),
                            ]),
                        Section::make('Detail Barang')
                            ->columns(1)
                            ->schema([
                                TextInput::make('kode')
                                    ->label('Kode Barang')
                                    ->required()
                                    ->maxLength(30),
                                TextInput::make('nama')
                                    ->label('Nama Barang')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('kategori_id')
                                    ->label('Kategori')
                                    ->relationship('kategori', 'nama')
                                    ->required(),
                                FileUpload::make('gambar')
                                    ->label('Gambar Barang')
                                    ->image()
                                    ->required(),
                            ]),
                        Section::make('Detail Tambahan')
                            ->columns(1)
                            ->schema([
                                TextInput::make('stok')
                                    ->label('Stok Barang')
                                    ->numeric()
                                    ->required(),
                                Textarea::make('deskripsi')
                                    ->label('Deskripsi Barang')
                                    ->maxLength(1000),
                            ]),
                    ])
            ]);
    }
}
