<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrayerTimeResource\Pages;
use App\Models\City;
use App\Models\PrayerTime;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PrayerTimeResource extends Resource
{
    protected static ?string $model = PrayerTime::class;

    protected static ?string $navigationIcon = 'tabler-building-mosque';

    protected static ?string $navigationGroup = 'Acara';

    public static function getModelLabel(): string
    {
        return __('Jadwal Sholat');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

        if (City::count() <= 0) {
            City::getRows();
        }

        return $query
            ->orderByRaw('date = ? desc', [now()->format('Y-m-d')])
            ->orderBy('date', 'asc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Placeholder::make('date')
                            ->label(__('Tanggal'))
                            ->columnSpanFull()
                            ->content(fn ($record) => date('d F Y', strtotime($record->date))),
                        Placeholder::make('city.name')
                            ->label(__('Kota'))
                            ->columnSpanFull()
                            ->content(fn ($record) => $record?->city->name),
                        Forms\Components\TimePicker::make('imsak')
                            ->label(__('Imsak'))
                            ->seconds(false)
                            ->required(),
                        Forms\Components\TimePicker::make('subuh')
                            ->label(__('Subuh'))
                            ->seconds(false)
                            ->required(),
                        Forms\Components\TimePicker::make('terbit')
                            ->label(__('Terbit'))
                            ->seconds(false)
                            ->required(),
                        Forms\Components\TimePicker::make('dhuha')
                            ->label(__('Dhuha'))
                            ->seconds(false)
                            ->required(),
                        Forms\Components\TimePicker::make('dzuhur')
                            ->label(__('Dzuhur'))
                            ->seconds(false)
                            ->required(),
                        Forms\Components\TimePicker::make('ashar')
                            ->label(__('Ashar'))
                            ->seconds(false)
                            ->required(),
                        Forms\Components\TimePicker::make('maghrib')
                            ->label(__('Maghrib'))
                            ->seconds(false)
                            ->required(),
                        Forms\Components\TimePicker::make('isya')
                            ->label(__('Isya'))
                            ->seconds(false)
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('city.name')
                    ->label(__('Kota'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date')
                    ->label(__('Tanggal'))
                    ->date('d F Y')
                    ->sortable()
                    ->searchable()
                    ->dateTimeTooltip('d F Y'),
                TextColumn::make('imsak')
                    ->label(__('Imsak'))
                    ->sortable()
                    ->searchable()
                    ->date('H:i'),
                TextColumn::make('subuh')
                    ->label(__('Subuh'))
                    ->sortable()
                    ->searchable()
                    ->date('H:i'),
                TextColumn::make('terbit')
                    ->label(__('Terbit'))
                    ->sortable()
                    ->searchable()
                    ->date('H:i'),
                TextColumn::make('dhuha')
                    ->label(__('Dhuha'))
                    ->sortable()
                    ->searchable()
                    ->date('H:i'),
                TextColumn::make('dzuhur')
                    ->label(__('Dzuhur'))
                    ->sortable()
                    ->searchable()
                    ->date('H:i'),
                TextColumn::make('ashar')
                    ->label(__('Ashar'))
                    ->sortable()
                    ->searchable()
                    ->date('H:i'),
                TextColumn::make('maghrib')
                    ->label(__('Maghrib'))
                    ->sortable()
                    ->searchable()
                    ->date('H:i'),
                TextColumn::make('isya')
                    ->label(__('Isya'))
                    ->sortable()
                    ->searchable()
                    ->date('H:i'),
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->relationship('city', 'name')
                    ->label(__('Kota'))
                    ->multiple()
                    ->placeholder(__('Semua Kota'))
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordClasses(fn (Model $record) => Carbon::parse($record->date)->format('Y-m-d') === now()->format('Y-m-d') ? 'bg-primary-500' : '');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePrayerTimes::route('/'),
        ];
    }
}
