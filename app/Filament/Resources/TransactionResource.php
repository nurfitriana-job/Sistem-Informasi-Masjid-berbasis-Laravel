<?php

namespace App\Filament\Resources;

use App\Actions\ManageTransaction;
use App\Enums\TransactionStatus;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\Widgets\TransactionStat;
use App\Models\Journal;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TransactionResource extends Resource
{
    protected static ?string $model = Journal::class;

    protected static ?string $navigationIcon = 'tabler-cash-banknote';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('Pemasukan & Pengeluaran');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

        return $query
            ->notUserTransaction()
            ->orderBy('created_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('transaction_date')
                            ->label(__('Tanggal Transaksi'))
                            ->placeholder(__('Pilih Tanggal Transaksi'))
                            ->validationAttribute(__('Tanggal Transaksi'))
                            ->required()
                            ->default(now())
                            ->minDate(now()->subYear(1)),
                        ToggleButtons::make('type')
                            ->label(__('Jenis Transaksi'))
                            ->options([
                                'income' => __('Pemasukan'),
                                'expense' => __('Pengeluaran'),
                            ])
                            ->live()
                            ->default('income')
                            ->inline()
                            ->required(),
                        Select::make('account_id')
                            ->label(fn ($get) => $get('type') === 'income' ? __('Akun Pemasukan') : __('Akun Pengeluaran'))
                            ->relationship(
                                'account',
                                'name',
                                function ($query, Get $get) {
                                    return $query
                                        ->when(
                                            $get('type') === 'income',
                                            fn ($query) => $query->whereIn('type', ['asset', 'revenue']),
                                            fn ($query) => $query->whereIn('type', ['liability', 'expense'])
                                        );
                                }
                            )
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->preload(),
                        Select::make('payment_account_id')
                            ->label(__('Akun Pembayaran'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship(
                                'paymentAccount',
                                'name',
                                function ($query, Get $get) {
                                    return $query
                                        ->when(
                                            $get('type') === 'expense',
                                            fn ($query) => $query->whereIn('type', ['asset', 'revenue']),
                                        );
                                }
                            )
                            ->hidden(fn ($get) => $get('type') === 'income'),
                        Select::make('user_id')
                            ->label(fn ($get) => $get('type') === 'income' ? __('Donatur') : __('Penerima'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('amount')
                            ->label(__('Jumlah'))
                            ->placeholder(__('Masukkan Jumlah'))
                            ->validationAttribute(__('Jumlah'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(9999999999)
                            ->live(onBlur: true)
                            ->prefix('Rp')
                            ->inputMode('decimal')
                            ->mask(RawJs::make('$money($input, \',\')'))
                            ->stripCharacters('.'),
                        MarkdownEditor::make('description')
                            ->label(__('Deskripsi'))
                            ->placeholder(__('Masukkan Deskripsi'))
                            ->validationAttribute(__('Deskripsi'))
                            ->columnSpanFull()
                            ->helperText(__('Deskripsi transaksi')),
                    ])
                    ->columns(2),
                Section::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('attachments')
                            ->label(__('Attachments'))
                            ->collection('transaction')
                            ->responsiveImages()
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label(__('No'))
                    ->rowIndex(),
                TextColumn::make('transaction_date')
                    ->label(__('Tanggal Transaksi'))
                    ->date('d-m-Y')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('account.name')
                    ->label(__('Akun'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('type')
                    ->label(__('Jenis Transaksi'))
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->label(__('Deskripsi'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->wrap()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description),
                TextColumn::make('amount')
                    ->label(__('Total Transaksi'))
                    ->money('IDR', locale: 'id_ID')
                    ->getStateUsing(function (Model $record) {
                        return $record->amount;
                    })
                    ->default(0)
                    ->sortable(['total_credit', 'total_debit'])
                    ->searchable([
                        'total_credit',
                        'total_debit',
                    ])
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(TransactionStatus::class),
                Filter::make('transaction_date')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hiddenLabel()
                    ->tooltip(__('Edit')),
                Tables\Actions\DeleteAction::make()
                    ->hiddenLabel()
                    ->tooltip(__('Hapus'))
                    ->action(function (Model $record) {
                        $journal = new ManageTransaction;
                        $journal->delete($record);

                        $record->delete();
                    }),
                Tables\Actions\ViewAction::make('journal')
                    ->hiddenLabel()
                    ->tooltip(__('Lihat Jurnal'))
                    ->icon('tabler-credit-card-pay')
                    ->color('green')
                    ->modalHeading(__('Lihat Jurnal'))
                    ->infolist([
                        ViewEntry::make('entries')
                            ->hiddenLabel()
                            ->view('infolists.components.journal-entry'),
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(
                            function (array $records) {
                                foreach ($records as $record) {
                                    $journal = new ManageTransaction;
                                    $journal->delete($record);

                                    $record->delete();
                                }
                            }
                        ),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            TransactionStat::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
