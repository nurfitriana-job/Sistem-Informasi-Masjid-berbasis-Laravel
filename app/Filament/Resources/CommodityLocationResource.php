<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommodityLocationResource\Pages;
use App\Models\CommodityLocation;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommodityLocationResource extends Resource
{
    protected static ?string $model = CommodityLocation::class;

    protected static ?string $navigationIcon = 'tabler-map-pin';

    protected static ?string $navigationGroup = 'Inventaris';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('Data Lokasi');
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
                TextInput::make('name')
                    ->label(__('Nama'))
                    ->required()
                    ->maxLength(255),
                MarkdownEditor::make('description')
                    ->label(__('Deskripsi'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Nama'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('Deskripsi'))
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description)
                    ->wrap()
                    ->markdown()
                    ->html()
                    ->sortable(),
                TextColumn::make('commodities_count')
                    ->label(__('Jumlah Barang'))
                    ->counts('commodities')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('Dibuat Pada'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Diperbarui Pada'))
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
            'index' => Pages\ManageCommodityLocations::route('/'),
        ];
    }
}
