<?php

namespace App\Filament\Resources;

use App\Filament\Exports\UserExporter;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Manajemen Akun';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('media')
                            ->hiddenLabel()
                            ->avatar()
                            ->collection('avatars')
                            ->alignCenter()
                            ->columnSpanFull(),

                        Section::make()
                            ->schema([
                                TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->revealable()
                                    ->required(),
                                TextInput::make('passwordConfirmation')
                                    ->password()
                                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->revealable()
                                    ->same('password')
                                    ->required(),
                            ])
                            ->compact()
                            ->hidden(fn (string $operation): bool => $operation === 'edit'),

                        Section::make()
                            ->schema([
                                Placeholder::make('email_verified_at')
                                    ->label(__('Diverifikasi Pada'))
                                    ->content(fn (User $record): ?string => new HtmlString("$record->email_verified_at")),

                                Placeholder::make('created_at')
                                    ->label(__('Dibuat Pada'))
                                    ->content(fn (User $record): ?string => $record->created_at?->diffForHumans()),

                                Placeholder::make('updated_at')
                                    ->label(__('Diperbarui Pada'))
                                    ->content(fn (User $record): ?string => $record->updated_at?->diffForHumans()),
                            ])
                            ->compact()
                            ->hidden(fn (string $operation): bool => $operation === 'create'),
                    ])
                    ->columnSpan(1),

                Tabs::make()
                    ->schema([
                        Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Nama Pengguna'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('username')
                                    ->label(__('Username'))
                                    ->required()
                                    ->maxLength(255)
                                    ->live()
                                    ->rules(function ($record) {
                                        $userId = $record?->id;

                                        return $userId
                                            ? ['unique:users,username,' . $userId]
                                            : ['unique:users,username'];
                                    }),

                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->rules(function ($record) {
                                        $userId = $record?->id;

                                        return $userId
                                            ? ['unique:users,email,' . $userId]
                                            : ['unique:users,email'];
                                    }),
                                TextInput::make('phone')
                                    ->label(__('Phone'))
                                    ->tel()
                                    ->required()
                                    ->rules(['starts_with:62'])
                                    ->maxLength(255)
                                    ->rules(function ($record) {
                                        $userId = $record?->id;

                                        return $userId
                                            ? ['unique:users,phone,' . $userId]
                                            : ['unique:users,phone'];
                                    }),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Roles')
                            ->icon('tabler-shield-lock')
                            ->schema([
                                Select::make('roles')
                                    ->label(__('Hak Akses'))
                                    ->relationship('roles', 'name')
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => Str::headline($record->name))
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->optionsLimit(5)
                                    ->helperText(__('Bisa digunakan untuk memberikan tag pada pengguna tanpa akses ke sistem.'))
                                    ->columnSpanFull(),

                            ]),
                    ])
                    ->columnSpan([
                        'sm' => 1,
                        'lg' => 2,
                    ]),
            ])
            ->columns(3);
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label(__('Image'))
                    ->searchable()
                    ->circular()
                    ->grow(false)
                    ->getStateUsing(fn ($record) => $record->avatar_url
                        ? $record->avatar_url
                        : 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Nama Pengguna'))
                    ->description(fn (User $record) => $record->email_verified_at
                        ? __('Diverifikasi Pada') . ': ' . $record->email_verified_at->diffForHumans()
                        : __('Belum Diverifikasi'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->label(__('Username'))
                    ->searchable()
                    ->icon('heroicon-o-user'),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email/Phone'))
                    ->description(fn (User $record) => $record->phone)
                    ->icon('heroicon-m-envelope')
                    ->searchable()
                    ->grow(false),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('Hak Akses'))
                    ->searchable()
                    ->icon('heroicon-o-shield-check')
                    ->grow(false),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('Set Role')
                        ->icon('heroicon-m-adjustments-vertical')
                        ->form([
                            Select::make('role')
                                ->relationship('roles', 'name')
                                ->multiple()
                                ->required()
                                ->searchable()
                                ->preload()
                                ->optionsLimit(10)
                                ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                        ]),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->link()
                    ->label('Aksi')
                    ->size(ActionSize::Small),
                Impersonate::make()
                    ->label(__('Impersonate')),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(UserExporter::class),
                ImportAction::make()
                    ->importer(UserImporter::class),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->exporter(UserExporter::class),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ActivitylogRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfolistSection::make('User Information')->schema([
                // Avatar image
                SpatieMediaLibraryImageEntry::make('media')
                    ->label(__('Foto Pengguna'))
                    ->collection('avatars')
                    ->default(fn ($record) => $record->avatar_url
                        ? $record->avatar_url
                        : 'https://ui-avatars.com/api/?name=' . urlencode($record->name))
                    ->columnSpanFull(),

                // Nama Pengguna
                TextEntry::make('name')
                    ->label(__('Nama Pengguna'))
                    ->default(fn (User $record) => $record->name),

                // Username
                TextEntry::make('username')
                    ->label(__('Username'))
                    ->default(fn (User $record) => $record->username),

                // Email
                TextEntry::make('email')
                    ->label(__('Email'))
                    ->default(fn (User $record) => $record->email),

                // Hak Akses
                TextEntry::make('roles.name')
                    ->label(__('Hak Akses'))
                    ->default(fn (User $record) => $record->roles->pluck('name')->implode(', ')),

                // Diverifikasi Pada
                TextEntry::make('email_verified_at')
                    ->label(__('Diverifikasi Pada'))
                    ->default(fn (User $record) => $record->email_verified_at ? $record->email_verified_at->diffForHumans() : '-'),

                // Dibuat Pada
                TextEntry::make('created_at')
                    ->label(__('Dibuat Pada'))
                    ->default(fn (User $record) => $record->created_at?->diffForHumans() ?: '-'),

                // Diperbarui Pada
                TextEntry::make('updated_at')
                    ->label(__('Diperbarui Pada'))
                    ->default(fn (User $record) => $record->updated_at?->diffForHumans() ?: '-'),
            ])
                ->columns(2),
        ]);
    }
}
