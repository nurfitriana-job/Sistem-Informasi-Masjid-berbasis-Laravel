<?php

namespace App\Filament\Resources\DashboardInventarisResource\Widgets;

use App\Enums\Condition;
use App\Models\Commodity;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CommodityConditionChart extends ApexChartWidget
{
    protected static ?string $chartId = 'commodity-condition-chart';

    protected static ?string $heading = 'Kondisi Barang';

    protected function getOptions(): array
    {
        $data = Commodity::selectRaw('`condition`, COUNT(*) as count')
            ->groupBy('condition')
            ->orderBy('condition', 'asc')
            ->get();

        $good = 0;
        $bad = 0;
        $broken = 0;

        foreach ($data as $item) {
            if ($item->condition == \App\Enums\Condition::GOOD) {
                $good = $item->count;
            } elseif ($item->condition == \App\Enums\Condition::BAD) {
                $bad = $item->count;
            } elseif ($item->condition == \App\Enums\Condition::BROKEN) {
                $broken = $item->count;
            }
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'plotOptions' => [
                'bar' => [
                    'distributed' => true,
                ],
            ],
            'series' => [
                [
                    'data' => [$good, $bad, $broken],
                ],
            ],
            'xaxis' => [
                'categories' => [
                    Condition::GOOD->getLabel(),
                    Condition::BAD->getLabel(),
                    Condition::BROKEN->getLabel(),
                ],
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
            'colors' => ['#4caf50', '#f59e0b', '#dc2626'],
        ];
    }
}
