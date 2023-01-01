<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationTemplateResource\Pages;
use App\Models\NotificationTemplate;
use App\Settings\GeneralSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationTemplateResource extends Resource
{
    protected static ?string $model = NotificationTemplate::class;

    protected static ?string $navigationIcon = 'tabler-bell-ringing-2';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('Template Notifikasi');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

        return $query
            ->orderBy('created_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Nama Template'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->validationAttribute(__('Nama Template'))
                    ->maxLength(50),
                Forms\Components\TextInput::make('subject')
                    ->label(__('Subjek'))
                    ->required()
                    ->helperText(__('Gunakan {masjid} untuk nama masjid'))
                    ->validationAttribute(__('Subjek'))
                    ->maxLength(100),
                Forms\Components\MarkdownEditor::make('body')
                    ->label(__('Isi Pesan'))
                    ->required()
                    ->validationAttribute(__('Isi Pesan'))
                    ->columnSpanFull()
                    ->helperText(__('Gunakan {name}, {category_name}, {status}, {user}, {transaction_date}, {nominal}, {subject}, {description}, {masjid}, {masjid_address}, {masjid_phone}, {masjid_email} untuk menggantikan nama masjid, alamat masjid, dan nomor telepon masjid.')),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('Aktif'))
                    ->helperText(__('Tandai jika template ini aktif'))
                    ->default(true)
                    ->inline()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Nama Template'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label(__('Subjek'))
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('Aktif'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['subject'] = str_replace(
                            ['{masjid}'],
                            [
                                app(GeneralSetting::class)->site_name,
                            ],
                            $data['subject']
                        );

                        return $data;
                    }),
                // Tables\Actions\DeleteAction::make(),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNotificationTemplates::route('/'),
        ];
    }
}
