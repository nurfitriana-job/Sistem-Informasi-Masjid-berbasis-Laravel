<?php

namespace App\Filament\Resources\DashboardInventarisResource\Widgets;

use App\Models\Commodity;
// Pastikan model ini sudah ada
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CommodityAcquisitionChart extends ApexChartWidget
{
    protected static ?string $chartId = 'commodity-acquisition-chart';

    protected static ?string $heading = 'Jumlah Barang Berdasarkan Perolehan';

    protected function getOptions(): array
    {
        $data = Commodity::selectRaw('commodity_acquisition_id, COUNT(*) as count')
            ->with('commodityAcquisition')
            ->groupBy('commodity_acquisition_id')
            ->orderBy('commodity_acquisition_id', 'asc')
            ->get();

        $acquisitions = [];
        $counts = [];
        $colors = [];

        $availableColors = ['#4caf50', '#f59e0b', '#dc2626', '#3498db', '#9b59b6', '#e74c3c'];

        $colorIndex = 0;

        foreach ($data as $item) {
            $acquisitions[] = $item?->commodityAcquisition?->name;
            $counts[] = $item->count;
            $colors[] = $availableColors[$colorIndex];

            $colorIndex = ($colorIndex + 1) % count($availableColors);
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Jumlah Barang',
                    'data' => $counts,
                ],
            ],
            'xaxis' => [
                'categories' => $acquisitions,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => $colors,
        ];
    }
}
