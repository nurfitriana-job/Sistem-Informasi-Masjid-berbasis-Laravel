<?php

namespace App\Filament\Resources;

use App\Actions\BankList;
use App\Filament\Resources\BankAccountResource\Pages;
use App\Models\BankAccount;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static ?string $navigationIcon = 'tabler-building-bank';

    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('Akun Bank');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('account_number')
                    ->label(__('Nomor Rekening'))
                    ->placeholder(__('Masukkan Nomor Rekening'))
                    ->validationAttribute(__('Nomor Rekening'))
                    ->autofocus()
                    ->required()
                    ->maxLength(50),
                TextInput::make('account_holder_name')
                    ->label(__('Nama Pemilik Rekening'))
                    ->placeholder(__('Masukkan Nama Pemilik Rekening'))
                    ->validationAttribute(__('Nama Pemilik Rekening'))
                    ->required()
                    ->maxLength(50),
                Select::make('bank_code')
                    ->label(__('Nama Bank'))
                    ->options(fn () => BankList::get())
                    ->afterStateUpdated(function (Set $set, $state) {
                        $bankName = BankList::get()[$state] ?? null;

                        if ($bankName) {
                            $set('bank_name', $bankName);
                        } else {
                            $set('bank_name', null);
                        }
                    })
                    ->searchable()
                    ->validationAttribute(__('Nama Bank'))
                    ->required(),
                Hidden::make('bank_name')
                    ->label(__('Nama Bank')),
                MarkdownEditor::make('description')
                    ->label(__('Deskripsi'))
                    ->placeholder(__('Masukkan Deskripsi Akun Bank, ex: Cara Transfer atau Catatan'))
                    ->helperText(__('Deskripsi ini akan ditampilkan pada user.'))
                    ->validationAttribute(__('Deskripsi'))
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(__('Aktif'))
                    ->helperText(__('Tandai jika akun bank ini aktif.'))
                    ->default(true)
                    ->inline()
                    ->validationAttribute(__('Aktif')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_holder_name')
                    ->label(__('Nama Pemilik Rekening'))
                    ->description(fn ($record) => $record->account_number)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bank_name')
                    ->label(__('Nama Bank'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('Deskripsi'))
                    ->limit(50)
                    ->wrap()
                    ->html()
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label(__('Aktif'))
                    ->sortable()
                    ->toggleable(),
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
            'index' => Pages\ManageBankAccounts::route('/'),
        ];
    }
}
