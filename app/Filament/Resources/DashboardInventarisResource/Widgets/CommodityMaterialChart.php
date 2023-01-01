<?php

namespace App\Filament\Resources\DashboardInventarisResource\Widgets;

use App\Models\Commodity;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CommodityMaterialChart extends ApexChartWidget
{
    protected static ?string $chartId = 'commodity-material-chart';

    protected static ?string $heading = 'Jumlah Barang Berdasarkan Material';

    protected static ?int $contentHeight = 315;

    protected function getOptions(): array
    {
        $data = Commodity::selectRaw('material, COUNT(*) as count')
            ->groupBy('material')
            ->orderBy('material', 'asc')
            ->get();

        $materials = [];
        $counts = [];
        $colors = [];

        $availableColors = ['#4caf50', '#f59e0b', '#dc2626', '#3498db', '#9b59b6', '#e74c3c'];

        $colorIndex = 0;

        foreach ($data as $item) {
            $materials[] = $item->material;
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
            'labels' => $materials,
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
