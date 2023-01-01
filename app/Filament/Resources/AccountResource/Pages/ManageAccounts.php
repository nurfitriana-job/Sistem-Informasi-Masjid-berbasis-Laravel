<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Actions\Accounts\InitializeAccount;
use App\Filament\Resources\AccountResource;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageAccounts extends ManageRecords
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('initialize')
                ->label(__('Inisialisasi'))
                ->color('warning')
                ->icon('heroicon-o-cog')
                ->requiresConfirmation()
                ->modalHeading(__('Inisialisasi Akun Keuangan'))
                ->action(function () {
                    $initializeAccount = new InitializeAccount;
                    $initializeAccount->generate();

                    $this->notify('success', [
                        'title' => __('Akun Keuangan Berhasil Diinisialisasi'),
                        'description' => __('Proses inisialisasi akun keuangan telah selesai.'),
                    ]);
                }),
            Actions\Action::make('import')
                ->label(__('Template Akun'))
                ->icon('tabler-download')
                ->form([
                    FileUpload::make('file')
                        ->label(__('File Template'))
                        ->required()
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->helperText(__('Unggah file template akun keuangan dalam format XLSX.'))
                        ->hintAction(
                            FormAction::make('download')
                                ->label(__('Unduh Template'))
                                ->icon('tabler-download')
                                ->action(function () {
                                    return response()->download(public_path('data/akun.xlsx'));
                                })
                                ->color('primary')
                        )
                        ->maxSize(1024 * 5),
                ])
                ->action(function (array $data) {
                    $initializeAccount = new InitializeAccount($data['file']);
                    $initializeAccount->generate();

                    if (file_exists($data['file'])) {
                        unlink($data['file']);
                    }

                    $this->notify('success', [
                        'title' => __('Akun Keuangan Berhasil Diimpor'),
                        'description' => __('Proses impor akun keuangan telah selesai.'),
                    ]);
                }),
        ];
    }

    public function notify(string $type, array $data): void
    {
        Notification::make()
            ->title($data['title'])
            ->body($data['description'])
            ->$type()
            ->send();
    }
}
