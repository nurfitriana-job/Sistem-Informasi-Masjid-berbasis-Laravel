<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'tabler-slideshow';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('Manage Sliders');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('Title'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\MarkdownEditor::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\TextInput::make('link')
                    ->label(__('Link'))
                    ->url()
                    ->helperText(__('Leave empty if you do not want to add a link. e.g. https://example.com'))
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('Status'))
                    ->default(true)
                    ->inline(false)
                    ->required(),
                Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                    ->label(__('Background Image'))
                    ->collection('background_image')
                    ->columnSpanFull()
                    ->image()
                    ->imageEditor()
                    ->helperText(__('Recommended size: 1920 x 1080 px'))
                    ->required(),
                Forms\Components\SpatieMediaLibraryFileUpload::make('hero_image')
                    ->label(__('Iconic Image'))
                    ->collection('hero_image')
                    ->columnSpanFull()
                    ->image()
                    ->imageEditor()
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('background_image')
                    ->label(__('Image')),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->limit(50)
                    ->html()
                    ->tooltip(fn (string $state): string => $state),
                Tables\Columns\TextColumn::make('description')
                    ->wrap()
                    ->limit(50)
                    ->html()
                    ->tooltip(fn (string $state): string => $state)
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('Status'))
                    ->sortable(),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSliders::route('/'),
        ];
    }

    protected function afterSave(): void
    {
        Artisan::call('cache:clear');
    }
}
