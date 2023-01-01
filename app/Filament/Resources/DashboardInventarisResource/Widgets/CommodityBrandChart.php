<?php

namespace App\Filament\Resources\DashboardInventarisResource\Widgets;

use App\Models\Commodity;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CommodityBrandChart extends ApexChartWidget
{
    protected static ?string $chartId = 'commodity-brand-chart';

    protected static ?string $heading = 'Jumlah Barang Berdasarkan Merek';

    protected static ?int $contentHeight = 315;

    protected function getOptions(): array
    {
        $data = Commodity::selectRaw('brand, COUNT(*) as count')
            ->groupBy('brand')
            ->orderBy('brand', 'asc')
            ->get();

        $brands = [];
        $counts = [];
        $colors = [];

        $availableColors = ['#4caf50', '#f59e0b', '#dc2626', '#3498db', '#9b59b6', '#e74c3c'];

        $colorIndex = 0;

        foreach ($data as $item) {
            $brands[] = $item->brand;
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
