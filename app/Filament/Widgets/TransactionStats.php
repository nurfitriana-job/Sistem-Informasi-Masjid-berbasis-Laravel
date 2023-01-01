<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Helpers\Format;
use App\Models\Journal;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class TransactionStats extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $startDate = filled($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])
            : now()->startOfWeek();

        $endDate = filled($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])
            : now()->endOfWeek();

        $commonQuery = fn ($typeField, $amountField) => Journal::where('type', $typeField)
            ->where('status', TransactionStatus::APPROVED->value)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum($amountField);

        $totalIncome = $commonQuery(TransactionType::INCOME->value, 'total_credit');
        $totalExpense = $commonQuery(TransactionType::EXPENSE->value, 'total_debit');

        $balance = $totalIncome - $totalExpense;

        return [
            Stat::make('Total Pemasukan', Format::formatShortCurrency($totalIncome))
                ->iconColor(TransactionType::INCOME->getColor())
                ->icon(TransactionType::INCOME->getIcon())
                ->description('Rp. ' . number_format($totalIncome, 0, ',', '.'))
                ->descriptionColor(TransactionType::INCOME->getColor()),

            Stat::make('Total Pengeluaran', Format::formatShortCurrency($totalExpense))
                ->iconColor(TransactionType::EXPENSE->getColor())
                ->icon(TransactionType::EXPENSE->getIcon())
                ->description('Rp. ' . number_format($totalExpense, 0, ',', '.'))
                ->descriptionColor(TransactionType::EXPENSE->getColor()),

            Stat::make('Saldo Akhir', Format::formatShortCurrency($balance))
                ->icon('heroicon-m-banknotes')
                ->iconColor($balance >= 0 ? 'success' : 'danger')
                ->description('Rp. ' . number_format($balance, 0, ',', '.'))
                ->descriptionColor($balance >= 0 ? 'success' : 'danger'),
        ];
    }
}
