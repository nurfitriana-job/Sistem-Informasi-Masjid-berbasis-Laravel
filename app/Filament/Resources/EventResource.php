<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Event;
use App\Forms\EventForm;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\EventResource\Pages;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'tabler-calendar-event';

    protected static ?string $navigationGroup = 'Acara';

    public static function getModelLabel(): string
    {
        return __('Kegiatan');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

        return $query
            ->orderBy('start_date', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(EventForm::make());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('category.name')
                    ->label('Tipe Kegiatan'),
                Group::make('is_active')
                    ->label('Aktif'),
            ])
            ->defaultGroup('category.name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('Nama Kegiatan'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('Tipe Kegiatan'))
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->label(__('Deskripsi Kegiatan'))
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn($record) => $record->description)
                    ->html()
                    ->markdown()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_date')
                    ->label(__('Tanggal Mulai'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('end_date')
                    ->label(__('Tanggal Selesai'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                ToggleColumn::make('is_active')
                    ->label(__('Aktif'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('Dibuat Pada'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Diubah Pada'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label(__('Status'))
                    ->placeholder('Semua')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEvents::route('/'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'description',
            'category.name',
            'start_date',
            'end_date'
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return $record->name . ' - ' . $record->category->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Kegiatan' => $record->name,
            'Tipe Kegiatan' => $record->category->name,
            'Tanggal Mulai' => date('l, d F Y H:i', strtotime($record->start_date)),
            'Tanggal Selesai' => date('l, d F Y H:i', strtotime($record->end_date)),
            'Aktif' => $record->is_active ? 'Ya' : 'Tidak',
            'Dibuat Pada' => date('l, d F Y H:i', strtotime($record->created_at)),
            'Diubah Pada' => date('l, d F Y H:i', strtotime($record->updated_at)),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['category']);
    }
}
