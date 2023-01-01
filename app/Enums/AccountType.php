<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AccountType: string implements HasLabel
{
    case Asset = 'asset';
    case Liability = 'liability';
    case Equity = 'equity';
    case Revenue = 'revenue';
    case Expense = 'expense';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Asset => 'Asset',
            self::Liability => 'Kewajiban',
            self::Equity => 'Ekuitas',
            self::Revenue => 'Pendapatan',
            self::Expense => 'Pengeluaran',
        };
    }

    public static function values(): array
    {
        return [
            self::Asset->value,
            self::Liability->value,
            self::Equity->value,
            self::Revenue->value,
            self::Expense->value,
        ];
    }
}
