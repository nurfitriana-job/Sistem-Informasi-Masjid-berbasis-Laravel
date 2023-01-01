<?php

namespace App\Filament\Resources\DashboardInventarisResource\Widgets;

use App\Models\Commodity;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CommodityLocationChart extends ApexChartWidget
{
    protected static ?string $chartId = 'commodity-location-chart';

    protected static ?string $heading = 'Persentase Barang Berdasarkan Lokasi';

    protected static ?string $description = 'Persentase barang berdasarkan lokasi penyimpanan';

    protected static ?int $contentHeight = 315;

    protected function getOptions(): array
    {
        $data = Commodity::selectRaw('commodity_location_id, COUNT(*) as count')
            ->with('commodityLocation')
            ->groupBy('commodity_location_id')
            ->orderBy('commodity_location_id', 'asc')
            ->get();

        $brands = [];
        $counts = [];
        $colors = [];

        $availableColors = ['#4caf50', '#f59e0b', '#dc2626', '#3498db', '#9b59b6', '#e74c3c'];

        $colorIndex = 0;

        foreach ($data as $item) {
            $brands[] = $item?->commodityLocation?->name;
            $counts[] = $item->count;
            $colors[] = $availableColors[$colorIndex];

            $colorIndex = ($colorIndex + 1) % count($availableColors);
        }

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 500,
            ],
            'series' => $counts,
            'labels' => $brands,
            'colors' => $colors,
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '60%',
                    ],
                ],
            ],
            'legend' => [
                'position' => 'right',
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
        ];
    }
}
