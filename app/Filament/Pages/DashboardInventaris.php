<?php

namespace App\Filament\Pages;

use App\Filament\Resources\CommodityResource\Widgets\CommodityStats;
use App\Filament\Resources\DashboardInventarisResource\Widgets\CommodityAcquisitionChart;
use App\Filament\Resources\DashboardInventarisResource\Widgets\CommodityBrandChart;
use App\Filament\Resources\DashboardInventarisResource\Widgets\CommodityConditionChart;
use App\Filament\Resources\DashboardInventarisResource\Widgets\CommodityLocationChart;
use App\Filament\Resources\DashboardInventarisResource\Widgets\CommodityMaterialChart;
use App\Filament\Resources\DashboardInventarisResource\Widgets\CommodityYearChart;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class DashboardInventaris extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'tabler-sitemap';

    protected static string $view = 'filament.pages.dashboard-inventaris';

    protected function getHeaderWidgets(): array
    {
        return [
            CommodityStats::class,
            CommodityConditionChart::class,
            CommodityYearChart::class,
            CommodityAcquisitionChart::class,
            CommodityMaterialChart::class,
            CommodityBrandChart::class,
            CommodityLocationChart::class,
        ];
    }
}
