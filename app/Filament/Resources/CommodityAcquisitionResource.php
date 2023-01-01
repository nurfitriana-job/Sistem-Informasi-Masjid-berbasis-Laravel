<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommodityAcquisitionResource\Pages;
use App\Models\CommodityAcquisition;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommodityAcquisitionResource extends Resource
{
    protected static ?string $model = CommodityAcquisition::class;

    protected static ?string $navigationIcon = 'tabler-shopping-cart-plus';

    protected static ?string $navigationGroup = 'Inventaris';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('Data Pengadaan Barang');
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
                    ->label(__('Nama Pengadaan'))
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
                    ->label(__('Nama Pengadaan'))
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('description')
                    ->label(__('Deskripsi'))
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn ($record) => $record->description)
                    ->html()
                    ->markdown(),
                TextColumn::make('commodities_count')
                    ->label(__('Jumlah Barang'))
                    ->counts('commodities')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('Tanggal Dibuat'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Tanggal Diperbarui'))
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
            'index' => Pages\ManageCommodityAcquisitions::route('/'),
        ];
    }
}
