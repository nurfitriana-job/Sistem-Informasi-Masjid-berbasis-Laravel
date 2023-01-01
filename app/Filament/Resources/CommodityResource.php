<?php

namespace App\Filament\Resources;

use App\Enums\Condition;
use App\Filament\Resources\CommodityResource\Pages;
use App\Filament\Resources\CommodityResource\Widgets\CommodityStats;
use App\Models\Commodity;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class CommodityResource extends Resource
{
    protected static ?string $model = Commodity::class;

    protected static ?string $navigationIcon = 'tabler-box';

    protected static ?string $navigationGroup = 'Inventaris';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('Data Barang');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

        return $query
            ->orderBy('created_at', 'desc');
    }

    public static function getWidgets(): array
    {
        return [
            CommodityStats::class,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('item_code')
                    ->label(__('Kode Barang'))
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label(__('Nama Barang'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('brand')
                    ->label(__('Merek'))
                    ->required()
                    ->maxLength(255),
                Group::make([
                    TextInput::make('material')
                        ->label(__('Bahan'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('year_of_purchase')
                        ->label(__('Tahun Pembelian'))
                        ->required()
                        ->numeric()
                        ->maxLength(4),
                    Select::make('condition')
                        ->label(__('Kondisi'))
                        ->options(Condition::class)
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('commodity_acquisition_id')
                        ->label(__('Asal Barang'))
                        ->relationship('commodityAcquisition', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('commodity_location_id')
                        ->label(__('Lokasi Barang'))
                        ->relationship('commodityLocation', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                    ->columns(3)
                    ->columnSpanFull(),
                TextInput::make('quantity')
                    ->label(__('Jumlah'))
                    ->required()
                    ->numeric()
                    ->suffix(' item')
                    ->maxLength(255),
                TextInput::make('price_per_item')
                    ->label(__('Harga Per Item'))
                    ->required()
                    ->numeric()
                    ->prefix('Rp ')
                    ->suffix(',-')
                    ->minValue(1)
                    ->maxLength(255)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $quantity = $set('quantity');
                        if ($quantity > 0) {
                            $set('price', $state * $quantity);
                        } else {
                            $set('price', 0);
                        }
                    }),
                TextInput::make('price')
                    ->label(__('Harga'))
                    ->required()
                    ->numeric()
                    ->prefix('Rp ')
                    ->suffix(',-')
                    ->minValue(1)
                    ->maxLength(255),
                Textarea::make('note')
                    ->label(__('Catatan'))
                    ->rows(3)
                    ->columnSpanFull(),

            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item_code')
                    ->label(__('Kode Barang'))
                    ->description(fn ($record) => $record?->commodityAcquisition?->name)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('name')
                    ->label(__('Nama Barang'))
                    ->description(fn ($record) => $record?->commodityLocation?->name)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('brand')
                    ->label(__('Merek'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('material')
                    ->label(__('Bahan'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('year_of_purchase')
                    ->label(__('Tahun'))
                    ->tooltip(__('Tahun Pembelian'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('condition')
                    ->label(__('Kondisi'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('quantity')
                    ->label(__('Jumlah'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('price_per_item')
                    ->label(__('Harga/Item'))
                    ->sortable()
                    ->tooltip(__('Harga Per Item'))
                    ->money('IDR', locale: app()->getLocale())
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price')
                    ->label(__('Harga'))
                    ->sortable()
                    ->money('IDR', locale: app()->getLocale())
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('Tanggal Dibuat'))
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Tanggal Diperbarui'))
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('commodity_location_id')
                    ->relationship('commodityLocation', 'name')
                    ->label(__('Lokasi Barang'))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->placeholder(__('Semua Lokasi Barang')),
                SelectFilter::make('commodity_acquisition_id')
                    ->relationship('commodityAcquisition', 'name')
                    ->label(__('Asal Barang'))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->placeholder(__('Semua Asal Barang')),
                SelectFilter::make('condition')
                    ->label(__('Kondisi'))
                    ->multiple()
                    ->options(Condition::class)
                    ->placeholder(__('Semua Kondisi')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $modelClass = static::$model;

        return (string) $modelClass::where('condition', Condition::GOOD->value)
            ->count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCommodities::route('/'),
        ];
    }
}
