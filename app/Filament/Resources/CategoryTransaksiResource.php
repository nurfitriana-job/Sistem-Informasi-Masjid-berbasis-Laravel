<?php

namespace App\Filament\Resources;

use App\Enums\CategoryType;
use App\Filament\Resources\CategoryTransaksiResource\Pages;
use App\Models\Category;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CategoryTransaksiResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'tabler-category-plus';

    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('Kategori Transaksi');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

        return $query
            ->where('type', CategoryType::TRANSACTION);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('Nama Kategori'))
                    ->placeholder(__('Masukkan Nama Kategori ex: Donasi, Zakat'))
                    ->validationAttribute(__('Nama Kategori'))
                    ->autofocus()
                    ->required()
                    ->maxLength(50),
                Select::make('account_id')
                    ->label(__('Akun Pencatatan'))
                    ->relationship('account', 'name')
                    ->placeholder(__('Pilih Akun'))
                    ->validationAttribute(__('Akun'))
                    ->searchable()
                    ->required()
                    ->preload()
                    ->columns(1),
                Toggle::make('is_user')
                    ->label(__('Transaksi Pengguna'))
                    ->helperText(__('Tandai jika kategori ini adalah kategori transaksi yang dapat dilakukan oleh pengguna'))
                    ->validationAttribute(__('Transaksi Pengguna'))
                    ->default(false)
                    ->live(),
                Select::make('bank_account_id')
                    ->label(__('Bank'))
                    ->relationship('bank', 'account_holder_name')
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->account_holder_name} ({$record->account_number})")
                    ->placeholder(__('Pilih Bank Akun'))
                    ->helperText(__('Pilih Bank Akun jika kategori ini adalah kategori transaksi yang dapat dilakukan oleh pengguna'))
                    ->searchable()
                    ->validationAttribute(__('Bank'))
                    ->hidden(fn (Get $get) => $get('is_user') === false)
                    ->preload()
                    ->reactive(),
                Textarea::make('description')
                    ->label(__('Deskripsi Kategori'))
                    ->placeholder(__('Masukkan Deskripsi Kategori'))
                    ->validationAttribute(__('Deskripsi Kategori'))
                    ->rows(3)
                    ->columnSpanFull(),
                Hidden::make('type')
                    ->default(CategoryType::TRANSACTION),
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
                TextColumn::make('name')
                    ->label(__('Nama Kategori'))
                    ->sortable()
                    ->searchable()
                    ->placeholder(__('Nama Kategori')),
                TextColumn::make('account.name')
                    ->label(__('Akun Pencatatan'))
                    ->description(fn (Category $record): string => $record->account->code ?? '-')
                    ->sortable()
                    ->searchable()
                    ->placeholder(__('Akun Pencatatan')),
                IconColumn::make('is_user')
                    ->label(__('Transaksi Pengguna'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('bank.account_holder_name')
                    ->label(__('Bank'))
                    ->description(fn (Category $record): string => $record->bank->account_number ?? '-')
                    ->sortable()
                    ->searchable()
                    ->placeholder(__('Bank')),
                TextColumn::make('description')
                    ->label(__('Deskripsi Kategori'))
                    ->wrap()
                    ->sortable()
                    ->searchable()
                    ->placeholder(__('Deskripsi Kategori')),
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
            'index' => Pages\ManageCategoryTransaksis::route('/'),
        ];
    }
}
