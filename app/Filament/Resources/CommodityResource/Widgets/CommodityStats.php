<?php

namespace App\Filament\Resources\CommodityResource\Widgets;

use App\Enums\Condition;
use App\Filament\Resources\CommodityResource\Pages\ManageCommodities;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;

class CommodityStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ManageCommodities::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Inventaris', $this->getPageTableQuery()->count())
                ->iconColor('success')
                ->icon('heroicon-o-shopping-cart')
                ->description(__('Total Barang')),
            Stat::make(Condition::GOOD->getLabel(), $this->getPageTableQuery()->where('condition', Condition::GOOD->value)->count())
                ->iconColor(Condition::GOOD->getColor())
                ->icon(Condition::GOOD->getIcon())
                ->description(__('Total Barang Baik')),
            Stat::make(Condition::BAD->getLabel(), $this->getPageTableQuery()->where('condition', Condition::BAD->value)->count())
                ->iconColor(Condition::BAD->getColor())
                ->icon(Condition::BAD->getIcon())
                ->description(__('Total Barang Kurang Baik')),
            Stat::make(Condition::BROKEN->getLabel(), $this->getPageTableQuery()->where('condition', Condition::BROKEN->value)->count())
                ->iconColor(Condition::BROKEN->getColor())
                ->icon(Condition::BROKEN->getIcon())
                ->description(__('Total Barang Rusak Berat')),
        ];
    }
}
