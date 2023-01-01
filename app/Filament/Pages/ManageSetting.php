<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSetting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Artisan;

class ManageSetting extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSetting::class;

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 1;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('General Settings')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Site Information')
                            ->id('site-information')
                            ->schema([
                                TextInput::make('site_name')
                                    ->label(__('Nama Situs'))
                                    ->validationAttribute(__('Nama Situs'))
                                    ->columnSpanFull()
                                    ->required(),
                                Group::make([
                                    FileUpload::make('site_logo')
                                        ->label(__('Logo Situs'))
                                        ->image()
                                        ->imageEditor()
                                        ->disk('public')
                                        ->directory('assets/images'),
                                    FileUpload::make('site_logo_dark')
                                        ->label(__('Logo Gelap'))
                                        ->image()
                                        ->imageEditor()
                                        ->disk('public')
                                        ->directory('assets/images'),
                                    FileUpload::make('site_favicon')
                                        ->label(__('Favicon'))
                                        ->image()
                                        ->imageEditor()
                                        ->disk('public')
                                        ->directory('assets/images'),
                                ])
                                    ->columnSpanFull()
                                    ->columns(3),
                                Group::make([
                                    ColorPicker::make('theme_color')
                                        ->label(__('Warna Tema'))
                                        ->default('#fbc50b')
                                        ->required(),
                                    ColorPicker::make('secondary_color')
                                        ->label(__('Warna Sekunder'))
                                        ->default('#007d3a')
                                        ->required(),
                                    TextInput::make('logo_height')
                                        ->label(__('Tinggi Logo'))
                                        ->default('5rem')
                                        ->helperText('Tinggi logo di sidebar'),
                                    TextInput::make('sidebar_width')
                                        ->label(__('Lebar Sidebar'))
                                        ->default('16rem')
                                        ->helperText('Lebar sidebar'),
                                ])
                                    ->columnSpanFull()
                                    ->columns(4),
                                Group::make([
                                    TextInput::make('site_support_email')
                                        ->label(__('Email Dukungan Situs'))
                                        ->email()
                                        ->helperText('Email untuk dukungan situs'),
                                    TextInput::make('site_support_phone')
                                        ->label(__('Telepon Dukungan Situs'))
                                        ->tel()
                                        ->helperText('Telepon untuk dukungan situs'),
                                    TextInput::make('site_support_telegram')
                                        ->label(__('Telegram Dukungan Situs'))
                                        ->helperText('Username Telegram untuk dukungan situs'),
                                ])
                                    ->columnSpanFull()
                                    ->columns(3),
                                Textarea::make('site_address')
                                    ->label(__('Alamat Lengkap'))
                                    ->placeholder('Masukkan alamat lengkap Anda di sini')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Map::make('location')
                                    ->liveLocation(true, true, 10000)
                                    ->showMarker()
                                    ->columnSpanFull()
                                    ->draggable()
                                    ->extraStyles([
                                        'min-height: 50vh',
                                    ])
                                    ->afterStateUpdated(function (Set $set, ?array $state): void {
                                        $set('site_address_latitude', $state['lat']);
                                        $set('site_address_longitude', $state['lng']);
                                    }),
                                TextInput::make('site_address_latitude')
                                    ->label(__('Latitude'))
                                    ->helperText('Latitude untuk map lokasi'),
                                TextInput::make('site_address_longitude')
                                    ->label(__('Longitude'))
                                    ->helperText('Longitude untuk map lokasi'),
                            ])
                            ->columns(2),
                        Tabs\Tab::make('Site Features')
                            ->id('site-features')
                            ->schema([
                                Toggle::make('site_active')
                                    ->label('Site Active')
                                    ->helperText('Enable or disable the site'),
                                Toggle::make('registration_enabled')
                                    ->label('Registration Enabled'),
                                Toggle::make('password_reset_enabled')
                                    ->label('Password Reset Enabled'),
                                Toggle::make('email_verification_enabled')
                                    ->label(__('Email Verification Enabled')),
                                Toggle::make('sso_enabled')
                                    ->label('SSO Enabled'),
                            ])
                            ->columns(2),
                        Tabs\Tab::make('SEO Settings')
                            ->schema([
                                TextInput::make('seo_title')
                                    ->label(__('Judul SEO'))
                                    ->helperText('Judul untuk SEO'),
                                MarkdownEditor::make('seo_description')
                                    ->label(__('Deskripsi SEO'))
                                    ->helperText('Deskripsi situs atau yang berkaitan.'),
                                KeyValue::make('seo_metadata')
                                    ->label(__('Metadata SEO'))
                                    ->helperText('Metadata untuk SEO'),
                            ]),
                        Tabs\Tab::make('Social Media')
                            ->schema([
                                KeyValue::make('site_social_links')
                                    ->label(__('Sosial Media'))
                                    ->helperText('Sosial media untuk situs')
                                    ->addable(false)
                                    ->deletable(false)
                                    ->keyLabel('Sosial Media')
                                    ->valueLabel('URL'),
                            ]),
                        Tabs\Tab::make('Jam Operasional')
                            ->schema([
                                KeyValue::make('operating_hours')
                                    ->label(__('Jam Operasional'))
                                    ->helperText('note: hari dalam bahasa inggris. contoh: monday, tuesday, dst')
                                    ->addable(false)
                                    ->deletable(false)
                                    ->keyLabel('Hari')
                                    ->valueLabel('Jam Kerja example: 08:00 - 17:00'),
                            ]),
                        Tabs\Tab::make('Analytics Settings')
                            ->schema([
                                TextInput::make('google_analytics')
                                    ->label(__('Google Analytics'))
                                    ->placeholder('Salin ID Google Analytics Anda di sini')
                                    ->helperText('ID Google Analytics untuk situs'),
                                Textarea::make('extra_javascript')
                                    ->label(__('Javascript Tambahan'))
                                    ->placeholder('Salin kode Javascript tambahan Anda di sini')
                                    ->rows(5)
                                    ->helperText('Kode Javascript tambahan untuk situs. pastikan tag <script> ditambahkan'),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }

    protected function afterSave(): void
    {
        Artisan::call('cache:clear');
    }

    public function getRedirectUrl(): ?string
    {
        return static::getUrl();
    }
}
