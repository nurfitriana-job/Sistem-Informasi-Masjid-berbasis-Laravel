<?php

namespace App\Filament\Resources\DashboardInventarisResource\Widgets;

use App\Models\Commodity;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CommodityYearChart extends ApexChartWidget
{
    protected static ?string $chartId = 'commodity-year-chart';

    protected static ?string $heading = 'Jumlah Barang Berdasarkan Tahun Pembelian';

    protected function getOptions(): array
    {
        $data = Commodity::selectRaw('year_of_purchase, COUNT(*) as count')
            ->groupBy('year_of_purchase')
            ->orderBy('year_of_purchase', 'asc')
            ->get();

        $years = [];
        $counts = [];
        $colors = [];

        $availableColors = ['#4caf50', '#f59e0b', '#dc2626', '#3498db', '#9b59b6', '#e74c3c'];

        $colorIndex = 0;

        foreach ($data as $item) {
            $years[] = $item->year_of_purchase;
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
                'categories' => $years,
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
