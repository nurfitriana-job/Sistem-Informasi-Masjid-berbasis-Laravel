<?php

namespace App\Filament\Resources\TransactionUserResource\Widgets;

use App\Enums\TransactionStatus;
use App\Filament\Resources\TransactionUserResource\Pages\ListTransactionUsers;
use App\Models\Journal;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TransactionStat extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListTransactionUsers::class;
    }

    protected function getStats(): array
    {
        $trxData = Trend::query(
            Journal::when(
                ! auth()->user()->can('verify', Journal::class),
                fn ($query) => $query->where('user_id', auth()->user()->id)
            )
                ->userTransaction()
        )
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            Stat::make('Pembayaran', 'Rp. ' . number_format($this->getPageTableQuery()->where('status', TransactionStatus::APPROVED->value)->sum('total_credit'), 2))
                ->chart(
                    $trxData
                        ->map(fn (TrendValue $value) => $value->aggregate)
                        ->toArray()
                ),
            Stat::make('Diproses', $this->getPageTableQuery()->whereIn('status', [TransactionStatus::REVIEW->value, TransactionStatus::PENDING->value])->count()),
            Stat::make('Rata Rata', 'Rp. ' . number_format($this->getPageTableQuery()->avg('total_credit'), 2)),
        ];
    }
}
