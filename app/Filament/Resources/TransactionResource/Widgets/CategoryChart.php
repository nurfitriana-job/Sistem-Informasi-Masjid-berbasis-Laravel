<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use App\Settings\GeneralSetting;
use Filament\Support\RawJs;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Spatie\Color\Hex;

class CategoryChart extends ApexChartWidget
{
    use InteractsWithPageTable;

    protected static ?string $heading = 'Alokasi Pengeluaran';

    protected static ?string $chartId = 'expense-category-chart';

    protected int | string | array $columnSpan = '1/2';

    protected static ?string $maxHeight = '340px';

    protected function getTablePage(): string
    {
        return ListTransactions::class;
    }

    protected function extraJsOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
        {
            'tooltip': {
                'y': {
                    'formatter': function (val, index) {
                        return val.toLocaleString('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0,
                        });
                    }
                }
            },
        }
    JS);
    }

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getOptions(): array
    {
        $data = $this->getPageTableQuery()
            ->where('type', 'expense')
            ->get();

        $grouped = $data->groupBy(fn ($item) => $item->account?->name ?? 'Tanpa Kategori');

        $chartData = $grouped->map(fn ($items, $key) => [
            'label' => $key,
            'total' => $items->sum(fn ($i) => (float) $i->total_debit),
        ])->values();

        $baseColor = Hex::fromString(app(GeneralSetting::class)->theme_color ?? '#3c5b99');
        $colors = [$baseColor->toHex()];

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 340,
            ],
            'colors' => [
                '#FF4C4C',
                '#FF6666',
                '#FF7F7F',
                '#FF9999',
                '#FFB2B2',
                '#FFCCCC',
                '#FFD6D6',
                '#FFE0E0',
                '#FFEBEB',
                '#FFF5F5',
                '#FFF0F0',
            ],
            'series' => $chartData->pluck('total'),
            'labels' => $chartData->pluck('label'),
            'legend' => [
                'show' => true,
                'position' => 'bottom',
                'horizontalAlign' => 'center',
                'floating' => false,
                'fontSize' => '14px',
                'fontFamily' => 'inherit',
                'fontWeight' => 600,
            ],

        ];
    }
}
