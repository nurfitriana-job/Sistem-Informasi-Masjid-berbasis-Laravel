<?php

namespace App\Filament\Resources\TransactionUserResource\Pages;

use App\Filament\Resources\TransactionUserResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListTransactionUsers extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = TransactionUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return TransactionUserResource::getWidgets();
    }
}
