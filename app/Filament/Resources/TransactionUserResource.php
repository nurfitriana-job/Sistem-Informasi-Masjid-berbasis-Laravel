<?php

namespace App\Filament\Resources;

use App\Actions\ManageTransaction;
use App\Enums\TransactionStatus;
use App\Filament\Resources\TransactionUserResource\Pages;
use App\Filament\Resources\TransactionUserResource\Widgets\TransactionStat;
use App\Helpers\MediaTypeHelper;
use App\Models\Category;
use App\Models\Journal;
use App\Models\NotificationTemplate;
use App\Models\TransactionUser;
use App\Models\User;
use App\Notifications\TelegramNotification;
use App\Settings\GeneralSetting;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class TransactionUserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = TransactionUser::class;

    protected static ?string $navigationIcon = 'tabler-credit-card-pay';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 1;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'verify',
        ];
    }

    public static function getModelLabel(): string
    {
        $user = User::find(Auth::id());
        if ($user->can('verify', TransactionUser::class)) {
            return __('Transaksi Pengguna');
        }

        return __('Pembayaran');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

        $query
            ->userTransaction();

        if (! auth()->user()->can('verify', Journal::class)) {
            $query->where('user_id', auth()->user()->id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Group::make([
                            DatePicker::make('transaction_date')
                                ->label(__('Tanggal Transaksi'))
                                ->placeholder(__('Pilih Tanggal Transaksi'))
                                ->validationAttribute(__('Tanggal Transaksi'))
                                ->readonly(fn() => ! auth()->user()->can('verify', Journal::class))
                                ->default(now())
                                ->minDate(now()->subYear(1)),
                            Hidden::make('category_name'),
                            Select::make('category_id')
                                ->label(__('Jenis Transaksi'))
                                ->placeholder(__('Pilih Jenis Transaksi'))
                                ->relationship('category', 'name')
                                ->validationAttribute(__('Jenis Transaksi'))
                                ->live()
                                ->searchable()
                                ->afterStateUpdated(function (Set $set, $state) {
                                    $category = Category::find($state);
                                    if (! $category) {
                                        return;
                                    }

                                    $set('category_name', $category->name);
                                })
                                ->required()
                                ->preload(),
                            Select::make('user_id')
                                ->label(__('Dari Pengguna'))
                                ->relationship('user', 'name')
                                ->hidden(fn() => ! auth()->user()->can('verify', Journal::class))
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
                                ->stripCharacters('.')
                                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                    $set('description', 'Transaksi ' . $get('category_name') . ' sebesar ' . $state . ' Pada ' . $get('transaction_date'));
                                }),
                        ])
                            ->columns(2)
                            ->columnSpanFull(),
                        ToggleButtons::make('status')
                            ->label(__('Status'))
                            ->options(TransactionStatus::class)
                            ->helperText(__('Status transaksi ini akan dikirimkan ke pengguna'))
                            ->hidden(fn($record) => ! auth()->user()->can('verify', Journal::class))
                            ->default(TransactionStatus::PENDING)
                            ->inline()
                            ->columnSpanFull(),
                        Placeholder::make('placeholder')
                            ->hiddenLabel()
                            ->content(function (Get $get) {
                                $category = Category::find($get('category_id'));
                                if (! $category || ! $get('amount') || ! $category?->bank?->account_holder_name) {
                                    return '';
                                }

                                return new HtmlString(__('messages.transaction_message', [
                                    'category_name' => $get('category_name'),
                                    'amount' => $get('amount'),
                                    'date' => now()->format('d-m-Y H:i:s'),
                                    'bank_name' => $category?->bank?->bank_name,
                                    'account_holder_name' => $category?->bank?->account_holder_name,
                                    'account_number' => $category?->bank?->account_number,
                                ]));
                            })
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label(__('Deskripsi Transaksi'))
                            ->placeholder(__('Masukkan Deskripsi'))
                            ->validationAttribute(__('Deskripsi'))
                            ->required()
                            ->columnSpanFull()
                            ->rows(3)
                            ->reactive(),
                        Placeholder::make('note')
                            ->label(__('Catatan'))
                            ->content(fn($record) => $record->note)
                            ->hidden(fn($record) => ! $record?->note)
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('attachments')
                            ->label(__('Bukti Transaksi'))
                            ->collection('transaction')
                            ->responsiveImages()
                            ->image()
                            ->imageEditor()
                            ->helperText(__('Upload bukti transaksi untuk mempercepat proses verifikasi transaksi'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
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
                TextColumn::make('category.name')
                    ->label(__('Jenis Transaksi'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->badge(),
                TextColumn::make('description')
                    ->label(__('Deskripsi'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->wrap(),
                TextColumn::make('total_credit')
                    ->label(__('Jumlah'))
                    ->money('IDR', locale: 'id_ID')
                    ->default(0)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->tooltip(fn($record) => $record->note)
                    ->badge(),
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
                                fn(Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('verify')
                        ->label(__('Verifikasi'))
                        ->action(function (Model $record, $data) {
                            $record->update([
                                'status' => $data['status'],
                                'note' => $data['note'],
                            ]);

                            $data = $record->toArray();

                            $update = new ManageTransaction($data);
                            $update->update($record);

                            Notification::make()
                                ->title(__('Transaksi Berhasil Diverifikasi'))
                                ->body(__('Transaksi berhasil diverifikasi dan statusnya telah diperbarui ke pengguna'))
                                ->success()
                                ->send();

                            $statusTrx = null;
                            $templateId = null;
                            if ($data['status'] === TransactionStatus::APPROVED->value) {
                                $statusTrx = __('Pembayaran Diterima');
                                $templateId = 'pembayaran_transaksi_accepted';
                            } elseif ($data['status'] === TransactionStatus::CANCELLED->value) {
                                $statusTrx = __('Dibatalkan');
                                $templateId = 'pembayaran_transaksi_cancelled';
                            } else {
                                $statusTrx = __('Pembayaran Ditolak');
                                $templateId = 'pembayaran_transaksi_gagal';
                            }
                            $user = User::find($record->user_id);
                            $template = NotificationTemplate::where('name', $templateId)
                                ->where('is_active', true)
                                ->first();

                            $body = null;
                            if ($template) {
                                $settings = app(GeneralSetting::class);
                                $body = $template->parseBody([
                                    '{subject}' => $statusTrx,
                                    '{user}' => $user->name,
                                    '{name}' => $record->description,
                                    '{transaction_date}' => date('d F Y H:i:s', strtotime($record->transaction_date)),
                                    '{nominal}' => 'Rp ' . number_format($record->total_credit, 0, ',', '.'),
                                    '{category_name}' => $record?->category?->name,
                                    '{masjid}' => $settings->site_name,
                                    '{masjid_address}' => $settings->site_address,
                                    '{masjid_phone}' => $settings->site_support_phone,
                                    '{masjid_email}' => $settings->site_support_email,
                                    '{status}' => $statusTrx,
                                ]);

                                $mediaData = self::getMediaDataFromTemplate($record);

                                if (! $user->telegram_chat_id) {
                                    Notification::make()
                                        ->title(__('Notifikasi Gagal Dikirim'))
                                        ->body(__('Pengguna belum menghubungkan akun Telegram, silakan hubungi pengguna untuk menghubungkan akun Telegram mereka'))
                                        ->danger()
                                        ->send();

                                    return;
                                }

                                $user->notify(new TelegramNotification(
                                    $body,
                                    $mediaData['url'],
                                    $mediaData['category'],
                                ));

                                $user->notify(
                                    Notification::make()
                                        ->title(__('Notifikasi Pembayaran'))
                                        ->body($body)
                                        ->info()
                                        ->toDatabase(),
                                );
                            }
                        })
                        ->form([
                            Select::make('status')
                                ->label(__('Status'))
                                ->options(TransactionStatus::class)
                                ->default(TransactionStatus::APPROVED)
                                ->afterStateUpdated(function (Set $set, $state) {
                                    $set('note', null);
                                })
                                ->required(),
                            Textarea::make('note')
                                ->label(__('Catatan'))
                                ->placeholder(__('Masukkan Catatan'))
                                ->required(fn($get) => $get('status') === TransactionStatus::REJECTED)
                                ->maxLength(255)
                                ->rows(3)
                                ->helperText(__('Catatan ini akan dikirimkan ke pengguna')),
                        ])
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->hidden(fn($record) => ! auth()->user()->can('verify', $record)),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->action(function (Model $record) {
                            $journal = new ManageTransaction;
                            $journal->delete($record);

                            $record->delete();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (array $records) {
                            foreach ($records as $record) {
                                $journal = new ManageTransaction;
                                $journal->delete($record);

                                $record->delete();
                            }
                        }),
                ]),
            ]);
    }

    protected static function getMediaDataFromTemplate(Journal $record): array
    {
        $mediaItem = $record->getFirstMedia('transaction');
        $mediaUrl = null;
        $mediaCategory = null;

        if ($mediaItem) {
            $mediaCategory = MediaTypeHelper::getCategoryFromMediaObject($mediaItem);
            $mediaUrl = $record->getFirstMediaUrl('transaction');
        }

        return [
            'url' => $mediaUrl,
            'category' => $mediaCategory,
        ];
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('transaction_date')
                    ->label(__('Tanggal Transaksi')),
                TextEntry::make('category.name')
                    ->label(__('Jenis Transaksi')),
                TextEntry::make('description')
                    ->label(__('Deskripsi')),
                TextEntry::make('total_credit')
                    ->label(__('Jumlah'))
                    ->money('IDR', locale: 'id_ID'),
                TextEntry::make('status')
                    ->label(__('Status')),
                TextEntry::make('created_by.name')
                    ->label(__('Dibuat Oleh')),
                TextEntry::make('created_at')
                    ->label(__('Dibuat Pada')),
                TextEntry::make('updated_at')
                    ->label(__('Diperbarui Pada')),
                TextEntry::make('note')
                    ->label(__('Catatan'))
                    ->hidden(fn($record) => ! $record?->note)
                    ->columnSpanFull(),
                SpatieMediaLibraryImageEntry::make('attachments')
                    ->label(__('Bukti Transaksi'))
                    ->collection('transaction')
                    ->conversion('thumb'),
            ])
            ->columns(2);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactionUsers::route('/'),
            'create' => Pages\CreateTransactionUser::route('/create'),
            'edit' => Pages\EditTransactionUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::where('status', '!=', 'approved')
            ->where('status', '!=', 'cancelled')
            ->count();
    }
}
