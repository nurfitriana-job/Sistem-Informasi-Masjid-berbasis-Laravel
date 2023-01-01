<?php

namespace App\Filament\Resources\CommodityLocationResource\Pages;

use App\Filament\Resources\CommodityLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCommodityLocations extends ManageRecords
{
    protected static string $resource = CommodityLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
