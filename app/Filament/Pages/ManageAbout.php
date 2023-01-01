<?php

namespace App\Filament\Pages;

use App\Settings\AboutSetting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Artisan;

class ManageAbout extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'tabler-building-mosque';

    protected static string $settings = AboutSetting::class;

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $title = 'About Masjid';

    protected static ?int $navigationSort = 2;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('subtitle')
                            ->label('Sub Judul')
                            ->maxLength(255),
                        MarkdownEditor::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->required(),
                        FileUpload::make('image')
                            ->label('Gambar')
                            ->image()
                            ->disk('public')
                            ->directory('about')
                            ->visibility('public')
                            ->columnSpanFull(),
                        Group::make([
                            TextInput::make('button_text')
                                ->label('Teks Tombol')
                                ->maxLength(255),
                            TextInput::make('button_link')
                                ->label('Tautan Tombol')
                                ->url()
                                ->maxLength(255),
                            Forms\Components\Toggle::make('show_button')
                                ->label('Tampilkan Tombol')
                                ->default(true),
                        ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(2);
    }

    protected function afterSave(): void
    {
        Artisan::call('cache:clear');
    }
}
