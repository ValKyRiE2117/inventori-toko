<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Supplier')
                    ->required()
                    ->maxLength(255),
                TextInput::make('alamat')
                    ->label('Alamat Supplier')
                    ->required()
                    ->maxLength(500),
                TextInput::make('email')
                    ->label('Email Supplier')
                    ->email()
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
