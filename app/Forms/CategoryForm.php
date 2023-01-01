<?php

namespace App\Forms;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class CategoryForm
{
    public static function make($type): array
    {
        return [
            Hidden::make('type')
                ->default($type),
            TextInput::make('name')
                ->required()
                ->label(__('Nama Kategori'))
                ->placeholder(__('Masukkan Nama Kategori'))
                ->validationAttribute(__('Nama Kategori')),
            Textarea::make('description')
                ->label(__('Deskripsi Kategori'))
                ->placeholder(__('Masukkan Deskripsi Kategori'))
                ->validationAttribute(__('Deskripsi Kategori'))
                ->rows(3)
                ->columnSpanFull(),
        ];
    }
}
