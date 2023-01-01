<?php

namespace App\Filament\Resources\CommodityResource\Pages;

use App\Enums\Condition;
use App\Filament\Resources\CommodityResource;
use App\Models\Commodity;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class ManageCommodities extends ManageRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = CommodityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return CommodityResource::getWidgets();
    }

    public function getTabs(): array
    {
        $tabs = [
            null => Tab::make('All'),
        ];
        $conditions = Condition::cases();
        foreach ($conditions as $key => $condition) {
            $tabs[$condition->getLabel()] = Tab::make($condition->getLabel())
                ->query(fn ($query) => $query->where('condition', $condition->value))
                ->badge(
                    Commodity::where('condition', $condition->value)->count()
                )
                ->badgeColor($condition->getColor());
        }

        return $tabs;
    }
}
