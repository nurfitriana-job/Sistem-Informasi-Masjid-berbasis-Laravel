<?php

namespace App\Filament\Resources;

use App\Enums\AccountType;
use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'tabler-chart-infographic';

    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('Akun Keuangan');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

        return $query
            ->orderBy('code');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')
                    ->label(__('Kode Akun'))
                    ->placeholder(__('Masukkan Kode Akun'))
                    ->validationAttribute(__('Kode Akun'))
                    ->autofocus()
                    ->required()
                    ->maxLength(50),
                TextInput::make('name')
                    ->label(__('Nama Akun'))
                    ->placeholder(__('Masukkan Nama Akun'))
                    ->validationAttribute(__('Nama Akun'))
                    ->autofocus()
                    ->columnSpanFull()
                    ->required()
                    ->maxLength(50),
                ToggleButtons::make('type')
                    ->label(__('Tipe Akun'))
                    ->options(AccountType::class)
                    ->inline()
                    ->required()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label(__('No'))
                    ->rowIndex(),
                TextColumn::make('code')
                    ->label(__('Kode Akun'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('name')
                    ->label(__('Nama Akun'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('type')
                    ->label(__('Tipe Akun'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('accountBalances.closing_balance')
                    ->label(__('Saldo'))
                    ->money('IDR', locale: 'id_ID')
                    ->default(0)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('Tipe Akun'))
                    ->options(AccountType::class)
                    ->multiple()
                    ->preload()
                    ->placeholder(__('Semua Tipe Akun')),
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
            'index' => Pages\ManageAccounts::route('/'),
        ];
    }
}
