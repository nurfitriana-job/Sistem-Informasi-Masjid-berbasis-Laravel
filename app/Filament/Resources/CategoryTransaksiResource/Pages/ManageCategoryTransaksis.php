<?php

namespace App\Filament\Resources\CategoryTransaksiResource\Pages;

use App\Filament\Resources\CategoryTransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCategoryTransaksis extends ManageRecords
{
    protected static string $resource = CategoryTransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
