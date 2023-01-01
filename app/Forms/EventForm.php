<?php

namespace App\Forms;

use App\Enums\CategoryType;
use App\Forms\Components\Separator;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\HtmlString;

class EventForm
{
    public static function make(): array
    {
        return [
            Group::make([
                TextInput::make('name')
                    ->label(__('Nama Kegiatan'))
                    ->required()
                    ->placeholder('Masukkan nama kegiatan')
                    ->helperText('Nama kegiatan yang akan ditampilkan.')
                    ->maxLength(255),
                Select::make('category_id')
                    ->label(__('Tipe Kegiatan'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label(__('Nama Kategori'))
                            ->placeholder(__('Masukkan Nama Kategori ex: Donasi, Zakat'))
                            ->validationAttribute(__('Nama Kategori'))
                            ->autofocus()
                            ->required()
                            ->maxLength(50),
                        Textarea::make('description')
                            ->label(__('Deskripsi Kategori'))
                            ->placeholder(__('Masukkan Deskripsi Kategori'))
                            ->validationAttribute(__('Deskripsi Kategori'))
                            ->rows(3)
                            ->columnSpanFull(),
                        Hidden::make('type')
                            ->default(CategoryType::EVENT),
                    ])

                    ->helperText('Tipe kegiatan, misal: Umum, Pengajian, dll.'),
            ])
                ->columns(2)
                ->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('events')
                ->label(__('Gambar Kegiatan'))
                ->collection('events')
                ->helperText('Gambar / Video yang akan ditampilkan pada kegiatan ini.')
                ->hiddenOn('view')
                ->columnSpanFull(),
            Placeholder::make('image')
                ->label(__('Gambar Kegiatan'))
                ->content(fn ($record) => new HtmlString('<img src="' . $record->getFirstMediaUrl('events') . '" class="w-full object-cover rounded-md" />'))
                ->hiddenOn(['edit', 'create'])
                ->columnSpanFull(),
            MarkdownEditor::make('description')
                ->label(__('Deskripsi Kegiatan'))
                ->columnSpanFull(),
            Toggle::make('is_active')
                ->label(__('Aktif'))
                ->default(true)
                ->inline(false)
                ->hiddenOn('view')
                ->helperText('Tandai jika kegiatan ini aktif.'),
            Group::make(
                [
                    DateTimePicker::make('start_date')
                        ->label(__('Tanggal Mulai'))
                        ->required()
                        ->placeholder('Pilih tanggal mulai kegiatan')
                        ->helperText('Tanggal dan waktu mulai kegiatan.'),
                    DateTimePicker::make('end_date')
                        ->label(__('Tanggal Selesai'))
                        ->placeholder('Pilih tanggal selesai kegiatan')
                        ->helperText('Tanggal dan waktu selesai kegiatan.')
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state && $state < now()) {
                                $set('end_date', null);
                            }
                        }),
                ]
            )
                ->columns(2)
                ->columnSpanFull(),
            Separator::make('separator')
                ->columnSpanFull()
                ->hiddenLabel(),
            Select::make('user_id')
                ->label(__('Pengguna Terkait'))
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->helperText('Pilih pengguna terkait kegiatan ini. ex: Admin, Panitia, Ustad, dll.')
                ->createOptionForm([
                    TextInput::make('name')
                        ->label(__('Nama Pengguna'))
                        ->required()
                        ->maxLength(255),

                    TextInput::make('username')
                        ->label(__('Username'))
                        ->required()
                        ->maxLength(255)
                        ->live()
                        ->rules(function ($record) {
                            $userId = $record?->id;

                            return $userId
                                ? ['unique:users,username,' . $userId]
                                : ['unique:users,username'];
                        }),

                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->rules(function ($record) {
                            $userId = $record?->id;

                            return $userId
                                ? ['unique:users,email,' . $userId]
                                : ['unique:users,email'];
                        }),
                    TextInput::make('phone')
                        ->label(__('Phone'))
                        ->tel()
                        ->required()
                        ->rules(['starts_with:62'])
                        ->maxLength(255)
                        ->rules(function ($record) {
                            $userId = $record?->id;

                            return $userId
                                ? ['unique:users,phone,' . $userId]
                                : ['unique:users,phone'];
                        }),
                    Select::make('roles')
                        ->hiddenLabel()
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->optionsLimit(5)
                        ->columnSpanFull(),
                ]),
            KeyValue::make('meta')
                ->columnSpanFull()
                ->label(__('Meta Data'))
                ->keyLabel(__('Kunci'))
                ->valueLabel(__('Nilai'))
                ->helperText('Data tambahan yang dapat digunakan untuk menyimpan informasi tambahan tentang kegiatan ini.'),
        ];
    }
}
