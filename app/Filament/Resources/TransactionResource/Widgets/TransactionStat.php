<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Enums\TransactionType;
use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;

class TransactionStat extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListTransactions::class;
    }

    protected function getStats(): array
    {
        $totalIncome = $this->getPageTableQuery()->where('type', TransactionType::INCOME->value)->sum('total_credit');
        $totalExpense = $this->getPageTableQuery()->where('type', TransactionType::EXPENSE->value)->sum('total_debit');

        $balance = $totalIncome - $totalExpense;

        return [
            Stat::make('Total Pemasukan', 'Rp. ' . number_format($totalIncome, 0, ',', '.'))
                ->iconColor(TransactionType::INCOME->getColor())
                ->icon(TransactionType::INCOME->getIcon()),

            Stat::make('Total Pengeluaran', 'Rp. ' . number_format($totalExpense, 0, ',', '.'))
                ->iconColor(TransactionType::EXPENSE->getColor())
                ->icon(TransactionType::EXPENSE->getIcon()),

            Stat::make('Saldo Akhir', 'Rp. ' . number_format($balance, 0, ',', '.'))
                ->icon('heroicon-m-banknotes')
                ->iconColor($balance >= 0 ? 'success' : 'danger'),
        ];
    }
}
