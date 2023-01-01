<?php

namespace App\Filament\Resources\CommodityAcquisitionResource\Pages;

use App\Filament\Resources\CommodityAcquisitionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCommodityAcquisitions extends ManageRecords
{
    protected static string $resource = CommodityAcquisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
