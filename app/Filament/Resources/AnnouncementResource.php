<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Announcement;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Support\Facades\FilamentColor;
use Filament\Forms\Components\MarkdownEditor;
use Guava\FilamentIconPicker\Forms\IconPicker;
use App\Filament\Resources\AnnouncementResource\Pages;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    public static function getModelLabel(): string
    {
        return __('Pengumuman & Informasi');
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
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->minLength(5)
                            ->required()
                            ->translateLabel(),
                        TextInput::make('title')
                            ->minLength(5)
                            ->required()
                            ->translateLabel(),
                        MarkdownEditor::make('body')
                            ->minLength(20)
                            ->required()
                            ->fileAttachmentsDirectory('attachments')
                            ->columnSpanFull()
                            ->translateLabel(),
                        Select::make('color')
                            ->options([
                                ...collect(FilamentColor::getColors())->map(fn($value, $key) => ucfirst($key))->toArray(),
                                'custom' => 'Custom',
                            ])
                            ->translateLabel()
                            ->live(),
                        ColorPicker::make('custom_color')
                            ->hidden(fn(Get $get) => $get('color') != 'custom')
                            ->requiredIf('color', 'custom')
                            ->rgb()
                            ->translateLabel(),
                        Select::make('users')
                            ->options(['all' => 'Semua'] + User::all()->pluck('name', 'id')->toArray())
                            ->multiple()
                            ->translateLabel()
                            ->required(),
                        IconPicker::make('icon')
                            ->columnSpanFull()
                            ->translateLabel(),

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pengumuman')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Judul Pengumuman')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('body')
                    ->label('Isi Pengumuman')
                    ->translateLabel()
                    ->wrap()
                    ->limit(50)
                    ->html()
                    ->tooltip(fn($record) => $record->body),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->translateLabel()
                    ->dateTime()
                    ->since()
                    ->dateTimeToolTip('d F Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Diubah Pada')
                    ->translateLabel()
                    ->since()
                    ->dateTimeToolTip('d F Y H:i')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'title',
            'body',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->title;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Dibuat Pada' => date('d F Y H:i', strtotime($record->created_at)),
            'Diubah Pada' => date('d F Y H:i', strtotime($record->updated_at)),
        ];
    }
}
